<?php

namespace App\Http\Controllers\Reports;

use App\Exports\DailyExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Zone;
use App\Models\Flight;
use App\Models\ScheduleFlight;
use Maatwebsite\Excel\Facades\Excel;

class DailyReportController extends Controller
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

        return view('reports.daily-report.list', compact('zones', 'flights', 'finalData'));
    }

    public function export(Request $request)
    {
        $finalData = $this->prepareReportData(
            $request->input('export_start_date'),
            $request->input('export_end_date'),
            $request->input('export_zone'),
            $request->input('export_flight')
        );

        $filename = 'daily-report.xlsx';

        return Excel::download(new DailyExport($finalData), $filename, \Maatwebsite\Excel\Excel::XLSX);
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
        ])
            ->whereHas('schedule', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->whereHas('flight', function ($query) use ($zone, $flight) {
                // ✅ Zone filter
                if (!in_array('all', (array) $zone)) {
                    $query->whereHas('location', function ($subQuery) use ($zone) {
                        $subQuery->whereIn('zone_id', (array) $zone);
                    });
                }

                // ✅ Flight filter
                if (!in_array('all', (array) $flight)) {
                    $query->whereIn('flight_number', (array) $flight);
                }
            })
            ->where('status', '!=', 3)         // Ignore Cancelled Flights
            ->get();

        $data = [];

        $data = collect($scheduleFlights)->flatMap(function ($scheduleFlight) {
            return collect($scheduleFlight->scheduleFlightCustomers)->flatMap(function ($scheduleFlightCustomer) use ($scheduleFlight) {
                $shipments = $scheduleFlightCustomer->scheduleFlightCustomerShipments;
                $products  = $scheduleFlightCustomer->scheduleFlightCustomerProducts;

                // Case 1: has shipments
                if ($shipments->isNotEmpty()) {
                    return $shipments->map(function ($shipment) use ($scheduleFlight, $scheduleFlightCustomer) {
                        return [
                            'date'          => Carbon::parse($scheduleFlight->schedule->date)->format('d-m-Y'),
                            'raw_date'      => $scheduleFlight->schedule->date,
                            'origin'        => $scheduleFlight->flight->fromAirport->iata ?? null,
                            'destination'   => $scheduleFlight->flight->toAirport->iata ?? null,
                            'end_destination' => $shipment->toAirport->iata ?? null,
                            'flight'        => $scheduleFlight->flight->flight_number ?? null,
                            'awb'           => (string) $shipment->awb ?? null,
                            'declared'      => $shipment->declared_weight ?? null,
                            'actual'        => $shipment->actual_weight ?? null,
                            'volume'        => $shipment->volumetric_weight ?? null,
                            'total_actual'  => $shipment->total_actual_weight ?? null,
                            'total_volume'  => $shipment->total_volumetric_weight ?? null,
                            'shipment_type' => $shipment->type ?? null,
                            'customer'      => $scheduleFlightCustomer->customer->code ?? null,
                            'product'       => $shipment->product->code ?? null,
                        ];
                    });
                }

                // Case 2: no shipments, fallback to products
                if ($products->isNotEmpty()) {
                    return $products->map(function ($product) use ($scheduleFlight, $scheduleFlightCustomer) {
                        return [
                            'date'          => Carbon::parse($scheduleFlight->schedule->date)->format('d-m-Y'),
                            'raw_date'      => $scheduleFlight->schedule->date,
                            'origin'        => $scheduleFlight->flight->fromAirport->iata,
                            'destination'   => $scheduleFlight->flight->toAirport->iata,
                            'end_destination' => null,
                            'flight'        => $scheduleFlight->flight->flight_number,
                            'awb'           => null,
                            'declared'      => null,
                            'actual'        => $product->uplifted_weight,
                            'volume'        => null,
                            'total_actual'  => null,
                            'total_volume'  => null,
                            'shipment_type' => null,
                            'customer'      => $scheduleFlightCustomer->customer->code ?? null,
                            'product'       => $product->product->code ?? null, // from product list
                        ];
                    });
                }

                // Case 3: neither shipments nor products
                return [[
                    'date'          => Carbon::parse($scheduleFlight->schedule->date)->format('d-m-Y'),
                    'raw_date'      => $scheduleFlight->schedule->date,
                    'origin'        => $scheduleFlight->flight->fromAirport->iata,
                    'destination'   => $scheduleFlight->flight->toAirport->iata ?? null,
                    'end_destination' => null,
                    'flight'        => $scheduleFlight->flight->flight_number ?? null,
                    'awb'           => null,
                    'declared'      => null,
                    'actual'        => null,
                    'volume'        => null,
                    'total_actual'  => null,
                    'total_volume'  => null,
                    'shipment_type' => null,
                    'customer'      => $scheduleFlightCustomer->customer->code ?? null,
                    'product'       => null,
                ]];
            });
        })->values()->all();

        return $data;
    }
}
