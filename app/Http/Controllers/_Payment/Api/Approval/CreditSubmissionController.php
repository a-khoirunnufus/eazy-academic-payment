<?php

namespace App\Http\Controllers\_Payment\API\Approval;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student\CreditSubmission;
use App\Http\Requests\Payment\Discount\DiscountSubmission;
use App\Models\Payment\PaymentBill;
use DB;

class CreditSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = CreditSubmission::query();
        $query = $query->with('period','student','payment')->whereHas('payment', function($q){
            $q->whereColumn('finance.payment_re_register.prr_school_year', 'finance.ms_credit_submission.mcs_school_year');
        })->orderBy('finance.ms_credit_submission.mcs_id');
        // dd($query->get());
        return datatables($query)->toJson();
    }
    
    public function store(DiscountSubmission $request)
    {
        $validated = $request->validated();
        $total = 0;
        foreach($validated['cse_amount'] as $item ){
            $total=$total+$item;
        }
        $data = CreditSubmission::with('payment')->whereHas('payment', function($q){
            $q->whereColumn('finance.payment_re_register.prr_school_year', 'finance.ms_credit_submission.mcs_school_year');
        })->where('mcs_id',$validated['msc_id'])->first();

        if(!$data->payment){
            return json_encode(array('success' => false, 'message' => 'Data tagihan tidak ditemukan'));
        }
        if($total != $data->payment->prr_total){
            return json_encode(array('success' => false, 'message' => 'Total nominal cicilan tidak sesuai tagihan, Total saat ini: Rp.'.number_format($total, 2)));
        }

        // dd($validated['cse_order'][1]);
        DB::beginTransaction();
        try{
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

            $data->update(['mcs_status' => 1]);
            $text = "Berhasil mengupdate pengajuan cicilan";
            
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json($e->getMessage());
        }
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function decline(request $request)
    {
        $data = CreditSubmission::findorfail($request->mcs_id);
        $data->update(['mcs_status' => 0,'mcs_decline_reason' => $request->mcs_decline_reason]);
        
        $text = "Berhasil menolak pengajuan cicilan";
        return json_encode(array('success' => true, 'message' => $text));
    }
}
