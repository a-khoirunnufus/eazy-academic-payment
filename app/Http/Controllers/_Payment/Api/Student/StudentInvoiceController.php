<?php

namespace App\Http\Controllers\_Payment\Api\Student;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Traits\Authentication\StaticStudentUser;
use App\Models\Payment\CreditSchemaPeriodPath;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentDetail;
use App\Models\Payment\PaymentBill;
use App\Models\Payment\PaymentMethod;
use App\Models\Payment\PaymentManualApproval;
use App\Models\Payment\PaymentTransaction;
use App\Models\Payment\Student;
use App\Models\Payment\StudentBalanceTrans;
use App\Models\Payment\StudentBalanceSpent;
use App\Models\PMB\Setting;
use App\Models\PMB\Participant as NewStudent;
use App\Models\PMB\Participant;
use App\Models\PMB\Register;
use App\Services\Payment\PaymentApi;
use App\Exceptions\MidtransException;
use Carbon\Carbon;

use App\Traits\Models\LoadDataRelationByRequest;
use App\Traits\Models\LoadDataAppendByRequest;
use App\Traits\Models\QueryFilterExtendByRequest;

class StudentInvoiceController extends Controller
{
    use LoadDataRelationByRequest, LoadDataAppendByRequest, QueryFilterExtendByRequest;

    /**
     * Bill Master
     */

    public function index(Request $request)
    {
        $validated = $request->validate([
            'student_number' => 'required_if:student_type,student',
            'status' => 'required|in:paid,unpaid',
        ]);

        $query = Payment::query();
        $query = $this->applyRelation($query, $request, [
            'paymentDetail',
            'paymentBill',
            'register',
            'student',
            'student.studyprogram',
            'student.lectureType',
            'year',
            'dispensation',
        ]);
        $query = $this->applyFilterWithOperator($query, $request, ['prr_school_year']);

        $query = $query->where('student_number', $validated['student_number']);
        if ($validated['status'] == 'unpaid') $query = $query->whereIn('prr_status', ['belum lunas', 'kredit']);
        if ($validated['status'] == 'paid') $query = $query->where('prr_status', 'lunas');

        $datatable = datatables($query);

        foreach ($request->input('withAppend') as $attribute) {
            $datatable->addColumn($attribute, function($invoice) use($attribute) {
                $value = $invoice->{$attribute};
                $new_value = $value;
                if ($value instanceof \Illuminate\Database\Eloquent\Collection) {
                    $new_value = [];
                    if ($value->isNotEmpty()) $new_value = [...$value->toArray()];
                }
                return $new_value;
            });
        }

        return $datatable->toJSON();
    }

    public function detail(Request $request, $prr_id)
    {
        $query = Payment::query();
        $query = $this->applyRelation($query, $request, [
            'paymentDetail',
            'paymentBill',
            'register',
            'student',
            'student.studyprogram',
            'student.lectureType',
            'year',
            'dispensation',
        ]);

        $master_bill = $query->where('prr_id', '=', $prr_id)->first();

        $master_bill = $this->applyAppend($master_bill, $request, [
            'computed_total_bill',
            'computed_payment_status',
            'computed_has_paid_bill',
        ]);

        return response()->json($master_bill);
    }

    /**
     * Bill
     */

    public function getBills(Request $request, $prr_id)
    {
        $query = PaymentBill::query();
        $query = $this->applyRelation($query, $request, [
            'payment',
            'paymentMethod',
            'paymentManualApproval',
            'paymentTransaction',
        ]);

        $bills = $query->where('prr_id', $prr_id)
            ->orderBy('prrb_order', 'asc')
            ->get();

        $bills = $this->applyAppend($bills, $request, [
            'computed_dispensation_applied',
            'computed_due_date',
            'computed_is_fully_paid',
            'computed_nominal_paid',
            'computed_nominal_paid_nett',
            'computed_nominal_paid_gross',
            'computed_payment_status',
            'computed_paid_date',
        ]);

        return response()->json($bills);
    }

    public function billDetail(Request $request, $prr_id, $prrb_id)
    {
        // TODO:
        // - throw 404 exception if bill not exist
        // - exception will return 404 response

        $query = PaymentBill::query();
        $query = $this->applyRelation($query, $request, [
            'payment',
            'paymentMethod',
            'paymentManualApproval',
            'paymentTransaction',
        ]);

        $bill = $query->where('prrb_id', $prrb_id)->first();

        $bill = $this->applyAppend($bill, $request, [
            'computed_dispensation_applied',
            'computed_due_date',
            'computed_is_fully_paid',
            'computed_nominal_paid',
            'computed_nominal_paid_nett',
            'computed_nominal_paid_gross',
            'computed_payment_status',
            'computed_paid_date',
        ]);

        return response()->json($bill);
    }

    /**
     * Others
     */

    public function selectPaymentMethod($prr_id, $prrb_id, Request $request)
    {
        $validated = $request->validate([
            'payment_method' => 'required',
            'use_student_balance' => 'nullable',
            'student_balance_spend' => 'required_with:use_student_balance',
        ]);

        try {
            DB::beginTransaction();

            $payment = Payment::with(['paymentDetail'])->where('prr_id', '=', $prr_id)->first();
            $payment_method = PaymentMethod::where('mpm_key', $validated['payment_method'])->first();
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

                $student = Student::find($payment->student_number);
                $student_type = 'student';

                // charge transaction
                $charge_result = (new PaymentApi())->chargeTransaction([
                    "order_id" => $order_id,
                    "payment_type" => $payment_method->mpm_key,
                    "amount_total" => ($bill->prrb_amount + $payment_method->mpm_fee) - intval($validated['student_balance_spend']),
                    "name" => $student->fullname,
                    "email" => $student->email,
                    "phone" => $student->phone_number,
                    "item_details" => [
                        0 => [
                            "name" => "Cicilan ke-".$bill->prrb_order,
                            "price" => ($bill->prrb_amount + $payment_method->mpm_fee) - intval($validated['student_balance_spend']),
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

            // Use student balance
            if ($validated['use_student_balance'] == '1') {

                $opening_balance = StudentBalanceTrans::where('student_number', $payment->student_number)
                    ->orderBy('sbt_time', 'desc')
                    ->first()
                    ?->sbt_closing_balance?? 0;

                $closing_balance = $opening_balance - intval($validated['student_balance_spend']);

                StudentBalanceTrans::create([
                    'student_number' => $payment->student_number,
                    'sbt_opening_balance' => $opening_balance,
                    'sbt_amount' => intval($validated['student_balance_spend']) * -1,
                    'sbtt_name' => 'pay_bill',
                    'sbtt_associate_id' => $bill->prrb_id,
                    'sbt_closing_balance' => $closing_balance,
                    'sbt_time' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s O'),
                ]);

                StudentBalanceSpent::create([
                    'student_number' => $payment->student_number,
                    'sbs_amount' => intval($validated['student_balance_spend']),
                    'sbs_remark' => 'Pembayaran Tagihan',
                    'prrb_id' => $bill->prrb_id,
                    'sbs_status' => 'reserved',
                    'sbs_time' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s O'),
                ]);

                // check is already full paid using student balance
                if (intval($validated['student_balance_spend']) >= ($bill->prrb_amout + $bill->prrb_admin_cost)) {
                    $bill->prrb_status = 'lunas';
                    $bill->save();
                }

                /**
                 * Set master bill(payment_re_register) to lunas if all of this bill child
                 * status are lunas.
                 */
                $unpaid_bills = PaymentBill::where('prr_id', $bill->prr_id)
                    ->where('prrb_status', 'belum lunas')
                    ->get();
                if ($unpaid_bills->count() == 0) {
                    $payment = Payment::find($bill->prr_id);
                    $payment->prr_status = 'lunas';
                    $payment->save();
                }
            }

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
        $force_reset = false;

        try {
            DB::beginTransaction();

            $bill_master = Payment::find($prr_id);
            $bill = PaymentBill::find($prrb_id);
            $payment_method = PaymentMethod::where('mpm_key', $bill->prrb_payment_method)->first();

            if (!$bill) {
                throw new \Exception('Bill not found!');
            }

            if (
                $bill->prrb_status == 'lunas'
                && !$force_reset
            ) {
                throw new \Exception('Bill already paid!');
            }

            // Cancel midtrans transaction
            if ($payment_method->mpm_type == 'bank_transfer_va') {
                $cancel_result = (new PaymentApi())->cancelTransaction($bill->prrb_order_id);
                if ($cancel_result->status != 'success') {
                    // try cancel transaction again later by put it into queue
                }
            }

            // Reset bill columns
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

            // Restore student_balance_spent if exists
            $condition = ['prrb_id' => $bill->prrb_id, 'sbs_status' => 'reserved'];
            if (StudentBalanceSpent::where($condition)->exists()) {

                $balance_spends = StudentBalanceSpent::where($condition)->get();
                StudentBalanceSpent::where($condition)->update(['sbs_status' => 'canceled']);

                $opening_balance = StudentBalanceTrans::where('student_number', $bill_master->student_number)
                    ->orderBy('sbt_time', 'desc')
                    ->first()
                    ?->sbt_closing_balance ?? 0;

                $closing_balance = $opening_balance;

                foreach ($balance_spends as $spend) {
                    $closing_balance += $spend->sbs_amount;

                    StudentBalanceTrans::create([
                        'student_number' => $bill_master->student_number,
                        'sbt_opening_balance' => $opening_balance,
                        'sbt_amount' => $spend->sbs_amount,
                        'sbtt_name' => 'cancel_pay_bill',
                        'sbtt_associate_id' => $spend->sbs_id,
                        'sbt_closing_balance' => $closing_balance,
                        'sbt_time' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s O'),
                    ]);

                    $opening_balance = $closing_balance;
                }

            }

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
        $force_reset = true;

        try {
            DB::beginTransaction();

            if (!$force_reset) {
                // cancel if there are bills that already paid
                $has_paid_bill = PaymentBill::where('prr_id', '=', $prr_id)
                    ->where('prrb_status', 'lunas')
                    ->exists();
                if ($has_paid_bill) {
                    throw new \Exception('Sudah ada tagihan yang terbayar!');
                }
            }

            // Delete Payment Bill
            $bills = PaymentBill::where('prr_id', '=', $prr_id)->get();
            foreach ($bills as $bill) {
                // if ($bill->prrb_manual_evidence && !config('app.disable_cloud_storage')) {
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
        $data = PaymentTransaction::with('paymentMethod')
            ->where('prrb_id', $prrb_id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->each->setAppends([
                'computed_initial_amount',
                'computed_overpayment',
            ]);

        return response()->json($data, 200);
    }

    public function detailTransaction($prr_id, $prrb_id, $prrt_id) {}

    public function getOverpayment($prr_id, $prrb_id)
    {
        $transaction_ids = DB::table('finance.payment_re_register_transaction')
            ->where('prrb_id', $prrb_id)
            ->select('prrt_id')
            ->get()
            ->toArray();

        $transaction_ids = array_map(function($item) {
            return $item->prrt_id;
        }, $transaction_ids);

        $data = StudentBalanceTrans::with('type')
            ->where('sbtt_name', 'overpaid_bill')
            ->whereIn('sbtt_associate_id', $transaction_ids)
            ->orderBy('sbt_time', 'asc')
            ->get();

        return response()->json($data);
    }

    public function getStudentBalanceUsed($prr_id, $prrb_id)
    {
        $amount = StudentBalanceSpent::where('prrb_id', $prrb_id)
            ->where(function ($query) {
                $query->where('sbs_status', 'reserved')
                    ->orWhere('sbs_status', 'used');
            })
            ->sum('sbs_amount');

        return response()->json(['amount' => $amount]);
    }

    public function regenerateVA($prr_id, $prrb_id)
    {
        try {
            DB::beginTransaction();

            $bill = PaymentBill::find($prrb_id);
            $payment_method = $bill->paymentMethod;
            $student = Student::find($bill->payment->student_number);

            $status_result = (new PaymentApi())->transactionStatus($bill->prrb_order_id);
            if ($status_result->payload?->transaction_status == 'pending') {
                $cancel_result = (new PaymentApi())->cancelTransaction($bill->prrb_order_id);
                if ($cancel_result->status != "success") {
                    // when cancel transaction action is failed
                }
            }

            $order_id = (string) Str::uuid();
            $invoice_gross_amount = $bill->prrb_amount;
            $admin_cost = $payment_method->mpm_fee;
            $student_balance_spend = $bill->computed_student_balance_spend_total;

            // charge transaction
            $charge_result = (new PaymentApi())->chargeTransaction([
                "order_id" => $order_id,
                "payment_type" => $bill->prrb_payment_method,
                "amount_total" => ($invoice_gross_amount + $admin_cost) - $student_balance_spend,
                "name" => $student->fullname,
                "email" => $student->email,
                "phone" => $student->phone_number,
                "item_details" => [
                    0 => [
                        "name" => "Cicilan ke-".$bill->prrb_order,
                        "price" => ($invoice_gross_amount + $admin_cost) - $student_balance_spend,
                    ]
                ]
            ]);

            if ($charge_result->status == 'error') {
                throw new \Exception($charge_result->message);
            }

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

            $bill->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil membuat nomor virtual account baru.",
            ], 200);

        } catch (\Throwable $th) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
