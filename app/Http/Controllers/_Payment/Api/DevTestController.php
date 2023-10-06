<?php

namespace App\Http\Controllers\_Payment\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\PaymentBill;
use DB;

class DevTestController extends Controller
{
    public function regenerateVA(Request $request)
    {
        $validated = $request->validate([
            'bill_id' => 'required|exists:pgsql.finance.payment_re_register_bill,prrb_id',
            'new_va_exp_time' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $bill = PaymentBill::find($validated['bill_id']);
            $bill->prrb_midtrans_transaction_exp = $validated['new_va_exp_time'];
            $bill->save();

            session()->flash('dev-test.success', 'Berhasil merubah tanggal expire!');

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            session()->flash('dev-test.success', 'Gagal merubah tanggal expire!');
        }

        return redirect()->back();
    }
}
