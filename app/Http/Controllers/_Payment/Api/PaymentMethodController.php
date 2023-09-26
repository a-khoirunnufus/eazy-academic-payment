<?php

namespace App\Http\Controllers\_Payment\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\PaymentMethod;
use App\Models\Payment\PaymentMethodType;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $data = PaymentMethod::all();
        return response()->json($data);
    }

    public function typeGroup()
    {
        $data = PaymentMethodType::with('paymentMethods')->get();
        return response()->json($data);
    }

    public function show($key)
    {
        $data = PaymentMethod::where('mpm_key', '=', $key)->first();

        return response()->json($data);
    }
}
