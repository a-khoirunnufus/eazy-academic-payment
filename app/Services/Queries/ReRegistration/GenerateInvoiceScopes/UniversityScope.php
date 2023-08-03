<?php

namespace App\Services\Queries\ReRegistration\GenerateInvoiceScopes;

use App\Contracts\GenerateReRegistInvoiceScope;

class UniversityScope implements GenerateReRegistInvoiceScope {

    protected $filters;

    public function __construct()
    {
        $this->filters = array();
    }

    public function getFilters(): array
    {
        return $this->filters;
    }
}
