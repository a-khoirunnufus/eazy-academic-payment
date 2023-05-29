<?php

namespace App\Services\Queries\NewStudent\Filters;

use App\Contracts\Queries\NewStudentFilter;

class RegPassed implements NewStudentFilter
{
    private $strict;

    public function __construct(bool $strict = false) {
        $this->strict = $strict;
    }

    public function apply($query)
    {
        if ($this->strict) {
            return $query->where('r.reg_status_pass', '=', 1)
                ->whereNotNull('r.reg_major_pass')
                ->whereNotNull('r.reg_major_lecture_type_pass')
                ->whereNotNull('r.reg_major_pass_date');
        } else {
            return $query->where('r.reg_status_pass', '=', 1);
        }

    }
}
