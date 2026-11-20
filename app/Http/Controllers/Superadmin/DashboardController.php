<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;

class DashboardController extends Controller
{
    public function index()
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('status', 1)->count();
        $inactiveTenants = Tenant::where('status', 0)->count();
        $recentTenants = Tenant::with('domains')->latest()->take(5)->get();

        return view('superadmin.dashboard', compact('totalTenants', 'activeTenants', 'inactiveTenants', 'recentTenants'));
    }
}