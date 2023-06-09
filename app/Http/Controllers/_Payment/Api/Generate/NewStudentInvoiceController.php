<?php

namespace App\Http\Controllers\_Payment\Api\Generate;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Masterdata\MsInstitution as Institution;
use App\Models\Studyprogram;
use App\Models\Faculty;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentDetail;
use App\Models\Payment\ComponentDetail;
use App\Models\PMB\Participant;
use App\Models\PMB\Register;
use App\Models\PMB\Setting;
use App\Exceptions\GenerateInvoiceException;
use App\Services\Queries\ReRegistration\ReRegistrationInvoice;
use App\Services\Queries\ReRegistration\GenerateInvoiceScopes\FacultyScope;
use App\Services\Queries\ReRegistration\GenerateInvoiceScopes\StudyprogramScope;
use App\Services\Queries\ReRegistration\GenerateInvoiceScopes\PathScope;
use App\Services\Queries\ReRegistration\GenerateInvoiceScopes\PeriodScope;
use App\Services\Queries\ReRegistration\GenerateInvoiceScopes\LectureTypeScope;
use App\Services\ReRegistInvoice\GenerateOne as GenerateOneInvoice;
use App\Services\ReRegistInvoice\GenerateByScope as GenerateInvoiceByScope;
use App\Services\ReRegistInvoice\GenerateAll as GenerateAllInvoice;
use App\Services\ReRegistInvoice\DeleteOne as DeleteOneInvoice;
use App\Services\ReRegistInvoice\DeleteByScope as DeleteInvoiceByScope;
use App\Services\ReRegistInvoice\DeleteAll as DeleteAllInvoice;

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

        $students = (new ReRegistrationInvoice())->query->get()->toArray();

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
                            $invoice_amount += intval($student->payment_reregist_invoice_amount);
                            if ($student->payment_reregist_invoice_status == 'Sudah Digenerate') $generated_invoice++;
                        }
                    }

                    $generated_status = '';
                    $generated_msg = '';
                    if ($generated_invoice == 0) {
                        $generated_status = 'not_yet';
                        $generated_msg = 'Belum Digenerate ('.$generated_invoice.'/'.$student_count.')';
                    } elseif ($generated_invoice != 0 && ($generated_invoice == $student_count)) {
                        $generated_status = 'done';
                        $generated_msg = 'Sudah Digenerate ('.$generated_invoice.'/'.$student_count.')';
                    } elseif ($generated_invoice != 0) {
                        $generated_status = 'partial';
                        $generated_msg = 'Sebagian Telah Digenerate ('.$generated_invoice.'/'.$student_count.')';
                    }

                    $data[] = [
                        'unit_type' => 'faculty',
                        'unit_id' => $faculty->faculty_id,
                        'unit_name' => $faculty->faculty_name,
                        'student_count' => $student_count,
                        'invoice_total_amount' => $invoice_amount,
                        'generated_status' => $generated_status,
                        'generated_msg' => $generated_msg,
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
                                $invoice_amount += intval($student->payment_reregist_invoice_amount);
                                if ($student->payment_reregist_invoice_status == 'Sudah Digenerate') $generated_invoice++;
                            }
                        }

                        $generated_status = '';
                        $generated_msg = '';
                        if ($generated_invoice == 0) {
                            $generated_status = 'not_yet';
                            $generated_msg = 'Belum Digenerate ('.$generated_invoice.'/'.$student_count.')';
                        } elseif ($generated_invoice != 0 && ($generated_invoice == $student_count)) {
                            $generated_status = 'done';
                            $generated_msg = 'Sudah Digenerate ('.$generated_invoice.'/'.$student_count.')';
                        } elseif ($generated_invoice != 0) {
                            $generated_status = 'partial';
                            $generated_msg = 'Sebagian Telah Digenerate ('.$generated_invoice.'/'.$student_count.')';
                        }

                        $data[] = [
                            'unit_type' => 'studyprogram',
                            'unit_id' => $studyprogram->studyprogram_id,
                            'unit_name' => strtoupper($studyprogram->studyprogram_type).' '.$studyprogram->studyprogram_name,
                            'student_count' => $student_count,
                            'invoice_total_amount' => $invoice_amount,
                            'generated_status' => $generated_status,
                            'generated_msg' => $generated_msg,
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

        $filters = [];
        if ($validated['scope'] == 'faculty') {
            $filters[] = ['faculty.faculty_id', '=', $validated['faculty_id']];
        } elseif ($validated['scope'] == 'studyprogram') {
            $filters[] = ['studyprogram.studyprogram_id', '=', $validated['studyprogram_id']];
        }

        $data = (new ReRegistrationInvoice())->query->where($filters)->get();

        return datatables($data)->toJSON();
    }

    public function invoiceDetail($prr_id)
    {
        $data = DB::table('finance.payment_re_register')
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
        $data = DB::table('finance.payment_re_register_detail')
            ->where('prr_id', '=', $prr_id)
            ->whereNull('deleted_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }

    /**
     * Generate invoice for one participant.
     */
    public function generateOne(Request $request)
    {
        $validated = $request->validate([
            'invoice_period_code' => 'required',
            'register_id' => 'required',
        ]);

        try {
            GenerateOneInvoice::generate($validated['invoice_period_code'], $validated['register_id']);
        } catch (GenerateInvoiceException $ex) {
            return response()->json([
                'success' => false,
                'message' => config('app.env') != 'production' ?
                    $ex->getMessage()
                    : 'Terjadi kesalahan pada sistem, silahkan hubungi administrator.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil generate tagihan mahasiswa.',
        ], 200);
    }

    /**
     * Delete invoice for one participant.
     */
    public function deleteOne(Request $request)
    {
        $validated = $request->validate([
            'payment_reregist_id' => 'required',
        ]);

        try {
            DeleteOneInvoice::delete($validated['payment_reregist_id']);
        } catch (DeleteInvoiceException $ex) {
            return response()->json([
                'success' => false,
                'message' => config('app.env') != 'production' ?
                    $ex->getMessage()
                    : 'Terjadi kesalahan pada sistem, silahkan hubungi administrator.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menghapus tagihan mahasiswa.',
        ], 200);
    }

    public function generateAll(Request $request)
    {
        $validated = $request->validate([
            'invoice_period_code' => 'required',
        ]);

        $generated_count = GenerateAllInvoice::generate($validated['invoice_period_code'], true);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil generate '.$generated_count.' tagihan mahasiswa.',
        ], 200);
    }

    public function deleteAll(Request $request)
    {
        $validated = $request->validate([
            'invoice_period_code' => 'required',
        ]);

        $deleted_count = DeleteAllInvoice::delete($validated['invoice_period_code'], true);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menghapus '.$deleted_count.' tagihan mahasiswa.',
        ], 200);
    }

    public function generateByScope(Request $request)
    {
        $validated = $request->validate([
            'invoice_period_code' => 'required',
            'scope' => 'required|in:faculty,studyprogram,path,period,lecture_type',
            'faculty_id' => 'required_if:scope,faculty|required_if:scope,studyprogram|required_if:scope,path|required_if:scope,period|required_if:scope,lecture_type',
            'studyprogram_id' => 'required_if:scope,studyprogram|required_if:scope,path|required_if:scope,period|required_if:scope,lecture_type',
            'path_id' => 'required_if:scope,path|required_if:scope,period|required_if:scope,lecture_type',
            'period_id' => 'required_if:scope,period|required_if:scope,lecture_type',
            'lecture_type_id' => 'required_if:scope,lecture_type',
        ]);

        $scope = null;
        $generated_count = 0;

        switch ($validated['scope']) {
            case 'faculty':
                $scope = new FacultyScope($validated['faculty_id']);
                $generated_count = GenerateInvoiceByScope::generate($validated['invoice_period_code'], $scope, true);
                break;

            case 'studyprogram':
                $scope = new StudyprogramScope($validated['faculty_id'], $validated['studyprogram_id']);
                $generated_count = GenerateInvoiceByScope::generate($validated['invoice_period_code'], $scope, true);
                break;

            case 'path':
                $scope = new PathScope($validated['faculty_id'], $validated['studyprogram_id'], $validated['path_id']);
                $generated_count = GenerateInvoiceByScope::generate($validated['invoice_period_code'], $scope, true);
                break;

            case 'period':
                $scope = new PeriodScope($validated['faculty_id'], $validated['studyprogram_id'], $validated['path_id'], $validated['period_id']);
                $generated_count = GenerateInvoiceByScope::generate($validated['invoice_period_code'], $scope, true);
                break;

            case 'lecture_type':
                $scope = new LectureTypeScope($validated['faculty_id'], $validated['studyprogram_id'], $validated['path_id'], $validated['period_id'], $validated['lecture_type_id']);
                $generated_count = GenerateInvoiceByScope::generate($validated['invoice_period_code'], $scope, true);
                break;

            default:
                break;
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil generate '.$generated_count.' tagihan mahasiswa.',
        ], 200);
    }

    public function deleteByScope(Request $request)
    {
        $validated = $request->validate([
            'invoice_period_code' => 'required',
            'scope' => 'required|in:faculty,studyprogram,path,period,lecture_type',
            'faculty_id' => 'required_if:scope,faculty|required_if:scope,studyprogram|required_if:scope,path|required_if:scope,period|required_if:scope,lecture_type',
            'studyprogram_id' => 'required_if:scope,studyprogram|required_if:scope,path|required_if:scope,period|required_if:scope,lecture_type',
            'path_id' => 'required_if:scope,path|required_if:scope,period|required_if:scope,lecture_type',
            'period_id' => 'required_if:scope,period|required_if:scope,lecture_type',
            'lecture_type_id' => 'required_if:scope,lecture_type',
        ]);

        $scope = null;
        $deleted_count = 0;

        switch ($validated['scope']) {
            case 'faculty':
                $scope = new FacultyScope($validated['faculty_id']);
                $deleted_count = DeleteInvoiceByScope::delete($validated['invoice_period_code'], $scope, true);
                break;

            case 'studyprogram':
                $scope = new StudyprogramScope($validated['faculty_id'], $validated['studyprogram_id']);
                $deleted_count = DeleteInvoiceByScope::delete($validated['invoice_period_code'], $scope, true);
                break;

            case 'path':
                $scope = new PathScope($validated['faculty_id'], $validated['studyprogram_id'], $validated['path_id']);
                $deleted_count = DeleteInvoiceByScope::delete($validated['invoice_period_code'], $scope, true);
                break;

            case 'period':
                $scope = new PeriodScope($validated['faculty_id'], $validated['studyprogram_id'], $validated['path_id'], $validated['period_id']);
                $deleted_count = DeleteInvoiceByScope::delete($validated['invoice_period_code'], $scope, true);
                break;

            case 'lecture_type':
                $scope = new LectureTypeScope($validated['faculty_id'], $validated['studyprogram_id'], $validated['path_id'], $validated['period_id'], $validated['lecture_type_id']);
                $deleted_count = DeleteInvoiceByScope::delete($validated['invoice_period_code'], $scope, true);
                break;

            default:
                break;
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil hapus '.$deleted_count.' tagihan mahasiswa.',
        ], 200);
    }


    public function generatePerStudyprogram(Request $request)
    {
        // Hierarchy:
        // 1. studyprogram
        // 2. school_year
        // 3. registration_path
        // 4. registration_period
        // 5. lecture_type

        // masterdata is student

        // Filter by:
        // - school year
        // - studyprogram
        $validated = $request->validate([
            'studyprogram_id' => 'required',
            'school_year_id' => 'required',
        ]);

        // get periods(@var periods) based on school_year_id
        $periods = Period::where('msy_id', '=', $validated['school_year_id'])->get();

        // get pmb.register records(@var regs) by school_year_id, period_id(s), studyprogram_id
        $registers = Register::where([
            'ms_school_year_id' => $validated['school_year_id'],
            'reg_major_pass' => $validated['studyprogram_id'],
        ]);

        // group participant by studyprogram
        // then generate per participant

        // from @var regs get participants(@var pars) related
        // condition where multiple register from same participant
        $participants = array();
        foreach ($registers as $register) {
            $participants[] = Participant::find($register->par_id);
        }

        // generate all invoice from @var pars
    }
}
