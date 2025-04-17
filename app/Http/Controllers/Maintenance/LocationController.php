<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Zone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = Location::all();
        $zones = Zone::active()->get();
        return view('maintenance.location.list', compact('locations', 'zones'));
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
            'zone' => ['required'],
            'code' => ['required', 'string', 'unique:locations,code,NULL,id,deleted_at,NULL'],
            'name' => ['required', 'string', 'unique:locations,name,NULL,id,deleted_at,NULL'],
            'status' => ['required']
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {

            Location::create([
                'zone_id' => $request->input('zone'),
                'code' => Str::upper($request->input('code')),
                'name' => Str::upper($request->input('name')),
                'active' => $request->input('status'),
                'added_by' => Auth::id()
            ]);
        } catch (Exception $e) {
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.location.list')->with('success', 'Location added successfully.');
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
    public function edit(Location $location)
    {
        $zones = Zone::active()->get();
        return view('_partials._modals.location.edit', compact('location', 'zones'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        $validator = Validator::make($request->all(), [
            'zone' => ['required'],
            'code' => ['required', 'string', Rule::unique('locations', 'code')->ignore($location->id)->whereNull('deleted_at')],
            'name' => ['required', 'string', Rule::unique('locations', 'name')->ignore($location->id)->whereNull('deleted_at')],
            'status' => ['required']
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {
            $location->zone_id = $request->input('zone');
            $location->code = Str::upper($request->input('code'));
            $location->name = Str::upper($request->input('name'));
            $location->active = $request->input('status');
            $location->save();
        } catch (Exception $e) {
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.location.list')->with('success', 'Location updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        try {
            DB::beginTransaction();

            $location->update([
                'deleted_by' => Auth::id()
            ]);

            $location->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.location.list')->with('success', 'Location deleted successfully.');
    }
}
