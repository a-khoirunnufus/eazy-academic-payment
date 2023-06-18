<?php

namespace App\Http\Controllers\_Payment\Api;

use App\Http\Controllers\Controller;
use App\Models\Studyprogram;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ReportControllerApi extends Controller
{
    //
    function oldStudent(Request $request)
    {
        // $list_studyProgram = $this->getColomns('ms.studyprogram_id', 'ms.studyprogram_type', 'ms.studyprogram_name')->distinct()->get();
        $year = Year::query();
        if($request->get('data_filter') !== '#ALL' && $request->get('data_filter') !== NULL){
            $year = $year->where('msy_id', '=', $request->get('data_filter'));
        }
        $year = $year->get();
        $data = [];

        foreach ($year as $tahun) {
            $list_studyProgram = $this->getColomns('ms.*')->where('msy.msy_id', '=', $tahun->msy_id)->distinct()->get();

            foreach ($list_studyProgram as $studyProgram) {
                // $listStudent = $this->getColomns('ms2.*','ms2.student_id', 'ms2.fullname');
                $listStudent = $this->getColomns('ms2.*');
                $listStudent->where('ms2.studyprogram_id', '=', $studyProgram->studyprogram_id)->where('msy.msy_id', '=', $tahun->msy_id)->distinct();
                $studyProgram->student = $listStudent->get();
                $studyProgram->year = $tahun;
    
                foreach ($studyProgram->student as $list_student) {
                    $listPayment = $this->getColomns('prr.*')
                        ->where('prr.student_number', '=', $list_student->student_number)
                        ->distinct()->get();
    
                    $denda = 0;
                    $beasiswa = 0;
                    $potongan = 0;
                    foreach ($listPayment as $list_payment) {
                        $listPaymentDetail = $this->getColomns('prrd.*')
                            ->where('prrd.prr_id', '=', $list_payment->prr_id)
                            ->distinct()
                            ->get();
                        $total_prrd_amount = 0;
                        foreach ($listPaymentDetail as $pd) {
                            $total_prrd_amount += $pd->prrd_amount;
                        }
    
                        $listPaymentBill = $this->getColomns('prrb.*')
                            ->where('prrb.prr_id', '=', $list_payment->prr_id)
                            ->distinct()
                            ->get();
                        $total_paid = 0;
                        foreach ($listPaymentBill as $pb) {
                            $total_paid += $pb->prrb_amount;
                        }
    
                        $list_payment->payment_detail = $listPaymentDetail;
                        $list_payment->payment_bill = $listPaymentBill;
                        $list_payment->prr_total = ($total_prrd_amount + $denda) - ($beasiswa + $potongan);
                        $list_payment->prr_amount = $total_prrd_amount;
                        $list_payment->prr_paid = $total_paid;
                        $list_payment->penalty = $denda;
                        $list_payment->schoolarsip = $beasiswa;
                        $list_payment->discount = $potongan;
                        $list_student->payment = $list_payment;
                        // $studyProgram->student = $list_student;
                    }
                }
                array_push($data, $studyProgram);
            }
        }

        if($request->get('search_filter') !== '#ALL' && $request->get('search_filter') !== NULL){
            $data_filter = [];
            foreach($data as $list){
                $row = json_encode($list);
                $find = $request->get('search_filter');
                if(strpos($row, $find)){
                    array_push($data_filter, $list);
                }
            }
            return DataTables($data_filter)->toJson();
        }
        
        // return json_encode($data);
        return DataTables($data)->toJson();
    }

    function getColomns()
    {
        $list_colomns = func_get_args();
        return DB::table('masterdata.ms_studyprogram as ms')
            ->select($list_colomns)
            ->join('hr.ms_student as ms2', 'ms2.studyprogram_id', '=', 'ms.studyprogram_id')
            ->join('finance.payment_re_register as prr', 'prr.student_number', '=', 'ms2.student_number')
            ->join('masterdata.ms_school_year as msy', 'msy.msy_id', '=', 'ms2.msy_id')
            ->join('finance.payment_re_register_detail as prrd', 'prrd.prr_id', '=', 'prr.prr_id')
            ->join('finance.payment_re_register_bill as prrb', 'prrb.prr_id', '=', 'prr.prr_id');
    }
}
