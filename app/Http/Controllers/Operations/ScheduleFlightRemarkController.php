<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\ScheduleFlight;
use App\Models\ScheduleFlightRemark;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScheduleFlightRemarkController extends Controller
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
    public function store(Request $request, Schedule $schedule, ScheduleFlight $scheduleFlight)
    {
        try {

            DB::beginTransaction();

            $customers = $request->input('customer');
            $remark = $request->input('remark');
            $isDfr = $request->has('dfr') ? true : false;
            $isFpr = $request->has('fpr') ? true : false;
            $isEmail = $request->has('email') ? true : false;

            // Handling new lines in Remark
            $remarksArray = preg_split('/\r\n|\r|\n/', trim($remark));
            $remarksArray = array_filter($remarksArray, fn($line) => trim($line) !== '');

            foreach ($customers as $customer) {
                foreach ($remarksArray as $remark) {
                    ScheduleFlightRemark::create([
                        'schedule_flight_id' => $scheduleFlight->id,
                        'customer_id' => $customer,
                        'remark' => $remark,
                        'email_required' => $isEmail,
                        'is_fpr' => $isFpr,
                        'added_by' => Auth::id()
                    ]);
                }
            }

            if ($isDfr) {
                foreach ($remarksArray as $remark) {
                    ScheduleFlightRemark::create([
                        'schedule_flight_id' => $scheduleFlight->id,
                        'is_dfr' => $isDfr,
                        'remark' => $remark,
                        'added_by' => Auth::id()
                    ]);
                }
                $scheduleFlight->latest_remark = end($remarksArray);
                $scheduleFlight->save();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('flight-operations.schedule.flight.show', [$schedule, $scheduleFlight])->with('success', 'Remark added successfully.');
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
    public function edit(Schedule $schedule, ScheduleFlight $scheduleFlight, ScheduleFlightRemark $scheduleFlightRemark)
    {
        return view('_partials._modals.schedule.flight.remark.edit', compact('schedule', 'scheduleFlight', 'scheduleFlightRemark'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule, ScheduleFlight $scheduleFlight, ScheduleFlightRemark $scheduleFlightRemark)
    {
        try {

            DB::beginTransaction();

            $remark = $request->input('remark');
            $isFpr = $request->has('fpr') ? true : false;
            $isEmail = $request->has('email') ? true : false;

            if ($scheduleFlightRemark->is_dfr && $scheduleFlightRemark->remark === $scheduleFlight->latest_remark) {
                $scheduleFlight->latest_remark = $remark;
                $scheduleFlight->save();
            }

            $scheduleFlightRemark->remark = $remark;
            $scheduleFlightRemark->is_fpr = $isFpr;
            $scheduleFlightRemark->email_required = $isEmail;
            $scheduleFlightRemark->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('flight-operations.schedule.flight.show', [$schedule, $scheduleFlight])->with('success', 'Remark updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule, ScheduleFlight $scheduleFlight, ScheduleFlightRemark $scheduleFlightRemark)
    {
        try {
            DB::beginTransaction();

            // Check if Remark is DFR
            if ($scheduleFlightRemark->is_dfr) {
                // check if same as latest_remark in Schedule Flight
                if ($scheduleFlight->latest_remark === $scheduleFlightRemark->remark) {
                    $scheduleFlight->latest_remark = null;
                    $scheduleFlight->save();
                }
            }

            $scheduleFlightRemark->update([
                'deleted_by' => Auth::id()
            ]);

            $scheduleFlightRemark->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('flight-operations.schedule.flight.show', [$schedule, $scheduleFlight])->with('success', 'Remark deleted successfully.');
    }
}
