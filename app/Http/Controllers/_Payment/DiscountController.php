<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment\Faculty;
use App\Models\Payment\Discount;
use App\Models\Payment\Studyprogram;
use Illuminate\Http\Request;
use App\Models\Payment\Year;

class DiscountController extends Controller
{
    public function index()
    {
        $period = Year::orderBy('msy_code')->get();
        return view('pages._payment.discount.index',compact('period'));
    }

    public function receiver()
    {
        $period = Year::orderBy('msy_code')->get();
        $discount = Discount::all();
        $faculty = Faculty::all();
        $studyprogram = Studyprogram::all();
        return view('pages._payment.discount.receiver',compact('period', 'discount', 'faculty', 'studyprogram'));
    }
}
