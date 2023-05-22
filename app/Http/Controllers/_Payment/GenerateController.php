<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GenerateController extends Controller
{
    public function StudentInvoice()
    {
        return view('pages._payment.generate.student-invoice.index');
    }

    public function StudentInvoiceDetail(Request $request)
    {
        $data['f'] = $request->query()['f'];
        $data['sp'] = $request->query()['sp'];

        return view('pages._payment.generate.student-invoice.detail',compact('data'));
    }
}
