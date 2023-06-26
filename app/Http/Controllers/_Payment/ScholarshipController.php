<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Year;

class ScholarshipController extends Controller
{
    public function index()
    {
        $period = Year::all();
        return view('pages._payment.scholarship.index',compact('period'));
    }

    public function receiver()
    {
        $period = Year::all();
        return view('pages._payment.scholarship.receiver',compact('period'));
    }
}
