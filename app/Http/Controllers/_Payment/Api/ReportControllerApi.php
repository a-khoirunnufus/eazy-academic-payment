<?php

namespace App\Http\Controllers\_Payment\Api;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Payment\Payment;
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

        $dataStudent = [];
        $spesifikProdi = $request->get('prodi');
        $prodi_filter_angkatan = $request->get('prodi_filter_angkatan');
        $prodi_search_filter = $request->get('prodi_search_filter');

        foreach ($year as $tahun) {
            $list_studyProgram = $this->getColomns('ms.*')->where('msy.msy_id', '=', $tahun->msy_id);
            if($spesifikProdi !== '#ALL' && $spesifikProdi !== NULL){
                $list_studyProgram = $list_studyProgram->where('ms.studyprogram_id', '=', $spesifikProdi);
            }
            $list_studyProgram = $list_studyProgram->distinct()->get();

            foreach ($list_studyProgram as $studyProgram) {
                // $listStudent = $this->getColomns('ms2.*','ms2.student_id', 'ms2.fullname');
                $listStudent = $this->getColomns('ms2.*');
                if($prodi_filter_angkatan !== '#ALL' && $prodi_filter_angkatan !== NULL){
                    $listStudent->where(DB::raw('SUBSTR(ms2.periode_masuk, 1, 4)'), '=', $prodi_filter_angkatan);
                }
                $listStudent->where('ms2.studyprogram_id', '=', $studyProgram->studyprogram_id)->where('msy.msy_id', '=', $tahun->msy_id)->distinct();
                
                $studyProgram->student = $listStudent->get();
                $studyProgram->year = $tahun;
                $studyProgram->faculty = Faculty::where('faculty_id', '=', $studyProgram->faculty_id)->get();
    
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
                            switch($pd->type){
                                case "component":
                                    $total_prrd_amount += $pd->prrd_amount;
                                    break;
                                case "denda":
                                    $denda += $pd->prrd_amount;
                                    break;
                                case "beasiswa":
                                    $beasiswa += $pd->prrd_amount;
                                    break;
                                case "potongan":
                                    $potongan += $pd->prrd_amount;
                                    break;
                            }
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
                        $list_payment->prr_amount = $total_prrd_amount + $denda;
                        $list_payment->prr_paid = $total_paid;
                        $list_payment->penalty = $denda;
                        $list_payment->schoolarsip = $beasiswa;
                        $list_payment->discount = $potongan;
                        $list_student->payment = $list_payment;
                    }

                    if($spesifikProdi !== '#ALL' && $spesifikProdi !== NULL){
                        $detail_prodi = $studyProgram;
                        unset($detail_prodi->student);
                        $list_student->studyprogram = $detail_prodi;
                        array_push($dataStudent, $list_student);
                    }
                }
                array_push($data, $studyProgram);
            }
        }

        if($spesifikProdi !== '#ALL' && $spesifikProdi !== NULL){
            if($prodi_search_filter !== '#ALL' && $prodi_search_filter !== NULL){
                $data_filter = [];
                foreach($dataStudent as $list){
                    $row = json_encode($list);
                    if(strpos($row, $prodi_search_filter)){
                        array_push($data_filter, $list);
                    }
                }
                return DataTables($data_filter)->toJson();
            }
            return DataTables($dataStudent)->toJson();
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

    function newStudent(Request $request){
        // $list_studyProgram = $this->getColomns('ms.studyprogram_id', 'ms.studyprogram_type', 'ms.studyprogram_name')->distinct()->get();
        $year = Year::query();
        if($request->get('data_filter') !== '#ALL' && $request->get('data_filter') !== NULL){
            $year = $year->where('msy_id', '=', $request->get('data_filter'));
        }
        $year = $year->get();
        $data = [];
        $id_faculty = $request->get('id_faculty');
        $id_prodi = $request->get('id_prodi');

        $dataStudent = [];
        $spesifikProdi = $request->get('prodi');
        $prodi_filter_angkatan = $request->get('prodi_filter_angkatan');
        $prodi_search_filter = $request->get('prodi_search_filter');

        foreach ($year as $tahun) {
            $list_studyProgram = $this->getNew('ms.*')->where('msy.msy_id', '=', $tahun->msy_id);
            if($spesifikProdi !== '#ALL' && $spesifikProdi !== NULL){
                $list_studyProgram = $list_studyProgram->where('ms.studyprogram_id', '=', $spesifikProdi);
            }
            if($id_faculty !== '#ALL' && $id_faculty !== NULL){
                $list_studyProgram = $list_studyProgram->where('ms.faculty_id', '=', $id_faculty);
            }
            if($id_prodi !== '#ALL' && $id_prodi !== NULL){
                $list_studyProgram = $list_studyProgram->where('ms.studyprogram_id', '=', $id_prodi);
            }
            $list_studyProgram = $list_studyProgram->distinct()->get();

            foreach ($list_studyProgram as $studyProgram) {
                // $listStudent = $this->getColomns('ms2.*','ms2.student_id', 'ms2.fullname');
                $listStudent = $this->getNew('p.*');
                if($prodi_filter_angkatan !== '#ALL' && $prodi_filter_angkatan !== NULL){
                    $listStudent->where(DB::raw('SUBSTR(ms2.periode_masuk, 1, 4)'), '=', $prodi_filter_angkatan);
                }
                $listStudent->where('ms.studyprogram_id', '=', $studyProgram->studyprogram_id)->where('msy.msy_id', '=', $tahun->msy_id)->distinct();
                
                $studyProgram->student = $listStudent->get();
                $studyProgram->year = $tahun;
                $studyProgram->faculty = Faculty::where('faculty_id', '=', $studyProgram->faculty_id)->get();
    
                foreach ($studyProgram->student as $list_student) {
                    $listPayment = $this->getNew('prr.*')
                        ->where('prr.student_number', '=', $list_student->student_number)
                        ->distinct()->get();
    
                    $denda = 0;
                    $beasiswa = 0;
                    $potongan = 0;
                    foreach ($listPayment as $list_payment) {
                        $listPaymentDetail = $this->getNew('prrd.*')
                            ->where('prrd.prr_id', '=', $list_payment->prr_id)
                            ->distinct()
                            ->get();
                        $total_prrd_amount = 0;
                        foreach ($listPaymentDetail as $pd) {
                            switch($pd->type){
                                case "component":
                                    $total_prrd_amount += $pd->prrd_amount;
                                    break;
                                case "denda":
                                    $denda += $pd->prrd_amount;
                                    break;
                                case "beasiswa":
                                    $beasiswa += $pd->prrd_amount;
                                    break;
                                case "potongan":
                                    $potongan += $pd->prrd_amount;
                                    break;
                            }
                        }
    
                        $listPaymentBill = $this->getNew('prrb.*')
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
                        $list_payment->prr_amount = $total_prrd_amount + $denda;
                        $list_payment->prr_paid = $total_paid;
                        $list_payment->penalty = $denda;
                        $list_payment->schoolarsip = $beasiswa;
                        $list_payment->discount = $potongan;
                        $list_student->payment = $list_payment;
                    }

                    if($spesifikProdi !== '#ALL' && $spesifikProdi !== NULL){
                        $detail_prodi = $studyProgram;
                        unset($detail_prodi->student);
                        $list_student->studyprogram = $detail_prodi;
                        array_push($dataStudent, $list_student);
                    }
                }
                array_push($data, $studyProgram);
            }
        }

        if($spesifikProdi !== '#ALL' && $spesifikProdi !== NULL){
            if($prodi_search_filter !== '#ALL' && $prodi_search_filter !== NULL){
                $data_filter = [];
                foreach($dataStudent as $list){
                    $row = json_encode($list);
                    if(strpos($row, $prodi_search_filter)){
                        array_push($data_filter, $list);
                    }
                }
                return DataTables($data_filter)->toJson();
            }
            return DataTables($dataStudent)->toJson();
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
    function oldStudentHistory($student_number, Request $request){
        $search = $request->get('search_filter');
        $data = $this->getColomns('prrb.*')->where('ms2.student_number', '=', $student_number)->distinct()->get();
        foreach($data as $items){
            $items->method = DB::select('SELECT prr_method FROM finance.payment_re_register WHERE prr_id = ?', [$items->prr_id])[0]->prr_method;
        }

        if($search !== '#ALL' && $search !== NULL){
            $data_filter = [];
            foreach($data as $list){
                $row = json_encode($list);
                if(strpos($row, $search)){
                    array_push($data_filter, $list);
                }
            }
            return DataTables($data_filter)->toJson();
        }
        
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

    function getNew(){
        $list_colomns = func_get_args();
        return DB::table('masterdata.ms_studyprogram as ms')
            ->select($list_colomns)
            ->join('pmb.register as r', 'r.reg_major_pass', '=', 'ms.studyprogram_id')
            ->join('pmb.participant as p', 'r.par_id = p.par_id')
            ->join('masterdata.ms_school_year as msy', 'msy.msy_id', '=', 'r.ms_school_year_id')
            ->join('finance.payment_re_register as prr', 'prr.reg_id', '=', 'r.reg_id')
            ->join('finance.payment_re_register_detail as prrd', 'prrd.prr_id', '=', 'prr.prr_id')
            ->join('finance.payment_re_register_bill as prrb', 'prrb.prr_id', '=', 'prr.prr_id');
    }
}
