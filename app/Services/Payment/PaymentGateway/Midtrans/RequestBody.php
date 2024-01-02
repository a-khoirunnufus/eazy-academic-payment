<?php

namespace App\Services\Payment\PaymentGateway\Midtrans;

use DB;
use Carbon\Carbon;

class RequestBody
{
    public static function create($config)
    {
        $midtrans_data = json_decode($config['payment_type']->service_data, true);
        $va_expiry_duration_minute = DB::table('finance.ms_settings')->where('name', 'payment_va_expiry_duration_minute')->first()->value;

        return [
            'payment_type' => $midtrans_data['payment_type'],

            // 'transaction_details' => 'required',
            // 'transaction_details.order_id' => 'required|string',
            // 'transaction_details.gross_amount' => 'required|string',
            'transaction_details' => [
                'order_id' => $config['order_id'],
                'gross_amount' => $config['total_amount'],
            ],

            // 'bank_transfer' => 'required',
            // 'bank_transfer.bank' => 'required|in:bca',
            'bank_transfer' => [
                'bank' => $midtrans_data['bank'],
            ],

            // 'item_details' => 'sometimes|array',
            // 'item_details.*.id' => 'required_with:item_details|string',
            // 'item_details.*.price' => 'required_with:item_details|number',
            // 'item_details.*.quantity' => 'required_with:item_details|number',
            // 'item_details.*.name' => 'required_with:item_details|string',
            'item_details' => $config['items'],

            // 'customer_details' => 'sometimes',
            // 'customer_details.first_name' => 'required_with:customer_details|string',
            // 'customer_details.last_name' => 'sometimes|string',
            // 'customer_details.email' => 'required_with:customer_details|string',
            // 'customer_details.phone' => 'required_with:customer_details|string',
            'customer_details' => [
                'first_name' => $config['student']->fullname,
                'last_name' => ' ',
                'email' => $config['student']->email,
                'phone' => $config['student']->phone_number,
            ],

            // 'custom_expiry' => 'sometimes',
            // 'custom_expiry.order_time' => 'required_with:custom_expiry|string',
            // 'custom_expiry.expiry_duration' => 'required_with:custom_expiry|number',
            // 'custom_expiry.unit' => 'required_with:custom_expiry|string|in:second,minute,hour,day',
            'custom_expiry' => [
                'order_time' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s O'),
                'expiry_duration' => $va_expiry_duration_minute,
                'unit' => 'minute',
            ],
        ];
    }
}
