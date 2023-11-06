<?php

namespace App\Http\Controllers\_Payment\Api\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PMB\Register;

class InvoiceRegistrantController extends Controller
{
    function studentRegistrant(Request $request){
        $data = Register::with('participant', 'studyProgram', 'lectureType', 'period', 'path' ,'paymentRegister', 'year');

        if($request->get('angkatan', '#ALL') !== '#ALL'){
            $data = $data->whereHas('year', function($q) use($request) {
                $q->where('msy_id', '=', $request->get('angkatan'));
            });
        }

        if($request->get('path', '#ALL') !== '#ALL'){
            $data = $data->whereHas('path', function($q) use($request) {
                $q->where('path_id', '=', $request->get('path'));
            });
        }

        if($request->get('period', '#ALL') !== '#ALL'){
            $data = $data->whereHas('period', function($q) use($request){
                $q->where('period_id', '=', $request->get('period'));
            });
        }

        return DataTables($data->get())->toJson();
    }
}
