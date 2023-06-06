<?php

namespace App\Services\Queries\NewStudent;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Contracts\Queries\NewStudent as INewStudent;
use App\Contracts\Queries\NewStudentFilter;
use App\Contracts\Queries\NewStudentSelect;

class NewStudent implements INewStudent {

    private $query;

    public function __construct()
    {
        $query = DB::table('pmb.participant as p')
            ->leftJoin('pmb.register as r', 'p.par_id', '=', 'r.par_id')

            // period and path in masterdata(schema) is outdated
            // ->leftJoin('masterdata.ms_period as period', 'period.period_id', '=', 'r.ms_period_id')
            // ->leftJoin('masterdata.ms_path as path', 'path.path_id', '=', 'r.ms_path_id')
            ->leftJoin('pmb.ms_period as period', 'period.period_id', '=', 'r.ms_period_id')
            ->leftJoin('pmb.ms_path as path', 'path.path_id', '=', 'r.ms_path_id')

            ->leftJoin('masterdata.ms_studyprogram as std', 'std.studyprogram_id', '=', 'r.reg_major_pass')
            ->leftJoin('masterdata.ms_lecture_type as lct', 'lct.mlt_id', '=', DB::raw('r.reg_major_lecture_type_pass::INTEGER'))
            ->leftJoin('masterdata.ms_faculties as fac', 'fac.faculty_id', '=', 'std.faculty_id')

            // condition when prr record not exist(or deleted) still join prr as null value
            ->join('finance.payment_re_register as prr', function ($join) {
                $join->on('r.reg_id', '=', 'prr.reg_id');
                $join->on('prr.deleted_at', 'is', DB::raw('null'));
            }, null, null, 'left outer')

            ->distinct()
            ->where('p.par_active_status', '=', 1)
            ->whereNotNull('p.par_fullname')
            ->whereNotNull('p.par_nik');

        $this->query = $query;
    }

    public function selects(NewStudentSelect ...$selects)
    {
        $data = [];
        foreach ($selects as $select) {
            $data = array_merge($data, $select->expressions());
        }
        $this->query = $this->query->select($data);

        return $this;
    }

    public function filters(NewStudentFilter ...$filters)
    {
        foreach ($filters as $filter) {
            $this->query = $filter->apply($this->query);
        }

        return $this;
    }

    public function result(): Collection
    {
        return $this->query->get();
    }

    public function getQuery()
    {
        return $this->query;
    }
}
