<?php

namespace App\Services\Payment\PaymentGateway\Finpay\Exceptions;

use \Exception;

class PaymentServiceClientException extends Exception
{
    private $payload;

    public function __construct($message, array|null $payload = null, $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);

        $this->payload = $payload;
    }

    public function getPayload()
    {
        return $this->paylaod;
    }
}
