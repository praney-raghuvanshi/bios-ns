<?php

namespace App\Http\Controllers\Reports;

use App\Exports\FlightPerformanceExport;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Flight;
use App\Models\OperationalCalendar;
use App\Models\OperationalCalendarDay;
use App\Models\ScheduleFlight;
use App\Models\ScheduleFlightCustomer;
use App\Models\Zone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class FlightPerformanceReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $operationalYears = OperationalCalendar::active()->get();
        $zones = Zone::active()->get();
        $customers = Customer::active()->get();
        $flights = Flight::active()->get(); // Need to update this - remove duplicate flight numbers
        $startWeeks = $endWeeks = $finalData = [];
        $customerToShow = $weeksToShow = null;
        if ($request->isMethod('post')) {

            [$startWeeks, $endWeeks, $finalData, $customerToShow, $weeksToShow] = $this->prepareReportData(
                $request->input('operational_year'),
                $request->input('start_week'),
                $request->input('end_week'),
                $request->input('zone'),
                $request->input('customer'),
                $request->input('flight')
            );
        }

        return view('reports.flight-performance-report.list', compact('operationalYears', 'zones', 'customers', 'startWeeks', 'endWeeks', 'flights', 'finalData', 'request', 'customerToShow', 'weeksToShow'));
    }

    public function getWeeksForOperationalYear(Request $request)
    {
        try {
            $yearId = $request->input('operational_year');
            // Fetch weeks with start & end dates
            $weeks = OperationalCalendarDay::whereHas('operationalCalendar', function ($query) use ($yearId) {
                $query->where('id', $yearId);
            })
                ->select('week', DB::raw('MIN(day) as start_date'), DB::raw('MAX(day) as end_date'))
                ->groupBy('week')
                ->orderBy('week')
                ->get();
        } catch (Exception $e) {
            return response()->json(['data' => [], 'error' => $e->getMessage()]);
        }

        return response()->json(['data' => $weeks]);
    }

    public function getFlightsForCustomer(Request $request)
    {
        try {
            $customerId = $request->input('customer');

            // Fetch flights for the customer
            $flights = ScheduleFlightCustomer::where('customer_id', $customerId)
                ->with('scheduleFlight.flight')
                ->get()
                ->pluck('scheduleFlight.flight.flight_number')
                ->unique()
                ->values();

            if ($flights) {
                $flights = $flights->toArray();
            }
        } catch (Exception $e) {
            return response()->json(['data' => [], 'error' => $e->getMessage()]);
        }

        return response()->json(['data' => $flights]);
    }

    public function getWeeks($yearId)
    {
        try {
            // Fetch weeks with start & end dates
            $weeks = OperationalCalendarDay::whereHas('operationalCalendar', function ($query) use ($yearId) {
                $query->where('id', $yearId);
            })
                ->select('week', DB::raw('MIN(day) as start_date'), DB::raw('MAX(day) as end_date'))
                ->groupBy('week')
                ->orderBy('week')
                ->get();
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $weeks;
    }

    public function export(Request $request)
    {
        [$startWeeks, $endWeeks, $finalData, $customerToShow, $weeksToShow] = $this->prepareReportData(
            $request->input('export_operational_year'),
            $request->input('export_start_week'),
            $request->input('export_end_week'),
            $request->input('export_zone'),
            $request->input('export_customer'),
            $request->input('export_flight')
        );

        $filename = 'flight-performance-report' . '-' . Str::slug($customerToShow) . '-weeks-' . Str::remove(' ', $weeksToShow) . '.xlsx';

        return Excel::download(new FlightPerformanceExport($startWeeks, $endWeeks, $finalData, $customerToShow, $weeksToShow), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }

    protected function prepareReportData($operationalYear, $startWeek, $endWeek, $zone, $customer, $flight)
    {
        $selectedOperationalYear = $operationalYear;
        $selectedStartWeek = $startWeek;
        $selectedEndWeek = $endWeek;
        $selectedZone = $zone;
        $selectedCustomer = $customer;
        $selectedFlight = $flight;

        $startWeeks = $endWeeks = $this->getWeeks($selectedOperationalYear);

        $customerToShow = Customer::find($selectedCustomer)->name ?? null;
        $weeksToShow = "$selectedStartWeek - $selectedEndWeek";

        // Fetch days based on selected weeks
        $days = OperationalCalendarDay::where('operational_calendar_id', $selectedOperationalYear)
            ->whereBetween('week', [$selectedStartWeek, $selectedEndWeek])
            ->pluck('week', 'day')
            ->toArray();

        $scheduleFlights = ScheduleFlight::with([
            'scheduleFlightCustomers' => function ($query) use ($selectedCustomer) {
                $query->where('customer_id', $selectedCustomer)
                    ->with(['scheduleFlightCustomerProducts', 'scheduleFlightCustomerShipments', 'customer']);
            },
            'scheduleFlightRemarks' => function ($query) use ($selectedCustomer) {
                $query->where('customer_id', $selectedCustomer)->where('is_fpr', 1);
            },
            'flight' => function ($query) {
                $query->with(['location', 'fromAirport', 'toAirport']); // Fetch the location (FROM-TO)
            },
        ])->whereHas('schedule', function ($query) use ($days) {
            $query->whereIn('date', array_keys($days));
        })->whereHas('scheduleFlightCustomers', function ($query) use ($selectedCustomer) {
            $query->where('customer_id', $selectedCustomer);
        })->whereHas('flight', function ($query) use ($selectedZone, $selectedFlight) {
            if ($selectedZone !== 'all') {
                $query->whereHas('location', function ($query) use ($selectedZone) {
                    $query->where('zone_id', $selectedZone);
                });
            }
            if ($selectedFlight !== 'all') {
                $query->where('flight_number', $selectedFlight);
            }
            $query->orderBy('departure_time');
        })->get();

        $locationRoutes = collect();

        foreach ($scheduleFlights as $scheduleFlight) {
            $flight = $scheduleFlight->flight;

            if ($flight) {
                $location = $flight->location->name ?? null;
                $route = $flight->fromAirport->iata . ' - ' . $flight->toAirport->iata;

                if ($location) {
                    if (!isset($locationRoutes[$location])) {
                        $locationRoutes[$location] = collect();
                    }
                    $locationRoutes[$location]->push($route);
                }
            }
        }

        // Remove duplicates for each location
        foreach ($locationRoutes as $loc => $routes) {
            $locationRoutes[$loc] = $routes->unique()->values();
        }

        $finalData = [];

        $groupedDays = [];
        foreach ($days as $day => $week) {
            $groupedDays[$week][] = $day; // Now weeks contain multiple days
        }

        foreach ($locationRoutes as $locationKey => $routes) { // 🔹 Loop: Locations
            $finalData[$locationKey] = [
                'location' => $locationKey,
                'routes' => []
            ];

            $processedRoutes = []; // 🔥 Track routes to avoid duplicate reverse key

            foreach ($routes as $route) { // 🔹 Loop: Routes in this location
                [$from, $to] = explode(' - ', $route);

                $sortedAirports = [$from, $to];
                $routeKey = "{$sortedAirports[0]} - {$sortedAirports[1]}:{$sortedAirports[1]} - {$sortedAirports[0]}";
                $reverseRouteKey = "{$sortedAirports[1]} - {$sortedAirports[0]}:{$sortedAirports[0]} - {$sortedAirports[1]}";

                $from = $sortedAirports[0];
                $to = $sortedAirports[1];

                // ✅ Skip if we already processed this route
                if (isset($processedRoutes[$routeKey]) || isset($processedRoutes[$reverseRouteKey])) {
                    continue;
                }
                $processedRoutes[$routeKey] = true;
                $processedRoutes[$reverseRouteKey] = true;

                foreach ($groupedDays as $week => $weekDays) { // 🔹 Loop: Weeks
                    foreach ($weekDays as $day) { // 🔹 Loop: Days
                        // Get flights for both directions
                        $inboundFlights = $scheduleFlights->filter(function ($scheduleFlight) use ($from, $to, $day) {
                            return $scheduleFlight->flight &&
                                $scheduleFlight->flight->fromAirport->iata == $from &&
                                $scheduleFlight->flight->toAirport->iata == $to &&
                                $scheduleFlight->schedule->date == $day;
                        });

                        $outboundFlights = $scheduleFlights->filter(function ($scheduleFlight) use ($from, $to, $day) {
                            return $scheduleFlight->flight &&
                                $scheduleFlight->flight->fromAirport->iata == $to &&
                                $scheduleFlight->flight->toAirport->iata == $from &&
                                $scheduleFlight->schedule->date == $day;
                        });

                        // Ensure the route key exists before adding flights
                        if (!isset($finalData[$locationKey]['routes'][$routeKey][$week])) {
                            $finalData[$locationKey]['routes'][$routeKey][$week] = [];
                        }

                        // Store flights under the correct week and day
                        $finalData[$locationKey]['routes'][$routeKey][$week][$day] = [
                            'flights' => $inboundFlights->values()->toArray(),
                            'reverse_flights' => $outboundFlights->values()->toArray()
                        ];
                    }
                }
            }
        }

        if (array_key_exists('Malta', $finalData)) {
            if (isset($finalData['Malta']['routes']['LCA - MLA:MLA - LCA']) && isset($finalData['Malta']['routes']['MLA - CGN:CGN - MLA'])) {
                $newKey = 'LCA - MLA:MLA - CGN';

                // Copy LCA → MLA structure as base
                $newData = $finalData['Malta']['routes']['LCA - MLA:MLA - LCA'];

                // Loop through MLA → CGN just once to push flights into reverse_flights
                foreach ($finalData['Malta']['routes']['MLA - CGN:CGN - MLA'] as $week => $days) {
                    foreach ($days as $day => $data) {
                        $newData[$week][$day]['reverse_flights'] = $data['flights'];
                    }
                }

                // Assign merged route
                $finalData['Malta']['routes'][$newKey] = $newData;

                // Remove old ones
                unset($finalData['Malta']['routes']['LCA - MLA:MLA - LCA'], $finalData['Malta']['routes']['MLA - CGN:CGN - MLA']);
            }
        }

        if (array_key_exists('CASABLANCA', $finalData)) {
            //dd($finalData['CASABLANCA']['routes']);
            if (array_key_exists('MAD - CMN:CMN - MAD', $finalData['CASABLANCA']['routes']) && array_key_exists('CMN - TNG:TNG - CMN', $finalData['CASABLANCA']['routes']) && array_key_exists('TNG - MAD:MAD - TNG', $finalData['CASABLANCA']['routes'])) {
                $firstNewKey = 'MAD - CMN:CMN - TNG - MAD';
                $secondNewKey = ':CMN - MAD';

                $newDataOne = $newDataTwo = $finalData['CASABLANCA']['routes']['MAD - CMN:CMN - MAD'];
                foreach ($finalData['CASABLANCA']['routes']['MAD - CMN:CMN - MAD'] as $week => $days) {
                    foreach ($days as $day => $data) {
                        $newDataOne[$week][$day]['reverse_flights'][0] = $finalData['CASABLANCA']['routes']['CMN - TNG:TNG - CMN'][$week][$day]['flights'][0] ?? [];
                        $newDataOne[$week][$day]['reverse_flights'][1] = $finalData['CASABLANCA']['routes']['TNG - MAD:MAD - TNG'][$week][$day]['flights'][0] ?? [];
                        $newDataTwo[$week][$day]['flights'] = [];
                    }
                }
                $finalData['CASABLANCA']['routes'][$firstNewKey] = $newDataOne;
                $finalData['CASABLANCA']['routes'][$secondNewKey] = $newDataTwo;
                unset($finalData['CASABLANCA']['routes']['MAD - CMN:CMN - MAD'], $finalData['CASABLANCA']['routes']['CMN - TNG:TNG - CMN'], $finalData['CASABLANCA']['routes']['TNG - MAD:MAD - TNG']);
            }
        }

        //dd($finalData);

        return [$startWeeks, $endWeeks, $finalData, $customerToShow, $weeksToShow];
    }
}
