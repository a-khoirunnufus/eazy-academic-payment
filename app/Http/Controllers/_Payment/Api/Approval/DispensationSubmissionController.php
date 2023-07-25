<?php

namespace App\Http\Controllers\_Payment\API\Approval;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student\DispensationSubmission;
use App\Http\Requests\Payment\Dispensation\DispensationSubmission as RequestDispensation;
use App\Models\Payment\Payment;
use App\Models\Studyprogram;
use DB;

class DispensationSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->input('custom_filters');
        $filters = array_filter($filters, function ($item) {
            return !is_null($item) && $item != '#ALL';
        });

        $query = DispensationSubmission::query();
        $query = $query->with('period','student','payment');
        
        if(isset($filters['year'])){
            $query = $query->where('mds_school_year', '=', $filters['year']);
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
            $query = $query->where('mds_status', '=', $filters['status']);
        }
        
        $query = $query->whereHas('payment', function($q){
            $q->whereColumn('finance.payment_re_register.prr_school_year', 'finance.ms_dispensation_submission.mds_school_year');
        })->orderBy('finance.ms_dispensation_submission.mds_id');
        // dd($query->get());
        return datatables($query)->toJson();
    }
    
    public function store(RequestDispensation $request)
    {
        $validated = $request->validated();
        
        $data = DispensationSubmission::with('payment')->whereHas('payment', function($q){
            $q->whereColumn('finance.payment_re_register.prr_school_year', 'finance.ms_dispensation_submission.mds_school_year');
        })->where('mds_id',$validated['msc_id'])->first();

        if(!$data->payment){
            return json_encode(array('success' => false, 'message' => 'Data tagihan tidak ditemukan'));
        }

        DB::beginTransaction();
        try{
            $payment = Payment::findorfail($data->payment->prr_id);
            $payment->update(['prr_dispensation_date' => $validated['prr_dispensation_date']]);

            $data->update(['mds_deadline' => $validated['prr_dispensation_date'],'mds_status' => 1]);
            $text = "Berhasil mengupdate pengajuan dispensasi";
            
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json($e->getMessage());
        }
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function decline(request $request)
    {
        $data = DispensationSubmission::findorfail($request->mds_id);
        $data->update(['mds_status' => 0,'mds_decline_reason' => $request->mds_decline_reason]);
        
        $text = "Berhasil menolak pengajuan dispensasi";
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function getProdi($faculty){
        $study_program = Studyprogram::where('faculty_id', '=', $faculty);

        return $study_program->get();
    }
}
