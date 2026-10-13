<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    // Devuelve la plantilla para visitantes.
    public function render(): View
    {
        return view('layouts.guest');
    }
}
