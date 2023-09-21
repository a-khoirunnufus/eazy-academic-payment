<?php

namespace App\Http\Controllers\_Payment\API\Generate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\Discount;
use App\Models\Payment\DiscountReceiver;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentDetail;
use App\Http\Requests\Payment\Discount\DiscountReceiverRequest;
use App\Models\Payment\Student;
use App\Models\Payment\Year;
use App\Traits\Payment\LogActivity;
use App\Traits\Payment\General;
use App\Enums\Payment\LogStatus;
use DB;

class DiscountGenerateController extends Controller
{
    use LogActivity, General;

    public function getReferenceTable(){
        return 'ms_discount_receiver';
    }

    public function index(Request $request)
    {
        $query = DiscountReceiver::query();
        $query = $query->with('period','student','newStudent','discount')->where('mdr_status',1)->orderBy('mdr_id');
        return datatables($query)->toJson();
    }

    public function store($id,$log_id){
        $data = DiscountReceiver::with('period','discount','student','newStudent')->findorfail($id);
        if(!$data->student){
            $payment = Payment::where('reg_id',$data->reg_id)->where('prr_school_year',$data->period->msy_code)->first();
        }else{
            $payment = Payment::where('student_number',$data->student_number)->where('prr_school_year',$data->period->msy_code)->first();
        }
        if(!$payment){
            $text = "Tagihan tidak ditemukan";
            $this->addToLogDetail($log_id,$this->getLogTitle($data->student,$data->newStudent,$text),LogStatus::Failed);
            return json_encode(array('success' => false, 'message' => $text));
        }
        if($data->mdr_status_generate == 1){
            $text = "Potongan telah digenerate";
            $this->addToLogDetail($log_id,$this->getLogTitle($data->student,$data->newStudent,$text),LogStatus::Failed);
            return json_encode(array('success' => false, 'message' => $text));
        }
        DB::beginTransaction();
        try{
            PaymentDetail::create([
                'prr_id' => $payment->prr_id,
                'prrd_component' => $data->discount->md_name,
                'prrd_amount' => $data->mdr_nominal,
                'is_plus' => 0,
                'type' => 'discount',
                'reference_table' => $this->getReferenceTable(),
                'reference_id' => $data->mdr_id,
            ]);

            $payment->update([
                'prr_total' => $payment->prr_total-$data->mdr_nominal,
                'prr_paid_net' => $payment->prr_paid_net-$data->mdr_nominal,
            ]);

            $data->update(['mdr_status_generate' => 1,'prr_id'=> $payment->prr_id]);
            $this->addToLogDetail($log_id,$this->getLogTitle($data->student,$data->newStudent),LogStatus::Success);
            // update payment bill case: 1(kalau dia udh lunas gimana) 2(kalau dia belum lunas gimana) 3(kalau dia cicilan, motong yg mana?)
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            $this->addToLogDetail($log_id,$this->getLogTitle($data->student,$data->newStudent,$e->getMessage()),LogStatus::Failed);
            return response()->json($e->getMessage());
        }

        $text = "Berhasil generate potongan mahasiswa ".$this->getStudentName($data->student,$data->newStudent);
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function generate(Request $request)
    {
        $log = $this->addToLog('Generate Potongan',$this->getAuthId(),LogStatus::Process,$request->url);
        $result = $this->store($request->mdr_id,$log->log_id);
        $this->updateLogStatus($log,$result);
        return $result;
    }

    public function generateBulk(Request $request)
    {
        $query = DiscountReceiver::where('mdr_status',1)->get();
        $log = $this->addToLog('Generate Bulk Potongan',$this->getAuthId(),LogStatus::Process,$request->url);
        foreach($query as $item){
            $this->store($item->mdr_id,$log->log_id);
        }
        $this->updateLogStatus($log,LogStatus::Success);
        $text = "Generate potongan mahasiswa berhasil dieksekusi";
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function delete(Request $request,$id)
    {
        $log = $this->addToLog('Delete Potongan',$this->getAuthId(),LogStatus::Process,$request->url);
        $result = $this->deleteProcess($id,$log->log_id);
        $this->updateLogStatus($log,$result);
        return $result;
    }

    public function deleteBulk(Request $request)
    {
        $log = $this->addToLog('Delete Bulk Potongan',$this->getAuthId(),LogStatus::Process,$request->url);
        $query = DiscountReceiver::where('mdr_status',1)->get();
        foreach($query as $item){
            $this->deleteProcess($item->mdr_id,$log->log_id);
        }
        $this->updateLogStatus($log,LogStatus::Success);
        $text = "Delete potongan mahasiswa berhasil dieksekusi";
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function deleteProcess($id,$log_id){
        $data = DiscountReceiver::with('period','discount','student','newStudent')->findorfail($id);
        $payment = Payment::where('prr_id',$data->prr_id)->first();
        if(!$payment){
            $text = "Tagihan tidak ditemukan";
            $this->addToLogDetail($log_id,$this->getLogTitle($data->student,$data->newStudent,$text),LogStatus::Failed);
            return json_encode(array('success' => false, 'message' => $text));
        }
        DB::beginTransaction();
        try{
            $paymentDetail = PaymentDetail::where('prr_id',$payment->prr_id)->where('reference_table',$this->getReferenceTable())->where('reference_id',$data->mdr_id)->first();
            if($paymentDetail){
                $payment->update([
                    'prr_total' => $payment->prr_total+$paymentDetail->prrd_amount,
                    'prr_paid_net' => $payment->prr_paid_net+$paymentDetail->prrd_amount,
                ]);
                $paymentDetail->delete();
            }

            $data->update(['mdr_status_generate' => 0]);
            $this->addToLogDetail($log_id,$this->getLogTitle($data->student,$data->newStudent),LogStatus::Success);
            // update payment bill case: 1(kalau dia udh lunas gimana) 2(kalau dia belum lunas gimana) 3(kalau dia cicilan, motong yg mana?)
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            $this->addToLogDetail($log_id,$this->getLogTitle($data->student,$data->newStudent,$e->getMessage()),LogStatus::Failed);
            return response()->json($e->getMessage());
        }

        $text = "Berhasil menghapus potongan mahasiswa ".$this->getStudentName($data->student,$data->newStudent);
        return json_encode(array('success' => true, 'message' => $text));
    }

}
