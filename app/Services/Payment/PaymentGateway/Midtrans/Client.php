<?php

namespace App\Services\Payment\PaymentGateway\Midtrans;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;

use App\Service\Payment\PaymentGateway\Contracts\PaymentServiceClient;
use App\Service\Payment\PaymentGateway\Exceptions\PaymentServiceClientException;

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
        (new RequestValidator('charge', $payment_type))->validate($request_body);

        $response = (array) Http::withHeaders($this->request_header)
            ->post($this->base_url.'/charge', $request_body)
            ->object();

        $this->createLog(
            'charge transaction',
            $response['status_code'] ?? 'n/a',
            $response['status_code'] ?? 'n/a',
            ['raw_response' => $response ?? 'n/a']
        );

        ResponseValidator::validate($response);

        return true;
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

    public function cancel()
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
            'lsm_timestamp' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s O'),
            'lsm_action' => $action,
            'lsm_status_code' => $code,
            'lsm_status_message' => $message,
            'lsm_payload' => json_encode($payload),
        ]);
    }
}
