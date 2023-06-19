<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use App\Models\Year;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    //
    function oldStudent()
    {
        $year = Year::all();
        return view('pages.report.old-student-invoice.per-study-program', compact('year'));
    }

    function oldStudentDetail($programStudy)
    {
        return view('pages.report.old-student-invoice.per-student', compact('programStudy'));
    }
}
