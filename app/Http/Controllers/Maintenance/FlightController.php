<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\Aircraft;
use App\Models\Airport;
use App\Models\Customer;
use App\Models\Flight;
use App\Models\FlightDay;
use App\Models\Location;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Helpers\Helpers;
use Illuminate\Support\Arr;

class FlightController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $processedFlights = $flights = [];
        $clonedFlight = null;
        if ($request->has('location') || session()->has('location')) {

            if (session('location') != $request->location) {
                session()->put('location', $request->location); // save location to session
            }

            $location = $request->location ?? session('location');

            $flights = Flight::with(['location', 'fromAirport', 'toAirport', 'aircraft', 'flightDays'])
                ->where('location_id', $location)
                ->when(!$request->has('include_inactive'), function ($query) {
                    $query->where('active', 1); // Only active flights when checkbox is NOT checked
                })
                ->get()
                ->groupBy('flight_pair_id');
        }
        $locations = Location::active()->get();
        $airports = Airport::active()->get();
        $aircrafts = Aircraft::active()->get();

        if (count($flights) > 0) {

            $dayNames = [1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun'];

            foreach ($flights as $flightPairId => $flightGroup) {

                // Create a structured row for each pair
                $row = [
                    'days' => array_fill_keys(array_values($dayNames), 0),
                    'effective_date' => Carbon::parse($flightGroup->first()->effective_date)->format('d/m/Y') ?? 'N/A',
                    'added_by' => $flightGroup->first()->addedByUser->name ?? 'N/A',
                    'flight_pair_id' => $flightPairId
                ];

                $activeFlightDays = $flightGroup
                    ->flatMap(fn($flight) => $flight->flightDays()->active()->pluck('day'))
                    ->unique()
                    ->toArray();

                for ($i = 1; $i <= 7; $i++) {
                    if (in_array($i, $activeFlightDays)) {
                        $row['days'][$dayNames[$i]] = 1; // Highlight active days
                    }
                }

                // Populate days based on available flight days
                foreach ($flightGroup as $flight) {

                    if ($flight->flight_type == 'inbound') {
                        $row['inbound'] = [
                            'flight_number' => $flight->flight_number,
                            'from' => $flight->fromAirport->iata,
                            'from_id' => $flight->from,
                            'to' => $flight->toAirport->iata,
                            'to_id' => $flight->to,
                            'std' => Carbon::parse($flight->departure_time)->format('H:i'),
                            'sta' => Carbon::parse($flight->arrival_time)->format('H:i'),
                            'aircraft' => $flight->aircraft->name,
                            'aircraft_id' => $flight->aircraft_id,
                            'capacity' => $flight->aircraft->capacity,
                            'active' => $flight->active,
                        ];
                    }

                    if ($flight->flight_type == 'outbound') {
                        $row['outbound'] = [
                            'flight_number' => $flight->flight_number,
                            'from' => $flight->fromAirport->iata,
                            'from_id' => $flight->from,
                            'to' => $flight->toAirport->iata,
                            'to_id' => $flight->to,
                            'std' => Carbon::parse($flight->departure_time)->format('H:i'),
                            'sta' => Carbon::parse($flight->arrival_time)->format('H:i'),
                            'aircraft' => $flight->aircraft->name,
                            'aircraft_id' => $flight->aircraft_id,
                            'capacity' => $flight->aircraft->capacity,
                            'active' => $flight->active,
                        ];
                    }
                }

                if ($request->has('clone_id') && $request->clone_id === $flightPairId) {
                    $clonedFlight = $row; // Store the cloned flight data
                    $clonedFlight['days'] = $activeFlightDays;

                    if (isset($row['inbound']) && isset($row['outbound'])) {
                        $clonedFlight['direction'] = 'both';
                    } elseif (isset($row['inbound'])) {
                        $clonedFlight['direction'] = 'inbound';
                    } elseif (isset($row['outbound'])) {
                        $clonedFlight['direction'] = 'outbound';
                    }
                }

                $processedFlights[] = $row;
            }
        }

        //dd($processedFlights);

        return view('maintenance.flight.list', compact('processedFlights', 'locations', 'airports', 'aircrafts', 'clonedFlight'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //dd($request->all());

        $validator = Validator::make($request->all(), [
            'location' => ['required', 'numeric'],
            'i_flight' => ['nullable', 'string'],
            'i_from' => ['nullable', 'numeric', 'different:i_to'],
            'i_to' => ['nullable', 'numeric', 'different:i_from'],
            'i_departure_time' => ['nullable', 'date_format:H:i'],
            'i_arrival_time' => ['nullable', 'date_format:H:i'],
            'i_aircraft' => ['nullable', 'numeric'],
            'effective_date' => ['required', 'date'],
            'day' => ['required', 'array'],
            'o_flight' => ['nullable', 'string'],
            'o_from' => ['nullable', 'numeric', 'different:o_to'],
            'o_to' => ['nullable', 'numeric', 'different:o_from'],
            'o_departure_time' => ['nullable', 'date_format:H:i'],
            'o_arrival_time' => ['nullable', 'date_format:H:i'],
            'o_aircraft' => ['nullable', 'numeric'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {

            DB::beginTransaction();

            $flightPairId = Str::uuid();

            $errors = $warnings = [];

            $inboundFlight = $outboundFlight = null;

            if ($request->has(['i_flight', 'i_from', 'i_to', 'i_departure_time', 'i_arrival_time', 'i_aircraft'])) {

                if ($request->has('clone_id')) {
                    $inboundFlight = $this->insertFlight($request, $flightPairId, 'inbound', $request->day, []);
                    $inboundFlight->update(['cloned_from' => $request->clone_id]);
                } else {
                    [$selectedDays, $existingDays] = $this->checkIfFlightExists($request, 'inbound');

                    // If all selected days already exist, prevent insertion and return an error
                    if (count($existingDays) === count($selectedDays)) {
                        $errors[] = 'The inbound flight already exists for all selected days: ' . implode(', ', Helpers::getFlightDaysName($existingDays));
                    } else {
                        // Insert flight only if there are no errors
                        $inboundFlight = $this->insertFlight($request, $flightPairId, 'inbound', $selectedDays, $existingDays);

                        // If some days already exist, add a warning
                        if (!empty($existingDays)) {
                            $warnings[] = 'Inbound flight added, but already existed on: ' . implode(', ', Helpers::getFlightDaysName($existingDays));
                        }
                    }
                }
            }

            if ($request->has(['o_flight', 'o_from', 'o_to', 'o_departure_time', 'o_arrival_time', 'o_aircraft'])) {

                if ($request->has('clone_id')) {
                    $outboundFlight = $this->insertFlight($request, $flightPairId, 'outbound', $request->day, []);
                    $outboundFlight->update(['cloned_from' => $request->clone_id]);
                } else {
                    [$selectedDays, $existingDays] = $this->checkIfFlightExists($request, 'outbound');

                    // Case 1: If all selected days already exist, prevent insertion and return an error
                    if (count($existingDays) == count($selectedDays)) {
                        $errors[] = 'The outbound flight already exists for all selected days: ' . implode(', ', Helpers::getFlightDaysName($existingDays));
                    } else {
                        // Insert flight only if there are no errors
                        $outboundFlight = $this->insertFlight($request, $flightPairId, 'outbound', $selectedDays, $existingDays);

                        // If some days already exist, add a warning
                        if (!empty($existingDays)) {
                            $warnings[] = 'Outbound flight added, but already existed on: ' . implode(', ', Helpers::getFlightDaysName($existingDays));
                        }
                    }
                }
            }

            if (!empty($inboundFlight) && !empty($outboundFlight)) {
                $inboundFlight->update(['corresponding_flight' => $outboundFlight->id]);
                $outboundFlight->update(['corresponding_flight' => $inboundFlight->id]);
            }

            if (count($errors) > 0) {
                return back()->with('failure', $errors)->withInput();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        if (count($warnings) > 0) {
            return redirect()->route('maintenance.flight.list', request()->query())->with('warning', $warnings);
        }

        $queryParams = Arr::except(request()->query(), ['clone_id']);

        return redirect()->route('maintenance.flight.list', $queryParams)->with('success', 'Flight added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $flightPairId)
    {
        $flights = Flight::with(['flightDays'])->where('flight_pair_id', $flightPairId)->get();

        $processedFlights = [];

        if (count($flights) > 0) {

            foreach ($flights as $flight) {

                $activeFlightDays = $flight->flightDays;

                if ($flight->flight_type == 'inbound') {
                    $row['inbound'] = [
                        'id' => $flight->id,
                        'formatted_id' => $flight->formatted_id,
                        'flight_number' => $flight->flight_number,
                        'from' => $flight->fromAirport->iata,
                        'to' => $flight->toAirport->iata,
                        'std' => Carbon::parse($flight->departure_time)->format('H:i'),
                        'sta' => Carbon::parse($flight->arrival_time)->format('H:i'),
                        'aircraft' => $flight->aircraft->name,
                        'capacity' => $flight->aircraft->capacity,
                        'active' => $flight->active,
                        'days' => $activeFlightDays,

                    ];
                }

                if ($flight->flight_type == 'outbound') {
                    $row['outbound'] = [
                        'id' => $flight->id,
                        'formatted_id' => $flight->formatted_id,
                        'flight_number' => $flight->flight_number,
                        'from' => $flight->fromAirport->iata,
                        'to' => $flight->toAirport->iata,
                        'std' => Carbon::parse($flight->departure_time)->format('H:i'),
                        'sta' => Carbon::parse($flight->arrival_time)->format('H:i'),
                        'aircraft' => $flight->aircraft->name,
                        'capacity' => $flight->aircraft->capacity,
                        'active' => $flight->active,
                        'days' => $activeFlightDays
                    ];
                }
            }

            $processedFlights = $row;
        }

        //dd($processedFlights);

        return view('maintenance.flight.detail', compact('processedFlights'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function inactiveFlightDay(Flight $flight, FlightDay $flightDay)
    {
        try {

            DB::beginTransaction();

            // Mark the flight day as inactive
            $flightDay->update(['active' => 0]);

            // Check if there are any other active flight days for this flight
            $activeFlightDays = $flight->flightDays()->active()->count();

            // If no active flight days remain, make the flight inactive
            if ($activeFlightDays === 0) {
                $flight->update(['active' => 0]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.flight.show', $flight->flight_pair_id)->with('success', 'Flight Day inactivated successfully.');
    }

    public function activeFlightDay(Flight $flight, FlightDay $flightDay)
    {
        try {

            DB::beginTransaction();

            // Mark the flight day as active
            $flightDay->update(['active' => 1]);

            $flight->update(['active' => 1]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.flight.show', $flight->flight_pair_id)->with('success', 'Flight Day activated successfully.');
    }

    public function flightDayDestroy(Flight $flight, FlightDay $flightDay)
    {
        try {
            DB::beginTransaction();

            $flightDay->update([
                'deleted_by' => Auth::id()
            ]);

            $flightDay->delete();

            // Check if there are any other active flight days for this flight
            $activeFlightDays = $flight->flightDays()->active()->count();

            // If no active flight days remain, delete the flight but not if used in Schedule
            // TODO
            if ($activeFlightDays === 0) {
                $flight->update(['deleted_by' => Auth::id()]);
                $flight->delete();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.flight.show', $flight->flight_pair_id)->with('success', 'Flight Day deleted successfully.');
    }

    public function flightDayCustomers(Flight $flight, FlightDay $flightDay)
    {
        $activeCustomers = Customer::active()->get();
        $flightDayCustomers = $flightDay->customers->pluck('id')->toArray();
        $linkedCustomers = $flightDay->customers->pluck('name')->toArray();
        return view('maintenance.flight.customer', compact('flight', 'flightDay', 'activeCustomers', 'linkedCustomers', 'flightDayCustomers'));
    }

    public function manageFlightDayCustomers(Request $request, Flight $flight, FlightDay $flightDay)
    {
        try {
            DB::beginTransaction();

            $customers = Customer::whereIn('id', $request->customers)->get(); // Get selected customers

            $syncData = [];
            foreach ($customers as $customer) {
                $syncData[$customer->id] = ['added_by' => Auth::id()];
            }

            if ($request->has('update_all_days')) {
                // If checkbox is checked, update all days for this flight
                foreach ($flight->flightDays as $day) {
                    $day->customers()->sync($syncData);
                }
            } else {
                // If checkbox is NOT checked, update only the selected flight day
                $flightDay->customers()->sync($syncData);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.flight.day.customer', [$flight, $flightDay])->with('success', 'Customers linked with Flight Day successfully.');
    }

    // public function clone(string $flightPairId)
    // {
    //     $flights = Flight::with(['flightDays'])->where('flight_pair_id', $flightPairId)->get();
    //     dd($flights);
    // }

    private function insertFlight($request, $flightPairId, $flightDirection, $selectedDays, $existingDays)
    {
        if ($flightDirection === 'inbound') {
            $arrivalTime = $request->input('i_arrival_time');
            $departureTime = $request->input('i_departure_time');
            $flightNumber = $request->input('i_flight');
            $from = $request->input('i_from');
            $to = $request->input('i_to');
            $aircraft = $request->input('i_aircraft');
        } else {
            $arrivalTime = $request->input('o_arrival_time');
            $departureTime = $request->input('o_departure_time');
            $flightNumber = $request->input('o_flight');
            $from = $request->input('o_from');
            $to = $request->input('o_to');
            $aircraft = $request->input('o_aircraft');
        }

        $locationId = $request->input('location');

        $customers = Customer::whereHas('emails.locations', function ($query) use ($locationId) {
            $query->where('location_id', $locationId);
        })->get();

        $arrivalDay = 0;
        if ($arrivalTime < $departureTime) {
            $arrivalDay = 1;
        }

        // Flight
        $flight = Flight::create([
            'flight_pair_id' => $flightPairId,
            'flight_number' => $flightNumber,
            'location_id' => $locationId,
            'from' => $from,
            'to' => $to,
            'departure_time' => $departureTime,
            'arrival_time' => $arrivalTime,
            'aircraft_id' => $aircraft,
            'effective_date' => $request->input('effective_date'),
            'arrival_day' => $arrivalDay,
            'flight_type' => $flightDirection,
            'active' => 1,
            'added_by' => Auth::id()
        ]);

        // Insert only the new days (skip existing ones)
        foreach ($selectedDays as $day) {
            if (!in_array($day, $existingDays)) {
                $flightDay = FlightDay::create([
                    'flight_id' => $flight->id,
                    'day' => $day,
                    'added_by' => Auth::id()
                ]);

                $syncData = [];
                foreach ($customers as $customer) {
                    $syncData[$customer->id] = ['added_by' => Auth::id()];
                }

                $flightDay->customers()->sync($syncData);
            }
        }

        return $flight;
    }

    private function checkIfFlightExists($request, $flightDirection)
    {
        if ($flightDirection === 'inbound') {
            $flightNumber = $request->i_flight;
            $from = $request->i_from;
            $to = $request->i_to;
        } else {
            $flightNumber = $request->o_flight;
            $from = $request->o_from;
            $to = $request->o_to;
        }

        $selectedDays = $request->day;

        // Find existing flights with the same flight number, from and to
        $existingFlights = Flight::where('flight_number', $flightNumber)
            ->where('from', $from)
            ->where('to', $to)
            ->where('active', 1)
            ->where('flight_type', $flightDirection)
            ->with('flightDays') // Fetch associated days
            ->get();

        $existingDays = [];

        // Check which days are already present
        foreach ($existingFlights as $flight) {
            foreach ($flight->flightDays as $datum) {
                if (in_array($datum->day, $selectedDays)) {
                    $existingDays[] = $datum->day;
                }
            }
        }

        $existingDays = array_unique($existingDays); // Remove duplicates

        return [$selectedDays, $existingDays];
    }

    public function checkFlight(Request $request)
    {
        $flightNumber = $request->input('flightNumber');
        $data = [
            'success' => false,
            'fill' => false,
            'data' => []
        ];

        try {

            $result = Flight::where('flight_number', $flightNumber)->first();

            if ($result) {
                $data = [
                    'success' => true,
                    'fill' => true,
                    'data' => $result
                ];
            } else {
                $data = [
                    'success' => true,
                    'fill' => false,
                    'data' => []
                ];
            }
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }

        return response()->json($data);
    }
}
