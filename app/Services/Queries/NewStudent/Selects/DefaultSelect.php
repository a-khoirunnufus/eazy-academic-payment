<?php

namespace App\Services\Queries\NewStudent\Selects;

use App\Contracts\Queries\NewStudentSelect;

class DefaultSelect implements NewStudentSelect
{
    public function expressions(): array
    {
        return [
            'p.par_id as participant_id',
            'p.par_fullname as participant_fullname',
            'p.par_number as participant_number',
            'p.par_nik as participant_nik',
            'p.par_phone as participant_phone',
            'p.par_birthday as participant_birthday',
            'p.par_birthplace as participant_birthplace',
            'p.par_gender as participant_gender',
            'p.par_religion as participant_religion',
            'r.ms_school_year_id as school_year_id',
            'school_year.msy_year as school_year_year',
            'school_year.msy_semester as school_year_semester',
            'r.ms_period_id as registration_period_id',
            'period.period_name as registration_period_name',
            'r.ms_path_id as registration_path_id',
            'path.path_name as registration_path_name',
            'fac.faculty_id as faculty_id',
            'fac.faculty_name as faculty_name',
            'r.reg_major_pass as studyprogram_id',
            'std.studyprogram_type as studyprogram_type',
            'std.studyprogram_name as studyprogram_name',
            'r.reg_major_lecture_type_pass as lecture_type_id',
            'lct.mlt_name as lecture_type_name',
        ];
    }
}
