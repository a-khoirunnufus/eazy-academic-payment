<?php

namespace App\Services\Payment\PaymentGateway\Midtrans\Validator;

use Illuminate\Support\Facades\Validator;

class ResponseValidator
{
    public static function validate($response)
    {
        if ($response['status_code'] == '200') {
            return true;
        }

        throw new PaymentServiceClientException('Response Error!', ['raw_response' => $response], 2);
    }
}
