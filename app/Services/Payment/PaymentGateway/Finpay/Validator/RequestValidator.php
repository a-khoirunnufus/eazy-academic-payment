<?php

namespace App\Services\Payment\PaymentGateway\Finpay\Validator;

use Illuminate\Support\Facades\Validator;

class RequestValidator
{
    private $payment_type;
    private $payment_method;

    public function __construct($payment_type)
    {
        $this->payment_type = $payment_type;
        $this->payment_method = $this->getPaymentMethod($payment_type);

        $this->validatePaymentType($payment_type);
        $this->validatePaymentMethod($this->payment_method);
    }

    public static function validate($data)
    {
        $rules = $this->getRules($this->payment_method);

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new PaymentServiceClientException('Request body validation error!', ['errors' => $validator->errors()]);
        }
    }

    private function getRules($payment_method)
    {
        $rules = [];

        switch (self::getPaymentMethod($payment_type)) {
            case 'virtual_account':
                $rules = include(__DIR__.DIRECTORY_SEPARATOR.'Rules'.DIRECTORY_SEPARATOR.'virtualAccount.php');
                break;

            case 'credit_card':
                $rules = include(__DIR__.DIRECTORY_SEPARATOR.'Rules'.DIRECTORY_SEPARATOR.'creditCard.php');
                break;

            case 'e_money':
                $rules = include(__DIR__.DIRECTORY_SEPARATOR.'Rules'.DIRECTORY_SEPARATOR.'eMoney.php');
                break;

            case 'qris':
                $rules = include(__DIR__.DIRECTORY_SEPARATOR.'Rules'.DIRECTORY_SEPARATOR.'qris.php');
                break;

            default:
                break;
        }

        return $rules;
    }

    private function getPaymentMethod($payment_type)
    {
        // Virtual Account
        if (in_array($payment_type, [
            'vamandiri',
            'vabni',
            'vabtn',
            'vamega',
            'vabsi',
            'vapermata',
            'vabca',
            'vabri',
            'vabjb',
        ])) {
            return 'virtual_account';
        }

        // Credit Card
        if ($payment_type == 'cc') {
            return 'credit_card';
        }

        // E-Money
        if (in_array($payment_type, [
            'linkaja',
            'shopeepay',
            'ovo',
            'dana',
        ])) {
            return 'e_money';
        }

        // QRIS
        if ($payment_type == 'qris') {
            return 'qris';
        }

        return null;
    }

    private function validatePaymentType($payment_type)
    {
        if (in_array($payment_type, [
            'vamandiri',
            'vabni',
            'vabtn',
            'vamega',
            'vabsi',
            'vapermata',
            'vabca',
            'vabri',
            'vabjb',
            'cc',
            'linkaja',
            'shopeepay',
            'ovo',
            'dana',
            'qris',
        ])) {
            return true;
        }

        throw new PaymentServiceClientException('Payment type invalid!', ['payment_type' => $payment_type]);
    }

    private function validatePaymentMethod($payment_method)
    {
        if (in_array($payment_method, [
            'virtual_account',
            'credit_card',
            'e_money',
            'qris',
        ])) {
            return true;
        }

        throw new PaymentServiceClientException('Payment method invalid!', ['payment_method' => $payment_method]);
    }
}
