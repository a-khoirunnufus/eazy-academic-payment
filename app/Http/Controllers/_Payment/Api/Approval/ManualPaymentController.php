<?php

namespace App\Http\Controllers\_Payment\Api\Approval;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentBill;
use App\Models\Payment\Studyprogram;
use App\Models\Payment\PaymentManualApproval;
use App\Models\Payment\PaymentTransaction;
use App\Models\Payment\OverpaymentBalance;
use App\Models\Payment\OverpaymentTransaction;
use Carbon\Carbon;
use DB;

class ManualPaymentController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->input('custom_filters');

        // remove item with null value or #ALL value
        $filters = array_filter($filters, function ($item) {
            return !is_null($item) && $item != '#ALL';
        });

        $data = PaymentManualApproval::query();

        if(isset($filters['status'])){
            $data = $data->where('pma_approval_status', 'like', '%'. $filters['status'] .'%');
        }
        if(isset($filters['student_type'])){
            $data = $data->where('pma_student_type', 'like', '%'. $filters['student_type'] .'%');
        }
        if(isset($filters['path'])){
            $data = $data->where('pma_student_reg_path', 'like', '%'. $filters['path'] .'%');
        }
        if(isset($filters['period'])){
            $data = $data->where('pma_student_reg_period', 'like', '%'. $filters['period'] .'%');
        }
        if(isset($filters['prodi'])){
            $data = $data->where('pma_student_studyprogram', 'like', '%'. $filters['prodi'] .'%');
        }
        $data = $data->get();

        return datatables($data)->toJSON();
    }

    public function processApproval($pma_id, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected',
            'notes' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            $approval = PaymentManualApproval::find($pma_id);
            $approval->pma_approval_status = $validated['status'];
            $approval->pma_notes = $validated['notes'];
            $approval->pma_processed_at = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s O');
            $approval->save();

            $bill = PaymentBill::with('paymentMethod')->where('prrb_id', $approval->prrb_id)->first();
            $bill_master = $bill->payment;
            $student_type = $bill_master->student_number ? 'student' : 'new_student';

            if ($validated['status'] == 'accepted') {
                // create transaction record
                $payment_transaction = new PaymentTransaction();
                $payment_transaction->prrb_id = $bill->prrb_id;
                $payment_transaction->prrt_payment_method = $bill->prrb_payment_method;
                if ($bill->paymentMethod->mpm_type == 'bank_transfer_manual') {
                    $payment_transaction->prrt_sender_account_number = $approval->pma_sender_account_number;
                    $payment_transaction->prrt_receiver_account_number = $approval->pma_receiver_account_number;
                }
                elseif ($bill->paymentMethod->mpm_type == 'bank_transfer_va') {
                    $payment_transaction->prrt_va_number = $bill->prrb_va_number;
                }
                elseif ($bill->paymentMethod->mpm_type == 'bank_transfer_bill_payment') {
                    $payment_transaction->prrt_biller_code = $bill->prrb_biller_code;
                    $payment_transaction->prrt_bill_key = $bill->prrb_bill_key;
                }
                $payment_transaction->prrt_amount = $approval->pma_amount;
                $payment_transaction->prrt_time = $approval->pma_payment_time;
                $payment_transaction->save();

                $total_paid = PaymentTransaction::where('prrb_id', '=', $approval->prrb_id)->sum('prrt_amount');
                $total_bill = $bill->prrb_amount + $bill->prrb_admin_cost;
                $total_payment = $approval->pma_amount;
                $overpayment_nominal = 0;

                $is_overpayment = ($total_paid + $total_payment) > $total_bill;
                if ($is_overpayment) {
                    $total_unpaid = $total_bill - $total_paid;
                    if ($total_unpaid < 0) $total_unpaid = 0;
                    $overpayment_nominal = $total_payment - $total_unpaid;
                }

                if ($overpayment_nominal > 0) {
                    OverpaymentTransaction::create([
                        'ovrt_cash_in' => $overpayment_nominal,
                        'prrb_id' => $bill->prrb_id,
                        'ovrt_remark' => 'Kelebihan Pembayaran Tagihan',
                        'ovrt_time' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s O'),
                    ]);

                    $op_balance = OverpaymentBalance::where(
                        $student_type == 'student' ? ['student_number' => $bill_master->student_number]
                            : ['participant_id' => $bill_master->par_id]
                    )->first();
                    if (!$op_balance) $op_balance = new OverpaymentBalance();
                    $op_balance->student_type = $student_type;
                    if ($student_type == 'student') $op_balance->student_number = $bill_master->student_number;
                    if ($student_type == 'new_student') $op_balance->participant_id = $bill_master->par_id;
                    if ($op_balance->ovrb_balance) {
                        $op_balance->ovrb_balance += $overpayment_nominal;
                    } else {
                        $op_balance->ovrb_balance = $overpayment_nominal;
                    }
                    $op_balance->save();
                }

                // check if bill fully paid
                if (
                    $bill->prrb_status == 'belum lunas'
                    && $total_paid >= $total_bill
                ) {
                    $bill->prrb_paid_date = $approval->pma_payment_time;
                    $bill->prrb_status = 'lunas';
                    $bill->save();
                }

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
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil memproses approval pembayaran',
        ], 200);
    }

    public function getProdi($faculty){
        $data = Studyprogram::where('faculty_id', '=', $faculty);

        return $data->get();
    }
}
