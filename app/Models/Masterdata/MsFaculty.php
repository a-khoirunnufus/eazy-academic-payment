<?php

// !! Gunakan App\Models\Faculty !!

namespace App\Models\Masterdata;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Studyprogram as MsStudyprogram;

class MsFaculty extends Model
{
    protected $table = "masterdata.ms_faculties";

    protected $primaryKey = 'faculty_id';

    public function studyprograms(): HasMany
    {
        return $this->hasMany(MsStudyprogram::class, 'faculty_id', 'faculty_id');
    }
}
