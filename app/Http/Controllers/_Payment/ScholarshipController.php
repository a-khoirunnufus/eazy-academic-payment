<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment\Faculty;
use App\Models\Payment\Scholarship;
use Illuminate\Http\Request;
use App\Models\Payment\Year;

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
        $schoolarship = Scholarship::all();
        $faculty = Faculty::all();

        return view('pages._payment.scholarship.receiver',compact('period', 'schoolarship', 'faculty'));
    }
}
