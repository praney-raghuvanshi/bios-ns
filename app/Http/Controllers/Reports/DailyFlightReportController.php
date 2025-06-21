<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Flight;
use App\Models\ScheduleFlight;
use App\Models\Zone;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DailyFlightReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $finalData = $finalReport = [];

        if ($request->isMethod('post')) {

            $finalData = $this->prepareReportData(
                $request->input('flight_date')
            );

            $finalReport = $this->formatReportData($finalData);
        }

        return view('reports.daily-flight-report.list', compact('request', 'finalReport'));
    }

    public function export(Request $request)
    {
        $finalData = $this->prepareReportData(
            $request->input('export_flight_date')
        );

        $filename = 'billing-extract.xlsx';

        //return Excel::download(new BillingExtractExport($finalData), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }

    protected function prepareReportData($flightDate)
    {
        $scheduleFlights = ScheduleFlight::with([
            'scheduleFlightCustomers',
            'scheduleFlightCustomers.scheduleFlightCustomerProducts',
            'scheduleFlightCustomers.scheduleFlightCustomerShipments',
            'scheduleFlightCustomers.customer',
            'flight' => function ($query) {
                $query->with(['location', 'fromAirport', 'toAirport']);
            },
        ])->whereHas('schedule', function ($query) use ($flightDate) {
            $query->where('date', $flightDate);
        })->get();

        $data = [];

        foreach ($scheduleFlights as $scheduleFlight) {
            $flightRow = [
                'date' => Carbon::parse($scheduleFlight->schedule->date)->format('d/m/Y'),
                'origin' => optional($scheduleFlight->flight->fromAirport)->iata,
                'destination' => optional($scheduleFlight->flight->toAirport)->iata,
                'flight' => optional($scheduleFlight->flight)->flight_number,
                'std' => Carbon::parse($scheduleFlight->flight->departure_time)->format('H:i'),
                'atd' => Carbon::parse($scheduleFlight->actual_departure_time)->format('H:i'),
                'departure_time_difference' => $scheduleFlight->departure_time_diff,
                'sta' => Carbon::parse($scheduleFlight->flight->arrival_time)->format('H:i'),
                'ata' => Carbon::parse($scheduleFlight->actual_arrival_time)->format('H:i'),
                'arrival_time_difference' => $scheduleFlight->arrival_time_diff,
                'customers' => []
            ];

            $customers = $scheduleFlight->scheduleFlightCustomers;

            foreach ($customers as $scheduleFlightCustomer) {
                $customerCode = optional($scheduleFlightCustomer->customer)->code;

                $customerData = [
                    'customer' => $customerCode,
                    'shipments' => []
                ];

                $shipments = $scheduleFlightCustomer->scheduleFlightCustomerShipments;

                foreach ($shipments as $shipment) {
                    $customerData['shipments'][] = [
                        'awb' => $shipment->awb,
                        'declared' => $shipment->declared_weight,
                        'actual' => $shipment->actual_weight,
                        'volume' => $shipment->volumetric_weight,
                        'total_actual' => $shipment->total_actual_weight,
                        'total_volume' => $shipment->total_volumetric_weight,
                        'offloaded_weight' => $shipment->offloaded_weight,
                        'uplifted_weight' => $shipment->uplifted_weight,
                        'shipment_type' => $shipment->type,
                        'end_destination' => optional($shipment->toAirport)->iata,
                        'product' => optional($shipment->product)->code
                    ];
                }

                $flightRow['customers'][] = $customerData;
            }

            $data[] = $flightRow;
        }

        return $data;
    }

    protected function formatReportData($finalData)
    {
        $finalReport = [];

        foreach ($finalData as $flight) {
            $flightInfo = [
                'date' => $flight['date'],
                'origin' => $flight['origin'],
                'destination' => $flight['destination'],
                'flight_number' => $flight['flight'],
                'std' => $flight['std'],
                'atd' => $flight['atd'],
                'sta' => $flight['sta'],
                'ata' => $flight['ata'],
                'departure_time_difference' => $flight['departure_time_difference'],
                'arrival_time_difference' => $flight['arrival_time_difference'],
            ];

            $shipmentGroups = [];

            foreach ($flight['customers'] as $customerData) {
                $customer = $customerData['customer'];

                foreach ($customerData['shipments'] as $shipment) {
                    $product = $shipment['product'];
                    $uplifted = $shipment['uplifted_weight'];
                    $offloaded = $shipment['offloaded_weight'];

                    // Skip if both weights are zero
                    if ($uplifted <= 0 && $offloaded <= 0) {
                        continue;
                    }

                    $key = $customer . '-' . $product;

                    if (!isset($shipmentGroups[$key])) {
                        $shipmentGroups[$key] = [
                            'customer' => $customer,
                            'product' => $product,
                            'uplifted_weight' => 0,
                            'offloaded_weight' => 0,
                        ];
                    }

                    $shipmentGroups[$key]['uplifted_weight'] += $uplifted;
                    $shipmentGroups[$key]['offloaded_weight'] += $offloaded;
                }
            }

            // Add grouped results to final report per flight
            $finalReport[] = array_merge($flightInfo, [
                'shipments' => array_values($shipmentGroups),
            ]);
        }

        return $finalReport;
    }
}
