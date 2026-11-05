<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Central\Tenant;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_tenants'  => Tenant::count(),
            'active_tenants' => Tenant::where('status', \App\Enums\TenantStatus::Active)->count(),
        ];

        return view('central.dashboard', compact('stats'));
    }
}
