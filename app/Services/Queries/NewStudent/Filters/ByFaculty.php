<?php

namespace App\Services\Queries\NewStudent\Filters;

use Illuminate\Support\Facades\DB;
use App\Contracts\Queries\NewStudentFilter;

class ByFaculty implements NewStudentFilter
{
    private $faculty_id;

    public function __construct(int $faculty_id) {
        $this->faculty_id = $faculty_id;
    }

    public function apply($query)
    {
        return $query->where('fac.faculty_id', '=', $this->faculty_id);
    }
}
