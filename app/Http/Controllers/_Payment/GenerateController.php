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
    
    public function StudentInvoiceDetail(Request $request)
    {
        $data['msy'] = $request->query()['msy'];
        $data['f'] = $request->query()['f'];
        $data['sp'] = $request->query()['sp'];

        return view('pages._payment.generate.student-invoice.detail',compact('data'));
    }
}