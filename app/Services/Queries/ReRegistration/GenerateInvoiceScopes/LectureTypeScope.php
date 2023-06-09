<?php

namespace App\Services\Queries\ReRegistration\GenerateInvoiceScopes;

class LectureTypeScope extends PeriodScope {

    public function __construct(int $faculty_id, int $studyprogram_id, int $path_id, int $period_id, int $lecture_type_id)
    {
        parent::__construct($faculty_id, $studyprogram_id, $path_id, $period_id);
        $this->filters = array_merge(
            $this->filters,
            [
                ['key' => 'lecture_type.mlt_id', 'value' => $lecture_type_id],
            ],
        );
    }

    public function getFilters(): array
    {
        return $this->filters;
    }
}
