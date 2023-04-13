<?php

namespace App\Http\Controllers\_Payment\Api\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Payment\Settings\ComponentRequest;
use App\Models\Payment\ComponentType;
use App\Models\Payment\Component;
use DB;

class ComponentInvoiceController extends Controller
{
    public function getComponentType()
    {
        $data = ComponentType::orderBy('msct_id')->get();
        return $data->toJson();
    }
    
    public function store(ComponentRequest $request)
    {
        $validate = $request->validated();
        $arr = ['msc_is_student','msc_is_new_student','msc_is_participant'];
        foreach($arr as $item){
            if(array_key_exists($item,$validate)){
                $validate[$item] = 1;
            }else{
                $validate[$item] = 0;
            }
        }

        DB::beginTransaction();
        try{
            Component::create($validate + [
                'active_status' => 1
            ]);
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json($e->getMessage()); 
        }
        return json_encode(array('status' => 'ok', 'text' => 'Berhasil menambahkan komponen tagihan'));
    }
}
