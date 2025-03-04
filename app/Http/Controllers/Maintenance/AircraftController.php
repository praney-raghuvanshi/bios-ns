<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\Aircraft;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AircraftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $aircrafts = Aircraft::all();
        return view('maintenance.aircraft.list', compact('aircrafts'));
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
            'name' => ['required', 'string', 'unique:aircrafts,name,NULL,id,deleted_at,NULL'],
            'capacity' => ['nullable', 'numeric'],
            'status' => ['required']
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {

            Aircraft::create([
                'name' => Str::upper($request->input('name')),
                'capacity' => $request->input('capacity'),
                'active' => $request->input('status'),
                'added_by' => Auth::id()
            ]);
        } catch (Exception $e) {
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.aircraft.list')->with('success', 'Aircraft added successfully.');
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
    public function edit(Aircraft $aircraft)
    {
        return view('_partials._modals.aircraft.edit', compact('aircraft'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Aircraft $aircraft)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', Rule::unique('aircrafts', 'name')->ignore($aircraft->id)],
            'capacity' => ['nullable', 'numeric'],
            'status' => ['required']
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {
            $aircraft->name = Str::upper($request->input('name'));
            $aircraft->capacity = $request->input('capacity');
            $aircraft->active = $request->input('status');
            $aircraft->save();
        } catch (Exception $e) {
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.aircraft.list')->with('success', 'Aircraft updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aircraft $aircraft)
    {
        try {
            DB::beginTransaction();

            $aircraft->update([
                'deleted_by' => Auth::id()
            ]);

            $aircraft->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.aircraft.list')->with('success', 'Aircraft deleted successfully.');
    }
}
