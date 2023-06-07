<?php

namespace App\Services\Queries\ReRegistration\GenerateInvoiceScopes;

class PathScope extends StudyprogramScope {

    public function __construct(int $faculty_id, int $studyprogram_id, int $path_id)
    {
        parent::__construct($faculty_id, $studyprogram_id);
        $this->filters = array_merge(
            $this->filters,
            [
                ['key' => 'path.path_id', 'value' => $path_id],
            ],
        );
    }

    public function getFilters(): array
    {
        return $this->filters;
    }
}
