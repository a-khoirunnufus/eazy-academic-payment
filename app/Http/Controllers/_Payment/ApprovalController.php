<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment\Faculty;
use App\Models\Payment\Path;
use App\Models\Payment\Period;
use App\Models\Payment\Year;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function getActiveSchoolYearCode(){
        return 20221;
    }
    public function getActiveSchoolYear(){
        return "2022/2023 - Ganjil";
    }

    public function manualPayment()
    {
        $path = Path::all();
        $period = Period::all();
        $faculty = Faculty::all();
        return view('pages._payment.approval.manual-payment.index', compact('path', 'period', 'faculty'));
    }

    public function index()
    {
        $path = Path::all();
        $period = Period::all();
        $faculty = Faculty::all();
        return view('pages._payment.approval.index', compact('path', 'period', 'faculty'));
    }

    public function dispensation()
    {
        $year = Year::all();
        $faculty = Faculty::all();

        $activeYear = $this->getActiveSchoolYear();
        $yearCode = $this->getActiveSchoolYearCode();
        return view('pages._payment.approval.dispensation.index', compact('year', 'faculty','activeYear', 'yearCode'));
    }

    public function credit()
    {
        $year = Year::all();
        $faculty = Faculty::all();

        $activeYear = $this->getActiveSchoolYear();
        $yearCode = $this->getActiveSchoolYearCode();
        return view('pages._payment.approval.credit.index', compact('year', 'faculty','activeYear', 'yearCode'));
    }
}
