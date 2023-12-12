<?php

namespace App\Services\Payment\PaymentGateway\Finpay\Validator;

use Illuminate\Support\Facades\Validator;
use App\Models\Payment\PaymentGateway\PaymentType;
use App\Services\Payment\PaymentGateway\Exceptions\PaymentServiceClientException;

class RequestValidator
{
    private $action;
    private $payment_type;

    public function __construct($action, PaymentType $payment_type)
    {
        $this->action = $action;
        $this->payment_type = $payment_type;
    }

    public static function validate($data)
    {
        $rules = (new Rules($this->action, $this->$payment_type))->get();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new PaymentServiceClientException('Request body validation error!', ['errors' => $validator->errors()]);
        }
    }
}
