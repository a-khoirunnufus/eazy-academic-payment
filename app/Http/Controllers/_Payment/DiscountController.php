<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Year;

class DiscountController extends Controller
{
    public function index()
    {
        $period = Year::all();
        return view('pages._payment.discount.index',compact('period'));
    }
}
