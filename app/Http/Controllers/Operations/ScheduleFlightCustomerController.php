<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Airport;
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

class ScheduleFlightCustomerController extends Controller
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

            $scheduleFlightCustomer = ScheduleFlightCustomer::updateOrCreate([
                'schedule_flight_id' => $scheduleFlight->id,
                'customer_id' => $request->input('customer'),
                'added_by' => Auth::id()
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('flight-operations.schedule.flight.customer.show', [$schedule, $scheduleFlight, $scheduleFlightCustomer])->with('success', 'Schedule Flight Customer added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule, ScheduleFlight $scheduleFlight, ScheduleFlightCustomer $scheduleFlightCustomer)
    {
        // Eager load products associated with the schedule flight customer
        $scheduleFlightCustomer->load(['scheduleFlightCustomerProducts.product', 'scheduleFlightCustomerShipments.product']);
        $products = $this->getAvailableProducts($scheduleFlightCustomer->customer_id, $scheduleFlightCustomer->id);
        $airports = Airport::active()->get();
        return view('operations.schedule.flight.customer.detail', compact('schedule', 'scheduleFlight', 'scheduleFlightCustomer', 'products', 'airports'));
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

    public function getAvailableProducts($customerId, $scheduleFlightCustomerId)
    {
        // Get all products linked to this customer
        $customerProducts = CustomerProduct::where('customer_id', $customerId)->pluck('product_id');

        // Get products already added for this schedule flight customer
        $addedProducts = ScheduleFlightCustomerProduct::where('schedule_flight_customer_id', $scheduleFlightCustomerId)
            ->pluck('product_id');

        // Exclude already added products
        $availableProducts = Product::whereIn('id', $customerProducts)
            ->whereNotIn('id', $addedProducts)
            ->get();

        return $availableProducts;
    }
}
