<?php

namespace App\Http\Controllers\_Payment\Api\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\PaymentRate;
use App\Models\Payment\PaymentCredit;
use App\Models\Payment\PaymentComponent;
use App\Models\Payment\Component;
use App\Models\Payment\CreditSchema;
use App\Http\Requests\Payment\Settings\PaymentRateRequest;
use App\Http\Requests\Payment\Settings\PaymentRateUpdateRequest;
use App\Models\Period;
use App\Models\Path;
use DB;

class PaymentRatesController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentRate::query();
        $query = $query->with('credit','path','period','studyProgram','component')->orderBy('f_id');
        // $query = $this->loadRelation($query, $request, ['faculty']);
        // $query = $this->applyFilter($query, $request, [
        //     'studyprogram_active_status', 'faculty_id'
        // ]);
        // dd($query->get());
        return datatables($query)->toJson();
    }
    
    public function getPeriod()
    {
        $period = Period::all();
        return $period->toJson();
    }
    
    public function getPath()
    {
        $path = Path::all();
        return $path->toJson();
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

    public function store(PaymentRateRequest $request)
    {
        $validated = $request->validated();
        // dd($validated);
        DB::beginTransaction();
        try{
            $paymentRate = PaymentRate::create([
                'f_period_id' => $validated['f_period_id'],
                'f_studyprogram_id' => $validated['f_studyprogram_id'],
                'f_path_id' => $validated['f_path_id'],
                'f_jenis_perkuliahan_id' => $validated['f_jenis_perkuliahan_id'],
            ]);
            $f_id = $paymentRate->f_id;

            $count = count($validated['cs_id']);
            for ($i=0; $i < $count; $i++) { 
                PaymentCredit::create([
                    'f_id' => $f_id,
                    'cs_id' => $validated['cs_id'][$i]
                ]);
            }
            
            $count = count($validated['msc_id']);
            for ($i=0; $i < $count; $i++) { 
                PaymentComponent::create([
                    'f_id' => $f_id,
                    'msc_id' => $validated['msc_id'][$i],
                    'fc_rate' => $validated['fc_rate'][$i],
                ]);
            }
            $text = "Berhasil menambahkan tarif dan pembayaran";
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json($e->getMessage());
        }
        return json_encode(array('success' => true, 'message' => $text));
    }
    
    public function update(PaymentRateUpdateRequest $request)
    {
        $validated = $request->validated();
        // dd($validated);
        DB::beginTransaction();
        try{
            $paymentRate = PaymentRate::findorfail($validated['f_id']);
            $f_id = $paymentRate->f_id;

            $count = count($validated['msc_id']);
            for ($i=0; $i < $count; $i++) { 
                if($validated['fc_id'][$i] == 0){
                    PaymentComponent::create([
                        'f_id' => $f_id,
                        'msc_id' => $validated['msc_id'][$i],
                        'fc_rate' => $validated['fc_rate'][$i]
                    ]);
                }else{
                    $data = PaymentComponent::findorfail($validated['fc_id'][$i]);
                    $data->update([
                        'msc_id' => $validated['msc_id'][$i],
                        'fc_rate' => $validated['fc_rate'][$i]
                    ]);
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
        $data = PaymentComponent::findOrFail($id);
        $data->delete();

        return json_encode(array('success' => true, 'message' => "Berhasil menghapus komponen"));
    }
    
    public function delete($id)
    {
        $data = PaymentRate::findOrFail($id);
        $data->delete();

        return json_encode(array('success' => true, 'message' => "Berhasil menghapus tarif dan pembayaran"));
    }
}
