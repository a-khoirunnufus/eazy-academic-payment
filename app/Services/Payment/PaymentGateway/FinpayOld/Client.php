<?php

namespace App\Services\Payment\PaymentGateway;

use App\Contracts\Payment\PaymentServiceClient;
use Illuminate\Support\Facades\Http;
use App\Exceptions\Payment\PaymentServiceClientException;
use Illuminate\Http\Client\Response;

class Client implements PaymentServiceClient
{
    private $base_url;
    private $request_headers;

    public function __construct()
    {
        $merchant_id = config('finpay.merchant_id');
        $merchant_key = config('finpay.merchant_key');
        $this->base_url = config('finpay.base_url');
        $this->req_header = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic '.base64_encode($merchant_id.':'.$merchant_key),
        ];
    }

    public function charge($request_body, $payment_type)
    {
        (new RequestValidator($payment_type))->validate($request_body);

        $url_subdirectory = $this->getUrlSubdirectoryByPaymentType($payment_type);
        $response = (array) Http::withHeaders($this->request_header)
            ->post($this->base_url.'/'.$url_subdirectory.'/initiate', $request_body)
            ->object();

        $is_success = ResponseValidator::validate($response);

        return $is_success;
    }

    public function status() {}

    public function cancel() {}

    private function getUrlSubdirectoryByPaymentType($payment_type)
    {
        if (in_array($payment_type, [
            'vamandiri',
            'vabni',
            'vabtn',
            'vamega',
            'vabsi',
            'vapermata',
            'vabca',
            'vabri',
            'vabjb',
        ])) {
            return 'pg/payment/card';
        }

        throw new PaymentServiceClientException('Payment type invalid!', null, 1);
    }
}
