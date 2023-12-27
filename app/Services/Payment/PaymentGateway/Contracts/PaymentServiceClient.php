<?php

namespace App\Services\Payment\PaymentGateway\Contracts;

interface PaymentServiceClient
{
    public function charge($request_body, $payment_type);
    public function status($order_id);
    public function cancel($order_id);
}
