<?php

namespace App\Http\Controllers\_Payment\API\Discount;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Payment\Discount\DiscountRequest;
use App\Models\Payment\Discount;
use App\Models\Year;
use DB;

class DiscountController extends Controller
{
    
    public function index(Request $request)
    {
        $filters = $request->input('custom_filters');
        $filters = array_filter($filters, function ($item) {
            return !is_null($item) && $item != '#ALL';
        });

        $query = Discount::query();
        
        if (isset($filters['md_period_start_filter'])) {
            $query = $query->where('md_period_start', '=', $filters['md_period_start_filter']);
        }

        if (isset($filters['md_period_end_filter'])) {
            $query = $query->where('md_period_end', '=', $filters['md_period_end_filter']);
        }

        $query = $query->with('periodStart','periodEnd')->orderBy('md_id');
        
        return datatables($query)->toJson();
    }
    
    public function period()
    {
        $query = Year::all();
        return $query;
    }
    
    public function store(DiscountRequest $request)
    {
        $validated = $request->validated();
        
        DB::beginTransaction();
        try{
            if(array_key_exists("msc_id",$validated)){
                $data = Discount::findOrFail($validated["msc_id"]);
                $data->update($validated);
                $text = "Berhasil memperbarui potongan";
            }else{
                Discount::create($validated + [
                    'md_realization' => 0
                ]);
                $text = "Berhasil menambahkan potongan";
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json($e->getMessage());
        }
        return json_encode(array('success' => true, 'message' => $text));
    }
    
    public function delete($id)
    {
        $data = Discount::findOrFail($id);
        $data->delete();

        return json_encode(array('success' => true, 'message' => "Berhasil menghapus potongan"));
    }

}
