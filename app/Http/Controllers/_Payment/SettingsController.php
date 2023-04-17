<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\ComponentType;

class SettingsController extends Controller
{
    public function component()
    {
        return view('pages._payment.settings.component.index');
    }
    
    public function subjectrates()
    {
        return view('pages._payment.settings.subjectrates.index');
    }
    
    public function creditSchema()
    {
        return view('pages._payment.settings.credit-schema.index');
    }
}
