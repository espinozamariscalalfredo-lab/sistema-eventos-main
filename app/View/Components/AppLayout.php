<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    // Devuelve la plantilla general para usuarios autenticados.
    public function render(): View
    {
        return view('layouts.app');
    }
}
