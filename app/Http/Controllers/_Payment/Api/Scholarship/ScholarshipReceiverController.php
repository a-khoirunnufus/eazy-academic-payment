<?php

namespace App\Http\Controllers\_Payment\API\Scholarship;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\Scholarship;
use App\Models\Payment\ScholarshipReceiver;
use App\Http\Requests\Payment\Scholarship\ScholarshipReceiverRequest;
use App\Models\Student;
use App\Models\Year;
use DB;

class ScholarshipReceiverController extends Controller
{
    
    public function index(Request $request)
    {
        // $filters = $request->input('custom_filters');
        // $filters = array_filter($filters, function ($item) {
        //     return !is_null($item) && $item != '#ALL';
        // });

        $query = ScholarshipReceiver::query();
        
        // if (isset($filters['md_period_start_filter'])) {
        //     $query = $query->where('md_period_start', '=', $filters['md_period_start_filter']);
        // }

        // if (isset($filters['md_period_end_filter'])) {
        //     $query = $query->where('md_period_end', '=', $filters['md_period_end_filter']);
        // }

        $query = $query->with('period','student','scholarship')->orderBy('msr_id');
        return datatables($query)->toJson();
    }
    
    public function scholarship()
    {
        $query = Scholarship::all();
        return $query;
    }
    
    public function student()
    {
        $query = Student::all();
        return $query;
    }
    
    public function period($ms_id)
    {
        $data = Scholarship::with('periodStart','periodEnd')->findorfail($ms_id);
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
    
    public function store(ScholarshipReceiverRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try{
            $data = Scholarship::findOrFail($validated["ms_id"]);
            if(array_key_exists("msc_id",$validated)){
                $receiver = ScholarshipReceiver::findOrFail($validated["msc_id"]);
                if($data->msr_nominal != $validated['msr_nominal']){
                    $realization = $data->ms_realization-$receiver->msr_nominal+$validated["msr_nominal"];
                    $data->update(['ms_realization' =>$realization]);
                }else{
                    $realization = $data->ms_realization;
                }
                if($realization > $data->ms_budget){
                    $text = "Budget Tidak Mencukupi";
                    return json_encode(array('success' => false, 'message' => $text));
                }else{
                    $receiver->update($validated);
                }
                $text = "Berhasil memperbarui penerima beasiswa";
            }else{
                $realization = $data->ms_realization+$validated["msr_nominal"];
                if($realization > $data->ms_budget){
                    $text = "Budget Tidak Mencukupi";
                    return json_encode(array('success' => false, 'message' => $text));
                }else{
                    ScholarshipReceiver::create($validated);
                    $data->update(['ms_realization' =>$realization]);
                }
                $text = "Berhasil menambahkan penerima beasiswa";
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
            $receiver = ScholarshipReceiver::findOrFail($id);
            $data = Scholarship::withTrashed()->findOrFail($receiver->ms_id);
            $realization = $data->ms_realization-$receiver->msr_nominal;
            $data->update(['ms_realization' =>$realization]);
            $receiver->delete();
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json($e->getMessage());
        }
        return json_encode(array('success' => true, 'message' => "Berhasil menghapus penerima beasiswa"));
    }
}
