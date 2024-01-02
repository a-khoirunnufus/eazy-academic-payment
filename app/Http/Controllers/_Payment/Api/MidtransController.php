<?php

namespace App\Http\Controllers\_Payment\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Payment\PaymentBill;

class MidtransController extends Controller
{
    public function notificationHandler(Request $request)
    {
        try {
            DB::beginTransaction();

            $signature = $request->order_id . $request->status_code . $request->gross_amount . config('midtrans-ap.MIDTRANS_AP_SERVER_KEY');
            $signature_key = hash("sha512", $signature);

            if ($signature_key != $request->signature_key) {
                // signature invalid
                throw new \Exception('Signature key not match!');
            }

            if (
                $request->status_code != "200"
                || $request->transaction_status != 'settlement'
                || $request->fraud_status != 'accept'
            ) {
                throw new \Exception('Payload identified as failed by midtrans terms!');
            }

            $bill = PaymentBill::where('prrb_pg_data', 'ilike', '%"order_id":"'.$request->order_id.'"%')->first();

            if (!$bill) {
                throw new \Exception('Bill not found!');
            }

            PaymentBill::where('prrb_id', $bill->prrb_id)
                ->update([
                    'prrb_status' => 'lunas',
                    'prrb_paid_date' => $request->settlement_time.'.000 +0700',
                ]);

            $not_yet_paid_off = PaymentBill::where('prr_id', $bill->prr_id)
                ->where('prrb_status', 'belum lunas')
                ->exists();

            if (!$not_yet_paid_off) {
                Payment::where('prr_id', $bill->prr_id)
                    ->update([
                        'prr_status' => 'lunas'
                    ]);
            }

            DB::commit();
            return response()->json(['status' => 'success'], 200);
        }
        catch (\Exception $ex) {
            DB::rollback();
            $this->_createLog('notification webhook', 'client_error', $ex->getMessage(), null);
            return response()->json(['status' => 'error'], 200);
        }

        $this->_createLog('notification webhook', $request->status_code, $request->status_message, $request->getContent());
    }

    private function _createLog($action, $status_code, $status_message, $payload)
    {
        return DB::table('finance.log_service_midtrans')->insert([
            'timestamp' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s O'),
            'action' => $action,
            'status_code' => $status_code,
            'status_message' => $status_message,
            'payload' => $payload,
        ]);
    }
}
