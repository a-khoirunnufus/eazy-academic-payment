<?php

return [
    'order' => 'required',
        'order.id' => 'required|alpha_dash|min:1|max:30',
        'order.amount' => 'required|numeric|digits_between:1,14',
        'order.description' => 'required|string|min:1|max:127',

    'customer' => 'required',
        'customer.email' => 'required|email|min:1|max:320',
        'customer.firstName' => 'required|string|min:1|max:50',
        'customer.lastName' => 'sometimes|string|min:1|max:50',
        'customer.mobilePhone' => 'required|string|min:1|max:20',

    'url' => 'required',
        'url.callbackUrl' => 'required|string|min:1|max:320',

    'sourceOfFunds' => 'required',
        'sourceOfFunds.type' => 'required|string|in:vabni',
];
