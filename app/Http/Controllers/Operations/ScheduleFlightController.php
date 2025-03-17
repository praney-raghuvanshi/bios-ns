<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Schedule;
use App\Models\ScheduleFlight;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleFlightController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule, ScheduleFlight $scheduleFlight)
    {
        // Eager load customers associated with the schedule flight
        $scheduleFlight->load(['scheduleFlightCustomers.customer', 'scheduleFlightRemarks.customer']);
        $customers = Customer::active()->get();

        $dfrRemarks = $fprRemarks = [];
        foreach ($scheduleFlight->scheduleFlightRemarks as $remark) {
            if ($remark->is_dfr) {
                $dfrRemarks[] = $remark;
            } else {
                $fprRemarks[] = $remark;
            }
        }
        return view('operations.schedule.flight.detail', compact('schedule', 'scheduleFlight', 'customers', 'dfrRemarks', 'fprRemarks'));
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
    public function update(Request $request, Schedule $schedule, ScheduleFlight $scheduleFlight)
    {
        try {

            $etd = $request->input('etd');
            $eta = $request->input('eta');
            $atd = $request->input('atd');
            $ata = $request->input('ata');

            DB::beginTransaction();

            $scheduleFlight->estimated_departure_time = $etd;
            $scheduleFlight->actual_departure_time = $atd;
            if ($atd && $etd) {
                $scheduleFlight->departure_time_diff = Carbon::parse($atd)->diffInMinutes(Carbon::parse($etd), false);
            }
            $scheduleFlight->estimated_arrival_time = $eta;
            $scheduleFlight->actual_arrival_time = $ata;
            if ($ata && $eta) {
                $scheduleFlight->arrival_time_diff = Carbon::parse($ata)->diffInMinutes(Carbon::parse($eta), false);
            }
            $scheduleFlight->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('flight-operations.schedule.flight.show', [$schedule, $scheduleFlight])->with('success', 'Schedule  Flight details updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule, ScheduleFlight $scheduleFlight)
    {
        // TODO
    }

    public function markComplete(Schedule $schedule, ScheduleFlight $scheduleFlight)
    {
        try {

            DB::beginTransaction();

            $scheduleFlight->status = 2;
            $scheduleFlight->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('flight-operations.schedule.flight.show', [$schedule, $scheduleFlight])->with('success', 'Schedule  Flight marked completed successfully.');
    }

    public function cancel(Schedule $schedule, ScheduleFlight $scheduleFlight)
    {
        try {

            DB::beginTransaction();

            $scheduleFlight->status = 3;
            $scheduleFlight->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('flight-operations.schedule.flight.show', [$schedule, $scheduleFlight])->with('success', 'Schedule  Flight cancelled successfully.');
    }

    public function scheduleFlightEmails(Schedule $schedule, ScheduleFlight $scheduleFlight)
    {
        return view('operations.schedule.flight.email.detail', compact('schedule', 'scheduleFlight'));
    }
}
