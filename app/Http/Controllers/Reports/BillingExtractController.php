<?php

namespace App\Http\Controllers\Reports;

use App\Exports\BillingExtractExport;
use App\Http\Controllers\Controller;
use App\Models\Flight;
use App\Models\ScheduleFlight;
use App\Models\Zone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BillingExtractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $zones = Zone::active()->get();
        $flights = Flight::active()->pluck('flight_number')->unique()->toArray();
        $finalData = [];

        if ($request->isMethod('post')) {

            $finalData = $this->prepareReportData(
                $request->input('start_date'),
                $request->input('end_date'),
                $request->input('zone'),
                $request->input('flight')
            );
        }

        return view('reports.billing-extract.list', compact('zones', 'flights', 'finalData'));
    }

    public function export(Request $request)
    {
        $finalData = $this->prepareReportData(
            $request->input('export_start_date'),
            $request->input('export_end_date'),
            $request->input('export_zone'),
            $request->input('export_flight')
        );

        $filename = 'billing-extract.xlsx';

        return Excel::download(new BillingExtractExport($finalData), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }

    protected function prepareReportData($startDate, $endDate, $zone, $flight)
    {
        $scheduleFlights = ScheduleFlight::with([
            'scheduleFlightCustomers',
            'scheduleFlightCustomers.scheduleFlightCustomerProducts',
            'scheduleFlightCustomers.scheduleFlightCustomerShipments',
            'scheduleFlightCustomers.customer',
            'flight' => function ($query) {
                $query->with(['location', 'fromAirport', 'toAirport']);
            },
        ])->whereHas('schedule', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        })->whereHas('flight', function ($query) use ($zone, $flight) {
            if ($zone !== 'all') {
                $query->whereHas('location', function ($query) use ($zone) {
                    $query->where('zone_id', $zone);
                });
            }
            if ($flight !== 'all') {
                $query->where('flight_number', $flight);
            }
        })->get();

        $data = [];

        foreach ($scheduleFlights as $scheduleFlight) {
            foreach ($scheduleFlight->scheduleFlightCustomers as $scheduleFlightCustomer) {
                foreach ($scheduleFlightCustomer->scheduleFlightCustomerShipments as $scheduleFlightCustomerShipment) {
                    $data[] = [
                        'date' => Carbon::parse($scheduleFlight->schedule->date)->format('d/m/Y'),
                        'origin' => $scheduleFlight->flight->fromAirport->iata,
                        'destination' => $scheduleFlight->flight->toAirport->iata,
                        'end_destination' => $scheduleFlightCustomerShipment->toAirport->iata,
                        'flight' => $scheduleFlight->flight->flight_number,
                        'awb' => $scheduleFlightCustomerShipment->awb,
                        'declared' => $scheduleFlightCustomerShipment->declared_weight,
                        'actual' => $scheduleFlightCustomerShipment->actual_weight,
                        'volume' => $scheduleFlightCustomerShipment->volumetric_weight,
                        'total_actual' => $scheduleFlightCustomerShipment->total_actual_weight,
                        'total_volume' => $scheduleFlightCustomerShipment->total_volumetric_weight,
                        'shipment_type' => $scheduleFlightCustomerShipment->type,
                        'customer' => $scheduleFlightCustomer->customer->code,
                        'product' => $scheduleFlightCustomerShipment->product->code
                    ];
                }
            }
        }

        return $data;
    }
}
