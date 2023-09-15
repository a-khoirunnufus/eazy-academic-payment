<?php

namespace App\Http\Controllers\_Student\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Traits\Authentication\StaticStudentUser;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentDetail;
use App\Models\Payment\PaymentBill;
use App\Models\Payment\MasterPaymentMethod;
use App\Models\Payment\PaymentManualApproval;
use App\Models\Payment\CreditSchemaPeriodPath;
use App\Models\Payment\PaymentTransaction;
use App\Models\PMB\Setting;
use App\Models\PMB\Participant as NewStudent;
use App\Models\PMB\Participant;
use App\Models\PMB\Register;
use App\Models\Masterdata\MsPaymentMethod; // old
use App\Models\HR\MsStudent as Student;
use App\Services\Payment\PaymentService;
use App\Exceptions\MidtransException;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * @var $default_user_email
     * @func getStaticUser()
     */
    use StaticStudentUser;

    public function index(Request $request)
    {
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
                            CONCAT('Tagihan daftar ulang Program Studi ', UPPER(std.studyprogram_type), ' ', std.studyprogram_name, ' ', mlt.mlt_name)
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
                    'register.participant',
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

    public function getBills($prr_id)
    {
        $bills = PaymentBill::where('prr_id', $prr_id)
            ->orderBy('prrb_order', 'asc')
            ->get();
        return response()->json($bills, 200);
    }

    public function billDetail($prr_id, $prrb_id)
    {
        $bill = PaymentBill::find($prrb_id);
        return response()->json($bill, 200);
    }

    public function selectPaymentMethod($prr_id, $prrb_id, Request $request)
    {
        $validated = $request->validate([
            'payment_method' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $payment = Payment::with(['paymentDetail'])->where('prr_id', '=', $prr_id)->first();
            $payment_method = MasterPaymentMethod::where('mpm_key', $validated['payment_method'])->first();
            $bill = PaymentBill::find($prrb_id);

            // check if previous payment already paid
            if ($bill->prrb_order > 1) {
                $prev_pay_paid = PaymentBill::where([
                        ['prr_id', '=', $prr_id],
                        ['prrb_order', '=', $bill->prrb_order-1],
                        ['prrb_status', '=', 'lunas'],
                    ])->exists();
                if (!$prev_pay_paid) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda belum membayar tagihan sebelumnya!',
                    ], 409);
                }
            }

            // set payment method
            $bill->prrb_payment_method = $payment_method->mpm_key;

            // bank transfer manual
            if ($payment_method->mpm_type == 'bank_transfer_manual') {
                $bill->prrb_account_number = $payment_method->mpm_account_number;
                $bill->prrb_admin_cost = $payment_method->mpm_fee;
            }
            // bank transfer virtual account / bill payment
            elseif (in_array($payment_method->mpm_type, ['bank_transfer_va', 'bank_transfer_bill_payment'])) {

                $order_id = (string) Str::uuid();

                $student_type = Payment::find($prr_id)->reg_id ? 'new_student' : 'student';
                $student = null;
                if ($student_type == 'new_student') {
                    $student = NewStudent::with('user')->where('par_id', $payment->par_id)->first();
                }
                elseif ($student_type == 'student') {
                    $student = Student::with('user')->where('student_number', $payment->student_number)->first();
                }

                // charge transaction
                $charge_result = (new PaymentService())->chargeTransaction([
                    "order_id" => $order_id,
                    "payment_type" => $payment_method->mpm_key,
                    "amount_total" => $bill->prrb_amount + $payment_method->mpm_fee,
                    "name" => $student_type == 'new_student' ? $student->par_fullname : $student->user->user_fullname,
                    "email" => $student->user->user_email,
                    "phone" => $student_type == 'new_student' ? $student->par_phone : $student->phone_number,
                    "item_details" => [
                        0 => [
                            "name" => "Cicilan ke-".$bill->prrb_order,
                            "price" => $bill->prrb_amount + $payment_method->mpm_fee,
                        ]
                    ]
                ]);

                if ($charge_result->status == 'error') {
                    throw new \Exception($charge_result->message);
                }

                $bill->prrb_admin_cost = $payment_method->mpm_fee;
                $bill->prrb_order_id = $order_id;
                $bill->prrb_midtrans_transaction_exp = $charge_result->payload->transaction_exp;
                $bill->prrb_midtrans_transaction_id = $charge_result->payload->transaction_id;

                if ($payment_method->mpm_type == 'bank_transfer_va') {
                    $bill->prrb_va_number = $charge_result->payload->va_number;
                }
                elseif ($payment_method->mpm_type == 'bank_transfer_bill_payment') {
                    $bill->prrb_mandiri_biller_code = $charge_result->payload->biller_code;
                    $bill->prrb_mandiri_bill_key = $charge_result->payload->bill_key;
                }
            }

            // save bill
            $bill->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil memilih metode pembayaran',
            ], 200);
        }
        catch (\Throwable $th) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function resetPaymentMethod($prr_id, $prrb_id)
    {
        try {
            DB::beginTransaction();

            $bill = PaymentBill::find($prrb_id);
            $payment_method = MasterPaymentMethod::where('mpm_key', $bill->prrb_payment_method)->first();

            if (!$bill) {
                throw new \Exception('Bill not found!');
            }

            if ($bill->prrb_status == 'lunas') {
                throw new \Exception('Bill already paid!');
            }

            if (in_array($payment_method->mpm_type, ['bank_transfer_va', 'bank_transfer_bill_payment'])) {
                // cancel midtrans transaction
                $cancel_result = (new PaymentService())->cancelTransaction($bill->prrb_order_id);

                if ($cancel_result->status == 'error') {
                    // try cancel transaction again later by put it into queue
                }
            }

            $bill->prrb_admin_cost = null;
            $bill->prrb_payment_method = null;
            $bill->prrb_order_id = null;
            $bill->prrb_va_number = null;
            $bill->prrb_mandiri_bill_key = null;
            $bill->prrb_mandiri_biller_code = null;
            $bill->prrb_account_number = null;
            $bill->prrb_midtrans_transaction_id = null;
            $bill->prrb_midtrans_transaction_exp = null;

            $bill->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mereset metode pembayaran',
            ], 200);
        }
        catch (\Throwable $th) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
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

    public function paymentOptionPreview($prr_id, Request $request)
    {
        $validated = $request->validate([
            'ppm_id' => 'required',
            'cs_id' => 'required',
        ]);

        $cspp = CreditSchemaPeriodPath::with(['creditSchema.creditSchemaDetail.creditSchemaDeadline'])
            ->where('cs_id', '=', $validated['cs_id'])
            ->where('ppm_id', '=', $validated['ppm_id'])
            ->first();

        return response()->json($cspp, 200);
    }

    public function selectPaymentOption($prr_id, Request $request)
    {
        $validated = $request->validate([
            'cs_id' => 'required',
        ]);

        $student_type = Payment::find($prr_id)->reg_id ? 'new_student' : 'student';

        $period_path_major = $this->_getPeriodPathMajor($prr_id, $student_type);

        $credit_schema_detail = $this->_getCreditSchemaDetail($validated['cs_id'], $period_path_major->ppm_id);

        $payment = Payment::with(['paymentDetail'])->where('prr_id', '=', $prr_id)->first();

        $invoice_total_amount = $this->_calculateInvoiceTotalAmount($payment->paymentDetail);

        try {
            DB::beginTransaction();

            // delete all bill belong this payment_re_register.prr_id
            PaymentBill::where('prr_id', '=', $prr_id)->delete();

            // create bill foreach based on item in credit schema
            foreach ($credit_schema_detail as $csd) {
                $temp_amount = (int) ceil($invoice_total_amount * (intval($csd->csd_percentage) / 100));

                PaymentBill::create([
                    'prr_id' => $prr_id,
                    'prrb_status' => 'belum lunas',
                    'prrb_due_date' => $csd->creditSchemaDeadline->cse_deadline,
                    'prrb_amount' => $temp_amount,
                    'prrb_order' => $csd->csd_order,
                ]);
            }

            Payment::where('prr_id', '=', $prr_id)->update([
                'prr_total' => $invoice_total_amount,
                'prr_paid_net' => $invoice_total_amount,
            ]);

            DB::commit();
        }
        catch (\Throwable $th) {
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

    /**
     * START
     * func selectPaymentOption() utility functions
     */

    private function _calculateInvoiceTotalAmount($prr_details)
    {
        $invoice_total_amount = 0;
        foreach($prr_details as $prrd) {
            if ($prrd->is_plus == 1) {
                $invoice_total_amount += $prrd->prrd_amount;
            } else {
                $invoice_total_amount -= $prrd->prrd_amount;
            }
        }
        return $invoice_total_amount;
    }

    private function _getPeriodPathMajor($prr_id, $student_type)
    {
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

        return $ppm;
    }

    private function _getCreditSchemaDetail($cs_id, $ppm_id)
    {
        $cspp = CreditSchemaPeriodPath::with(['creditSchema.creditSchemaDetail.creditSchemaDeadline'])
            ->where('cs_id', '=', $cs_id)
            ->where('ppm_id', '=', $ppm_id)
            ->first();

        return $cspp->creditSchema->creditSchemaDetail;
    }

    /**
     * END
     * func selectPaymentOption() utility functions
     */

    public function resetPayment($prr_id)
    {
        try {
            DB::beginTransaction();

            // cancel if there are bills that already paid
            $has_paid_bill = PaymentBill::where('prr_id', '=', $prr_id)
                ->where('prrb_status', 'lunas')
                ->exists();
            if ($has_paid_bill) {
                throw new \Exception('Sudah ada tagihan yang terbayar!');
            }

            // delete payment bill
            $bills = PaymentBill::where('prr_id', '=', $prr_id)->get();
            foreach ($bills as $bill) {
                // if($bill->prrb_manual_evidence && !config('app.disable_cloud_storage')) {
                //     Storage::disk('minio')->delete($bill->prrb_manual_evidence);
                // }
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

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mereset pembayaran.'
            ], 200);
        }
        catch (\Throwable $th) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
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
        dd($request->all());

        $validated = $request->validate([
            'account_owner_name' => 'required',
            'account_number' => 'required',
            'file_evidence' => 'nullable|file|mimes:jpg,png,pdf|max:1000',
        ]);

        try {
            if (config('app.disable_cloud_storage')) {
                $upload_success = '/';
            } else {
                $upload_success = Storage::disk('minio')->putFile('payment/bukti_bayar/re_register/'.$prr_id, $validated['file_evidence']);
            }

            if (!$upload_success) {
                throw new \Exception('Failed uploading file!');
            }

            DB::beginTransaction();

            $bill = PaymentBill::find($prrb_id);
            $bill->prrb_manual_name = $validated['account_owner_name'];
            $bill->prrb_manual_norek = $validated['account_number'];
            $bill->prrb_manual_evidence = $upload_success;
            $bill->prrb_manual_status = 'waiting';
            $bill->prrb_manual_note = null;
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

    public function getApproval($prr_id, $prrb_id)
    {
        $data = PaymentManualApproval::where('prrb_id', $prrb_id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($data, 200);
    }

    public function detailApproval($prr_id, $prrb_id, $pma_id)
    {
        $data = PaymentManualApproval::find($pma_id);

        return response()->json($data, 200);
    }

    public function storeApproval($prr_id, $prrb_id, Request $request)
    {
        $validated = $request->validate([
            'student_type' => 'required',
            'sender_account_name' => 'required',
            'sender_account_number' => 'required',
            'sender_bank' => 'required',
            'amount' => 'required',
            'receiver_account_number' => 'required',
            'receiver_account_name' => 'required',
            'receiver_bank' => 'required',
            'payment_time' => 'required',
            'evidence' => 'required|file|mimes:jpg,png,pdf|max:1000',
        ]);

        try {
            if (config('app.disable_cloud_storage')) {
                $upload_success = '/';
            } else {
                $upload_success = Storage::disk('minio')->putFile('payment/bukti_bayar/re_register/'.$prr_id, $validated['evidence']);
            }

            if (!$upload_success) {
                throw new \Exception('Failed uploading file!');
            }

            $student = null;
            if ($validated['student_type'] == 'new_student') {
                $reg_id = Payment::find($prr_id)->reg_id;
                $par_id = Register::find($reg_id)->par_id;
                $student = NewStudent::with([
                        'register' => function($query) use($reg_id) {
                            $query->where('reg_id', $reg_id);
                        },
                        'register.studyprogram',
                        'register.lectureType',
                        'register.year',
                        'register.period',
                        'register.path',
                    ])
                    ->where('par_id', '=', $par_id)
                    ->first();
            }
            elseif ($validated['student_type'] == 'student') {
                $student_number = Payment::find($prr_id)->student_number;
                $student = Student::with(['studyprogram', 'lectureType', 'year', 'period', 'path'])
                    ->where('student_number', '=', $student_number)
                    ->first();
            }

            DB::beginTransaction();

            $approval = new PaymentManualApproval();
            $approval->prrb_id = $prrb_id;

            if ($validated['student_type'] == 'student') {
                $approval->pma_student_name = $student->fullname;
                $approval->pma_student_id = $student->student_id;
                $approval->pma_student_type = 'Mahasiswa Lama';
                $approval->pma_student_studyprogram = strtoupper($student->studyprogram->studyprogram_type).' '.$student->studyprogram->studyprogram_name;
                $approval->pma_student_lecturetype = $student->lectureType->mlt_name;
                $approval->pma_student_reg_year = $student->year->msy_year;
                $approval->pma_student_reg_period = $student->period->period_name;
                $approval->pma_student_reg_path = $student->path->path_name;
            }
            elseif ($validated['student_type'] == 'new_student') {
                $approval->pma_student_name = $student->par_fullname;
                $approval->pma_student_id = null;
                $approval->pma_student_type = 'Mahasiswa Baru';
                $approval->pma_student_studyprogram = strtoupper($student->register->studyprogram->studyprogram_type).' '.$student->register->studyprogram->studyprogram_name;
                $approval->pma_student_lecturetype = $student->register->lectureType->mlt_name;
                $approval->pma_student_reg_year = $student->register->year->msy_year;
                $approval->pma_student_reg_period = $student->register->period->period_name;
                $approval->pma_student_reg_path = $student->register->path->path_name;
            }

            $approval->pma_sender_account_name = $validated['sender_account_name'];
            $approval->pma_sender_account_number = $validated['sender_account_number'];
            $approval->pma_sender_bank = $validated['sender_bank'];
            $approval->pma_amount = $validated['amount'];
            $approval->pma_receiver_account_number = $validated['receiver_account_number'];
            $approval->pma_receiver_account_name = $validated['receiver_account_name'];
            $approval->pma_receiver_bank = $validated['receiver_bank'];
            $approval->pma_payment_time = $validated['payment_time'];
            $approval->pma_evidence = $upload_success;
            $approval->pma_approval_status = 'waiting';
            $approval->save();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();

            throw $th;
            // return response()->json([
            //     'success' => false,
            //     'message' => $th->getMessage(),
            // ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil upload bukti pembayaran',
        ], 200);
    }

    public function getTransaction($prr_id, $prrb_id)
    {
        $data = PaymentTransaction::where('prrb_id', $prrb_id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($data, 200);
    }

    public function detailTransaction($prr_id, $prrb_id, $prrt_id)
    {

    }
}
