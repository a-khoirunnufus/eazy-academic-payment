<?php

namespace App\Services\Queries\NewStudent\Filters;

use App\Contracts\Queries\NewStudentFilter;

class ReRegUnpaid implements NewStudentFilter
{
    public function __construct() {}

    public function apply($query)
    {
        return $query->where('prr.prr_status', '=', 'belum lunas')
            ->whereNull('prr.deleted_at')
            ->where('r.re_register_status', '=', 0);
    }
}
