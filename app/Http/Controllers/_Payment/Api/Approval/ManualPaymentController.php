<?php

namespace App\Http\Controllers\_Payment\Api\Approval;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentBill;
use App\Models\Payment\Studyprogram;
use App\Models\Payment\PaymentManualApproval;
use App\Models\Payment\PaymentTransaction;
use App\Models\Payment\StudentBalanceTrans;
use App\Models\Payment\StudentBalanceSpent;
use App\Models\Payment\Student;
use App\Traits\Payment\General as PaymentGeneral;
use App\Enums\Payment\BillStatus;
use App\Enums\Payment\BalanceTransType;
use App\Enums\Payment\BalanceSpentStatus;
use App\Enums\Payment\PaymentMethodType;
use Carbon\Carbon;
use DB;

class ManualPaymentController extends Controller
{
    use PaymentGeneral;

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
            $approval->pma_processed_at = $this->getCurrentDateTime();
            $approval->save();

            $bill = PaymentBill::with('paymentMethod')->where('prrb_id', $approval->prrb_id)->first();
            $bill_master = $bill->payment;
            $student = Student::find($bill_master->student_number);
            $student_type = 'student';

            if ($validated['status'] == 'accepted') {

                $total_bill = $bill->prrb_amount + $bill->prrb_admin_cost;

                $total_discount = StudentBalanceSpent::where('prrb_id', $approval->prrb_id)
                    ->where(function ($query) {
                        $query->where('sbs_status', BalanceSpentStatus::Reserved);
                            // ->orWhere('sbs_status', BalanceSpentStatus::Used);
                    })
                    ->sum('sbs_amount');

                $total_paid = PaymentTransaction::where('prrb_id', '=', $approval->prrb_id)->sum('prrt_amount');
                $total_paid += $total_discount;

                $total_payment = $approval->pma_amount;
                $overpayment_nominal = 0;
                $amount_for_transaction = $total_payment;

                $is_overpayment = ($total_paid + $total_payment) > $total_bill;
                if ($is_overpayment) {
                    $total_unpaid = $total_bill - $total_paid;
                    if ($total_unpaid < 0) $total_unpaid = 0;
                    $overpayment_nominal = $total_payment - $total_unpaid;
                }

                if ($overpayment_nominal > 0) {
                    $amount_for_transaction -= $overpayment_nominal;
                }

                // Create Transaction Record
                $payment_transaction = new PaymentTransaction();
                $payment_transaction->prrb_id = $bill->prrb_id;
                $payment_transaction->prrt_payment_method = $bill->prrb_payment_method;
                if ($bill->paymentMethod->mpm_type == PaymentMethodType::BankTransferManual->value) {
                    $payment_transaction->prrt_sender_account_number = $approval->pma_sender_account_number;
                    $payment_transaction->prrt_receiver_account_number = $approval->pma_receiver_account_number;
                }
                elseif ($bill->paymentMethod->mpm_type == PaymentMethodType::BankTransferVA->value) {
                    $payment_transaction->prrt_va_number = $bill->prrb_va_number;
                }
                elseif ($bill->paymentMethod->mpm_type == PaymentMethodType::BankTransferBillPayment->value) {
                    $payment_transaction->prrt_biller_code = $bill->prrb_biller_code;
                    $payment_transaction->prrt_bill_key = $bill->prrb_bill_key;
                }
                $payment_transaction->prrt_amount = $amount_for_transaction;
                $payment_transaction->prrt_admin_cost = 0;
                $payment_transaction->prrt_time = $approval->pma_payment_time;
                $payment_transaction->save();

                // Store overpayment balance
                if ($overpayment_nominal > 0) {

                    $opening_balance = StudentBalanceTrans::where('student_number', $student->student_number)
                        ->orderBy('sbt_time', 'desc')
                        ->first()
                        ?->sbt_closing_balance?? 0;

                    $closing_balance = $opening_balance + $overpayment_nominal;

                    StudentBalanceTrans::create([
                        'student_number' => $student->student_number,
                        'sbt_opening_balance' => $opening_balance,
                        'sbt_amount' => $overpayment_nominal,
                        'sbtt_name' => 'overpaid_bill',
                        'sbtt_associate_id' => $payment_transaction->prrt_id,
                        'sbt_closing_balance' => $closing_balance,
                        'sbt_time' => $this->getCurrentDateTime(),
                    ]);
                }

                // Update student_balance_spent status to used
                StudentBalanceSpent::where([
                        'prrb_id' => $bill->prrb_id,
                        'sbs_status' => BalanceSpentStatus::Reserved,
                    ])
                    ->update(['sbs_status' => BalanceSpentStatus::Used]);

                /**
                 * Set bill to lunas if sum on transaction nominal belong this bill is equal
                 * or greater than bill nominal.
                 */
                $total_discount = StudentBalanceSpent::where('prrb_id', $approval->prrb_id)
                    ->where(function ($query) {
                        $query->where('sbs_status', BalanceSpentStatus::Used);
                    })
                    ->sum('sbs_amount');
                $total_paid = PaymentTransaction::where('prrb_id', '=', $approval->prrb_id)->sum('prrt_amount');
                $total_paid += $total_discount;
                if (
                    $bill->prrb_status == BillStatus::NotPaidOff->value
                    && $total_paid >= $total_bill
                ) {
                    $bill->prrb_paid_date = $approval->pma_payment_time;
                    $bill->prrb_status = BillStatus::PaidOff->value;
                    $bill->save();
                }

                /**
                 * Set master bill(payment_re_register) to lunas if all of this bill child
                 * status are lunas.
                 */
                $unpaid_bills = PaymentBill::where('prr_id', $bill->prr_id)
                    ->where('prrb_status', BillStatus::NotPaidOff)
                    ->get();
                if ($unpaid_bills->count() == 0) {
                    $payment = Payment::find($bill->prr_id);
                    $payment->prr_status = BillStatus::PaidOff;
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
