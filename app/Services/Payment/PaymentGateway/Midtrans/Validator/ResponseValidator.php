<?php

namespace App\Services\Payment\PaymentGateway\Midtrans\Validator;

use Illuminate\Support\Facades\Validator;
use App\Services\Payment\PaymentGateway\Exceptions\PaymentServiceClientException;

class ResponseValidator
{
    public static function validate($response)
    {
        if (
            $response['status_code'] == '200'
            || $response['status_code'] == '201'
        ) {
            return true;
        }

        throw new PaymentServiceClientException('Response Error!', ['raw_response' => $response], 2);
    }
}
