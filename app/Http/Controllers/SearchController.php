<?php

namespace App\Http\Controllers;

use App\Models\ScheduleFlightCustomerShipment;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        $data = ScheduleFlightCustomerShipment::with(['scheduleFlight.flight', 'scheduleFlight.flight.fromAirport', 'scheduleFlight.flight.toAirport', 'scheduleFlightCustomer.customer', 'product'])->where('awb', 'like', '%' . $query . '%')->get();
        return view('search', compact('query', 'data'));
    }
}
