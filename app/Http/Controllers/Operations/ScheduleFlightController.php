<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Aircraft;
use App\Models\AircraftType;
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
        $scheduleFlight->load(['auditLogs', 'scheduleFlightCustomers.customer', 'scheduleFlightRemarks.customer', 'scheduleFlightCustomers.auditLogs', 'scheduleFlightCustomers.scheduleFlightCustomerProducts.auditLogs', 'scheduleFlightCustomers.scheduleFlightCustomerShipments.auditLogs', 'scheduleFlightEmails.auditLogs', 'scheduleFlightRemarks.auditLogs']);
        $customers = Customer::active()->get();
        $aircraftTypes = AircraftType::active()->orderBy('name')->get();
        $aircrafts = Aircraft::active()->orderBy('registration')->get();

        $dfrRemarks = $fprRemarks = [];
        foreach ($scheduleFlight->scheduleFlightRemarks as $remark) {
            if ($remark->is_dfr) {
                $dfrRemarks[] = $remark;
            } else {
                $fprRemarks[] = $remark;
            }
        }

        $logs = collect();

        $logs = $logs->merge($scheduleFlight->auditLogs);

        foreach ($scheduleFlight->scheduleFlightCustomers as $customer) {
            $logs = $logs->merge($customer->auditLogs);

            foreach ($customer->scheduleFlightCustomerProducts as $product) {
                $logs = $logs->merge($product->auditLogs);
            }

            foreach ($customer->scheduleFlightCustomerShipments as $shipment) {
                $logs = $logs->merge($shipment->auditLogs);
            }
        }

        foreach ($scheduleFlight->scheduleFlightEmails as $email) {
            $logs = $logs->merge($email->auditLogs);
        }
        foreach ($scheduleFlight->scheduleFlightRemarks as $remark) {
            $logs = $logs->merge($remark->auditLogs);
        }

        // Sort logs by time
        $logs = $logs->sortByDesc('performed_at');

        return view('operations.schedule.flight.detail', compact('schedule', 'scheduleFlight', 'customers', 'dfrRemarks', 'fprRemarks', 'logs', 'aircraftTypes', 'aircrafts'));
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
            if (!is_null($etd)) {
                $etd = Carbon::createFromFormat('H:i', $etd)->format('H:i:s');
            }

            $eta = $request->input('eta');
            if (!is_null($eta)) {
                $eta = Carbon::createFromFormat('H:i', $eta)->format('H:i:s');
            }

            $atd = $request->input('atd');
            if (!is_null($atd)) {
                $atd = Carbon::createFromFormat('H:i', $atd)->format('H:i:s');
            }

            $ata = $request->input('ata');
            if (!is_null($ata)) {
                $ata = Carbon::createFromFormat('H:i', $ata)->format('H:i:s');
            }

            $aircraft = $request->input('aircraft_registration');

            DB::beginTransaction();

            $original = $scheduleFlight->getOriginal(); // store current values
            $hasChanges = false;

            if ($etd != $original['estimated_departure_time']) {
                $scheduleFlight->estimated_departure_time = $etd;
                $hasChanges = true;
            }

            if ($atd != $original['actual_departure_time']) {
                $scheduleFlight->actual_departure_time = $atd;
                $hasChanges = true;
            }

            if ($etd && $atd) {
                $departureDiff = Carbon::parse($atd)->diffInMinutes(Carbon::parse($scheduleFlight->flight->departure_time), false);
                $originalDepartureDiff = isset($original['departure_time_diff'])
                    ? (int) $original['departure_time_diff']
                    : null;
                if ($originalDepartureDiff === null || $departureDiff !== $originalDepartureDiff) {
                    $scheduleFlight->departure_time_diff = $departureDiff;
                    $hasChanges = true;
                }
            }

            if ($eta != $original['estimated_arrival_time']) {
                $scheduleFlight->estimated_arrival_time = $eta;
                $hasChanges = true;
            }

            if ($ata != $original['actual_arrival_time']) {
                $scheduleFlight->actual_arrival_time = $ata;
                $hasChanges = true;
            }

            if ($eta && $ata) {
                $arrivalDiff = Carbon::parse($ata)->diffInMinutes(Carbon::parse($scheduleFlight->flight->arrival_time), false);
                $originalArrivalDiff = isset($original['arrival_time_diff'])
                    ? (int) $original['arrival_time_diff']
                    : null;
                if ($originalArrivalDiff === null || $arrivalDiff !== $originalArrivalDiff) {
                    $scheduleFlight->arrival_time_diff = $arrivalDiff;
                    $hasChanges = true;
                }
            }

            if ($aircraft != $scheduleFlight->aircraft_id) {
                $scheduleFlight->aircraft_id = $aircraft;
                $hasChanges = true;
            }

            if ($hasChanges) {
                $scheduleFlight->save();
            }

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

    public function reOpen(Schedule $schedule, ScheduleFlight $scheduleFlight)
    {
        try {

            DB::beginTransaction();

            $scheduleFlight->status = 1; // Re-open the flight
            $scheduleFlight->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('flight-operations.schedule.flight.show', [$schedule, $scheduleFlight])->with('success', 'Schedule Flight re-opened successfully.');
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
