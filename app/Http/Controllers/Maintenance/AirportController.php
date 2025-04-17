<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\Airport;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AirportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $airports = Airport::all();
        $timezones = DateTimeZone::listIdentifiers();

        return view('maintenance.airport.list', compact('airports', 'timezones'));
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
            'iata' => ['required', 'string', 'size:3', 'unique:airports,iata,NULL,id,deleted_at,NULL'],
            'name' => ['required', 'string', 'unique:airports,name,NULL,id,deleted_at,NULL'],
            'city' => ['required', 'string'],
            'country' => ['required', 'string'],
            'timezone' => ['required', 'string'],
            // 'summer_difference' => ['required', 'numeric'],
            // 'winter_difference' => ['required', 'numeric'],
            'status' => ['required']
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {

            Airport::create([
                'iata' => Str::upper($request->input('iata')),
                'name' => Str::upper($request->input('name')),
                'city' => Str::upper($request->input('city')),
                'country' => Str::upper($request->input('country')),
                'timezone' => $request->input('timezone'),
                'summer_difference' => $request->input('summer_difference') ?? 0,
                'winter_difference' => $request->input('winter_difference') ?? 0,
                'active' => $request->input('status'),
                'added_by' => Auth::id()
            ]);
        } catch (Exception $e) {
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.airport.list')->with('success', 'Airport added successfully.');
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
    public function edit(Airport $airport)
    {
        $timezones = DateTimeZone::listIdentifiers();
        return view('_partials._modals.airport.edit', compact('airport', 'timezones'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Airport $airport)
    {
        $validator = Validator::make($request->all(), [
            'iata' => ['required', 'string', 'size:3', Rule::unique('airports', 'iata')->ignore($airport->id)->whereNull('deleted_at')],
            'name' => ['required', 'string', Rule::unique('airports', 'name')->ignore($airport->id)->whereNull('deleted_at')],
            'city' => ['required', 'string'],
            'country' => ['required', 'string'],
            'timezone' => ['required', 'string'],
            // 'summer_difference' => ['required', 'numeric'],
            // 'winter_difference' => ['required', 'numeric'],
            'status' => ['required']
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {
            $airport->iata = Str::upper($request->input('iata'));
            $airport->name = Str::upper($request->input('name'));
            $airport->city = Str::upper($request->input('city'));
            $airport->country = Str::upper($request->input('country'));
            $airport->timezone = $request->input('timezone');
            $airport->summer_difference = $request->input('summer_difference') ?? 0;
            $airport->winter_difference = $request->input('winter_difference') ?? 0;
            $airport->active = $request->input('status');
            $airport->save();
        } catch (Exception $e) {
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.airport.list')->with('success', 'Airport updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Airport $airport)
    {
        try {
            DB::beginTransaction();

            $airport->update([
                'deleted_by' => Auth::id()
            ]);

            $airport->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('maintenance.airport.list')->with('success', 'Airport deleted successfully.');
    }
}
