<?php

namespace App\Http\Controllers\_Payment\API\Approval;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\CreditSubmission;
use App\Http\Requests\Payment\Credit\CreditSubmission as CreditRequest;
use App\Models\Payment\PaymentBill;
use App\Models\Payment\Payment;
use App\Models\Payment\Studyprogram;
use App\Models\Payment\Student;
use App\Traits\Payment\LogActivity;
use App\Traits\Payment\General;
use App\Enums\Payment\LogStatus;
use DB;

class CreditSubmissionController extends Controller
{
    use LogActivity, General;

    public function index(Request $request)
    {
        $filters = $request->input('custom_filters');
        $filters = array_filter($filters, function ($item) {
            return !is_null($item) && $item != '#ALL';
        });

        $query = CreditSubmission::query();
        $query = $query->with('period','student','payment');

        if(isset($filters['year'])){
            $query = $query->where('mcs_school_year', '=', $filters['year']);
        }

        if(isset($filters['faculty'])){
            $query = $query->whereHas('student.studyProgram.faculty', function($q) use($filters) {
                $q->where('faculty_id', '=', $filters['faculty']);
            });
        }

        if(isset($filters['prodi'])){
            $query = $query->whereHas('student.studyProgram', function($q) use($filters) {
                $q->where('studyprogram_id', '=', $filters['prodi']);
            });
        }

        if(isset($filters['status'])){
            $query = $query->where('mcs_status', '=', $filters['status']);
        }

        $query = $query->whereHas('payment', function($q){
            $q->whereColumn('finance.payment_re_register.prr_school_year', 'finance.ms_credit_submission.mcs_school_year');
        })->orderBy('finance.ms_credit_submission.mcs_id');
        // dd($query->get());
        return datatables($query)->toJson();
    }

    public function store(CreditRequest $request)
    {
        $log = $this->addToLog('Approve Pengajuan Cicilan',$this->getAuthId(),LogStatus::Process,$request->url);
        $result = $this->storeProcess($request,$log->log_id);
        $this->updateLogStatus($log,$result);
        return $result;
    }

    public function storeProcess(CreditRequest $request,$log_id){

        $validated = $request->validated();
        $total = 0;
        foreach($validated['cse_amount'] as $item ){
            $total=$total+$item;
        }
        $data = CreditSubmission::with('payment','student')->whereHas('payment', function($q){
            $q->whereColumn('finance.payment_re_register.prr_school_year', 'finance.ms_credit_submission.mcs_school_year');
        })->where('mcs_id',$validated['msc_id'])->first();

        if(!$data->payment){
            $text = 'Data tagihan tidak ditemukan';
            $this->addToLogDetail($log_id,$this->getLogTitleStudent($data->student,null,$text),LogStatus::Failed);
            return json_encode(array('success' => false, 'message' => $text));
        }
        if($total != $data->payment->prr_total){
            $text = 'Total nominal cicilan tidak sesuai tagihan, Total saat ini: Rp.'.number_format($total, 2);
            $this->addToLogDetail($log_id,$this->getLogTitleStudent($data->student,null,$text),LogStatus::Failed);
            return json_encode(array('success' => false, 'message' => $text));
        }

        // if student has ever paid this bill, then reject this credit submission approval
        if ($data->payment->computed_has_paid_bill) {
            $text = 'Tidak dapat mengubah skema cicilan, terdapat tagihan yang telah dibayar oleh mahasiswa.';
            $data->update(['mcs_status' => 0, 'mcs_decline_reason' => $text]);
            $this->addToLogDetail($log_id,$this->getLogTitle($data->student,null,$text),LogStatus::Failed);
            return json_encode(array('success' => false, 'message' => $text));
        }

        // dd($validated['cse_order'][1]);
        DB::beginTransaction();
        try{
            $payment = Payment::findorfail($data->payment->prr_id);
            $payment->update(['prr_status' => 'kredit']);

            PaymentBill::where('prr_id', '=', $data->payment->prr_id)->delete();

            foreach ($validated['cse_amount'] as $key => $item) {
                $temp_amount = (int) $item;

                PaymentBill::create([
                    'prr_id' => $data->payment->prr_id,
                    'prrb_status' => 'belum lunas',
                    'prrb_due_date' => $validated['cse_deadline'][$key],
                    'prrb_amount' => $temp_amount,
                    'prrb_order' => $validated['cse_order'][$key],
                ]);
            }

            $data->update(['mcs_status' => 1,'prr_id'=> $data->payment->prr_id,'cs_id'=> $validated['cs_id']]);
            $text = "Berhasil mengupdate pengajuan cicilan";
            $this->addToLogDetail($log_id,$this->getLogTitleStudent($data->student,null),LogStatus::Success);
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            $this->addToLogDetail($log_id,$this->getLogTitleStudent($data->student,null,$e->getMessage()),LogStatus::Failed);
            return response()->json($e->getMessage());
        }
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function decline(request $request)
    {
        $log = $this->addToLog('Decline Pengajuan Cicilan',$this->getAuthId(),LogStatus::Process,$request->url);
        $data = CreditSubmission::with('student')->findorfail($request->mcs_id);
        $data->update(['mcs_status' => 0,'mcs_decline_reason' => $request->mcs_decline_reason]);
        $this->addToLogDetail($log->log_id,$this->getLogTitleStudent($data->student,null),LogStatus::Success);
        $text = "Berhasil menolak pengajuan cicilan";
        $this->updateLogStatus($log,LogStatus::Success);
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function getProdi($faculty){
        $study_program = Studyprogram::where('faculty_id', '=', $faculty);

        return $study_program->get();
    }

    public function getStudent(){
        $data = Student::all();
        return $data->toJson();
    }
}
