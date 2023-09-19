<?php

namespace App\Http\Controllers\_Payment\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\LogActivity;
use App\Traits\Payment\LogActivity as logActivityTraits;

class LogController extends Controller
{
    use logActivityTraits;

    public function logActivity(Request $request)
    {
        $url = $request->get('url') ? $request->get('url') : 'default';
        $log = $this->logActivityLists($url);
        return view('pages._payment.log.activity', compact('log'))->render();
    }
}
