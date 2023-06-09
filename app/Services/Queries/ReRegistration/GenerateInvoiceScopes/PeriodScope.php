<?php

namespace App\Services\Queries\ReRegistration\GenerateInvoiceScopes;

class PeriodScope extends PathScope {

    public function __construct(int $faculty_id, int $studyprogram_id, int $path_id, int $period_id)
    {
        parent::__construct($faculty_id, $studyprogram_id, $path_id);
        $this->filters = array_merge(
            $this->filters,
            [
                ['key' => 'period.period_id', 'value' => $period_id],
            ],
        );
    }

    public function getFilters(): array
    {
        return $this->filters;
    }
}
