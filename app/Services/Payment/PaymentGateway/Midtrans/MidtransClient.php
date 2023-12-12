<?php

namespace App\Services\Payment\PaymentGateway\Midtrans;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use App\Service\Payment\PaymentGateway\Contracts\PaymentServiceClient;
use App\Service\Payment\PaymentGateway\Exceptions\PaymentServiceClientException;


class MidtransClient implements PaymentServiceClient
{
    private $base_url;
    private $request_headers;

    public function __construct()
    {
        $server_key = config('payment-midtrans.server_key');
        $this->base_url = config('payment-midtrans.base_url');
        $this->request_headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic '.base64_encode($server_key.':'),
        ];
    }

    public function charge($request_body, $payment_type)
    {
        (new RequestValidator('charge', $payment_type))->validate($request_body);

        $response = (array) Http::withHeaders($this->request_header)
            ->post($this->base_url.'/charge', $request_body)
            ->object();

        $is_success = ResponseValidator::validate($response);

        return $is_success;
    }

    public function status() {}

    public function cancel() {}
}
