<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $todaysSchedule = Schedule::whereDate('date', now()->toDateString())->first();
        return view('dashboard', compact('todaysSchedule'));
    }
}
