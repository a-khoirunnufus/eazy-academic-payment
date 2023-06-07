<?php

namespace App\Services\Queries\ReRegistration\GenerateInvoiceScopes;

class StudyprogramScope extends FacultyScope {

    public function __construct(int $faculty_id, int $studyprogram_id)
    {
        parent::__construct($faculty_id);
        $this->filters = array_merge(
            $this->filters,
            [
                ['key' => 'studyprogram.studyprogram_id','value' => $studyprogram_id],
            ],
        );
    }

    public function getFilters(): array
    {
        return $this->filters;
    }
}
