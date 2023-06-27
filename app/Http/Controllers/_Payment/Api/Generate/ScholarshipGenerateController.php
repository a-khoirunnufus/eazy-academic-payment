<?php

namespace App\Http\Controllers\_Payment\API\Generate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\Scholarship;
use App\Models\Payment\ScholarshipReceiver;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentDetail;
use App\Http\Requests\Payment\Scholarship\ScholarshipReceiverRequest;
use App\Models\Student;
use App\Models\Year;
use DB;

class ScholarshipGenerateController extends Controller
{
    
    public function getActiveSchoolYearCode(){
        return 22231;
    }

    public function getReferenceTable(){
        return 'ms_scholarship_receiver';
    }

    public function index(Request $request)
    {
        $query = ScholarshipReceiver::query();
        $query = $query->with('period','student','scholarship')->where('msr_status',1)->orderBy('msr_id');
        return datatables($query)->toJson();
    }
    
    public function store($id){
        $data = ScholarshipReceiver::with('period','scholarship','student')->findorfail($id);
        $payment = Payment::where('student_number',$data->student_number)->where('prr_school_year',$data->period->msy_code)->first();
        if(!$payment){
            $text = "Tagihan tidak ditemukan";
            return json_encode(array('success' => false, 'message' => $text));
        }
        DB::beginTransaction();
        try{
            PaymentDetail::create([
                'prr_id' => $payment->prr_id,
                'prrd_component' => $data->scholarship->ms_name,
                'prrd_amount' => $data->msr_nominal,
                'is_plus' => 0,
                'type' => 'discount',
                'reference_table' => $this->getReferenceTable(),
                'reference_id' => $data->msr_id,
            ]);

            $payment->update([
                'prr_total' => $payment->prr_total-$data->msr_nominal,
                'prr_paid_net' => $payment->prr_paid_net-$data->msr_nominal,
            ]);

            $data->update(['msr_status_generate' => 1]);

            // update payment bill case: 1(kalau dia udh lunas gimana) 2(kalau dia belum lunas gimana) 3(kalau dia cicilan, motong yg mana?)
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json($e->getMessage());
        }
        $text = "Berhasil generate beasiswa mahasiswa ".$data->student->fullname;
        return json_encode(array('success' => true, 'message' => $text));
    }
    
    public function generate(Request $request)
    {
        return $this->store($request->msr_id);
    }
    
    public function generateBulk()
    {
        $query = ScholarshipReceiver::where('msr_status',1)->get();
        foreach($query as $item){
            $this->store($item->msr_id);
        }
        $text = "Generate beasiswa mahasiswa berhasil dieksekusi";
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function delete($id)
    {
        $data = ScholarshipReceiver::with('period','scholarship','student')->findorfail($id);
        $payment = Payment::where('student_number',$data->student_number)->where('prr_school_year',$data->period->msy_code)->first();
        if(!$payment){
            $text = "Tagihan tidak ditemukan";
            return json_encode(array('success' => false, 'message' => $text));
        }
        DB::beginTransaction();
        try{
            $paymentDetail = PaymentDetail::where('prr_id',$payment->prr_id)->where('reference_table',$this->getReferenceTable())->where('reference_id',$data->msr_id)->first();
            $payment->update([
                'prr_total' => $payment->prr_total+$paymentDetail->prrd_amount,
                'prr_paid_net' => $payment->prr_paid_net+$paymentDetail->prrd_amount,
            ]);
            $paymentDetail->delete();
            
            $data->update(['msr_status_generate' => 0]);

            // update payment bill case: 1(kalau dia udh lunas gimana) 2(kalau dia belum lunas gimana) 3(kalau dia cicilan, motong yg mana?)
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json($e->getMessage());
        }
        $text = "Berhasil menghapus beasiswa mahasiswa ".$data->student->fullname;
        return json_encode(array('success' => true, 'message' => $text));
    }
    
    public function deleteBulk()
    {
        $query = ScholarshipReceiver::where('msr_status',1)->get();
        foreach($query as $item){
            $this->delete($item->msr_id);
        }
        $text = "Delete beasiswa mahasiswa berhasil dieksekusi";
        return json_encode(array('success' => true, 'message' => $text));
    }
    
}
