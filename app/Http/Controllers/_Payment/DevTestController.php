<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DevTestController extends Controller
{
    public function index()
    {
        return view('pages._payment.dev-test.index');
    }
}
