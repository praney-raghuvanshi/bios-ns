<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $zones = Zone::all();
        return view('maintenance.zone.list', compact('zones'));
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
            'code' => ['required', 'string', 'unique:zones,code,NULL,id,deleted_at,NULL'],
            'name' => ['required', 'string', 'unique:zones,name,NULL,id,deleted_at,NULL'],
            'status' => ['required']
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {

            Zone::create([
                'code' => Str::upper($request->input('code')),
                'name' => Str::upper($request->input('name')),
                'active' => $request->input('status'),
                'added_by' => Auth::id()
            ]);
        } catch (Exception $e) {
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.zone.list')->with('success', 'Zone added successfully.');
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
    public function edit(Zone $zone)
    {
        return view('_partials._modals.zone.edit', compact('zone'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Zone $zone)
    {
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', Rule::unique('zones', 'code')->ignore($zone->id)],
            'name' => ['required', 'string', Rule::unique('zones', 'name')->ignore($zone->id)],
            'status' => ['required']
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {

            $zone->code = Str::upper($request->input('code'));
            $zone->name = Str::upper($request->input('name'));
            $zone->active = $request->input('status');
            $zone->save();
        } catch (Exception $e) {
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.zone.list')->with('success', 'Zone updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Zone $zone)
    {
        try {
            DB::beginTransaction();

            // Remove zone_id from locations
            $zone->locations()->update(['zone_id' => null]);

            $zone->update([
                'deleted_by' => Auth::id()
            ]);

            $zone->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.zone.list')->with('success', 'Zone deleted successfully.');
    }
}
