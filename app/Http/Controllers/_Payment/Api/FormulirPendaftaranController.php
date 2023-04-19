<?php

namespace App\Http\Controllers\_Payment\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\Settings\FormRegistrationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormulirPendaftaranController extends Controller
{
    //
    public function create(FormRegistrationRequest $request){
        $validated = $request->validated();

        try{
            $insert = DB::insert("
                insert into 
                    pmb.period_path(path_id, period_id, ppd_code, ppd_fee)
                values (?,?,?,?)
            ", [$validated["jalur"], $validated["gelombang"], $validated["periode"], $validated["rate"]]);
        }catch(\Throwable $e){
            return $e;
        }

        return response()->json([
            'success'=>true,
            'message'=>"Berhasil Menambahkan Formulir"
        ]);
    }

    public function registrationForm()
    {
        $data = DB::select("
        select 
            pp.ppd_id as id,
            msy.msy_year as period,
            mp2.path_name as track,
            mp.period_name as wave,
            pp.ppd_fee as rate
        from pmb.period_path pp 
        join pmb.ms_period mp 
            on pp.period_id = mp.period_id 
        join masterdata.ms_school_year msy 
            on mp.msy_id = msy.msy_id 
        join pmb.ms_path mp2 
            on pp.path_id = mp2.path_id 
        ");

        $datatable = datatables($data);

        return $datatable->toJSON();
    }

    public function byId($id){
        $data = DB::select("
        select 
            pp.ppd_id as id,
            msy.msy_year as period,
            mp2.path_name as track,
            mp.period_name as wave,
            pp.ppd_fee as rate
        from pmb.period_path pp 
        join pmb.ms_period mp 
            on pp.period_id = mp.period_id 
        join masterdata.ms_school_year msy 
            on mp.msy_id = msy.msy_id 
        join pmb.ms_path mp2 
            on pp.path_id = mp2.path_id  
        where pp.ppd_id = ?
        ", [$id]);

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

        $edit = DB::update("
            update pmb.period_path set ppd_fee = ?
            where pmb.period_path.ppd_id = ?
        ", [$fee, $id]);
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
