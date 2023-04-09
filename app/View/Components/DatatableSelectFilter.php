<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DatatableSelectFilter extends Component
{
    public $title;
    public $elementId;
    public $resourceName;
    public $value;
    public $labelTemplate;
    public $labelTemplateItems;

    /**
     * Create a new component instance.
     */
    public function __construct($title, $elementId, $resourceName, $value, $labelTemplate, $labelTemplateItems)
    {
        $this->title = $title;
        $this->elementId = $elementId;
        $this->resourceName = $resourceName;
        $this->value = $value;
        $this->labelTemplate = $labelTemplate;
        $this->labelTemplateItems = $labelTemplateItems;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.datatable-select-filter');
    }
}
