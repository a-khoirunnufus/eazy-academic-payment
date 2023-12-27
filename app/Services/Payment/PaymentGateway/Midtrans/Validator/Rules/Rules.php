<?php

namespace App\Services\Payment\PaymentGateway\Midtrans\Validator\Rules;

use App\Service\Payments\PaymentGateway\Exceptions\PaymentServiceClientException;

class Rules
{
    private $rule_list;

    public function __construct($action, $payment_type)
    {
        $file_path = __DIR__.DIRECTORY_SEPARATOR.$action.'.'.$payment_type.'.php';

        if (!file_exists($file_path)) {
            throw new PaymentServiceClientException('Rule file not exists!');
        }

        $this->rule_list = include($file_path);
    }

    public function get()
    {
        return $this->rule_list;
    }
}
