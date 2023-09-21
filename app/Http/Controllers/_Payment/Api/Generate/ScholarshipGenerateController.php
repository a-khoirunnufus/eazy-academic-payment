<?php

namespace App\Http\Controllers\_Payment\API\Generate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\Scholarship;
use App\Models\Payment\ScholarshipReceiver;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentDetail;
use App\Http\Requests\Payment\Scholarship\ScholarshipReceiverRequest;
use App\Models\Payment\Student;
use App\Models\Payment\Year;
use App\Traits\Payment\LogActivity;
use App\Traits\Payment\General;
use App\Enums\Payment\LogStatus;
use DB;

class ScholarshipGenerateController extends Controller
{
    use LogActivity, General;

    public function getReferenceTable(){
        return 'ms_scholarship_receiver';
    }

    public function index(Request $request)
    {
        $query = ScholarshipReceiver::query();
        $query = $query->with('period','student','newStudent','scholarship')->where('msr_status',1)->orderBy('msr_id');
        return datatables($query)->toJson();
    }

    public function store($id,$log_id){
        $data = ScholarshipReceiver::with('period','scholarship','student','newStudent')->findorfail($id);

        if($data->msr_status_generate){
            $text = "Sudah Tergenerate";
            $this->addToLogDetail($log_id,$this->getLogTitle($data->student,$data->newStudent,$text),LogStatus::Failed);
            return json_encode(array('success' => false, 'message' => $text));
        }

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

        DB::beginTransaction();
        try{
            PaymentDetail::create([
                'prr_id' => $payment->prr_id,
                'prrd_component' => $data->scholarship->ms_name,
                'prrd_amount' => $data->msr_nominal,
                'is_plus' => 0,
                'type' => 'scholarship',
                'reference_table' => $this->getReferenceTable(),
                'reference_id' => $data->msr_id,
            ]);

            $payment->update([
                'prr_total' => $payment->prr_total-$data->msr_nominal,
                'prr_paid_net' => $payment->prr_paid_net-$data->msr_nominal,
            ]);

            $data->update(['msr_status_generate' => 1,'prr_id'=> $payment->prr_id]);

            $this->addToLogDetail($log_id,$this->getLogTitle($data->student,$data->newStudent),LogStatus::Success);
            // update payment bill case: 1(kalau dia udh lunas gimana) 2(kalau dia belum lunas gimana) 3(kalau dia cicilan, motong yg mana?)
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            $this->addToLogDetail($log_id,$this->getLogTitle($data->student,$data->newStudent,$e->getMessage()),LogStatus::Failed);
            return response()->json($e->getMessage());
        }

        $text = "Berhasil generate beasiswa mahasiswa ".$this->getStudentName($data->student,$data->newStudent);
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function generate(Request $request)
    {
        $log = $this->addToLog('Generate Beasiswa',$this->getAuthId(),LogStatus::Process,$request->url);
        $result = $this->store($request->msr_id,$log->log_id);
        $this->updateLogStatus($log,$result);
        return $result;
    }

    public function generateBulk(Request $request)
    {
        $query = ScholarshipReceiver::where('msr_status',1)->get();
        $log = $this->addToLog('Generate Bulk Beasiswa',$this->getAuthId(),LogStatus::Process,$request->url);
        foreach($query as $item){
            $result = $this->store($item->msr_id,$log->log_id);
        }
        $this->updateLogStatus($log,LogStatus::Success);
        $text = "Generate beasiswa mahasiswa berhasil dieksekusi";
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function delete(Request $request,$id)
    {
        $log = $this->addToLog('Delete Beasiswa',$this->getAuthId(),LogStatus::Process,$request->url);
        $result = $this->deleteProcess($id,$log->log_id);
        $this->updateLogStatus($log,$result);
        return $result;
    }

    public function deleteBulk(Request $request)
    {
        $log = $this->addToLog('Delete Bulk Beasiswa',$this->getAuthId(),LogStatus::Process,$request->url);
        $query = ScholarshipReceiver::where('msr_status',1)->get();
        foreach($query as $item){
            $this->deleteProcess($item->msr_id,$log->log_id);
        }
        $this->updateLogStatus($log,LogStatus::Success);
        $text = "Delete beasiswa mahasiswa berhasil dieksekusi";
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function deleteProcess($id,$log_id){
        $data = ScholarshipReceiver::with('period','scholarship','student','newStudent')->findorfail($id);
        $payment = Payment::where('prr_id',$data->prr_id)->first();
        if(!$payment){
            $text = "Tagihan tidak ditemukan";
            $this->addToLogDetail($log_id,$this->getLogTitle($data->student,$data->newStudent,$text),LogStatus::Failed);
            return json_encode(array('success' => false, 'message' => $text));
        }
        DB::beginTransaction();
        try{
            $paymentDetail = PaymentDetail::where('prr_id',$payment->prr_id)->where('reference_table',$this->getReferenceTable())->where('reference_id',$data->msr_id)->first();
            if($paymentDetail){
                $payment->update([
                    'prr_total' => $payment->prr_total+$paymentDetail->prrd_amount,
                    'prr_paid_net' => $payment->prr_paid_net+$paymentDetail->prrd_amount,
                ]);
                $paymentDetail->delete();
            }

            $data->update(['msr_status_generate' => 0]);
            $this->addToLogDetail($log_id,$this->getLogTitle($data->student,$data->newStudent),LogStatus::Success);
            // update payment bill case: 1(kalau dia udh lunas gimana) 2(kalau dia belum lunas gimana) 3(kalau dia cicilan, motong yg mana?)
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            $this->addToLogDetail($log_id,$this->getLogTitle($data->student,$data->newStudent,$e->getMessage()),LogStatus::Failed);
            return response()->json($e->getMessage());
        }

        $text = "Berhasil menghapus beasiswa mahasiswa ".$this->getStudentName($data->student,$data->newStudent);
        return json_encode(array('success' => true, 'message' => $text));
    }

}
