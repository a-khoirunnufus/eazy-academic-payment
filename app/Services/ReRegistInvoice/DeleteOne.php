<?php

namespace App\Services\ReRegistInvoice;

use Illuminate\Support\Facades\DB;
use App\Exceptions\DeleteInvoiceException;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentDetail;
use App\Models\Payment\PaymentBill;

class DeleteOne {
    public static function delete(int $payment_reregist_id)
    {
        try {
            DB::beginTransaction();

            PaymentDetail::where('prr_id', '=', $payment_reregist_id)->delete();
            PaymentBill::where('prr_id', '=', $payment_reregist_id)->delete();
            Payment::destroy($payment_reregist_id);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            throw new DeleteInvoiceException($th->getMessage(), 500);
        }
    }
}
