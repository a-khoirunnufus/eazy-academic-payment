<?php

namespace App\Http\Controllers\_Payment\Api\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Payment\Settings\ComponentRequest;
use App\Models\Payment\ComponentType;
use App\Models\Payment\Component;
use App\Traits\Models\QueryFilterByRequest;
use App\Traits\Models\LoadDataRelationByRequest;
use DB;

class ComponentInvoiceController extends Controller
{
    use QueryFilterByRequest, LoadDataRelationByRequest;
    
    public function index(Request $request)
    {
        $query = Component::query();
        $query = $query->orderBy('msc_id');
        // $query = $this->loadRelation($query, $request, ['faculty']);
        // $query = $this->applyFilter($query, $request, [
        //     'studyprogram_active_status', 'faculty_id'
        // ]);
        return datatables($query)->toJson();
    }

    public function getComponentType()
    {
        $data = ComponentType::orderBy('msct_id')->get();
        return $data->toJson();
    }
    
    public function store(ComponentRequest $request)
    {
        $validated = $request->validated();
        $arr = ['msc_is_student','msc_is_new_student','msc_is_participant'];
        foreach($arr as $item){
            if(array_key_exists($item,$validated)){
                $validated[$item] = 1;
            }else{
                $validated[$item] = 0;
            }
        }

        DB::beginTransaction();
        try{
            if(array_key_exists("msc_id",$validated)){
                $data = Component::findOrFail($validated["msc_id"]);
                $data->update($validated);
                $text = "Berhasil memperbarui komponen tagihan";
            }else{
                Component::create($validated + [
                    'active_status' => 1
                ]);
                $text = "Berhasil menambahkan komponen tagihan";
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json($e->getMessage()); 
        }
        return json_encode(array('status' => 'ok', 'text' => $text));
    }
    
    public function delete($id)
    {
        $data = Component::findOrFail($id);
        $data->delete();

        return json_encode(array('success' => true, 'text' => "Berhasil menghapus komponen tagihan"));
    }
}
