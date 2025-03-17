<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Airport;
use App\Models\Schedule;
use App\Models\ScheduleFlight;
use App\Models\ScheduleFlightCustomer;
use App\Models\ScheduleFlightCustomerShipment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScheduleFlightCustomerShipmentController extends Controller
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
    public function store(Request $request, Schedule $schedule, ScheduleFlight $scheduleFlight, ScheduleFlightCustomer $scheduleFlightCustomer)
    {
        try {

            DB::beginTransaction();

            $awbType = $request->input('awb_type');
            $awb = $request->input('new_awb') ?? $request->input('subsequent_awb');

            ScheduleFlightCustomerShipment::create([
                'schedule_flight_customer_id' => $scheduleFlightCustomer->id,
                'awb' => $awb,
                'product_id' => $request->input('product'),
                'declared_weight' => $request->input('declared_weight') ?? 0,
                'actual_weight' => $request->input('actual_weight') ?? 0,
                'volumetric_weight' => $request->input('volumetric_weight') ?? 0,
                'uplifted_weight' => $request->input('actual_weight') ?? 0,
                'offloaded_weight' => $request->input('offloaded_weight') ?? 0,
                'total_volumetric_weight' => $request->input('total_volumetric_weight') ?? 0,
                'total_actual_weight' => $request->input('total_actual_weight') ?? 0,
                'destination' => $request->input('destination'),
                'type' => $awbType,
                'added_by' => Auth::id()
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('flight-operations.schedule.flight.customer.show', [$schedule, $scheduleFlight, $scheduleFlightCustomer])->with('success', 'AWB added successfully.');
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
    public function edit(Schedule $schedule, ScheduleFlight $scheduleFlight, ScheduleFlightCustomer $scheduleFlightCustomer, ScheduleFlightCustomerShipment $scheduleFlightCustomerShipment)
    {
        $products = $scheduleFlightCustomer->scheduleFlightCustomerProducts;
        $airports = Airport::active()->get();
        return view('_partials._modals.schedule.flight.customer.shipment.edit', compact('schedule', 'scheduleFlight', 'scheduleFlightCustomer', 'scheduleFlightCustomerShipment', 'products', 'airports'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule, ScheduleFlight $scheduleFlight, ScheduleFlightCustomer $scheduleFlightCustomer, ScheduleFlightCustomerShipment $scheduleFlightCustomerShipment)
    {
        try {

            DB::beginTransaction();

            $scheduleFlightCustomerShipment->declared_weight = $request->input('declared_weight');
            $scheduleFlightCustomerShipment->actual_weight = $request->input('actual_weight');
            $scheduleFlightCustomerShipment->volumetric_weight = $request->input('volumetric_weight');
            $scheduleFlightCustomerShipment->uplifted_weight = $request->input('actual_weight');
            $scheduleFlightCustomerShipment->offloaded_weight = $request->input('offloaded_weight');
            $scheduleFlightCustomerShipment->total_volumetric_weight = $request->input('total_volumetric_weight');
            $scheduleFlightCustomerShipment->total_actual_weight = $request->input('total_actual_weight');
            $scheduleFlightCustomerShipment->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('flight-operations.schedule.flight.customer.show', [$schedule, $scheduleFlight, $scheduleFlightCustomer])->with('success', 'AWB updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule, ScheduleFlight $scheduleFlight, ScheduleFlightCustomer $scheduleFlightCustomer, ScheduleFlightCustomerShipment $scheduleFlightCustomerShipment)
    {
        try {
            DB::beginTransaction();

            $scheduleFlightCustomerShipment->update([
                'deleted_by' => Auth::id()
            ]);

            $scheduleFlightCustomerShipment->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('flight-operations.schedule.flight.customer.show', [$schedule, $scheduleFlight, $scheduleFlightCustomer])->with('success', 'AWB deleted successfully.');
    }

    public function checkAwbForScheduleFlightCustomer(Request $request)
    {
        $awbType = $request->input('awb_type');
        $awb = $request->input('awb');
        $data = [
            'success' => false,
            'exist' => false,
            'data' => []
        ];

        try {

            $result = ScheduleFlightCustomerShipment::where('awb', $awb)->first();
            if ($awbType === 'new') {
                if ($result) {
                    $data = [
                        'success' => true,
                        'show_error' => true,
                        'data' => $result
                    ];
                } else {
                    $data = [
                        'success' => true,
                        'show_error' => false,
                        'data' => []
                    ];
                }
            } elseif ($awbType === 'subsequent') {
                if ($result) {
                    $data = [
                        'success' => true,
                        'show_error' => false,
                        'data' => $result
                    ];
                } else {
                    $data = [
                        'success' => true,
                        'show_error' => true,
                        'data' => []
                    ];
                }
            }
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }

        return response()->json($data);
    }
}
