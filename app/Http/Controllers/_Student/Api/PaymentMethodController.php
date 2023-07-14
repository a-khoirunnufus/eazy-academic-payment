<?php

namespace App\Http\Controllers\_Student\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Masterdata\MsPaymentMethod;
use App\Models\Payment\MasterPaymentMethod;
use App\Models\Payment\MasterPaymentMethodType;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $data = MasterPaymentMethodType::with('paymentMethods')->get();

        return response()->json($data, 200);
    }

    public function detail($method_code)
    {
        $data = MasterPaymentMethod::where('mpm_key', '=', $method_code)->first();

        return response()->json($data, 200);
    }
}
