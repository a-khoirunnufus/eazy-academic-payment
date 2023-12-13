<?php

namespace App\Services\Payment\PaymentGateway;

use Illuminate\Support\Facades\DB;

use App\Services\Payment\PaymentGateway\Midtrans\Client as MidtransClient;
use App\Services\Payment\PaymentGateway\Finpay\Client as FinpayClient;
use App\Services\Payment\PaymentGateway\Midtrans\RequestBody as MidtransRequestBody;
use App\Services\Payment\PaymentGateway\Finpay\RequestBody as FinpayRequestBody;
use App\Services\Payment\PaymentGateway\Exceptions\PaymentServiceClientException;

class PaymentServiceApi
{
    private $payment_gateway;
    private $client;

    public function __construct()
    {
        $payment_gateway = DB::table('finance.ms_settings')
            ->where('name', 'payment_payment_gateway_use')
            ->first()
            ->value;

        $this->payment_gateway = $payment_gateway;

        if ($payment_gateway == 'midtrans') {
            $this->client = new MidtransClient();
        }

        if ($payment_gateway == 'finpay') {
            $this->client = new FinpayClient();
        }
    }

    public function charge($options)
    {
        if ($this->payment_gateway == 'midtrans') {
            $request_body = MidtransRequestBody::create($options);
        }

        if ($this->payment_gateway == 'finpay') {
            $request_body = FinpayRequestBody::create($options);
        }

        try {
            $this->client->charge($request_body, $options['payment_type']);
        }
        catch (PaymentServiceClientException $ex) {
            throw $th;
        }
    }

    public function status() {}

    public function cancel() {}
}
