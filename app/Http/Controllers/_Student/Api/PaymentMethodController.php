<?php

namespace App\Http\Controllers\_Student\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Masterdata\MsPaymentMethod;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $data = MsPaymentMethod::where('mpm_type', '=', 'bank')->get();

        return response()->json($data, 200);
    }

    public function detail($method_code)
    {
        $data = MsPaymentMethod::where('mpm_key', '=', $method_code)->first();

        return response()->json($data, 200);
    }
}
