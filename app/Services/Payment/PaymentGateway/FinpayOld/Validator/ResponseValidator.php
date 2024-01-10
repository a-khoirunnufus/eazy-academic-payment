<?php

namespace App\Services\PaymentGateway\Finpay\Validator;

use Illuminate\Support\Facades\Validator;

class ResponseValidator
{
    public static function validate($response)
    {
        if (is_null($response)) {
            return true;
        }

        if ($response['responseCode'] == '2000000') {
            return true;
        }

        throw new PaymentServiceClientException('Response Error!', ['raw_response' => $response], 2);
    }
}
