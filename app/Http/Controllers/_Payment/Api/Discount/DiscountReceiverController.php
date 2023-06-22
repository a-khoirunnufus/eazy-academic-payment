<?php

namespace App\Http\Controllers\_Payment\API\Discount;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\Discount;
use App\Models\Payment\DiscountReceiver;
use App\Http\Requests\Payment\Discount\DiscountReceiverRequest;
use App\Models\Student;
use App\Models\Year;
use DB;

class DiscountReceiverController extends Controller
{
    
    public function index(Request $request)
    {
        // $filters = $request->input('custom_filters');
        // $filters = array_filter($filters, function ($item) {
        //     return !is_null($item) && $item != '#ALL';
        // });

        $query = DiscountReceiver::query();
        
        // if (isset($filters['md_period_start_filter'])) {
        //     $query = $query->where('md_period_start', '=', $filters['md_period_start_filter']);
        // }

        // if (isset($filters['md_period_end_filter'])) {
        //     $query = $query->where('md_period_end', '=', $filters['md_period_end_filter']);
        // }

        $query = $query->with('period','student','discount')->orderBy('mdr_id');
        return datatables($query)->toJson();
    }
    
    public function discount()
    {
        $query = Discount::all();
        return $query;
    }
    
    public function student()
    {
        $query = Student::all();
        return $query;
    }
    
    public function period($md_id)
    {
        $data = Discount::with('periodStart','periodEnd')->findorfail($md_id);
        $start = $data->periodStart->msy_code;
        $end = $data->periodEnd->msy_code;
        $arr = [$start,$end];

        while($start < $end){
            $split = str_split($start, 4);
            $year = (int) $split[0];
            $sem = (int) $split[1];
            if($sem == 1){
                $start = $year.''.($sem+1);
            }else{
                $year = $year+1;
                $start = $year.'1';
            }
            $arr[] = $start;
        }
        $period = Year::whereIn('msy_code', $arr)->orderBy('msy_code')->get();
        return $period;
    }
    
    public function store(DiscountReceiverRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try{
            $data = Discount::findOrFail($validated["md_id"]);
            if(array_key_exists("msc_id",$validated)){
                $receiver = DiscountReceiver::findOrFail($validated["msc_id"]);
                if($data->mdr_nominal != $validated['mdr_nominal']){
                    $realization = $data->md_realization-$receiver->mdr_nominal+$validated["mdr_nominal"];
                    $data->update(['md_realization' =>$realization]);
                }else{
                    $realization = $data->md_realization;
                }
                if($realization > $data->md_budget){
                    $text = "Budget Tidak Mencukupi";
                    return json_encode(array('success' => false, 'message' => $text));
                }else{
                    $receiver->update($validated);
                }
                $text = "Berhasil memperbarui penerima potongan";
            }else{
                $realization = $data->md_realization+$validated["mdr_nominal"];
                if($realization > $data->md_budget){
                    $text = "Budget Tidak Mencukupi";
                    return json_encode(array('success' => false, 'message' => $text));
                }else{
                    DiscountReceiver::create($validated);
                    $data->update(['md_realization' =>$realization]);
                }
                $text = "Berhasil menambahkan penerima potongan";
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
        DB::beginTransaction();
        try{
            $receiver = DiscountReceiver::findOrFail($id);
            $data = Discount::withTrashed()->findOrFail($receiver->md_id);
            $realization = $data->md_realization-$receiver->mdr_nominal;
            $data->update(['md_realization' =>$realization]);
            $receiver->delete();
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json($e->getMessage());
        }
        return json_encode(array('success' => true, 'message' => "Berhasil menghapus penerima potongan"));
    }
    
}
