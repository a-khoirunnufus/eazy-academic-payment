<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Traits\Payment\General as PaymentGeneral;

class StudentInvoiceController extends Controller
{
    use PaymentGeneral;

    public function index()
    {
        $student = $this->getStudentData();

        return view('pages._payment.student.student-invoice.index', compact('student'));
    }

    public function proceedPayment($prr_id)
    {
        $student = $this->getStudentData();

        return view('pages._payment.student.student-invoice.proceed-payment.index', compact('prr_id', 'student'));
    }

    public function invoiceCicilan(Request $request){
        return view('pages._payment.student.studnet-invoice.invoice-cicilan', ['content' => $request->get('content')]);
    }
}
