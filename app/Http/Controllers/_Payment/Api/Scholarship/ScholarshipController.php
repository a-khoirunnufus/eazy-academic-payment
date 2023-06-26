<?php

namespace App\Http\Controllers\_Payment\API\Scholarship;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\Scholarship;
use App\Http\Requests\Payment\Scholarship\ScholarshipRequest;
use App\Models\Year;
use DB;

class ScholarshipController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->input('custom_filters');
        $filters = array_filter($filters, function ($item) {
            return !is_null($item) && $item != '#ALL';
        });

        $query = Scholarship::query();
        
        if (isset($filters['ms_period_start_filter'])) {
            $query = $query->where('ms_period_start', '=', $filters['ms_period_start_filter']);
        }

        if (isset($filters['ms_period_end_filter'])) {
            $query = $query->where('ms_period_end', '=', $filters['ms_period_end_filter']);
        }

        $query = $query->with('periodStart','periodEnd')->orderBy('ms_id');
        
        return datatables($query)->toJson();
    }
    
    public function period()
    {
        $query = Year::all();
        return $query;
    }
    
    public function store(ScholarshipRequest $request)
    {
        $validated = $request->validated();
        
        DB::beginTransaction();
        try{
            if(array_key_exists("msc_id",$validated)){
                $data = Scholarship::findOrFail($validated["msc_id"]);
                $data->update($validated);
                $text = "Berhasil memperbarui beasiswa";
            }else{
                Scholarship::create($validated + [
                    'ms_realization' => 0
                ]);
                $text = "Berhasil menambahkan beasiswa";
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
        $data = Scholarship::findOrFail($id);
        $data->delete();

        return json_encode(array('success' => true, 'message' => "Berhasil menghapus beasiswa"));
    }
}
