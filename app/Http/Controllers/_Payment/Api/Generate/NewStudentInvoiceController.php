<?php

namespace App\Http\Controllers\_Payment\Api\Generate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Masterdata\MsInstitution;
use App\Models\PeriodPath as RegistrationPeriodPath;
use DB;

class NewStudentInvoiceController extends Controller
{
    public function index()
    {
        $data = DB::table('pmb.participant as p')
                    ->leftJoin('pmb.register as r', 'p.par_id', '=', 'r.par_id')
                    ->select(
                        'p.par_fullname as fullname',
                        'p.par_nik as nik',
                        'p.par_phone as phone',
                        'p.par_birthday as birthday',
                        'p.par_birthplace as birthplace',
                        'p.par_gender as gender',
                        'p.par_religion as religion'
                    )
                    ->whereNotNull('r.reg_major_pass')
                    ->whereNotNull('r.reg_major_lecture_type_pass')
                    ->where('r.reg_status_pass', '=', 1)
                    ->where('r.re_register_status', '=', 0)
                    ->where('p.par_active_status', '=', 1)
                    ->get();

        return datatables($data)->toJSON();
    }

    /**
     * Datatable source to list period and path in this institution/university
     */
    public function getPeriodPath(Request $request)
    {
        $filters = $request->input('custom_filters');

        // remove item with null value or #ALL value
        $filters = array_filter($filters, function($item){
            return !is_null($item) && $item != '#ALL';
        });

        $query = DB::table('masterdata.period_path as prpt')
            ->leftJoin('masterdata.ms_period as pr', 'prpt.period_id', '=', 'pr.period_id')
            ->leftJoin('masterdata.ms_path as pt', 'prpt.path_id', '=', 'pt.path_id')
            ->leftJoin('masterdata.ms_school_year as sy', 'sy.msy_id', '=', 'pr.msy_id')
            // Cek Institusi Begin
            ->leftJoin('masterdata.period_path_major as ppm', 'ppm.ppd_id', '=', 'prpt.ppd_id')
            ->leftJoin('masterdata.ms_major_lecture_type as mlct', 'mlct.mma_lt_id', '=', 'ppm.mma_lt_id')
            ->leftJoin('masterdata.ms_studyprogram as mstd', 'mstd.studyprogram_id', '=', 'mlct.mma_id')
            ->leftJoin('masterdata.ms_faculties as mfac', 'mfac.faculty_id', '=', 'mstd.faculty_id')
            ->where('mfac.institution_id', MsInstitution::$defaultInstitutionId)
            // Cek Institusi End
            ->select(
                'prpt.ppd_id as ppd_id',
                'pr.period_id as period_id',
                'pr.period_name as period_name',
                'pr.period_start as period_start',
                'pr.period_end as period_end',
                'pt.path_id as path_id',
                'pt.path_name as path_name',
                'sy.msy_year as academic_year'
            )
            ->groupBy(
                'prpt.ppd_id',
                'pr.period_id',
                'pr.period_name',
                'pr.period_start',
                'pr.period_end',
                'pt.path_id',
                'pt.path_name',
                'sy.msy_year'
            );

        foreach($filters as $key => $value) {
            $query->where($key, '=', $value);
        }

        $data = $query->get();

        return datatables($data)->toJSON();
    }

    public function getFaculties(Request $request)
    {
        $validated = $request->validate([
            'period_path_id' => 'required'
        ]);

        $query = DB::table('masterdata.ms_faculties as mfac')
            ->leftJoin('masterdata.ms_studyprogram as mstd', 'mstd.faculty_id', '=', 'mfac.faculty_id')
            ->leftJoin('masterdata.ms_major_lecture_type as mlct', 'mlct.mma_id', '=', 'mstd.studyprogram_id')
            ->leftJoin('masterdata.ms_lecture_type as lct', 'lct.mlt_id', '=', 'mlct.mlt_id')
            ->leftJoin('masterdata.period_path_major as ppm', 'ppm.mma_lt_id', '=', 'mlct.mma_lt_id')
            ->leftJoin('masterdata.period_path as pp', 'pp.ppd_id', '=', 'ppm.ppd_id')
            ->select('mfac.faculty_id', 'mfac.faculty_name')
            ->where('pp.ppd_id', $validated['period_path_id'])
            ->where('mfac.institution_id', MsInstitution::$defaultInstitutionId) // Cek Institusi
            ->groupBy('mfac.faculty_id', 'mfac.faculty_name');

        $data = $query->get();

        return datatables($data)->toJSON();
    }

    public function getStudyprogramsLectureTypes(Request $request)
    {
        $validated = $request->validate([
            'period_path_id' => 'required',
            'faculty_id' => 'required',
        ]);

        $query = DB::table('masterdata.ms_major_lecture_type as mlct')
            ->leftJoin('masterdata.ms_lecture_type as lct', 'lct.mlt_id', '=', 'mlct.mlt_id')
            ->leftJoin('masterdata.ms_studyprogram as mstd', 'mlct.mma_id', '=', 'mstd.studyprogram_id')
            ->leftJoin('masterdata.ms_faculties as mfac', 'mstd.faculty_id', '=', 'mfac.faculty_id')
            ->leftJoin('masterdata.period_path_major as ppm', 'ppm.mma_lt_id', '=', 'mlct.mma_lt_id')
            ->leftJoin('masterdata.period_path as pp', 'pp.ppd_id', '=', 'ppm.ppd_id')
            ->select('mlct.mma_lt_id', 'mstd.studyprogram_name', 'lct.mlt_name')
            ->where('pp.ppd_id', $validated['period_path_id'])
            ->where('mfac.faculty_id', $validated['faculty_id'])
            ->where('mfac.institution_id', MsInstitution::$defaultInstitutionId); // Cek Institusi

        $data = $query->get();

        return datatables($data)->toJSON();
    }

    public function getStudents(Request $request)
    {
        $validated = $request->validate([
            'period_path_id' => 'required',
            'studyprogram_lecture_type_id' => 'required',
        ]);

        $period_path = RegistrationPeriodPath::find($validated['period_path_id']);
        $studyprogram_lecture_type = DB::table('masterdata.ms_major_lecture_type')
            ->where('mma_lt_id', '=', $validated['studyprogram_lecture_type_id'])
            ->first();

        $data = DB::table('pmb.participant as p')
            ->leftJoin('pmb.register as r', 'p.par_id', '=', 'r.par_id')
            ->leftJoin('admission.payment_re_register as prr', 'r.reg_id', '=', 'prr.reg_id')
            ->select(
                'p.par_fullname as fullname',
                'p.par_nik as nik',
                'p.par_phone as phone',
                'p.par_birthday as birthday',
                'p.par_birthplace as birthplace',
                'p.par_gender as gender',
                'p.par_religion as religion'
            )
            // Cek Kelulusan
            ->whereNotNull('r.reg_major_pass')
            ->whereNotNull('r.reg_major_lecture_type_pass')
            ->whereNotNull('r.reg_major_pass_date')
            ->where('r.reg_status_pass', '=', 1)
            // Filter Berdasarkan Period, Path, Studyprogram, Lecture Type
            ->where('r.ms_period_id', '=', $period_path->period_id)
            ->where('r.ms_path_id', '=', $period_path->path_id)
            ->where('r.reg_major_pass', '=', $studyprogram_lecture_type->mma_id)
            ->where('r.reg_major_lecture_type_pass', '=', $studyprogram_lecture_type->mlt_id)
            // Cek Belum Bayar Daftar Ulang
            ->where('prr.prr_status', '=', 'belum lunas')
            ->whereNull('prr.deleted_at')
            ->where('r.re_register_status', '=', 0)
            ->get();

        return datatables($data)->toJSON();
    }

    public function getStudentCount(Request $request)
    {
        $validated = $request->validate([
            'scope' => 'required',
            'period_path_id' => 'required_if:scope,institution|required_if:scope,faculty|required_if:scope,studyprogram',
            'faculty_id' => 'required_if:scope,faculty|required_if:scope,studyprogram',
            'studyprogram_lecture_type_id' => 'required_if:scope,studyprogram',
        ]);

        $query = DB::table('pmb.participant as p')
            ->leftJoin('pmb.register as reg', 'p.par_id', '=', 'reg.par_id')
            ->leftJoin('admission.payment_re_register as prr', 'reg.reg_id', '=', 'prr.reg_id')
            ->leftJoin('masterdata.period_path as ppd', function($join) {
                $join->on('ppd.period_id', '=', 'reg.ms_period_id');
                $join->on('ppd.path_id', '=', 'reg.ms_path_id');
            })
            ->leftJoin('masterdata.period_path_major as ppm', 'ppm.ppd_id', '=', 'ppd.ppd_id')
            ->leftJoin('masterdata.ms_major_lecture_type as mlt', 'mlt.mma_lt_id', '=', 'ppm.mma_lt_id')
            ->leftJoin('masterdata.ms_studyprogram as sprg', 'sprg.studyprogram_id', '=', 'mlt.mma_id')
            ->leftJoin('masterdata.ms_faculties as fac', 'fac.faculty_id', '=', 'sprg.faculty_id')

            ->select('p.par_id')

            // Cek Institusi
            ->where('fac.institution_id', MsInstitution::$defaultInstitutionId)

            // Cek Kelulusan
            ->whereNotNull('reg.reg_major_pass')
            ->whereNotNull('reg.reg_major_lecture_type_pass')
            ->whereNotNull('reg.reg_major_pass_date')
            ->where('reg.reg_status_pass', '=', 1)

            // Cek Belum Bayar Daftar Ulang
            ->where('prr.prr_status', '=', 'belum lunas')
            ->whereNull('prr.deleted_at')
            ->where('reg.re_register_status', '=', 0);

        if ($validated['scope'] == 'institution') {
            $period_path = RegistrationPeriodPath::find($validated['period_path_id']);

            $query->where('reg.ms_period_id', '=', $period_path->period_id)
                ->where('reg.ms_path_id', '=', $period_path->path_id);
        }

        if ($validated['scope'] == 'faculty') {
            $period_path = RegistrationPeriodPath::find($validated['period_path_id']);

            $query->where('reg.ms_period_id', '=', $period_path->period_id)
                ->where('reg.ms_path_id', '=', $period_path->path_id)
                ->where('fac.faculty_id', '=', $validated['faculty_id']);
        }

        if ($validated['scope'] == 'studyprogram') {
            $period_path = RegistrationPeriodPath::find($validated['period_path_id']);
            $studyprogram_lecture_type = DB::table('masterdata.ms_major_lecture_type')
                ->where('mma_lt_id', '=', $validated['studyprogram_lecture_type_id'])
                ->first();

            $query->where('reg.ms_period_id', '=', $period_path->period_id)
                ->where('reg.ms_path_id', '=', $period_path->path_id)
                ->where('fac.faculty_id', '=', $validated['faculty_id'])
                ->where('reg.reg_major_pass', '=', $studyprogram_lecture_type->mma_id)
                ->where('reg.reg_major_lecture_type_pass', '=', $studyprogram_lecture_type->mlt_id);
        }

        $data = $query->groupBy('p.par_id')->get()->count();

        return response()->json(['count' => $data], 200);
    }
}
