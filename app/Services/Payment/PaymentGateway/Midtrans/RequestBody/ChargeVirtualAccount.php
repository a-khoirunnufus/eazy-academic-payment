<?php

namespace App\Services\Payment\PaymentGateway\Midtrans\RequestBody;

use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class ChargeVirtualAccount
{
    public static $action = 'charge';
    public static $payment_method = 'virtual_account';

    /**
     * @param array {
     *      order_id: int,
     *      payment_type: MasterPaymentTypeMidtrans|MasterPaymentTypeFinpay,
     *      student: Student,
     *      items: array[] {
     *          id: int,
     *          price: int,
     *          quantity: int,
     *          name: string
     *      }
     * } $options
     */
    public static function create($options)
    {
        $payment_type = $options['payment_type'];
        $midtrans_specific_data = json_decode($payment_type->mptm_midtrans_specific_data, true);
        $student = $options['student'];

        $total_price_amount = array_reduce($options['items'], function($carry, $item) {
            $carry += (int) $item->price;
            return $carry;
        }, 0);

        $item_details = array_map(function($item) {
            return [
                'id' => $item['id'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'name' => $item['name'],
            ];
        }, $options['items']);

        $va_expiry_duration_minute = DB::table('finance.ms_settings')
            ->where('name', 'payment_va_expiry_duration_minute')
            ->first()
            ->value;

        return [
            'payment_type' => $midtrans_specific_data['payment_type'],

            'transaction_details' => [
                'order_id' => $options['order_id'],
                'gross_amount' => $total_price_amount,
            ],

            'bank_transfer' => [
                'bank' => $midtrans_specific_data['bank'],
            ],

            'item_details' => $item_details,

            'customer_details' => [
                'first_name' => $student->fullname,
                'last_name' => '',
                'email' => $student->email,
                'phone' => $student->phone_number,
            ],

            'custom_expiry' => [
                'order_time' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s O'),
                'expiry_duration' => $va_expiry_duration_minute,
                'unit' => 'minute',
            ],
        ];
    }
}
