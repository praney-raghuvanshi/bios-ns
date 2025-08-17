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



            // $awb = $request->input('new_awb') ?? $request->input('subsequent_awb');
            // $awbType = $request->input('awb_type'); // new / connecting / subsequent
            // $flightCustomerId = $scheduleFlightCustomer->id;

            // $records = ScheduleFlightCustomerShipment::where('awb', $awb)->get();
            // $parent  = $records->first();



            // $parentId = null;

            // if ($awbType === 'connecting') {
            //     $parent = ScheduleFlightCustomerShipment::where('awb', $awb)
            //         ->latest('id')
            //         ->first();
            //     $parentId = $parent?->id;
            // }

            // if ($awbType === 'subsequent') {
            //     $parent = ScheduleFlightCustomerShipment::where('awb', $awb)
            //         ->where('type', 'new')
            //         ->first();
            //     $parentId = $parent?->id;
            // }

            // ScheduleFlightCustomerShipment::create([
            //     'schedule_flight_customer_id' => $flightCustomerId,
            //     'awb' => $awb,
            //     'product_id' => $request->input('product'),
            //     'declared_weight' => $request->input('declared_weight') ?? 0,
            //     'actual_weight' => $request->input('actual_weight') ?? 0,
            //     'volumetric_weight' => $request->input('volumetric_weight') ?? 0,
            //     'uplifted_weight' => $request->input('actual_weight') ?? 0,
            //     'offloaded_weight' => $request->input('offloaded_weight') ?? 0,
            //     'total_volumetric_weight' => $request->input('total_volumetric_weight') ?? 0,
            //     'total_actual_weight' => $request->input('total_actual_weight') ?? 0,
            //     'destination' => $request->input('destination'),
            //     'type' => $awbType,
            //     'parent_awb_id' => $parentId,
            //     'added_by' => auth()->id(),
            // ]);

            $awbNumber = $request->input('new_awb') ?? $request->input('subsequent_awb');
            $awbType   = $request->input('awb_type');
            $flightCustomerId = $scheduleFlightCustomer->id;
            $parentId  = null;

            // Find existing AWBs with same number
            $records = ScheduleFlightCustomerShipment::where('awb', $awbNumber)->get();
            $parent  = $records->first();

            /**
             * Case 1: User selected NEW, but AWB already exists → 
             * Treat as CONNECTING, only for this new entry.
             */
            if ($awbType === 'new' && $records->isNotEmpty()) {
                $awbType  = 'connecting';
                $parentId = $parent->id;
            }

            /**
             * Case 2: User selected SUBSEQUENT →
             * Ensure total actual weight does not exceed available.
             */
            if ($awbType === 'subsequent' && $records->isNotEmpty()) {
                $totalActual = $records->sum('actual_weight');
                $totalAllowed = $records->first()->total_actual_weight;

                $newActual = $request->input('actual_weight') ?? 0;

                if ($totalActual + $newActual > $totalAllowed) {
                    return back()->withErrors([
                        'actual_weight' => 'Total actual weight already reached for this AWB. Cannot add more subsequent shipments.',
                    ])->withInput();
                }

                $parentId = $parent->id;
            }

            // ✅ Create the shipment entry
            ScheduleFlightCustomerShipment::create([
                'schedule_flight_customer_id' => $flightCustomerId,
                'awb'                         => $awbNumber,
                'product_id'                  => $request->input('product'),
                'declared_weight'             => $request->input('declared_weight') ?? 0,
                'actual_weight'               => $request->input('actual_weight') ?? 0,
                'volumetric_weight'           => $request->input('volumetric_weight') ?? 0,
                'uplifted_weight'             => $request->input('actual_weight') ?? 0, // always same as actual
                'offloaded_weight'            => $request->input('offloaded_weight') ?? 0,
                'total_volumetric_weight'     => $request->input('total_volumetric_weight') ?? 0,
                'total_actual_weight'         => $request->input('total_actual_weight') ?? 0,
                'destination'                 => $request->input('destination'),
                'type'                        => $awbType,
                'parent_awb_id'               => $parentId,
                'added_by'                    => Auth::id(),
            ]);

            //return back()->with('success', 'AWB stored successfully.');

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
        try {

            $awbNumber = $request->input('awb');
            $awbType   = $request->input('awb_type');

            if (!$awbNumber || !$awbType) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Missing AWB number or type.'
                ]);
            }

            // Query existing AWB records
            $records = ScheduleFlightCustomerShipment::where('awb', $awbNumber)->get();

            // Case: NEW AWB
            if ($awbType === 'new') {
                if ($records->isEmpty()) {
                    // Brand new AWB, not used anywhere
                    return response()->json([
                        'status'  => 'available',
                        'message' => 'AWB available.'
                    ]);
                } else {
                    // Already exists → treat as CONNECTING shipment (duplicate allowed)
                    $normal = $records->first(); // pick first for prefill
                    return response()->json([
                        'status'   => 'exists',
                        'message'  => 'AWB already exists. It will be treated as a Connecting shipment.',
                        'normal'   => [
                            'product_id'            => $normal->product_id,
                            'destination'           => $normal->destination,
                            'declared_weight'       => $normal->declared_weight,
                            'volumetric_weight'     => $normal->volumetric_weight,
                            'total_actual_weight'   => $normal->total_actual_weight,
                            'total_volumetric_weight' => $normal->total_volumetric_weight,
                        ],
                        'records'  => $records
                    ]);
                }
            }

            // Case: SUBSEQUENT AWB
            if ($awbType === 'subsequent') {
                if ($records->isEmpty()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'No existing record for this AWB. Enter it as a New/Connecting shipment first.'
                    ]);
                } else {
                    // Must already exist → allow subsequent (partial load)
                    $normal = $records->first();
                    return response()->json([
                        'status'   => 'exists',
                        'message'  => 'AWB found. Entering as Subsequent (partial) shipment.',
                        'normal'   => [
                            'product_id'            => $normal->product_id,
                            'destination'           => $normal->destination,
                            'declared_weight'       => $normal->declared_weight,
                            'volumetric_weight'     => $normal->volumetric_weight,
                            'actual_weight'         => $normal->actual_weight,
                            'total_actual_weight'   => $normal->total_actual_weight,
                            'total_volumetric_weight' => $normal->total_volumetric_weight,
                        ],
                        'records'  => $records
                    ]);
                }
            }

            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid AWB type.'
            ]);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
