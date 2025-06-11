<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\AircraftManufacturer;
use App\Models\AircraftType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AircraftTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $aircraftManufacturers = AircraftManufacturer::all();
        $aircraftTypes = AircraftType::all();
        return view('maintenance.aircraft-type.list', compact('aircraftManufacturers', 'aircraftTypes'));
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
            'name' => ['required', 'string', 'unique:aircraft_types,name,NULL,id,deleted_at,NULL'],
            'capacity' => ['nullable', 'numeric'],
            'status' => ['required']
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {

            AircraftType::create([
                'aircraft_manufacturer_id' => $request->input('aircraft_manufacturer'),
                'name' => Str::upper($request->input('name')),
                'capacity' => $request->input('capacity'),
                'active' => $request->input('status'),
                'added_by' => Auth::id()
            ]);
        } catch (Exception $e) {
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.aircraft-type.list')->with('success', 'Aircraft Type added successfully.');
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
    public function edit(AircraftType $aircraftType)
    {
        $aircraftManufacturers = AircraftManufacturer::all();
        return view('_partials._modals.aircraft-type.edit', compact('aircraftManufacturers', 'aircraftType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AircraftType $aircraftType)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', Rule::unique('aircraft_types', 'name')->ignore($aircraftType->id)->whereNull('deleted_at')],
            'capacity' => ['nullable', 'numeric'],
            'status' => ['required']
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {
            $aircraftType->aircraft_manufacturer_id = $request->input('aircraft_manufacturer');
            $aircraftType->name = Str::upper($request->input('name'));
            $aircraftType->capacity = $request->input('capacity');
            $aircraftType->active = $request->input('status');
            $aircraftType->save();
        } catch (Exception $e) {
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.aircraft-type.list')->with('success', 'Aircraft Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AircraftType $aircraftType)
    {
        try {
            DB::beginTransaction();

            $aircraftType->update([
                'deleted_by' => Auth::id()
            ]);

            $aircraftType->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.aircraft-type.list')->with('success', 'Aircraft Type deleted successfully.');
    }
}
