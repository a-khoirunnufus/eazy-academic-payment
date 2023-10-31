<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentsBalanceController extends Controller
{
    public function index()
    {
        return view('pages._payment.students-balance.index');
    }

    public function withdraw(Request $request)
    {
        $issuers = \DB::table('finance.vw_student_balance_withdraw_issuer')->get();

        return view('pages._payment.students-balance.withdraw', compact('issuers'));
    }
}
