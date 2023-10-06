<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Payment\General as PaymentGeneral;

class StudentDispensationController extends Controller
{
    use PaymentGeneral;

    public function index(Request $request)
    {
        $student = $student = $this->getStudentData();
        $year = $this->getActiveSchoolYear();
        $yearCode = $this->getActiveSchoolYearCode();

        return view('pages._payment.student.student-dispensation.index', compact('student','year','yearCode'));
    }
}
