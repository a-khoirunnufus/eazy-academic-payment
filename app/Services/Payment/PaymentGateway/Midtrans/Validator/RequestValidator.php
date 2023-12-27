<?php

namespace App\Services\Payment\PaymentGateway\Midtrans\Validator;

use Illuminate\Support\Facades\Validator;
use App\Services\Payment\PaymentGateway\Exceptions\PaymentServiceClientException;
use App\Services\Payment\PaymentGateway\Midtrans\Validator\Rules\Rules;

class RequestValidator
{
    private $action;
    private $payment_type;

    public function __construct($action, $payment_type)
    {
        $this->action = $action;
        $this->payment_type = $payment_type;
    }

    public function validate($data)
    {
        $rules = (new Rules($this->action, $this->payment_type))->get();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new PaymentServiceClientException('Request body validation error!', ['errors' => $validator->errors()]);
        }
    }
}
