<?php



class RequestBody
{
    public static function create($options)
    {
        return [
            'payment_type' => $options['payment_type'],

            'transaction_details' => 'required',
                'transaction_details.order_id' => 'required|string',
                'transaction_details.gross_amount' => 'required|string',

            'bank_transfer' => 'required',
                'bank_transfer.bank' => 'required|in:bca',

            'item_details' => 'sometimes|array',
                'item_details.*.id' => 'required_with:item_details|string',
                'item_details.*.price' => 'required_with:item_details|number',
                'item_details.*.quantity' => 'required_with:item_details|number',
                'item_details.*.name' => 'required_with:item_details|string',

            'customer_details' => 'sometimes',
                'customer_details.first_name' => 'required_with:customer_details|string',
                'customer_details.last_name' => 'required_with:customer_details|string',
                'customer_details.email' => 'required_with:customer_details|string',
                'customer_details.phone' => 'required_with:customer_details|string',

            'custom_expiry' => 'sometimes',
                'custom_expiry.order_time' => 'required_with:custom_expiry|string',
                'custom_expiry.expiry_duration' => 'required_with:custom_expiry|number',
                'custom_expiry.unit' => 'required_with:custom_expiry|string|in:second,minute,hour,day',
        ];
    }
}
