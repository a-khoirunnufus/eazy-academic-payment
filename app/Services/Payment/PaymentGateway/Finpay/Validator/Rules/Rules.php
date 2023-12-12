<?php

namespace App\Services\Payment\PaymentGateway\Finpay\Validator\Rules;

use App\Service\Payment\PaymentGateway\Finpay\Exceptions\PaymentServiceClientException;

class Rules
{
    private $rule_list;

    public function __construct($action, $payment_method)
    {
        $file_path = __DIR__.DIRECTORY_SEPARATOR.'charge.virtual_account.php';

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
