<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SelectOption extends Component
{

    public function __construct(
        public string $title,
        public string $selectId,
        public string $resourceUrl,
        public string $value,
        public string $labelTemplate,
        public array $labelTemplateItems,
        public string|null $defaultValue = null,
        public string|null $defaultLabel = null,
        public string|null $withoutAllOption = null
    ) {}

    public function render(): View
    {
        return view('components.select-option');
    }
}
