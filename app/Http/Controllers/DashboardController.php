<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participant;
use App\Models\Location;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard Operator — Mode UI / Zero-Database
     * Seluruh data tampilan dikelola secara modular via Mock Data Frontend.
     */
    public function index(Request $request)
    {
        return view('operator.dashboard');
    }
}
