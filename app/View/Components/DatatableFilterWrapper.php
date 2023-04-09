<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DatatableFilterWrapper extends Component
{
    public $oneRow;
    public $handler;

    /**
     * Create a new component instance.
     */
    public function __construct($oneRow = false, $handler)
    {
        $this->oneRow = $oneRow;
        $this->handler = $handler;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.datatable-filter-wrapper');
    }
}
