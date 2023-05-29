<?php

namespace App\Services\Queries\NewStudent\Filters;

use App\Contracts\Queries\NewStudentFilter;

class ReRegDone implements NewStudentFilter
{
    public function __construct() {}

    public function apply($query)
    {
        return $query->where('r.re_register_status', '=', 1);
    }
}
