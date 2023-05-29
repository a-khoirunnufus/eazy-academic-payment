<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use App\Models\Path;
use App\Models\Period;
use App\Models\Year;
use Illuminate\Http\Request;

class GenerateController extends Controller
{
    public function StudentInvoice()
    {
        $year = Year::all();
        $path = Path::all();
        $period = Period::all();
        return view('pages._payment.generate.student-invoice.index', compact('year','path','period'));
    }

    public function StudentInvoiceDetail(Request $request)
    {
        $data['f'] = $request->query()['f'];
        $data['sp'] = $request->query()['sp'];

        $year = Year::all();
        $path = Path::all();
        $period = Period::all();

        return view('pages._payment.generate.student-invoice.detail',compact('data','year','path','period'));
    }
}
