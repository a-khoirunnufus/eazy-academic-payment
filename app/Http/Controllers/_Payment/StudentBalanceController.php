<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Payment\General as PaymentGeneral;

class StudentBalanceController extends Controller
{
    use PaymentGeneral;

    public function index(Request $request)
    {
        $student = $this->getStudentData();

        return view('pages._payment.student.student-balance.index', compact('student'));
    }
}
