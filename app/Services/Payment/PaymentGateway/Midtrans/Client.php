<?php

namespace App\Services\Payment\PaymentGateway\Midtrans;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;

use App\Services\Payment\PaymentGateway\Contracts\PaymentServiceClient;
use App\Services\Payment\PaymentGateway\Exceptions\PaymentServiceClientException;
use App\Services\Payment\PaymentGateway\Midtrans\Validator\RequestValidator;
use App\Services\Payment\PaymentGateway\Midtrans\Validator\ResponseValidator;

use Carbon\Carbon;

class Client implements PaymentServiceClient
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
        (new RequestValidator('charge', $payment_type->code))->validate($request_body);

        $response = (array) Http::withHeaders($this->request_headers)
            ->post($this->base_url.'/charge', $request_body)
            ->object();

        $this->createLog(
            'charge transaction',
            $response['status_code'] ?? 'n/a',
            $response['status_code'] ?? 'n/a',
            ['raw_response' => $response ?? 'n/a']
        );

        ResponseValidator::validate($response);

        return [
            'va_number' => $response['va_numbers'][0]->va_number,
            'expiry_time' => $response['expiry_time'],
        ];
    }

    public function status($order_id)
    {
        $response = (array) Http::withHeaders($this->request_header)
            ->get($this->base_url.'/'.$order_id.'/status')
            ->object();

        $this->createLog(
            'check transaction status',
            $response['status_code'] ?? 'n/a',
            $response['status_code'] ?? 'n/a',
            ['raw_response' => $response ?? 'n/a']
        );

        ResponseValidator::validate($response);

        return true;
    }

    public function cancel($order_id)
    {
        $response = (array) Http::withHeaders($this->request_header)
            ->post($this->base_url.'/'.$order_id.'/cancel')
            ->object();

        $this->createLog(
            'cancel transaction',
            $response['status_code'] ?? 'n/a',
            $response['status_code'] ?? 'n/a',
            ['raw_response' => $response ?? 'n/a']
        );

        ResponseValidator::validate($response);

        return true;
    }

    private function createLog($action, $code, $message, $payload)
    {
        DB::table('finance.log_service_midtrans')->insert([
            'timestamp' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s O'),
            'action' => $action,
            'status_code' => $code,
            'status_message' => $message,
            'payload' => json_encode($payload),
        ]);
    }
}
