<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Location;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::all();
        return view('maintenance.customer.list', compact('customers'));
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
            'code' => ['required', 'string', 'unique:customers,code,NULL,id,deleted_at,NULL'],
            'name' => ['required', 'string', 'unique:customers,name,NULL,id,deleted_at,NULL'],
            'status' => ['required']
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {

            Customer::create([
                'code' => Str::upper($request->input('code')),
                'name' => $request->input('name'),
                'active' => $request->input('status'),
                'added_by' => Auth::id()
            ]);
        } catch (Exception $e) {
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.customer.list')->with('success', 'Customer added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $customerProducts = $customer->products;
        $customerProductIds = $customerProducts->pluck('id')->toArray();
        $products = Product::active()->get();
        $locations = Location::active()->get();
        $customerEmails = $customer->emails()->with('locations')->get();
        return view('maintenance.customer.detail', compact('customer', 'customerProducts', 'products', 'customerProductIds', 'locations', 'customerEmails'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return view('_partials._modals.customer.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', Rule::unique('customers', 'code')->ignore($customer->id)],
            'name' => ['required', 'string', Rule::unique('customers', 'name')->ignore($customer->id)],
            'status' => ['required']
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {
            $customer->code = Str::upper($request->input('code'));
            $customer->name = $request->input('name');
            $customer->active = $request->input('status');
            $customer->save();
        } catch (Exception $e) {
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.customer.list')->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        // TODO: Delete customer related data for products & emails

        try {
            DB::beginTransaction();

            $customer->update([
                'deleted_by' => Auth::id()
            ]);

            $customer->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.customer.list')->with('success', 'Customer deleted successfully.');
    }

    public function manageCustomerProducts(Request $request, Customer $customer)
    {
        try {

            // Get the selected product IDs from the form
            $newProductIds = $request->input('products', []);

            DB::beginTransaction();

            $oldProductIds = $customer->products->pluck('id')->toArray();

            $customer->products()->sync($newProductIds);

            activity()->performedOn($customer)->by(auth()->user())->useLog('Customer')->withProperties([
                'old' => $oldProductIds,
                'new' => $newProductIds
            ])->log('Customer products updated');

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.customer.show', $customer)->with('success', 'Customer products updated successfully.');
    }
}
