<?php

namespace App\Http\Controllers\_Payment\Api\Generate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Studyprogram;
use App\Models\Faculty;
use App\Models\PeriodPath;
use App\Models\Student;
use App\Models\ActiveYear;
use App\Models\Year;
use App\Models\Payment\ComponentDetail;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentBill;
use App\Models\Payment\PaymentDetail;
use Carbon\Carbon;
use DB;

class StudentInvoiceController extends Controller
{
    public function index(){
        
        $query = Faculty::with('studyProgram')->orderBy('faculty_name')->get();
        $result = collect();
        foreach($query as $item){
            $collection = collect();
            $data = ['faculty' => $item,'study_program' => null,'total' => null];
            $result->push($data);
            if($item->studyProgram){
                foreach($item->studyProgram as $sp){
                    $data = ['faculty' => null,'study_program' => $sp,'total' => null];
                    $result->push($data);
                }
            }
        }
        return datatables($result)->toJson();
    }

    public function detail(Request $request){
        // dd($request);
        $data['f'] = $request->query()['f'];
        $data['sp'] = $request->query()['sp'];
        $query = Student::query();
        $query = $query->with('lectureType','period','payment')
        ->join('masterdata.ms_studyprogram as sp','sp.studyprogram_id','hr.ms_student.studyprogram_id')
        
        ->select('hr.ms_student.*');
        if($data['f'] != 0 && $data['f']){
            $query = $query->where('sp.faculty_id',$data['f']);
        }
        if($data['sp'] != 0 && $data['sp']){
            $query = $query->where('sp.studyprogram_id',$data['sp']);
        }
        // dd($query->get());
        return datatables($query->get())->toJson();
    }

    // MANUAL
    public function getActiveSchoolYear(){
        return "2022/2023 - Ganjil";
    }
    
    public function header(Request $request){
        // dd($request);
        $data['f'] = $request->query()['f'];
        $data['sp'] = $request->query()['sp'];

        // dd($data);
        $faculty = Faculty::find($data['f']);
        if($data['sp'] != 0){
            $studyProgram = Studyprogram::with('faculty')->find($data['sp']);
        }else{
            $studyProgram = "-";
        }
        $date = Carbon::today()->toDateString();
        $activeSchoolYear = $this->getActiveSchoolYear();

        if($faculty){
            $header['faculty'] = $faculty->faculty_name;
        }else{
            $header['faculty'] = $studyProgram->faculty->faculty_name;
        }
        $header['study_program'] = ($data['sp'] != 0) ? $studyProgram->studyprogram_type.' '.$studyProgram->studyprogram_name : $studyProgram;
        $header['active'] = $activeSchoolYear;

        // dd($query->get());
        return $header;
    }

    public function studentGenerate(Request $request){
        $student = Student::with('getComponent')->findorfail($request['student_number']);

        $components = $student->getComponent()
        ->where('path_id',$student->path_id)
        ->where('period_id',$student->period_id)
        ->where('msy_id',$student->msy_id)
        ->where('mlt_id',$student->mlt_id)
        ->get();

        if($components){
            $prr_total = 0;
            foreach($components as $item){
                $prr_total = $prr_total+$item->cd_fee;
            }
            DB::beginTransaction();
            try{
                $payment = Payment::create([
                    'prr_status' => 'belum lunas',
                    'prr_total' => $prr_total,
                    'prr_paid_net' => $prr_total,
                    'student_number' => $student->student_number
                ]);

                foreach($components as $item){
                    PaymentDetail::create([
                        'prr_id' => $payment->prr_id,
                        'prrd_component' => $item->component->msc_name,
                        'prrd_amount' => $item->cd_fee
                    ]);
                }
                DB::commit();
            }catch(\Exception $e){
                DB::rollback();
                return response()->json($e->getMessage());
            }
        }
        $text = "Berhasil generate tagihan mahasiswa ".$student->fullname;
        return json_encode(array('success' => true, 'message' => $text));

    }
    
}
