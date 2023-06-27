<?php

namespace App\Http\Controllers\_Student\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Authentication\StaticStudentUser;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentDetail;
use App\Models\Payment\PaymentBill;
use App\Models\Masterdata\MsPaymentMethod;
use App\Models\HR\MsStudent as Student;
use App\Models\PMB\Setting;
use App\Models\Payment\CreditSchemaPeriodPath;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * @var $default_user_email
     * @func getStaticUser()
     */
    use StaticStudentUser;

    public function index(Request $request) {
        /**
         * NOTE:
         * - invoice number still using prr_id, change later.
         */

        $validated = $request->validate([
            'student_type' => 'required|in:new_student,student',
            'participant_id' => 'required_if:student_type,new_student',
            'student_id' => 'required_if:student_type,student',
            'status' => 'required|in:paid,unpaid',
        ]);

        $invoices = DB::table('finance.payment_re_register as prr')
            ->leftJoin('finance.payment_re_register_detail as prrd', 'prrd.prr_id', '=', 'prr.prr_id')
            ->leftJoin('masterdata.ms_school_year as msy', 'msy.msy_code', '=', DB::raw("prr.prr_school_year::VARCHAR"))
            ->leftJoin('pmb.register as reg', 'reg.reg_id', '=', 'prr.reg_id')
            ->leftJoin('masterdata.ms_studyprogram as std', 'std.studyprogram_id', '=', 'reg.reg_major_pass')
            ->leftJoin('masterdata.ms_lecture_type as mlt', 'mlt.mlt_id', '=', DB::raw("reg.reg_major_lecture_type_pass::INTEGER"));

        if ($validated['student_type'] == 'new_student') {
            $invoices = $invoices->where('prr.par_id', '=', $validated['participant_id']);
        }

        if ($validated['student_type'] == 'student') {
            $student = Student::find($validated['student_id']);
            $invoices = $invoices->where('prr.student_number', '=', $student->student_number);
        }

        $invoices = $invoices->where([
                ['prr.prr_status', '=', $validated['status'] == 'unpaid' ? 'belum lunas' : 'lunas'],
                ['prr.deleted_at', 'is', DB::raw("NULL")],
            ])
            ->select(
                DB::raw("
                    CASE
                        WHEN prr.reg_id is not null THEN 'new_student'
                        ELSE 'student'
                    END as invoice_student_type
                "),
                'prr.prr_id as prr_id',
                'msy.msy_year as invoice_school_year_year',
                'msy.msy_semester as invoice_school_year_semester',
                DB::raw("'INV/' || prr.prr_id as invoice_number"),
                'prr.created_at as invoice_issued_date',
                DB::raw("
                    CASE
                        WHEN prrd.type = 'component' THEN
                            '[' || STRING_AGG('{\"name\":\"'::TEXT || prrd.prrd_component || '\",\"nominal\":'::TEXT || prrd.prrd_amount || '}'::TEXT, ',') || ']'
                        ELSE
                            '[]'::TEXT
                    END as invoice_detail
                "),
                DB::raw("
                    CASE
                        WHEN prrd.type = 'beasiswa' THEN
                            '[' || STRING_AGG('{\"name\":\"'::TEXT || prrd.prrd_component || '\",\"nominal\":'::TEXT || prrd.prrd_amount || '}'::TEXT, ',') || ']'
                        ELSE
                            '[]'::TEXT
                    END as scholarship_detail
                "),
                DB::raw("
                    CASE
                        WHEN prrd.type = 'potongan' THEN
                            '[' || STRING_AGG('{\"name\":\"'::TEXT || prrd.prrd_component || '\",\"nominal\":'::TEXT || prrd.prrd_amount || '}'::TEXT, ',') || ']'
                        ELSE
                            '[]'::TEXT
                    END as discount_detail
                "),
                DB::raw("
                    CASE
                        WHEN prrd.type = 'denda' THEN
                            '[' || STRING_AGG('{\"name\":\"'::TEXT || prrd.prrd_component || '\",\"nominal\":'::TEXT || prrd.prrd_amount || '}'::TEXT, ',') || ']'
                        ELSE
                            '[]'::TEXT
                    END as penalty_detail
                "),
                'prr.prr_total as total_amount',
                'prr.prr_method as payment_method',
                'prr.prr_status as payment_status',
                DB::raw("
                    CASE
                        WHEN prr.reg_id is not null THEN
                            'Tagihan daftar ulang Program Studi ' || UPPER(std.studyprogram_type) || ' ' || std.studyprogram_name || ' ' || mlt.mlt_name
                        ELSE
                            NULL
                    END as notes
                ")
            )
            ->distinct()
            ->groupBy('prr.prr_id', 'msy.msy_year', 'msy.msy_semester', 'prrd.type', 'prr.prr_total', 'std.studyprogram_name', 'std.studyprogram_type', 'mlt.mlt_name')
            ->orderBy('prr.prr_id', 'asc')
            ->get()
            ->toArray();

        $datatable = datatables($invoices);

        return $datatable->toJSON();
    }

    public function detail($prr_id)
    {
        $student_type = Payment::find($prr_id)->reg_id ? 'new_student' : 'student';

        if($student_type == 'new_student') {
            $payment = Payment::with([
                    'paymentDetail',
                    'paymentBill',
                    'paymentMethod',
                    'year',
                    'register',
                    'register.studyprogram',
                    'register.lectureType',
                ])
                ->where('prr_id', '=', $prr_id)
                ->first();
        }
        elseif ($student_type == 'student') {
            $payment = Payment::with([
                    'paymentDetail',
                    'paymentBill',
                    'paymentMethod',
                    'year',
                    'student',
                    'student.studyprogram',
                    'student.lectureType'
                ])
                ->where('prr_id', '=', $prr_id)
                ->first();
        }

        return response()->json($payment, 200);
    }

    public function selectMethod(Request $request)
    {
        $validated = $request->validate([
            'prr_id' => 'required',
            'method' => 'required|in:bca,mandiri,bni',
        ]);

        try {
            DB::beginTransaction();

            $payment = Payment::find($validated['prr_id']);
            $payment->prr_method = $validated['method'];
            $payment->save();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasl memilih metode pembayaran',
        ], 200);
    }

    public function creditSchemas($prr_id, Request $request)
    {
        $validated = $request->validate([
            'student_type' => 'required|in:new_student,student',
        ]);

        $credit_schemas = DB::table('finance.payment_re_register as prr');

        if ($validated['student_type'] == 'new_student') {
            $credit_schemas = $credit_schemas
                ->rightJoin('pmb.register as reg', 'reg.reg_id', '=', 'prr.reg_id')
                ->rightJoin('masterdata.period_path as pp', function($join) {
                    $join->on('pp.period_id', '=', 'reg.ms_period_id');
                    $join->on('pp.path_id', '=', 'reg.ms_path_id');
                })
                ->rightJoin('masterdata.ms_major_lecture_type as mma_lt', function($join) {
                    $join->on('mma_lt.mma_id', '=', 'reg.reg_major_pass');
                    $join->on('mma_lt.mlt_id', '=', DB::raw("reg.reg_major_lecture_type_pass::INTEGER"));
                });
        }
        elseif ($validated['student_type'] == 'student') {
            $credit_schemas = $credit_schemas
                ->rightJoin('hr.ms_student as student', 'student.student_number', '=', 'prr.student_number')
                ->rightJoin('masterdata.period_path as pp', function($join) {
                    $join->on('pp.period_id', '=', 'student.period_id');
                    $join->on('pp.path_id', '=', 'student.path_id');
                })
                ->rightJoin('masterdata.ms_major_lecture_type as mma_lt', function($join) {
                    $join->on('mma_lt.mma_id', '=', 'student.studyprogram_id');
                    $join->on('mma_lt.mlt_id', '=', 'student.mlt_id');
                });
        }

        $credit_schemas = $credit_schemas->rightJoin('masterdata.period_path_major as ppm', function($join) {
                $join->on('ppm.ppd_id', '=', 'pp.ppd_id');
                $join->on('ppm.mma_lt_id', '=', 'mma_lt.mma_lt_id');
            })
            ->rightJoin('finance.credit_schema_periodpath as cspp', 'cspp.ppm_id', '=', 'ppm.ppm_id')
            ->rightJoin('finance.credit_schema as cs', 'cs.cs_id', '=', 'cspp.cs_id')
            ->select('cs.*')
            ->distinct()
            ->where('prr.prr_id', '=', $prr_id)
            ->whereNull('cspp.deleted_at')
            ->orderBy('cs.cs_id', 'asc')
            ->get();

        return response()->json($credit_schemas, 200);
    }

    public function getPpm($prr_id)
    {
        $student_type = Payment::find($prr_id)->reg_id ? 'new_student' : 'student';

        if($student_type == 'new_student') {
            $ppm = DB::table('finance.payment_re_register as prr')
                ->leftJoin('pmb.register as reg', 'reg.reg_id', '=', 'prr.reg_id')
                ->leftJoin('masterdata.period_path as ppd', function($join) {
                    $join->on('ppd.period_id', '=', 'reg.ms_period_id');
                    $join->on('ppd.path_id', '=', 'reg.ms_path_id');
                })
                ->leftJoin('masterdata.ms_major_lecture_type as mma_lt', function($join) {
                    $join->on('mma_lt.mma_id', '=', 'reg.reg_major_pass');
                    $join->on('mma_lt.mlt_id', '=', DB::raw("reg.reg_major_lecture_type_pass::INTEGER"));
                })
                ->leftJoin('masterdata.period_path_major as ppm', function($join) {
                    $join->on('ppm.ppd_id', '=', 'ppd.ppd_id');
                    $join->on('ppm.mma_lt_id', '=', 'mma_lt.mma_lt_id');
                })
                ->select('ppm.ppm_id')
                ->where('prr.prr_id', '=', $prr_id)
                ->first();
        }
        elseif ($student_type == 'student') {
            $ppm = DB::table('finance.payment_re_register as prr')
                ->leftJoin('hr.ms_student as std', 'std.student_number', '=', 'prr.student_number')
                ->rightJoin('masterdata.period_path as ppd', function($join) {
                    $join->on('ppd.period_id', '=', 'std.period_id');
                    $join->on('ppd.path_id', '=', 'std.path_id');
                })
                ->leftJoin('masterdata.ms_major_lecture_type as mma_lt', function($join) {
                    $join->on('mma_lt.mma_id', '=', 'std.studyprogram_id');
                    $join->on('mma_lt.mlt_id', '=', 'std.mlt_id');
                })
                ->leftJoin('masterdata.period_path_major as ppm', function($join) {
                    $join->on('ppm.ppd_id', '=', 'ppd.ppd_id');
                    $join->on('ppm.mma_lt_id', '=', 'mma_lt.mma_lt_id');
                })
                ->select('ppm.ppm_id')
                ->where('prr.prr_id', '=', $prr_id)
                ->first();
        }

        return response()->json($ppm, 200);
    }

    public function paymentOptionPreview($cs_id, Request $request)
    {
        $validated = $request->validate([
            'ppm_id' => 'required',
        ]);

        $cspp = CreditSchemaPeriodPath::with(['creditSchema.creditSchemaDetail.creditSchemaDeadline'])
            ->where('cs_id', '=', $cs_id)
            ->where('ppm_id', '=', $validated['ppm_id'])
            ->first();

        return response()->json($cspp, 200);
    }

    public function getBills($prr_id)
    {
        $bills = PaymentBill::where('prr_id', $prr_id)->orderBy('prrb_order', 'asc')->get();

        return response()->json($bills, 200);
    }

    public function createBill($prr_id, Request $request)
    {
        $validated = $request->validate([
            'cs_id' => 'required',
        ]);

        $payment = Payment::with(['paymentDetail'])->where('prr_id', '=', $prr_id)->first();
        $invoice_total_amount = 0;
        foreach($payment->paymentDetail as $prrd) {
            if ($prrd->is_plus == 1) {
                $invoice_total_amount += $prrd->prrd_amount;
            } else {
                $invoice_total_amount -= $prrd->prrd_amount;
            }
        }

        $payment_method = MsPaymentMethod::where('mpm_key', $payment->prr_method)->first();

        $student_type = Payment::find($prr_id)->reg_id ? 'new_student' : 'student';

        if($student_type == 'new_student') {
            $ppm = DB::table('finance.payment_re_register as prr')
                ->leftJoin('pmb.register as reg', 'reg.reg_id', '=', 'prr.reg_id')
                ->leftJoin('masterdata.period_path as ppd', function($join) {
                    $join->on('ppd.period_id', '=', 'reg.ms_period_id');
                    $join->on('ppd.path_id', '=', 'reg.ms_path_id');
                })
                ->leftJoin('masterdata.ms_major_lecture_type as mma_lt', function($join) {
                    $join->on('mma_lt.mma_id', '=', 'reg.reg_major_pass');
                    $join->on('mma_lt.mlt_id', '=', DB::raw("reg.reg_major_lecture_type_pass::INTEGER"));
                })
                ->leftJoin('masterdata.period_path_major as ppm', function($join) {
                    $join->on('ppm.ppd_id', '=', 'ppd.ppd_id');
                    $join->on('ppm.mma_lt_id', '=', 'mma_lt.mma_lt_id');
                })
                ->select('ppm.ppm_id')
                ->where('prr.prr_id', '=', $prr_id)
                ->first();
        }
        elseif ($student_type == 'student') {
            $ppm = DB::table('finance.payment_re_register as prr')
                ->leftJoin('hr.ms_student as std', 'std.student_number', '=', 'prr.student_number')
                ->rightJoin('masterdata.period_path as ppd', function($join) {
                    $join->on('ppd.period_id', '=', 'std.period_id');
                    $join->on('ppd.path_id', '=', 'std.path_id');
                })
                ->leftJoin('masterdata.ms_major_lecture_type as mma_lt', function($join) {
                    $join->on('mma_lt.mma_id', '=', 'std.studyprogram_id');
                    $join->on('mma_lt.mlt_id', '=', 'std.mlt_id');
                })
                ->leftJoin('masterdata.period_path_major as ppm', function($join) {
                    $join->on('ppm.ppd_id', '=', 'ppd.ppd_id');
                    $join->on('ppm.mma_lt_id', '=', 'mma_lt.mma_lt_id');
                })
                ->select('ppm.ppm_id')
                ->where('prr.prr_id', '=', $prr_id)
                ->first();
        }

        $cspp = CreditSchemaPeriodPath::with(['creditSchema.creditSchemaDetail.creditSchemaDeadline'])
            ->where('cs_id', '=', $validated['cs_id'])
            ->where('ppm_id', '=', $ppm->ppm_id)
            ->first();

        $credit_schema_detail = $cspp->creditSchema->creditSchemaDetail;

        $invoice_total_amount_plus_admin_fee = 0;

        try {
            DB::beginTransaction();

            PaymentBill::where('prr_id', '=', $prr_id)->delete();

            foreach($credit_schema_detail as $csd) {
                $temp_amount = $invoice_total_amount * (intval($csd->csd_percentage) / 100);
                $invoice_total_amount_plus_admin_fee += $temp_amount + $payment_method->mpm_fee;
                PaymentBill::create([
                    'prr_id' => $prr_id,
                    'prrb_status' => 'belum lunas',
                    'prrb_invoice_num' => $payment_method->mpm_account_number,
                    'prrb_expired_date' => $csd->creditSchemaDeadline->cse_deadline,
                    'prrb_amount' => $temp_amount,
                    'prrb_admin_cost' => $payment_method->mpm_fee,
                    'prrb_order' => $csd->csd_order,
                ]);
            }

            Payment::where('prr_id', '=', $prr_id)->update([
                'prr_total' => $invoice_total_amount_plus_admin_fee,
                'prr_paid_net' => $invoice_total_amount,
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();

            return response()->json([
                'success' => true,
                'message' => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil memilih opsi pembayaran.',
        ], 200);
    }

    public function resetPayment($prr_id)
    {
        try {
            DB::beginTransaction();

            // delete payment bill
            $bills = PaymentBill::where('prr_id', '=', $prr_id)->get();
            foreach ($bills as $bill) {
                if($bill->prrb_manual_evidence && !config('app.disable_cloud_storage')) {
                    Storage::cloud()->delete($bill->prrb_manual_evidence);
                }
                PaymentBill::destroy($bill->prrb_id);
            }

            $payment_detail = PaymentDetail::where('prr_id', '=', $prr_id)->get();
            $invoice_total_amount = 0;
            foreach($payment_detail as $prrd) {
                if ($prrd->is_plus == 1) {
                    $invoice_total_amount += $prrd->prrd_amount;
                } else {
                    $invoice_total_amount -= $prrd->prrd_amount;
                }
            }

            $payment = Payment::find($prr_id);
            $payment->prr_method = null;
            $payment->prr_total = $invoice_total_amount;
            $payment->prr_paid_net = $invoice_total_amount;
            $payment->save();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mereset pembayaran.'
        ], 200);
    }

    public function getEvidence($prr_id, $prrb_id)
    {
        $bill = PaymentBill::find($prrb_id);
        $data = [
            'account_owner_name' => $bill->prrb_manual_name,
            'account_number' => $bill->prrb_manual_norek,
            'file_evidence' => $bill->prrb_manual_evidence,
            'approval_status' => $bill->prrb_manual_status,
        ];
        return response()->json($data, 200);
    }

    public function uploadEvidence($prr_id, $prrb_id, Request $request)
    {
        $validated = $request->validate([
            'account_owner_name' => 'required',
            'account_number' => 'required',
            'file_evidence' => 'nullable|file|mimes:jpg,png,pdf|max:1000',
        ]);

        try {
            if (config('app.disable_cloud_storage')) {
                $upload_success = '/';
            } else {
                $upload_success = Storage::cloud()->putFile('bukti_bayar/re_register/'.$prr_id, $validated['file_evidence']);
            }

            if (!$upload_success) {
                throw new \Exception('Failed uploading file!');
            }

            DB::beginTransaction();

            $bill = PaymentBill::find($prrb_id);
            $bill->prrb_paid_date = Carbon::now()->toDateString();
            $bill->prrb_manual_name = $validated['account_owner_name'];
            $bill->prrb_manual_norek = $validated['account_number'];
            $bill->prrb_manual_evidence = $upload_success;
            $bill->prrb_manual_status = 'waiting';
            $bill->save();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil upload bukti pembayaran',
        ], 200);
    }
}
