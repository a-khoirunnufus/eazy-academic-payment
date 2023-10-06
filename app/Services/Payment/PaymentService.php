<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;

class PaymentService {

    private $base_url;
    private $req_header;

    public function __construct()
    {
        $this->base_url = config('payment-service.payment_service_url');
        $this->req_header = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    public function transactionStatus($order_id)
    {
        $response = Http::withHeaders($this->req_header)
            ->get($this->base_url.'/'.$order_id.'/status')
            ->object();

        return $response;
    }

    public function chargeTransaction($data)
    {
        $response = Http::withHeaders($this->req_header)
            ->post($this->base_url.'/charge', $data)
            ->object();

        return $response;
    }

    public function cancelTransaction($order_id)
    {
        $response = Http::withHeaders($this->req_header)
            ->post($this->base_url.'/'.$order_id.'/cancel')
            ->object();

        return $response;
    }
}
