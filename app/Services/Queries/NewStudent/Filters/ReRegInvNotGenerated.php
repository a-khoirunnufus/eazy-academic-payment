<?php

namespace App\Services\Queries\NewStudent\Filters;

use App\Contracts\Queries\NewStudentFilter;

class ReRegInvNotGenerated implements NewStudentFilter
{
    public function __construct() {}

    public function apply($query)
    {
        return $query->where('prr.prr_id', '=', null);
    }
}
