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
            ->filters(new RegPassed(true))
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
                                $invoice_amount += intval($student->invoice_amount);
                                if ($student->invoice_status == 'Sudah Digenerate') $generated_invoice++;
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

        $selects = [new DefaultSelect(), new InvoiceData()];

        $filters = [new RegPassed(true)];
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
            'period_id' => 'required',
            'path_id' => 'required',
            'studyprogram_id' => 'required',
            'lecture_type_id' => 'required',
            'participant_id' => 'required',
        ]);

        // get participant (pmb.participant)
        $participant = Participant::find($validated['participant_id']);

        if($participant == null) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa tidak ditemukan.'
            ], 404);
        }

        // get invoice component related
        // filter by finance.ms_component.is_new_student
        $invoice_components = ComponentDetail::with('component')
            ->whereHas('component', function ($q) {
                $q->where('msc_is_new_student', '=', 1);
            })
            ->where([
                ['period_id', '=', $validated['period_id']],
                ['path_id', '=', $validated['path_id']],
                ['mma_id', '=', $validated['studyprogram_id']],
                ['mlt_id', '=', $validated['lecture_type_id']],
            ])
            ->get();

        $eazy_service_cost = Setting::where('setting_key', 'biaya_service_eazy')->first()->setting_value;
        // fix this later
        $invoice_period = 22231;
        // fix this later
        $school_year_id = 1;


        // total invoice
        $invoice_total = 0;
        foreach($invoice_components as $item){
            $invoice_total = $invoice_total + $item->cd_fee;
        }

        // partner's net income
        $partner_net_income = $invoice_total - intval($eazy_service_cost);

        $register = Register::where([
            ['par_id', '=', $participant->par_id],
            ['ms_period_id', '=', $validated['period_id']],
            ['ms_path_id', '=', $validated['path_id']],
            ['ms_school_year_id', '=', $school_year_id],
            ['reg_major_pass', '=', $validated['studyprogram_id']],
            ['reg_major_lecture_type_pass', '=', $validated['lecture_type_id']],
            ['reg_status_pass', '=', 1]
        ])->first();

        if ($register == null) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem, silahkan hubungi administrator.',
            ], 500);
        }

        try {
            DB::beginTransaction();

            // insert payment_re_register record
            $payment = Payment::create([
                'reg_id' => $register->reg_id,
                'prr_status' => 'belum lunas',
                'prr_total' => $invoice_total,
                'prr_paid_net' => $partner_net_income,
                'prr_school_year' => $invoice_period,
                'par_id' => $participant->par_id,
            ]);

            // insert payment_re_register_detail records
            foreach($invoice_components as $item){
                PaymentDetail::create([
                    'prr_id' => $payment->prr_id,
                    'prrd_component' => $item->component->msc_name,
                    'prrd_amount' => $item->cd_fee,
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => config('app.env') != 'production' ?
                    $th->getMessage()
                    : 'Terjadi kesalahan pada sistem, silahkan hubungi administrator.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil generate tagihan mahasiswa dengan nama '.$participant->par_fullname.'.',
        ], 200);
    }

    /**
     * Delete invoice for one participant.
     */
    public function deleteOne(Request $request)
    {
        $validated = $request->validate([
            'payment_re_register_id' => 'required',
        ]);

        try {
            DB::beginTransaction();

            PaymentDetail::where('prr_id', '=', $validated['payment_re_register_id'])->delete();
            Payment::destroy($validated['payment_re_register_id']);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => config('app.env') != 'production' ?
                    $th->getMessage()
                    : 'Terjadi kesalahan pada sistem, silahkan hubungi administrator.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menghapus tagihan mahasiswa.',
        ], 200);
    }
}
