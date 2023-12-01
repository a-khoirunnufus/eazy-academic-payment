<?php

namespace App\Services\PaymentGateway\Finpay;

use Illuminate\Support\Facades\Validator;

class CoreApi
{
    public static function validate($request_body)
    {
        $validator = Validator::make($request_body, [
            'title' => 'required|unique:posts|max:255',
            'body' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('post/create')
                        ->withErrors($validator)
                        ->withInput();
        }
    }
}
