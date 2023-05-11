<?php

namespace App\Http\Controllers\_Payment\Api\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\PaymentRate;
use App\Models\Payment\PaymentCredit;
use App\Models\Payment\PaymentComponent;
use App\Models\Payment\Component;
use App\Models\Payment\ComponentDetail;
use App\Models\Payment\CreditSchema;
use App\Models\Payment\CreditSchemaPeriodPath;
use App\Models\Payment\CreditSchemaDeadline;
use App\Http\Requests\Payment\Settings\PaymentRateRequest;
use App\Http\Requests\Payment\Settings\PaymentRateUpdateRequest;
use App\Models\Period;
use App\Models\Path;
use App\Models\PeriodPath;
use App\Models\PeriodPathMajor;
use DB;
use Builder;

class PaymentRatesController extends Controller
{
    public function index(Request $request)
    {
        // $query = PaymentRate::query();
        // $query = $query->with('credit','path','period','studyProgram','component')->orderBy('f_id');
        
        $query = PeriodPath::query();
        $query = $query->with('major','path','period')->orderBy('ppd_id');
        // dd($query->get());
        return datatables($query)->toJson();
    }

    public function detail($id)
    {
        $query = PeriodPathMajor::query();
        $query = $query->where('ppd_id',$id)->with('majorLectureType','credit','periodPath')->orderBy('ppm_id');
        $query = $query->get();
        $collection = collect();
        foreach($query as $item){
            $mma_id = 0;
            $mlt_id = 0;
            $path_id = 0;
            $period_id = 0;
            if($item->majorLectureType){
                $mma_id = $item->majorLectureType->mma_id;
                $mlt_id = $item->majorLectureType->mlt_id;
            }
            if($item->periodPath){
                $path_id = $item->periodPath->path_id;
                $period_id = $item->periodPath->period_id;
            }
            $search = ComponentDetail::with('component')->where('mma_id', $mma_id)->where('mlt_id', $mlt_id)->where('path_id', $path_id)->where('period_id', $period_id)->get();
            $data = ['ppm' => $item,'component' => $search];
            $collection->push($data);
        }
        return datatables($collection)->toJson();
    }
    
    public function getComponent()
    {
        $component = Component::all();
        return $component->toJson();
    }
    
    public function getSchema()
    {
        $schema = CreditSchema::all();
        return $schema->toJson();
    }

    public function getSchemaById($ppm_id,$cs_id)
    {
        $schema = CreditSchemaPeriodPath::with('creditSchema')->where('ppm_id',$ppm_id)->where('cs_id',$cs_id)->first();
        if(!$schema){
            $create = CreditSchemaPeriodPath::create([
                'cs_id' => $cs_id,
                'ppm_id' => $ppm_id
            ]);
            $schema = CreditSchemaPeriodPath::with('creditSchema')->where('cspp_id',$create->cspp_id)->first();
        }
        return $schema->toJson();
    }

    public function removeSchemaById($ppm_id,$cs_id)
    {
        $schema = CreditSchemaPeriodPath::with('creditSchema')->where('ppm_id',$ppm_id)->where('cs_id',$cs_id)->delete();
        return json_encode(array('success' => true, 'message' => "Berhasil menghapus skema"));
    }

    public function update(PaymentRateUpdateRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try{
            if(isset($validated['msc_id'])){
                $count = count($validated['msc_id']);
                for ($i=0; $i < $count; $i++) { 
                    if($validated['cd_id'][$i] == 0){
                        ComponentDetail::create([
                            'mma_id' => $validated['mma_id'][$i],
                            'msc_id' => $validated['msc_id'][$i],
                            'period_id' => $validated['period_id'][$i],
                            'path_id' => $validated['path_id'][$i],
                            'cd_fee' => $validated['cd_fee'][$i],
                            'msy_id' => $validated['msy_id'][$i],
                            'mlt_id' => $validated['mlt_id'][$i],
                            'ppm_id' => $validated['ppm_id'][$i]
                        ]);
                    }else{
                        $data = ComponentDetail::findorfail($validated['cd_id'][$i]);
                        $data->update([
                            'msc_id' => $validated['msc_id'][$i],
                            'cd_fee' => $validated['cd_fee'][$i]
                        ]);
                    }
                }
            }
            if(isset($validated['cs_id'])){
                foreach($validated['cs_id'] as $item){
                    $data = CreditSchemaPeriodPath::where('cs_id',$item)->where('ppm_id', $validated['main_ppm_id'])->first();
                    if(!$data){
                        CreditSchemaPeriodPath::create([
                            'cs_id' => $item,
                            'ppm_id' => $validated['main_ppm_id']
                        ]);
                    }
                }
                CreditSchemaPeriodPath::where('ppm_id', $validated['main_ppm_id'])->whereNotIn('cs_id', $validated['cs_id'])->delete();
            }
            if(isset($validated['cse_cs_id'])){
                foreach($validated['cse_cs_id'] as $key => $item){
                    $data = CreditSchemaDeadline::where('cs_id',$item)->where('csd_id', $validated['cse_csd_id'][$key])->first();
                    if(!$data){
                        CreditSchemaDeadline::create([
                            'cs_id' => $item,
                            'csd_id' => $validated['cse_csd_id'][$key],
                            'cse_deadline' => $validated['cse_deadline'][$key],
                        ]);
                    }else{
                        $data->update([
                            'cse_deadline' => $validated['cse_deadline'][$key]
                        ]);
                    }
                }
            }
            $text = "Berhasil memperbarui tarif dan pembayaran";
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json($e->getMessage());
        }
        return json_encode(array('success' => true, 'message' => $text));
    }
    
    public function deleteComponent($id)
    {
        $data = ComponentDetail::findOrFail($id);
        $data->delete();

        return json_encode(array('success' => true, 'message' => "Berhasil menghapus komponen"));
    }
    
    // OLD CODE
    // public function store(request $request)
    // {
    //     $validated = $request->validated();
    //     // dd($validated);
    //     DB::beginTransaction();
    //     try{
    //         $paymentRate = PaymentRate::create([
    //             'f_period_id' => $validated['f_period_id'],
    //             'f_studyprogram_id' => $validated['f_studyprogram_id'],
    //             'f_path_id' => $validated['f_path_id'],
    //             'f_jenis_perkuliahan_id' => $validated['f_jenis_perkuliahan_id'],
    //         ]);
    //         $f_id = $paymentRate->f_id;

    //         $count = count($validated['cs_id']);
    //         for ($i=0; $i < $count; $i++) { 
    //             PaymentCredit::create([
    //                 'f_id' => $f_id,
    //                 'cs_id' => $validated['cs_id'][$i]
    //             ]);
    //         }
            
    //         $count = count($validated['msc_id']);
    //         for ($i=0; $i < $count; $i++) { 
    //             PaymentComponent::create([
    //                 'f_id' => $f_id,
    //                 'msc_id' => $validated['msc_id'][$i],
    //                 'fc_rate' => $validated['fc_rate'][$i],
    //             ]);
    //         }
    //         $text = "Berhasil menambahkan tarif dan pembayaran";
    //         DB::commit();
    //     }catch(\Exception $e){
    //         DB::rollback();
    //         return response()->json($e->getMessage());
    //     }
    //     return json_encode(array('success' => true, 'message' => $text));
    // }
    
    // public function getPeriod()
    // {
    //     $period = Period::all();
    //     return $period->toJson();
    // }
    
    // public function getPath()
    // {
    //     $path = Path::all();
    //     return $path->toJson();
    // }
    
    // public function delete($id)
    // {
    //     $data = PaymentRate::findOrFail($id);
    //     $data->delete();

    //     return json_encode(array('success' => true, 'message' => "Berhasil menghapus tarif dan pembayaran"));
    // }
}
