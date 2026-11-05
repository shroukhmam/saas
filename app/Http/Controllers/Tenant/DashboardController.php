<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $tenant = tenant()->load('plan', 'currentSubscription');

        return view('tenant.dashboard', [
            'tenant' => $tenant,
            'subscription' => $tenant->currentSubscription,
        ]);
    }
}
