<?php

namespace App\Http\Controllers\_Payment\Api\Approval;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentBill;
use DB;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $data = DB::table('finance.payment_re_register as prr')
            ->leftJoin('finance.payment_re_register_bill as prrb', 'prrb.prr_id', '=', 'prr.prr_id')
            ->leftJoin('pmb.register as reg', 'reg.reg_id', '=', 'prr.reg_id')
            ->leftJoin('pmb.participant as par', 'par.par_id', '=', 'reg.par_id')
            ->leftJoin('hr.ms_student as student', 'student.student_number', 'prr.student_number')
            ->leftJoin('masterdata.ms_payment_method as mpm', 'mpm.mpm_key', '=', 'prr.prr_method')
            ->whereNull('prr.deleted_at')
            ->whereNull('prrb.deleted_at')
            ->whereNotNull('prrb.prrb_manual_name')
            ->select(
                DB::raw("
                    CASE
                        WHEN prr.reg_id is not null THEN par.par_fullname
                        ELSE student.fullname
                    END as student_name
                "),
                DB::raw("
                    CASE
                        WHEN prr.reg_id is not null THEN 'new_student'
                        ELSE 'student'
                    END as student_type
                "),
                DB::raw("
                    CASE
                        WHEN prr.reg_id is not null THEN par.par_number
                        ELSE NULL
                    END AS par_number
                "),
                DB::raw("
                    CASE
                        WHEN prr.student_number is not null THEN student.student_id
                        ELSE NULL
                    END AS student_id
                "),
                'prr.prr_id',
                'prrb.prrb_id',
                DB::raw("prrb.prrb_amount + prrb.prrb_admin_cost as bill_total"),
                'prrb.prrb_manual_name as sender_name',
                'mpm.mpm_name as bank_name',
                'prrb.prrb_manual_norek as sender_account_number',
                'prrb.prrb_manual_evidence as file_payment_evidence',
                'prrb.prrb_manual_status as approval_status',
                'prrb.prrb_manual_note as approval_notes'
            )
            ->distinct()
            ->get();

        return datatables($data)->toJSON();
    }

    public function processApproval($prrb_id, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected',
            'notes' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            $bill = PaymentBill::find($prrb_id);
            $bill->prrb_status = $validated['status'] == 'accepted' ? 'lunas' : 'belum lunas';
            $bill->prrb_manual_status = $validated['status'];
            $bill->prrb_manual_note = $validated['notes'];
            $bill->save();

            if (PaymentBill::where('prr_id', $bill->prr_id)->get()->count() == 1) {
                $payment = Payment::find($bill->prr_id);
                $payment->prr_status = $validated['status'] == 'accepted' ? 'lunas' : 'belum lunas';
                $payment->save();
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
}
