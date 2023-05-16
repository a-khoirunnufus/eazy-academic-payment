<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GenerateController extends Controller
{
    public function newStudentInvoice()
    {
        return view('pages._payment.generate.new-student-invoice.detail');
    }

    public function StudentInvoice()
    {
        return view('pages._payment.generate.student-invoice.index');
    }
}
