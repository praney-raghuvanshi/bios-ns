<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Flight;
use App\Models\Schedule;
use App\Models\ScheduleFlight;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            })->with(['fromAirport', 'toAirport', 'aircraft'])->whereDate('effective_date', '<=', Carbon::now())->get();
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
}
