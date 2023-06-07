<?php

namespace App\Services\Queries\ReRegistration\GenerateInvoiceScopes;

use App\Contracts\GenerateReRegistInvoiceScope;

class FacultyScope implements GenerateReRegistInvoiceScope {

    protected $filters;

    public function __construct(int $faculty_id)
    {
        $this->filters = array([
            'key' => 'faculty.faculty_id',
            'value' => $faculty_id,
        ]);
    }

    public function getFilters(): array
    {
        return $this->filters;
    }
}
