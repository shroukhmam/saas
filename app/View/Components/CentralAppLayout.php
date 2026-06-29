<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class CentralAppLayout extends Component
{
    public function render(): View
    {
        return view('central.layouts.app');
    }
}
