<?php

namespace App\Http\Controllers\_Payment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Payment\Year;
use App\Models\Payment\Period;
use App\Models\Payment\Path;
use App\Models\Payment\Faculty;
use App\Models\Payment\Studyprogram;
use App\Models\Payment\LectureType;
use App\Models\Payment\Settings;
use App\Traits\Payment\General as PaymentGeneral;

class ReportController extends Controller
{
    use PaymentGeneral;

    function invoiceOldStudentPerStudyprogram()
    {
        $current_year = $this->getActiveSchoolYearCode();
        $current_year = Year::where('msy_code', $current_year)->first();

        return view('pages._payment.report.old-student-invoice.per-study-program', compact('current_year'));
    }

    function invoiceOldStudentPerStudent(Request $request)
    {
        $year_code = $this->getActiveSchoolYearCode();
        $year = Year::where('msy_code', $year_code)->first();
        if ($request->get('school_year')) {
            $year = Year::where('msy_code', $request->get('school_year'))->first();
        }

        $studyprogram = null;
        if ($request->get('studyprogram')) {
            $studyprogram = Studyprogram::find($request->get('studyprogram'));
        }

        return view('pages._payment.report.old-student-invoice.per-student', compact('year', 'studyprogram'));
    }

    function invoiceNewStudentPerStudyprogram()
    {
        $current_year = $this->getActiveSchoolYearCode();
        $current_year = Year::where('msy_code', $current_year)->first();

        return view('pages._payment.report.new-student-invoice.per-study-program', compact('current_year'));
    }

    function invoiceNewStudentPerStudent(Request $request)
    {
        $year_code = $this->getActiveSchoolYearCode();
        $year = Year::where('msy_code', $year_code)->first();
        if ($request->get('school_year_id')) {
            $year = Year::find($request->get('school_year_id'));
        }

        $period = null;
        if ($request->get('period_id')) {
            $period = Period::find($request->get('period_id'));
        }

        $path = null;
        if ($request->get('path_id')) {
            $path = Path::find($request->get('path_id'));
        }

        $studyprogram = null;
        if ($request->get('studyprogram_id')) {
            $studyprogram = Studyprogram::find($request->get('studyprogram_id'));
        }

        $faculty = null;
        if ($studyprogram) {
            $faculty = $studyprogram->faculty;
        }

        $lecture_type = null;
        if ($request->get('lecture_type_id')) {
            $lecture_type = LectureType::find($request->get('lecture_type_id'));
        }

        \Log::debug(compact('year', 'period', 'path', 'studyprogram', 'lecture_type'));
        return view('pages._payment.report.new-student-invoice.per-student', compact('year', 'period', 'path', 'faculty', 'studyprogram', 'lecture_type'));
    }

    function invoiceRegistrant(){
        $current_year = $this->getActiveSchoolYearCode();
        $current_year = Year::where('msy_code', $current_year)->first();

        return view('pages._payment.report.registrant-invoice', compact('current_year'));
    }

    /**
     * OLD
     */

    function oldStudentReceivable(){
        $year = Year::all();
        $faculty = Faculty::all();
        return view('pages._payment.report.old-student-receivables.per-study-program', compact('year', 'faculty'));
    }

    function oldStudentReceivableDetail($programStudy){
        $angkatan = Year::select(DB::raw("SUBSTR(msy_code, 1, 4) as Tahun"))->distinct()->get();
        $periode = Period::all();
        $jalur = Path::all();
        return view('pages._payment.report.old-student-receivables.per-student', compact('programStudy','angkatan', 'periode', 'jalur'));
    }

    function newStudentReceivables(){
        $year = Year::all();
        $faculty = Faculty::all();
        return view('pages._payment.report.new-student-receivables.per-study-program', compact('year', 'faculty'));
    }

    function newStudentReceivableDetail($programStudy){
        $angkatan = Year::all();
        $periode = Period::all();
        $jalur = Path::all();
        return view('pages._payment.report.new-student-receivables.per-student', compact('programStudy','angkatan', 'periode', 'jalur'));
    }
}
