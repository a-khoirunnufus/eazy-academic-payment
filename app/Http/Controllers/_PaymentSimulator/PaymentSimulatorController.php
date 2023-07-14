<?php

namespace App\Http\Controllers\_PaymentSimulator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Payment\PaymentBill;
use Carbon\Carbon;

class PaymentSimulatorController extends Controller
{
    public function showBca()
    {
        return view('pages._payment-simulator.bca');
    }

    public function showMandiri()
    {
        return view('pages._payment-simulator.mandiri');
    }

    public function showBni()
    {
        return view('pages._payment-simulator.bni');
    }

    public function pay(Request $request)
    {
        $validated = $request->validate([
            'payment_method' => 'required',
            'va_number' => 'required_if:payment_method,bca_va|required_if:payment_method,bni_va',
            'bill_key' => 'required_if:payment_method,mandiri_bill_payment',
            'biller_code' => 'required_if:payment_method,mandiri_bill_payment',
            'payment_nominal' => 'required',
        ]);

        $bill = null;

        if (in_array($validated['payment_method'], ['bca_va', 'bni_va'])) {
            $bill = PaymentBill::where('prrb_va_number', $validated['va_number'])->first();
        }
        elseif ($validated['payment_method'] == 'mandiri_bill_payment') {
            $bill = PaymentBill::where([
                ['prrb_mandiri_bill_key', '=', $validated['bill_key']],
                ['prrb_mandiri_biller_code', '=', $validated['biller_code']],
            ])->first();
        }

        if (!$bill) {
            return response()->json(['error' => 'Bill not found!'], 404);
        }

        $result = null;
        $signature = $bill->prrb_order_id . '200' . strval($bill->prrb_amount+$bill->prrb_admin_cost).'.00' . config('midtrans.MIDTRANS_SERVER_KEY');
        $signature = hash("sha512", $signature);

        if ($validated['payment_method'] == 'bca_va') {
            $result = Http::post(config('payment-service.payment_service_url').'/midtrans-notification-callback', [
                    "va_numbers" => [
                        [
                            "va_number" => $bill->prrb_va_number,
                            "bank" => "bca"
                        ]
                    ],
                    "transaction_time" => "2023-01-01 00:00:00",
                    "transaction_status" => "settlement",
                    "transaction_id" => $bill->prrb_midtrans_transaction_id,
                    "status_message" => "midtrans payment notification",
                    "status_code" => "200",
                    "signature_key" => $signature,
                    "settlement_time" =>  Carbon::now('Asia/Jakarta')->toDateTimeString(),
                    "payment_type" => "bank_transfer",
                    "payment_amounts" => [
                        [
                            "paid_at" => Carbon::now('Asia/Jakarta')->toDateTimeString(),
                            "amount" => strval($bill->prrb_amount+$bill->prrb_admin_cost).'.00',
                        ]
                    ],
                    "order_id" => $bill->prrb_order_id,
                    "merchant_id" => "G141532850",
                    "gross_amount" => strval($bill->prrb_amount+$bill->prrb_admin_cost).'.00',
                    "fraud_status" => "accept",
                    "currency" => "IDR"
                ])
                ->object();
        }
        elseif ($validated['payment_method'] == 'bni_va') {
            $result = Http::post(config('payment-service.payment_service_url').'/midtrans-notification-callback', [
                    "va_numbers" => [
                        [
                            "va_number" => $bill->prrb_va_number,
                            "bank" => "bni"
                        ]
                    ],
                    "transaction_time" => "2023-01-01 00:00:00",
                    "transaction_status" => "settlement",
                    "transaction_id" => $bill->prrb_midtrans_transaction_id,
                    "status_message" => "midtrans payment notification",
                    "status_code" => "200",
                    "signature_key" => $signature,
                    "settlement_time" => Carbon::now('Asia/Jakarta')->toDateTimeString(),
                    "payment_type" => "bank_transfer",
                    "payment_amounts" => [
                        [
                            "paid_at" => Carbon::now('Asia/Jakarta')->toDateTimeString(),
                            "amount" => strval($bill->prrb_amount+$bill->prrb_admin_cost).'.00',
                        ]
                    ],
                    "order_id" => $bill->prrb_order_id,
                    "merchant_id" => "G141532850",
                    "gross_amount" => strval($bill->prrb_amount+$bill->prrb_admin_cost).'.00',
                    "fraud_status" => "accept",
                    "currency" => "IDR"
                ])
                ->object();
        }
        elseif ($validated['payment_method'] == 'mandiri_bill_payment') {
            $result = Http::post(config('payment-service.payment_service_url').'/midtrans-notification-callback', [
                    "transaction_time" => "2023-01-01 00:00:00",
                    "transaction_status" => "settlement",
                    "transaction_id" => $bill->prrb_midtrans_transaction_id,
                    "status_message" => "midtrans payment notification",
                    "status_code" => "200",
                    "signature_key" => $signature,
                    "settlement_time" => Carbon::now('Asia/Jakarta')->toDateTimeString(),
                    "payment_type" => "echannel",
                    "order_id" => $bill->prrb_order_id,
                    "merchant_id" => "G141532850",
                    "gross_amount" => strval($bill->prrb_amount+$bill->prrb_admin_cost).'.00',
                    "fraud_status" => "accept",
                    "currency" => "IDR",
                    "biller_code" => $bill->prrb_mandiri_biller_code,
                    "bill_key" => $bill->prrb_mandiri_bill_key,
                ])
                ->object();
        }

        return response()->json($result, 200);
    }
}
