<?php

namespace App\Services\Payment\PaymentGateway\Finpay;

use DB;
use Carbon\Carbon;

class RequestBody
{
    public static function create($config)
    {
        $finpay_data = json_decode($config['payment_type']->service_data, true);
        $va_expiry_duration_minute = DB::table('finance.ms_settings')->where('name', 'payment_va_expiry_duration_minute')->first()->value;

        return [
            // 'order' => 'required',
            // 'order.id' => 'required|alpha_dash|min:1|max:30',
            // 'order.amount' => 'required|numeric|digits_between:1,14',
            // 'order.description' => 'required|string|min:1|max:127',
            'order' => [
                'id' => $config['order_id'],
                'amount' => $config['total_amount'],
                'description' => 'Tagihan Registrasi',
            ],

            // 'customer' => 'required',
            // 'customer.email' => 'required|email|min:1|max:320',
            // 'customer.firstName' => 'required|string|min:1|max:50',
            // 'customer.lastName' => 'required|string|min:1|max:50',
            // 'customer.mobilePhone' => 'required|string|min:1|max:20',
            'customer' => [
                'email' => $config['student']->email,
                'firstName' => $config['student']->fullname,
                'lastName' => ' ',
                'mobilePhone' => $config['student']->phone_number,
            ],

            // 'url' => 'required',
            // 'url.callbackUrl' => 'required|string|min:1|max:320',
            'url' => [
                'callbackUrl' => url('/api/payment/pg-notification-handler/finpay'),
            ],

            // 'sourceOfFunds' => 'required',
            // 'sourceOfFunds.type' => 'required|string|in:vabni',
            'sourceOfFunds' => [
                'type' => $finpay_data['type']
            ]
        ];
    }
}
