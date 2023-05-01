<?php

namespace App\Http\Controllers\_Payment\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\Settings\FormRegistrationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormulirPendaftaranController extends Controller
{
    public function registrationForm()
    {
        $data = DB::table('pmb.period_path as pp')
            ->select(
                'pp.ppd_id as id',
                'msy.msy_year as period',
                'mp2.path_name as track',
                'mp.period_name as wave',
                'pp.ppd_fee as rate'
            )
            ->join('pmb.ms_period as mp', 'pp.period_id', '=', 'mp.period_id')
            ->join('masterdata.ms_school_year as msy', 'mp.msy_id', '=', 'msy.msy_id')
            ->join('pmb.ms_path as mp2', 'pp.path_id', '=', 'mp2.path_id')
            ->distinct()->get();

        $datatable = datatables($data);

        return $datatable->toJSON();
    }

    public function byId($id){
        $data = DB::table('pmb.period_path as pp')
            ->select(
                'pp.ppd_id as id',
                'msy.msy_year as period',
                'mp2.path_name as track',
                'mp.period_name as wave',
                'pp.ppd_fee as rate'
            )
            ->join('pmb.ms_period as mp', 'pp.period_id', '=', 'mp.period_id')
            ->join('masterdata.ms_school_year as msy', 'mp.msy_id', '=', 'msy.msy_id')
            ->join('pmb.ms_path as mp2', 'pp.path_id', '=', 'mp2.path_id')
            ->whereRaw('pp.ppd_id = ?', [$id])
            ->distinct()->get();

        return $data[0];
    }

    public function setFee($id, Request $request){
        $fee = $request->input('rate', null);
        if($fee == null || $fee == ""){
            return response()->json([
                'success'=>false,
                'message'=>"Tarif kosong"
            ]);
        }

        $edit = DB::table('pmb.period_path')
                ->whereRaw('ppd_id = ?', [$id])
                ->update(['ppd_fee'=>DB::raw($fee)]);
        if($edit > 0){
            return response()->json([
                'success'=>true,
                'message'=>"Behasil Memperbaharui tarif"
            ]);
        }else {
            return response()->json([
                'success'=>false,
                'message'=>"Gagal Memperbaharui tarif"
            ]);
        }

    }
}
