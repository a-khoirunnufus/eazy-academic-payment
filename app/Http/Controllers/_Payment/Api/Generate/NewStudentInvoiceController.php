<?php

namespace App\Http\Controllers\_Payment\Api\Generate;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Masterdata\MsInstitution as Institution;
use App\Models\Studyprogram;
use App\Models\Faculty;
use App\Services\Queries\NewStudent\NewStudent;
use App\Services\Queries\NewStudent\Selects\DefaultSelect;
use App\Services\Queries\NewStudent\Selects\InvoiceData;
use App\Services\Queries\NewStudent\Filters\ByFaculty;
use App\Services\Queries\NewStudent\Filters\ByStudyprogram;
use App\Services\Queries\NewStudent\Filters\RegPassed;

class NewStudentInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $faculty_w_studyprogram = Faculty::with(['studyProgram' => function ($query) {
                // $query->select('studyprogram_id', 'studyprogram_name');
                $query->orderBy('studyprogram_type', 'asc');
                $query->orderBy('studyprogram_name', 'asc');
            }])
            // ->select('faculty_id', 'faculty_name')
            ->where('institution_id', '=', Institution::$defaultInstitutionId)
            ->orderBy('faculty_name', 'asc')
            ->get();

        $students = (new NewStudent())
            ->selects(new DefaultSelect(), new InvoiceData())
            ->filters(new RegPassed(false))
            ->result()
            ->toArray();

        $data = [];
        foreach($faculty_w_studyprogram as $faculty){
            foreach (['faculty', 'studyprogram'] as $unit_type) {
                if ($unit_type == 'faculty') {
                    $student_count = 0;
                    $invoice_amount = 0;
                    $generated_invoice = 0;
                    foreach ($students as $student) {
                        if ($student->faculty_id == $faculty->faculty_id) {
                            $student_count++;
                            $invoice_amount += intval($student->invoice_amount);
                            if ($student->invoice_status == 'Sudah Digenerate') $generated_invoice++;
                        }
                    }
                    $data[] = [
                        'unit_type' => 'faculty',
                        'unit_id' => $faculty->faculty_id,
                        'unit_name' => $faculty->faculty_name,
                        'student_count' => $student_count,
                        'invoice_total_amount' => $invoice_amount,
                        'generated_invoice' => $generated_invoice.' / '.$student_count,
                    ];
                }

                if ($unit_type == 'studyprogram') {
                    foreach ($faculty->studyProgram as $studyprogram) {
                        $student_count = 0;
                        $invoice_amount = 0;
                        $generated_invoice = 0;
                        foreach ($students as $student) {
                            if ($student->studyprogram_id == $studyprogram->studyprogram_id) {
                                $student_count++;
                                $invoice_amount += intval($student->invoice_amount);
                                if ($student->invoice_status == 'Sudah Digenerate') $generated_invoice++;
                            }
                        }
                        $data[] = [
                            'unit_type' => 'studyprogram',
                            'unit_id' => $studyprogram->studyprogram_id,
                            'unit_name' => strtoupper($studyprogram->studyprogram_type).' '.$studyprogram->studyprogram_name,
                            'student_count' => $student_count,
                            'invoice_total_amount' => $invoice_amount,
                            'generated_invoice' => $generated_invoice.' / '.$student_count,
                        ];
                    }
                }
            }
        }

        return datatables($data)->toJSON();
    }

    public function detail(Request $request)
    {
        $validated = $request->validate([
            'scope' => 'required|in:all,faculty,studyprogram',
            'faculty_id' => 'required_if:scope,faculty',
            'studyprogram_id' => 'required_if:scope,studyprogram',
        ]);

        $selects = [new DefaultSelect(), new InvoiceData()];

        $filters = [new RegPassed(false)];
        if ($validated['scope'] == 'faculty') {
            $filters[] = new ByFaculty(intval($validated['faculty_id']));
        } elseif ($validated['scope'] == 'studyprogram') {
            $filters[] = new ByStudyprogram(intval($validated['studyprogram_id']));
        }

        $data = (new NewStudent())
            ->selects(...$selects)
            ->filters(...$filters)
            ->result();

        return datatables($data)->toJSON();
    }

    public function invoiceDetail($prr_id)
    {
        $data = DB::table('admission.payment_re_register')
            ->where('prr_id', '=', $prr_id)
            ->whereNull('deleted_at')
            ->first();

        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }

    public function invoiceComponentDetail($prr_id)
    {
        $data = DB::table('admission.payment_re_register_detail')
            ->where('prr_id', '=', $prr_id)
            ->whereNull('deleted_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }
}
