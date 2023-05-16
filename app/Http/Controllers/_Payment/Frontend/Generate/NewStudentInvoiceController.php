<?php

namespace App\Http\Controllers\_Payment\Frontend\Generate;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Masterdata\MsInstitution;
use App\Models\Masterdata\MsFaculty;
use App\Models\Period as MsRegistrationPeriod;
use App\Models\Path as MsRegistrationPath;
use App\Models\PeriodPath as RegistrationPeriodPath;
use DB;

class NewStudentInvoiceController extends Controller
{
    public function perInstitution()
    {
        $registration_periods = MsRegistrationPeriod::all();
        $registration_paths = MsRegistrationPath::all();

        return view(
            'pages._payment.generate.new-student-invoice.per-institution',
            compact('registration_periods', 'registration_paths')
        );
    }

    public function perFaculty(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'period_path_id' => 'required|integer',
        ]);

        $this->redirectIfError($validator);

        $validated = $validator->validated();
        $period_path_id = $validated['period_path_id'];

        $period_path = RegistrationPeriodPath::with(['period', 'period.schoolyear', 'path'])
            ->where('ppd_id', $period_path_id)
            ->first()
            ->toArray();

        return view(
            'pages._payment.generate.new-student-invoice.per-faculty',
            compact('period_path_id', 'period_path')
        );
    }

    public function perStudyprogram(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'period_path_id' => 'required|integer',
            'faculty_id' => 'required|integer',
        ]);

        $this->redirectIfError($validator);

        $validated = $validator->validated();
        $period_path_id = $validated['period_path_id'];
        $faculty_id = $validated['faculty_id'];

        $period_path = RegistrationPeriodPath::with(['period', 'period.schoolyear', 'path'])
            ->where('ppd_id', $period_path_id)
            ->first()
            ->toArray();
        $faculty = MsFaculty::find($faculty_id)->toArray();

        return view(
            'pages._payment.generate.new-student-invoice.per-studyprogram',
            compact('period_path_id', 'faculty_id', 'period_path', 'faculty')
        );
    }

    public function perStudent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'period_path_id' => 'required|integer',
            'faculty_id' => 'required|integer',
            'studyprogram_lecture_type_id' => 'required|integer',
        ]);

        $this->redirectIfError($validator);

        $validated = $validator->validated();
        $period_path_id = $validated['period_path_id'];
        $faculty_id = $validated['faculty_id'];
        $studyprogram_lecture_type_id = $validated['studyprogram_lecture_type_id'];

        $period_path = RegistrationPeriodPath::with(['period', 'period.schoolyear', 'path'])
            ->where('ppd_id', $period_path_id)
            ->first()
            ->toArray();
        $faculty = MsFaculty::find($faculty_id)->toArray();
        $studyprogram = DB::table('masterdata.ms_studyprogram as sp')
            ->leftJoin('masterdata.ms_major_lecture_type as mlt', 'sp.studyprogram_id', '=', 'mlt.mma_id')
            ->select('sp.studyprogram_id', 'sp.studyprogram_name')
            ->where('mlt.mma_lt_id', '=', $studyprogram_lecture_type_id)
            ->first();
        $lecture_type = DB::table('masterdata.ms_lecture_type as lt')
            ->leftJoin('masterdata.ms_major_lecture_type as mlt', 'lt.mlt_id', '=', 'mlt.mlt_id')
            ->select('lt.mlt_id', 'lt.mlt_name')
            ->where('mlt.mma_lt_id', '=', $studyprogram_lecture_type_id)
            ->first();

        return view(
            'pages._payment.generate.new-student-invoice.per-student',
            compact('period_path_id', 'faculty_id', 'studyprogram_lecture_type_id', 'period_path', 'faculty', 'studyprogram', 'lecture_type')
        );
    }

    private function redirectIfError($validator)
    {
        if ($validator->errors()->has('period_path_id')) {
            return redirect()
                ->route('payment.generate.new-student-invoice.per-institution', $request->query())
                ->with('warning', 'Silahkan pilih periode, jalur dan gelombang terlebih dahulu!');
        }

        if ($validator->errors()->has('faculty_id')) {
            return redirect()
                ->route('payment.generate.new-student-invoice.per-faculty', $request->query())
                ->with('warning', 'Silahkan pilih fakultas terlebih dahulu!');
        }

        if ($validator->errors()->has('studyprogram_lecture_type_id')) {
            return redirect()
                ->route('payment.generate.new-student-invoice.per-studyprogram', $request->query())
                ->with('warning', 'Silahkan pilih program studi terlebih dahulu!');
        }
    }
}
