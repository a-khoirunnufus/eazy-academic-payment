<?php

namespace App\Services\Queries\NewStudent\Filters;

use App\Contracts\Queries\NewStudentFilter;

class ByStudyprogram implements NewStudentFilter
{
    private $studyprogram_id;

    public function __construct(int $studyprogram_id) {
        $this->studyprogram_id = $studyprogram_id;
    }

    public function apply($query)
    {
        return $query->where('r.reg_major_pass', '=', $this->studyprogram_id);
    }
}
