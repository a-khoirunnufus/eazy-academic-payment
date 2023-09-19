<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment\Path;
use App\Models\Payment\Period;
use App\Models\Payment\Year;
use App\Models\Payment\Faculty;
use App\Models\Payment\Studyprogram;
use App\Models\Payment\MasterJob;
use Illuminate\Http\Request;
use App\Traits\Payment\LogActivity;
use DB;

class GenerateController extends Controller
{
    use LogActivity;

    public function newStudentInvoice()
    {
        $invoice_periods = DB::table('pmb.register as reg')
            ->leftJoin('masterdata.ms_school_year as msy', 'msy.msy_id', '=', 'reg.ms_school_year_id')
            ->groupBy('msy.msy_id', 'msy.msy_year', 'msy.msy_code')
            ->select(
                'msy.msy_id as school_year_id',
                'msy.msy_year as school_year_year',
                'msy.msy_semester as school_year_semester',
                'msy.msy_code as school_year_code',
            )
            ->get();
        $current_period_code = 20221;

        return view('pages._payment.generate.new-student-invoice.index', compact('invoice_periods', 'current_period_code'));
    }

    public function newStudentInvoiceDetail(Request $request)
    {
        $validated = $request->validate([
            'invoice_period_code' => 'required',
            'scope' => 'required|in:all,faculty,studyprogram',
            'faculty_id' => 'required_if:scope,faculty',
            'studyprogram_id' => 'required_if:scope,studyprogram',
        ]);

        $invoice_period = Year::where('msy_code', '=', $validated['invoice_period_code'])->first();
        $scope = $validated['scope'];
        $faculty = null;
        $studyprogram = null;

        if ($validated['scope'] == 'faculty') {
            $faculty = Faculty::find(intval($validated['faculty_id']));
        } elseif ($validated['scope'] == 'studyprogram') {
            $studyprogram = Studyprogram::find(intval($validated['studyprogram_id']));
            $faculty = Faculty::find($studyprogram->faculty_id);
        }

        return view('pages._payment.generate.new-student-invoice.detail', compact('invoice_period', 'scope', 'faculty', 'studyprogram'));
    }

    public function StudentInvoice()
    {
        $year = Year::all();
        $path = Path::all();
        $period = Period::all();
        $log = $this->logActivityLists(request()->path());
        return view('pages._payment.generate.student-invoice.index', compact('year','path','period','log'));
    }

    public function StudentInvoiceDetail(Request $request)
    {
        $data['f'] = $request->query()['f'];
        $data['sp'] = $request->query()['sp'];

        $year = Year::all();
        $path = Path::all();
        $period = Period::all();
        $log = $this->logActivityLists(request()->path());
        return view('pages._payment.generate.student-invoice.detail',compact('data','year','path','period','log'));
    }

    public function discount()
    {
        $period = Year::all();
        return view('pages._payment.generate.discount.index',compact('period'));
    }

    public function scholarship()
    {
        $period = Year::all();
        $log = $this->logActivityLists(request()->path());
        return view('pages._payment.generate.scholarship.index',compact('period','log'));
    }
}
