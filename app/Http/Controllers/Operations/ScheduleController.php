<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Aircraft;
use App\Models\AircraftType;
use App\Models\Airport;
use App\Models\Customer;
use App\Models\Flight;
use App\Models\FlightDay;
use App\Models\Location;
use App\Models\Schedule;
use App\Models\ScheduleFlight;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedules = Schedule::orderBy('id', 'desc')->get();
        return view('operations.schedule.list', compact('schedules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('operations.schedule.add');
    }

    public function confirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => ['required', 'date', 'unique:schedules,date,NULL,id,deleted_at,NULL'],
            'description' => ['required', 'string'],
            'schedule_type' => ['required']
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        $scheduleDate = $request->input('date');
        $scheduleDay = Carbon::parse($scheduleDate)->format('N');
        $scheduleType = $request->input('schedule_type');

        $flights = collect();

        if ($scheduleType === 'auto') {
            // Fetch flights scheduled on the given day
            $flights = Flight::whereHas('flightDays', function ($query) use ($scheduleDay) {
                $query->where('day', $scheduleDay);
            })->with(['fromAirport', 'toAirport', 'aircraftType'])->whereDate('effective_date', '<=', Carbon::now())->get();
        }

        return view('operations.schedule.confirm', compact('request', 'flights'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $requestData = json_decode($request->input('request'), true);

            $scheduleDate = $requestData['date'];

            DB::beginTransaction();

            // Add Schedule
            $schedule = Schedule::create([
                'date' => $scheduleDate,
                'description' => $requestData['description'],
                'day' => Carbon::parse($scheduleDate)->format('N'),
                'type' => $requestData['schedule_type'],
                'added_by' => Auth::id()
            ]);

            // Add Flights to Schedule

            $flightIds = array_filter(explode(',', $request->input('flights'))); // Remove empty values

            foreach ($flightIds as $flightId) {
                ScheduleFlight::create([
                    'schedule_id' => $schedule->id,
                    'flight_id' => $flightId,
                    'added_by' => Auth::id()
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('flight-operations.schedule.list')->with('success', 'Schedule added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        // Eager load flights associated with the schedule
        $schedule->load('scheduleFlights.flight');

        return view('operations.schedule.detail', compact('schedule'));
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
    public function destroy(Schedule $schedule)
    {
        try {
            DB::beginTransaction();

            // TODO : Need to delete all related data for the schedule

            $schedule->update([
                'deleted_by' => Auth::id()
            ]);

            $schedule->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('flight-operations.schedule.list')->with('success', 'Schedule deleted successfully.');
    }

    public function manualList(Schedule $schedule)
    {
        $scheduleDate = $schedule->date;
        $scheduleDay = Carbon::parse($scheduleDate)->format('N');
        $flights = Flight::whereHas('flightDays', function ($query) use ($scheduleDay) {
            $query->where('day', $scheduleDay);
        })->whereDoesntHave('schedules', function ($query) use ($schedule) {
            $query->where('schedule_id', $schedule->id);
        })->with(['fromAirport', 'toAirport', 'aircraftType'])->whereDate('effective_date', '<=', $scheduleDate)->get();
        return view('operations.schedule.flight.list', compact('schedule', 'flights'));
    }

    public function manualStore(Request $request, Schedule $schedule)
    {
        try {
            $flightIds = $request->input('flight');

            foreach ($flightIds as $flightId) {
                ScheduleFlight::create([
                    'schedule_id' => $schedule->id,
                    'flight_id' => $flightId,
                    'added_by' => Auth::id()
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('flight-operations.schedule.show', $schedule)->with('success', 'Flights added to Schedule successfully.');
    }

    public function contingency(Schedule $schedule)
    {
        $airports = Airport::active()->get();
        $aircraftTypes = AircraftType::active()->get();
        $locations = Location::active()->get();
        return view('operations.schedule.flight.contingency', compact('schedule', 'airports', 'aircraftTypes', 'locations'));
    }

    public function contingencyStore(Request $request, Schedule $schedule)
    {
        try {

            DB::beginTransaction();

            $flightPairId = Str::uuid();

            $locationId = $request->input('location');
            $arrivalTime = $request->input('arrival_time');
            $departureTime = $request->input('departure_time');
            $flightNumber = $request->input('flight');
            $from = $request->input('from');
            $to = $request->input('to');
            $aircraftType = $request->input('aircraft');
            $flightDirection = $request->input('direction');
            $arrivalDay = $request->input('arrival_day');

            $customers = Customer::whereHas('emails.locations', function ($query) use ($locationId) {
                $query->where('location_id', $locationId);
            })->get();

            // Flight
            $flight = Flight::create([
                'flight_pair_id' => $flightPairId,
                'flight_number' => $flightNumber,
                'location_id' => $locationId,
                'from' => $from,
                'to' => $to,
                'departure_time' => $departureTime,
                'arrival_time' => $arrivalTime,
                'aircraft_type_id' => $aircraftType,
                'effective_date' => $request->input('effective_date'),
                'arrival_day' => $arrivalDay,
                'flight_type' => $flightDirection,
                'active' => 1,
                'added_by' => Auth::id()
            ]);


            $flightDay = FlightDay::create([
                'flight_id' => $flight->id,
                'day' => $schedule->day,
                'added_by' => Auth::id()
            ]);

            $syncData = [];
            foreach ($customers as $customer) {
                $syncData[$customer->id] = ['added_by' => Auth::id()];
            }

            $flightDay->customers()->sync($syncData);


            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('flight-operations.schedule.manual.list', [$schedule])->with('success', 'Contingency Flight added successfully.');
    }
}
