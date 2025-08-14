<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\CustomerProduct;
use App\Models\Product;
use App\Models\Schedule;
use App\Models\ScheduleFlight;
use App\Models\ScheduleFlightCustomer;
use App\Models\ScheduleFlightCustomerProduct;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScheduleFlightCustomerProductController extends Controller
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

            $upliftedWeight = $request->input('uplifted_weight');
            $offloadedWeight = $request->input('offloaded_weight');

            $scheduleFlightCustomerProduct = ScheduleFlightCustomerProduct::create([
                'schedule_flight_customer_id' => $scheduleFlightCustomer->id,
                'product_id' => $request->input('product'),
                'uplifted_weight' => $upliftedWeight,
                'offloaded_weight' => $offloadedWeight,
                'added_by' => Auth::id()
            ]);

            // Get Flight Aircraft Capacity for Utlisation
            $capacity = $scheduleFlight->flight->aircraftType->capacity ?? 0;

            // Update Total Uplifted & Total Offloaded weights for Customer
            $oldTotalUpliftedWeight = $scheduleFlightCustomer->total_uplifted_weight ?? 0;
            $oldTotalOffloadedWeight = $scheduleFlightCustomer->total_offloaded_weight ?? 0;

            $scheduleFlightCustomer->total_uplifted_weight = $oldTotalUpliftedWeight + $upliftedWeight;
            $scheduleFlightCustomer->total_offloaded_weight = $oldTotalOffloadedWeight + $offloadedWeight;
            $scheduleFlightCustomer->save();

            // Update Total Uplifted & Total Offloaded weights for Flight
            $oldUpliftedWeight = $scheduleFlight->uplifted ?? 0;
            $oldOffloadedWeight = $scheduleFlight->offloaded ?? 0;

            $scheduleFlight->uplifted = $oldUpliftedWeight + $upliftedWeight;
            $scheduleFlight->offloaded = $oldOffloadedWeight + $offloadedWeight;
            if ($capacity > 0) {
                $scheduleFlight->utilisation = round(((($oldUpliftedWeight + $upliftedWeight) / $capacity) * 100), 2);
            }
            $scheduleFlight->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('flight-operations.schedule.flight.customer.show', [$schedule, $scheduleFlight, $scheduleFlightCustomer])->with('success', 'Product added successfully.');
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
    public function edit(Schedule $schedule, ScheduleFlight $scheduleFlight, ScheduleFlightCustomer $scheduleFlightCustomer, ScheduleFlightCustomerProduct $scheduleFlightCustomerProduct)
    {
        $products = $this->getAvailableProducts($scheduleFlightCustomer->customer_id);
        return view('_partials._modals.schedule.flight.customer.product.edit', compact('schedule', 'scheduleFlight', 'scheduleFlightCustomer', 'scheduleFlightCustomerProduct', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule, ScheduleFlight $scheduleFlight, ScheduleFlightCustomer $scheduleFlightCustomer, ScheduleFlightCustomerProduct $scheduleFlightCustomerProduct)
    {
        try {

            DB::beginTransaction();

            $upliftedWeight = $request->input('uplifted_weight');
            $offloadedWeight = $request->input('offloaded_weight');

            $oldProductUpliftedWeight = $scheduleFlightCustomerProduct->uplifted_weight ?? 0;
            $oldProductOffloadedWeight = $scheduleFlightCustomerProduct->offloaded_weight ?? 0;

            $scheduleFlightCustomerProduct->uplifted_weight = $upliftedWeight;
            $scheduleFlightCustomerProduct->offloaded_weight = $offloadedWeight;
            $scheduleFlightCustomerProduct->save();

            // Get Flight Aircraft Capacity for Utlisation
            $capacity = $scheduleFlight->flight->aircraftType->capacity ?? 0;

            // Update Total Uplifted & Total Offloaded weights for Customer
            $oldTotalUpliftedWeight = $scheduleFlightCustomer->total_uplifted_weight ?? 0;
            $oldTotalOffloadedWeight = $scheduleFlightCustomer->total_offloaded_weight ?? 0;

            $scheduleFlightCustomer->total_uplifted_weight = $oldTotalUpliftedWeight - $oldProductUpliftedWeight + $upliftedWeight;
            $scheduleFlightCustomer->total_offloaded_weight = $oldTotalOffloadedWeight - $oldProductOffloadedWeight + $offloadedWeight;
            $scheduleFlightCustomer->save();

            // Update Total Uplifted & Total Offloaded weights for Flight
            $oldUpliftedWeight = $scheduleFlight->uplifted ?? 0;
            $oldOffloadedWeight = $scheduleFlight->offloaded ?? 0;

            $scheduleFlight->uplifted = $oldUpliftedWeight - $oldProductUpliftedWeight + $upliftedWeight;
            $scheduleFlight->offloaded = $oldOffloadedWeight - $oldProductOffloadedWeight + $offloadedWeight;
            if ($capacity > 0) {
                $scheduleFlight->utilisation = round(((($oldUpliftedWeight - $oldProductUpliftedWeight + $upliftedWeight) / $capacity) * 100), 2);
            }
            $scheduleFlight->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('flight-operations.schedule.flight.customer.show', [$schedule, $scheduleFlight, $scheduleFlightCustomer])->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule, ScheduleFlight $scheduleFlight, ScheduleFlightCustomer $scheduleFlightCustomer, ScheduleFlightCustomerProduct $scheduleFlightCustomerProduct)
    {
        try {
            DB::beginTransaction();

            // Remove product weights from Customer & Flight
            $oldProductUpliftedWeight = $scheduleFlightCustomerProduct->uplifted_weight ?? 0;
            $oldProductOffloadedWeight = $scheduleFlightCustomerProduct->offloaded_weight ?? 0;

            $oldTotalUpliftedWeight = $scheduleFlightCustomer->total_uplifted_weight ?? 0;
            $oldTotalOffloadedWeight = $scheduleFlightCustomer->total_offloaded_weight ?? 0;

            $scheduleFlightCustomer->total_uplifted_weight = $oldTotalUpliftedWeight - $oldProductUpliftedWeight;
            $scheduleFlightCustomer->total_offloaded_weight = $oldTotalOffloadedWeight - $oldProductOffloadedWeight;
            $scheduleFlightCustomer->save();

            // Get Flight Aircraft Capacity for Utlisation
            $capacity = $scheduleFlight->flight->aircraftType->capacity ?? 0;

            $oldUpliftedWeight = $scheduleFlight->uplifted ?? 0;
            $oldOffloadedWeight = $scheduleFlight->offloaded ?? 0;

            $scheduleFlight->uplifted = $oldUpliftedWeight - $oldProductUpliftedWeight;
            $scheduleFlight->offloaded = $oldOffloadedWeight - $oldProductOffloadedWeight;
            if ($capacity > 0) {
                $scheduleFlight->utilisation = round(((($oldUpliftedWeight - $oldProductUpliftedWeight) / $capacity) * 100), 2);
            }
            $scheduleFlight->save();

            $scheduleFlightCustomerProduct->update([
                'deleted_by' => Auth::id()
            ]);

            $scheduleFlightCustomerProduct->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('flight-operations.schedule.flight.customer.show', [$schedule, $scheduleFlight, $scheduleFlightCustomer])->with('success', 'Product deleted successfully.');
    }

    public function getAvailableProducts($customerId)
    {
        // Get all products linked to this customer
        $customerProducts = CustomerProduct::where('customer_id', $customerId)->pluck('product_id');

        $availableProducts = Product::whereIn('id', $customerProducts)
            ->get();

        return $availableProducts;
    }
}
