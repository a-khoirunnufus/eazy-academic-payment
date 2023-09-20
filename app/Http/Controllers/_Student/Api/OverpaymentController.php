<?php

namespace App\Http\Controllers\_Student\Api;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Payment\OverpaymentBalance;
use App\Models\Payment\OverpaymentTransaction;

class OverpaymentController extends Controller
{
    public function balance(Request $request)
    {
        $validated = $request->validate([
            'student_number' => 'required_if:student_type,student',
            'participant_id' => 'required_if:student_type,new_student',
            'student_type' => 'required|in:student,new_student',
        ]);

        if ($validated['student_type'] == 'student') {
            $balance = OverpaymentBalance::where('student_number', $validated['student_number'])->first();
        }

        if ($validated['student_type'] == 'new_student') {
            $balance = OverpaymentBalance::where('participant_id', $validated['participant_id'])->first();
        }

        return response()->json($balance, 200);
    }

    public function dtTransaction(Request $request)
    {
        $student_email = $request->input('student_email');
        $student_type = $request->input('student_type');

        if (!$student_email || !$student_type) {
            return datatables(OverpaymentTransaction::query())->toJson();
        }

        if ($student_type == 'student') {
            $query = DB::table('finance.overpayment_transaction as ovrt')
                ->leftJoin('finance.payment_re_register_bill as prrb', 'prrb.prrb_id', '=', 'ovrt.prrb_id')
                ->leftJoin('finance.payment_re_register as prr', 'prr.prr_id', '=', 'prrb.prr_id')
                ->leftJoin('hr.ms_student as std', 'std.student_number', '=', 'prr.student_number')
                ->select('ovrt.*')
                ->where('std.email', '=', $student_email);
        }

        if ($student_type == 'new_student') {
            $query = DB::table('finance.overpayment_transaction as ovrt')
                ->leftJoin('finance.payment_re_register_bill as prrb', 'prrb.prrb_id', '=', 'ovrt.prrb_id')
                ->leftJoin('finance.payment_re_register as prr', 'prr.prr_id', '=', 'prrb.prr_id')
                ->leftJoin('pmb.participant as par', 'par.par_id', '=', 'prr.par_id')
                ->leftJoin('pmb.users as usr', 'usr.user_id', '=', 'par.user_id')
                ->select('ovrt.*')
                ->where('usr.user_email', '=', $student_email);
        }

        $query = $query->orderBy('ovrt.ovrt_time', 'asc');

        return datatables($query)->toJson();
    }
}
