<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class TenantAppLayout extends Component
{
    public function render(): View
    {
        return view('tenant.layouts.app');
    }
}
