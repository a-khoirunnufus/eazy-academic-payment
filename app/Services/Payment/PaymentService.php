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

    public function chargeTransaction($data)
    {
        $response = Http::withHeaders($this->req_header)
            ->post($this->base_url.'/charge', $data)
            ->object();

        return $response;
    }

    public function cancelTransaction($data)
    {
        $response = Http::withHeaders($this->req_header)
            ->post($this->base_url.'/cancel', $data)
            ->object();

        return $response;
    }
}
