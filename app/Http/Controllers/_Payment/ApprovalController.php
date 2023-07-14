<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Path;
use App\Models\Period;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index()
    {
        $path = Path::all();
        $period = Period::all();
        $faculty = Faculty::all();
        return view('pages._payment.approval.index', compact('path', 'period', 'faculty'));
    }

    public function dispensation()
    {
        return view('pages._payment.approval.dispensation.index');
    }
    
    public function credit()
    {
        return view('pages._payment.approval.credit.index');
    }
}
