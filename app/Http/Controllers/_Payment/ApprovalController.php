<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function manualPayment()
    {
        return view('pages._payment.approval.manual-payment.index');
    }

    public function dispensation()
    {
        return view('pages._payment.approval.dispensation.index');
    }

    public function credit()
    {
        return view('pages._payment.approval.credit.index');
    }
}
