<?php

namespace App\Http\Controllers\_Payment\Api\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\Settings;
use Illuminate\Support\Facades\Cache;
use DB;

class SettingsController extends Controller
{
    public function update(Request $request)
    {
        if(!$request->val && $request->name){
            $text= 'Input tidak valid!';
            return json_encode(array('success' => false, 'message' => $text));
        }

        $data = Settings::where('name',$request->name)->first();
        if(!$data){
            $text= 'Data tidak ditemukan!';
            return json_encode(array('success' => false, 'message' => $text));
        }

        DB::beginTransaction();
        try{
            $data->value = $request->val;
            $data->save();
            Cache::flush();
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json($e->getMessage());
        }

        $text= 'Setting berhasil terupdate!';
        return json_encode(array('success' => true, 'message' => $text));
    }
}
