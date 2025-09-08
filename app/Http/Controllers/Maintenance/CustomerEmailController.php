<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerEmail;
use App\Models\Location;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CustomerEmailController extends Controller
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
    public function store(Request $request, Customer $customer)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'email' => [
                'required',
                'string',
                Rule::unique('customer_emails', 'email')
                    ->where(function ($query) use ($customer) {
                        $query->whereNull('deleted_at')
                            ->where('customer_id', $customer->id);
                    })
            ],
            'status' => ['required']
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {

            DB::beginTransaction();

            $customerEmail = CustomerEmail::create([
                'customer_id' => $customer->id,
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'active' => $request->input('status'),
                'added_by' => Auth::id()
            ]);

            if (count($request->input('location', [])) > 0) {
                $locationIds = $request->input('location');
                $customerEmail->locations()->sync($locationIds);

                activity()->performedOn($customerEmail)->by(auth()->user())->useLog('Customer Email Locations')->withProperties([
                    'old' => [],
                    'new' => $locationIds
                ])->log('Customer email locations updated');
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.customer.show', $customer)->with('success', 'Customer Email added successfully.');
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
    public function edit(Customer $customer, CustomerEmail $customerEmail)
    {
        $locations = Location::active()->get();
        $customerEmailLocationIds = $customerEmail->locations->pluck('id')->toArray();
        return view('_partials._modals.customer.email.edit', compact('customer', 'customerEmail', 'locations', 'customerEmailLocationIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer, CustomerEmail $customerEmail)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'email' => ['required', 'string', Rule::unique('customer_emails', 'email')
                ->where(function ($query) use ($customer) {
                    $query->whereNull('deleted_at')
                        ->where('customer_id', $customer->id);
                })
                ->ignore($customerEmail->id, 'id')],
            'status' => ['required']
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {

            DB::beginTransaction();

            $customerEmail->name = $request->input('name');
            $customerEmail->email = $request->input('email');
            $customerEmail->active = $request->input('status');
            $customerEmail->save();

            if (count($request->input('location', [])) > 0) {
                $oldLocationIds = $customerEmail->locations->pluck('id')->toArray();
                $newLocationIds = $request->input('location');
                $customerEmail->locations()->sync($newLocationIds);

                activity()->performedOn($customerEmail)->by(auth()->user())->useLog('Customer Email Locations')->withProperties([
                    'old' => $oldLocationIds,
                    'new' => $newLocationIds
                ])->log('Customer email locations updated');
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.customer.show', $customer)->with('success', 'Customer Email updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer, CustomerEmail $customerEmail)
    {
        try {

            DB::beginTransaction();

            $customerEmail->locations()->detach();

            $customerEmail->update([
                'deleted_by' => Auth::id()
            ]);

            $customerEmail->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.customer.show', $customer)->with('success', 'Customer Email deleted successfully.');
    }
}
