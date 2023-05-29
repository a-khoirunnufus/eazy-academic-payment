<?php

namespace App\Contracts\Queries;

interface NewStudentFilter {
    public function apply($query);
}
