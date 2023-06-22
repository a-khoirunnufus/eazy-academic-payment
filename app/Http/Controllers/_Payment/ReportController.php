<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Path;
use App\Models\Period;
use App\Models\Studyprogram;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    //
    function oldStudent()
    {
        $year = Year::all();
        $faculty = Faculty::all();
        return view('pages.report.old-student-invoice.per-study-program', compact('year', 'faculty'));
    }

    function oldStudentDetail($programStudy)
    {
        $angkatan = Year::select(DB::raw("SUBSTR(msy_code, 1, 4) as Tahun"))->distinct()->get();
        $periode = Period::all();
        $jalur = Path::all();
        return view('pages.report.old-student-invoice.per-student', compact('programStudy','angkatan', 'periode', 'jalur'));
    }

    function newStudentDetail($programStudy)
    {
        $angkatan = Year::all();
        $periode = Period::all();
        $jalur = Path::all();
        return view('pages.report.new-student-invoice.per-student', compact('programStudy','angkatan', 'periode', 'jalur'));
    }

    function newStudent(){
        $year = Year::all();
        $faculty = Faculty::all();
        return view('pages.report.new-student-invoice.per-study-program', compact('year', 'faculty'));
    }
}
