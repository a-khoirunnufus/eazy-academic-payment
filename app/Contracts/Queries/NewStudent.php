<?php

namespace App\Contracts\Queries;

use Illuminate\Support\Collection;
use App\Contracts\Queries\NewStudentSelect;
use App\Contracts\Queries\NewStudentFilter;

interface NewStudent {
    public function selects(NewStudentSelect ...$selects);
    public function filters(NewStudentFilter ...$filters);
    public function result(): Collection;
    public function getQuery();
}
