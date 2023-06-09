<?php

namespace App\Services\Queries\ReRegistration;

use Illuminate\Support\Facades\DB;

class ReRegistration {

    public $query;

    /**
     * Table Aliases:
     * pmb.register -> register
     * pmb.participant -> participant
     * masterdata.ms_school_year as school_year
     * pmb.ms_period -> period
     * pmb.ms_path -> path
     * masterdata.ms_studyprogram -> studyprogram
     * masterdata.ms_lecture_type -> lecture_type
     * masterdata.ms_faculties -> faculty
     */

    public function __construct(bool $custom_select = false)
    {
        $this->queryBase();
        $this->queryFilter();
        if (!$custom_select) {
            $this->query->select($this->getSelectColumns())->distinct();
        }
    }

    protected function queryBase()
    {
        $query = DB::table('pmb.register as register')
            ->leftjoin('pmb.participant as participant', 'participant.par_id', '=', 'register.par_id')
            ->leftJoin('masterdata.ms_school_year as school_year', 'school_year.msy_id', '=', 'register.ms_school_year_id')
            ->leftJoin('pmb.ms_period as period', 'period.period_id', '=', 'register.ms_period_id')
            ->leftJoin('pmb.ms_path as path', 'path.path_id', '=', 'register.ms_path_id')
            ->leftJoin('masterdata.ms_studyprogram as studyprogram', 'studyprogram.studyprogram_id', '=', 'register.reg_major_pass')
            ->leftJoin('masterdata.ms_lecture_type as lecture_type', 'lecture_type.mlt_id', '=', DB::raw('register.reg_major_lecture_type_pass::INTEGER'))
            ->leftJoin('masterdata.ms_faculties as faculty', 'faculty.faculty_id', '=', 'studyprogram.faculty_id');

        $this->query = $query;
    }

    protected function queryFilter()
    {
        $query = $this->query
            // Registration passed condition
            ->where('register.reg_status_pass', '=', 1)
            ->whereNotNull('register.reg_major_pass')
            ->whereNotNull('register.reg_major_lecture_type_pass')
            ->whereNotNull('register.reg_major_pass_date')

            // Filter participant data
            ->where('participant.par_active_status', '=', 1)
            ->whereNotNull('participant.par_fullname')
            ->whereNotNull('participant.par_number');

        $this->query = $query;
    }

    protected function getSelectColumns()
    {
        return [
            'school_year.msy_id as school_year_id',
            'school_year.msy_year as school_year_year',
            'school_year.msy_semester as school_year_semester',

            'register.reg_id as registration_id',
            'register.reg_number as registration_number',

            'participant.par_id as participant_id',
            'participant.par_number as participant_number',
            'participant.par_fullname as participant_fullname',
            'participant.par_nik as participant_nik',
            'participant.par_phone as participant_phone',

            'period.period_id as registration_period_id',
            'period.period_name as registration_period_name',

            'path.path_id as registration_path_id',
            'path.path_name as registration_path_name',

            'faculty.faculty_id as faculty_id',
            'faculty.faculty_name as faculty_name',

            'studyprogram.studyprogram_id as studyprogram_id',
            'studyprogram.studyprogram_type as studyprogram_type',
            'studyprogram.studyprogram_name as studyprogram_name',

            'lecture_type.mlt_id as lecture_type_id',
            'lecture_type.mlt_name as lecture_type_name',
        ];
    }
}
