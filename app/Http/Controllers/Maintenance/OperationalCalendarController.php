<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\OperationalCalendar;
use App\Models\OperationalCalendarDay;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OperationalCalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $operationalCalendars = OperationalCalendar::all();
        return view('maintenance.operational-calendar.list', compact('operationalCalendars'));
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
        $validator = Validator::make($request->all(), [
            'year' => ['required', 'numeric', 'unique:operational_calendars,year,NULL,id,deleted_at,NULL'],
            'start_date' => ['required', 'date'],
            'weeks' => ['required', 'numeric'],
            'status' => ['required']
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {

            DB::beginTransaction();

            $startDate = $request->input('start_date');
            $weeks = $request->input('weeks');

            $operationalCalendar = OperationalCalendar::create([
                'year' => $request->input('year'),
                'start_date' => $startDate,
                'weeks' => $weeks,
                'active' => $request->input('status'),
                'added_by' => Auth::id()
            ]);

            $startDate = Carbon::parse($startDate);
            $endDate   = $startDate->copy()->addWeeks($weeks)->subDay();

            $currentDate = $startDate->copy();

            while ($currentDate->lte($endDate)) {
                OperationalCalendarDay::create([
                    'operational_calendar_id' => $operationalCalendar->id,
                    'day'         => $currentDate->copy(),
                    'week'    => $currentDate->isoWeek,      // ISO week number (1–53)
                ]);

                $currentDate->addDay();
            }


            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.operational-calendar.list')->with('success', 'Operational Calendar added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function destroy(OperationalCalendar $operationalCalendar)
    {
        try {
            DB::beginTransaction();

            // Delete Operational Days
            $operationalCalendar->operationalDays()->delete();

            $operationalCalendar->update([
                'deleted_by' => Auth::id()
            ]);

            $operationalCalendar->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.operational-calendar.list')->with('success', 'Operational Calendar deleted successfully.');
    }
}
