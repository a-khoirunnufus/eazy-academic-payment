<?php

namespace App\Services\Payment\PaymentGateway\Finpay\Validator;

use Illuminate\Support\Facades\Validator;
use App\Models\Payment\PaymentGateway\PaymentType;

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
        $payment_method = $this->payment_type->payment_method;
        $rules = (new Rules($this->action, $payment_method))->get();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new PaymentServiceClientException('Request body validation error!', ['errors' => $validator->errors()]);
        }
    }

    // private function getPaymentMethod($payment_type)
    // {
    //     // Virtual Account
    //     if (in_array($payment_type, [
    //         'vamandiri',
    //         'vabni',
    //         'vabtn',
    //         'vamega',
    //         'vabsi',
    //         'vapermata',
    //         'vabca',
    //         'vabri',
    //         'vabjb',
    //     ])) {
    //         return 'virtual_account';
    //     }

    //     // Credit Card
    //     if ($payment_type == 'cc') {
    //         return 'credit_card';
    //     }

    //     // E-Money
    //     if (in_array($payment_type, [
    //         'linkaja',
    //         'shopeepay',
    //         'ovo',
    //         'dana',
    //     ])) {
    //         return 'e_money';
    //     }

    //     // QRIS
    //     if ($payment_type == 'qris') {
    //         return 'qris';
    //     }

    //     return null;
    // }

    // private function validatePaymentType($payment_type)
    // {
    //     if (in_array($payment_type, [
    //         'vamandiri',
    //         'vabni',
    //         'vabtn',
    //         'vamega',
    //         'vabsi',
    //         'vapermata',
    //         'vabca',
    //         'vabri',
    //         'vabjb',
    //         'cc',
    //         'linkaja',
    //         'shopeepay',
    //         'ovo',
    //         'dana',
    //         'qris',
    //     ])) {
    //         return true;
    //     }

    //     throw new PaymentServiceClientException('Payment type invalid!', ['payment_type' => $payment_type]);
    // }

    // private function validatePaymentMethod($payment_method)
    // {
    //     if (in_array($payment_method, [
    //         'virtual_account',
    //         'credit_card',
    //         'e_money',
    //         'qris',
    //     ])) {
    //         return true;
    //     }

    //     throw new PaymentServiceClientException('Payment method invalid!', ['payment_method' => $payment_method]);
    // }
}
