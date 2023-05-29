<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use App\Models\Path;
use App\Models\Period;
use App\Models\Year;
use App\Models\Faculty;
use App\Models\Studyprogram;
use Illuminate\Http\Request;

class GenerateController extends Controller
{
    public function newStudentInvoice()
    {
        return view('pages._payment.generate.new-student-invoice.index');
    }

    public function newStudentInvoiceDetail(Request $request)
    {
        $validated = $request->validate([
            'scope' => 'required|in:all,faculty,studyprogram',
            'faculty_id' => 'required_if:scope,faculty',
            'studyprogram_id' => 'required_if:scope,studyprogram',
        ]);

        $scope = $validated['scope'];
        $faculty = null;
        $studyprogram = null;

        if ($validated['scope'] == 'faculty') {
            $faculty = Faculty::find(intval($validated['faculty_id']));
        } elseif ($validated['scope'] == 'studyprogram') {
            $studyprogram = Studyprogram::find(intval($validated['studyprogram_id']));
            $faculty = Faculty::find($studyprogram->faculty_id);
        }

        return view('pages._payment.generate.new-student-invoice.detail', compact('scope', 'faculty', 'studyprogram'));
    }

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
