<?php

namespace App\Services\Payment\PaymentGateway;

use App\Contracts\Payment\PaymentServiceClient;
use Illuminate\Support\Facades\Http;
use App\Exceptions\Payment\PaymentServiceClientException;
use Illuminate\Http\Client\Response;

class FinpayClient implements PaymentServiceClient
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

    public function charge($request_body)
    {
        RequestValidator::validate($request_body);

        $response = Http::withHeaders($this->request_header)
            ->post($this->base_url.'/'.$url_subdirectory.'/initiate', $request_body)
            ->object();

        ResponseValidator::validate((array)$response);
    }

    public function chargeTransaction(array $config): bool
    {
        $this->_validateChargeTransactionConfig($config);

        $url_subdirectory = $this->_getUrlSubdirectoryByPaymentType($config['payment_type']);
        $order_param = $config['order_data'];
        $customer_param = $config['customer_data'];
        $url_param = ['callbackUrl' => $config['callback_url']];
        $source_of_funds_param = ['type' => $config['payment_type']];

        $response = Http::withHeaders($this->request_header)
            ->post(
                $this->base_url.'/'.$url_subdirectory.'/initiate',
                [
                    'order' => $order_param,
                    'customer' => $customer_param,
                    'url' => $url_param,
                    'sourceOfFunds' => $source_of_funds_param,
                ]
            )
            ->object();

        if ($this->_validateResponse($response)) {
            return true;
        } else {
            return false;
        }
    }

    private function _getUrlSubdirectoryByPaymentType($payment_type)
    {
        if (in_array($payment_type, [
            'vamandiri',
            'vabni',
            'vabtn',
            'vamega',
            'vabsi',
            'vapermata',
            // 'vabca',
            // 'vabri',
            // 'vabjb',
        ])) {
            return 'pg/payment/card';
        }

        throw new PaymentServiceClientException('Payment type invalid!', null, 1);
    }

    private function _validateResponse(object|null $response)
    {
        if (is_null($response)) {
            return true;
        }

        if ($response->responseCode == '2000000') {
            return true;
        }

        throw new PaymentServiceClientException('Response Error!', ['raw_response' => (array)$response], 2);
    }
}
