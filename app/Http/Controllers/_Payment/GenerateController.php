<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment\Path;
use App\Models\Payment\Period;
use App\Models\Payment\Year;
use App\Models\Payment\Faculty;
use App\Models\Payment\Studyprogram;
use App\Models\Payment\MasterJob;
use App\Traits\Payment\General;
use Illuminate\Http\Request;
use DB;

class GenerateController extends Controller
{
    use General;

    public function newStudentInvoice()
    {
        $year = Year::all();
        $path = Path::all();
        $period = Period::all();
        $yearCode = $this->getActiveSchoolYearId();
        return view('pages._payment.generate.new-student-invoice.index', compact('year','path','period','yearCode'));
    }

    public function newStudentInvoiceDetail(Request $request)
    {
        $data['f'] = $request->query()['f'];
        $data['sp'] = $request->query()['sp'];
        $data['year'] = $request->query()['year'];

        $year = Year::all();
        $path = Path::all();
        $period = Period::all();
        return view('pages._payment.generate.new-student-invoice.detail',compact('data','year','path','period'));
    }

    public function StudentInvoice()
    {
        $year = Year::all();
        $path = Path::all();
        $period = Period::all();
        $yearCode = $this->getActiveSchoolYearCode();
        return view('pages._payment.generate.student-invoice.index', compact('year','path','period','yearCode'));
    }

    public function StudentInvoiceDetail(Request $request)
    {
        $data['f'] = $request->query()['f'];
        $data['sp'] = $request->query()['sp'];
        $data['year'] = $request->query()['year'];

        $year = Year::all();
        $path = Path::all();
        $period = Period::all();
        return view('pages._payment.generate.student-invoice.detail',compact('data','year','path','period'));
    }

    public function discount()
    {
        $period = Year::orderBy('msy_code')->get();
        return view('pages._payment.generate.discount.index',compact('period'));
    }

    public function scholarship()
    {
        $period = Year::orderBy('msy_code')->get();
        return view('pages._payment.generate.scholarship.index',compact('period'));
    }
}
