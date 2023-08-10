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
use App\Models\Payment\MasterJob;
use App\Jobs\GenerateInvoice;
use App\Jobs\GenerateBulkInvoice;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use DB;

class StudentInvoiceController extends Controller
{
    public function index(Request $request){
        $activeSchoolYearCode = $this->getActiveSchoolYearCode();

        $query = Faculty::with('studyProgram')->orderBy('faculty_name')->get();
        $student = Student::with(['payment' => function($query) use ($activeSchoolYearCode){
            $query->where('prr_school_year',$activeSchoolYearCode);
        }])->get();


        $result = collect();
        foreach($query as $item){
            $collection = collect();
            $arrSp = [];
            $total_invoice = 0;
            $total_generate = 0;
            if($item->studyProgram){
                foreach($item->studyProgram as $sp){
                    $arrSp[] = $sp->studyprogram_id;
                }
                $filter = $student->whereIn('studyprogram_id', $arrSp);
                $total_student = $student->whereIn('studyprogram_id', $arrSp)->count();
                foreach($filter as $t){
                    if($t->payment){
                        $total_invoice = $total_invoice+$t->payment->prr_total;
                        $total_generate = $total_generate+1;
                    }
                }
            }
            $data = ['faculty' => $item,'study_program' => null,'total_student' => $total_student,'total_invoice' => $total_invoice,'total_generate' => $total_generate];
            $result->push($data);
            if($item->studyProgram){
                foreach($item->studyProgram as $sp){
                    $filter = $student->whereIn('studyprogram_id', $sp->studyprogram_id);
                    $total_student = $student->whereIn('studyprogram_id', $sp->studyprogram_id)->count();
                    $total_invoice = 0;
                    $total_generate = 0;
                    foreach($filter as $t){
                        if($t->payment){
                            $total_invoice = $total_invoice+$t->payment->prr_total;
                            $total_generate = $total_generate+1;
                        }
                    }
                    $data = ['faculty' => null,'study_program' => $sp,'total_student' => $total_student,'total_invoice' => $total_invoice,'total_generate' => $total_generate];
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
        $query = $query->with('lectureType','period','payment','path','year','studyProgram')
        ->join('masterdata.ms_studyprogram as sp','sp.studyprogram_id','hr.ms_student.studyprogram_id')

        ->select('hr.ms_student.*');
        if($data['f'] != 0 && $data['f']){
            $query = $query->where('sp.faculty_id',$data['f']);
        }
        if($data['sp'] != 0 && $data['sp']){
            $query = $query->where('sp.studyprogram_id',$data['sp']);
        }
        if($request->query('year') !== "all"){
            $query = $query->where('msy_id', '=', $request->query('year'));
            // $query = $query->whereIn('studyprogram_id', $year);
        }
        if($request->query('path') !== "all"){
            $query = $query->where('path_id', '=', $request->query('path'));
        }
        if($request->query('period') !== "all"){
            $query = $query->where('period_id', '=', $request->query('period'));
        }
        // dd($query->get());
        return datatables($query->get())->toJson();
    }

    // MANUAL
    public function getActiveSchoolYear(){
        return "2022/2023 - Ganjil";
    }

    public function getActiveSchoolYearCode(){
        return 20221;
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

    public function headerAll(){
        $activeSchoolYear = $this->getActiveSchoolYear();

        $header['university'] = "Universitas Telkom";
        $header['active'] = $activeSchoolYear;

        return $header;
    }

    public function studentGenerate(Request $request){
        $student = Student::with('getComponent')->findorfail($request['student_number']);
        $result = $this->storeStudentGenerate($student);
        return $result;
    }

    public function choice($f, $sp){
        // dd($f);
        $activeSchoolYearCode = $this->getActiveSchoolYearCode();
        $student = Student::query()
        ->with('studyProgram','lectureType','period','path','year','getComponent')
        ->leftJoin('finance.payment_re_register', function($join) use ($activeSchoolYearCode)
        {
            $join->on('finance.payment_re_register.student_number', '=', 'hr.ms_student.student_number');
            $join->where('finance.payment_re_register.prr_school_year','=',$activeSchoolYearCode);
            $join->where('finance.payment_re_register.deleted_at','=',null);
        });

        if($f && $f != 0){
            $sp_in_faculty = Studyprogram::where('faculty_id',$f)->pluck('studyprogram_id')->toArray();
            $student = $student->whereIn('studyprogram_id',$sp_in_faculty);
        }else{
            $student = $student->where('studyprogram_id',$sp);
        }
        $student = $student
        ->where('student_type_id',1)
        ->select('hr.ms_student.mlt_id','hr.ms_student.path_id','hr.ms_student.period_id','hr.ms_student.msy_id','hr.ms_student.studyprogram_id',DB::raw('count(hr.ms_student.*) as total_student'),DB::raw('count(prr_id) as total_generate'))
        ->groupBy('hr.ms_student.mlt_id','hr.ms_student.path_id','hr.ms_student.period_id','hr.ms_student.msy_id','hr.ms_student.studyprogram_id')
        ->get();

        foreach($student as $key => $s){
            $getComponent = $s->getComponent()
            ->where('path_id',$s->path_id)
            ->where('period_id',$s->period_id)
            ->where('msy_id',$s->msy_id)
            ->where('mlt_id',$s->mlt_id)
            ->get();
            $student[$key]->setRelation('component_filter', $getComponent->values());
        }

        return $student;
    }

    public function choiceAll(){
        // dd($f);
        $activeSchoolYearCode = $this->getActiveSchoolYearCode();
        $student = Student::query()
        ->with('studyProgram','getComponent')
        ->join('masterdata.ms_studyprogram','masterdata.ms_studyprogram.studyprogram_id','hr.ms_student.studyprogram_id')
        ->join('masterdata.ms_faculties','masterdata.ms_faculties.faculty_id','masterdata.ms_studyprogram.faculty_id')
        ->leftJoin('finance.payment_re_register', function($join) use ($activeSchoolYearCode)
        {
            $join->on('finance.payment_re_register.student_number', '=', 'hr.ms_student.student_number');
            $join->where('finance.payment_re_register.prr_school_year','=',$activeSchoolYearCode);
            $join->where('finance.payment_re_register.deleted_at','=',null);
        });

        $student = $student
        ->where('student_type_id',1)
        ->select('hr.ms_student.studyprogram_id','masterdata.ms_faculties.faculty_id',DB::raw('count(hr.ms_student.*) as total_student'),DB::raw('count(prr_id) as total_generate'))
        ->groupBy('hr.ms_student.studyprogram_id','masterdata.ms_faculties.faculty_id')
        ->get();

        foreach($student as $key => $s){
            $getComponent = $s->getComponent()
            ->where('path_id',$s->path_id)
            ->where('period_id',$s->period_id)
            ->where('msy_id',$s->msy_id)
            ->where('mlt_id',$s->mlt_id)
            ->get();
            $student[$key]->setRelation('component_filter', $getComponent->values());
        }

        return $student;
    }

    public function storeStudentGenerate($student){

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
                    'student_number' => $student->student_number,
                    'prr_school_year' => $this->getActiveSchoolYearCode(),
                ]);

                foreach($components as $item){
                    PaymentDetail::create([
                        'prr_id' => $payment->prr_id,
                        'prrd_component' => $item->component->msc_name,
                        'prrd_amount' => $item->cd_fee,
                        'is_plus' => 1,
                        'type' => 'component',
                    ]);
                }
                DB::commit();
            }catch(\Exception $e){
                DB::rollback();
                return response()->json($e->getMessage());
            }
        }else{
            return json_encode(array('success' => false, 'message' => 'Komponen Tagihan Tidak Ditemukan'));
        }
        $text = "Berhasil generate tagihan mahasiswa ".$student->fullname;
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function storeBulkStudentGenerate($generate_checkbox, $from,$mj_id){
        foreach($generate_checkbox as $item){
            if($item != "null"){
                // Parsing the key from string
                $store = explode("_",$item);
                $studyprogram_id = ($store[0]) ? $store[0] : 0;
                $msy_id = ($store[1]) ? $store[1] : 0;
                $path_id = ($store[2]) ? $store[2] : 0;
                $period_id = ($store[3]) ? $store[3] : 0;
                $mlt_id = ($store[4]) ? $store[4] : 0;

                // Starting Query
                $students = Student::query();
                $students = $students->with('getComponent')
                ->where('studyprogram_id',$studyprogram_id);

                // If from details, not index, using more parameters
                if($from == 'detail'){
                    $students = $students
                    ->where('msy_id',$msy_id)
                    ->where('path_id',$path_id)
                    ->where('period_id',$period_id)
                    ->where('mlt_id',$mlt_id);
                }
                $students = $students->get();

                // Loop for each student
                foreach($students as $student){
                    $payment = Payment::where('student_number',$student->student_number)->where('prr_school_year',$this->getActiveSchoolYearCode())->first();
                    if(!$payment){
                        GenerateInvoice::dispatch($student,$mj_id)->onQueue('invoice');
                    }
                }
            }
        }
        return true;
    }

    public function studentBulkGenerate(Request $request){
        if($request->generate_checkbox){
            GenerateBulkInvoice::dispatch($request->generate_checkbox, $request->from)->onQueue('bulk');
            return json_encode(array('success' => true, 'message' => "Generate Tagihan Sedang Diproses"));
        }else{
            return json_encode(array('success' => false, 'message' => "Belum ada data yang dipilih!"));
        }

    }

    public function delete($prr_id)
    {
        // Deleting Detail invoice
        DB::beginTransaction();
        try{
            $detail = PaymentDetail::where('prr_id', $prr_id)->get();
            if($detail){
                foreach($detail as $item){
                    if($item->type == 'discount' or $item->type == 'scholarship'){
                        return json_encode(array('success' => false, 'message' => "Terdapat potongan, beasiswa, cicilan ataupun dispensasi yang sudah disetujui pada tagihan ini."));
                    }
                }
            }
            // Deleting Detail Bill
            $data = Payment::findorfail($prr_id);
            if($data->prr_status == 'kredit'){
                return json_encode(array('success' => false, 'message' => "Terdapat potongan, beasiswa, cicilan ataupun dispensasi yang sudah disetujui pada tagihan ini."));
            }
            $data->delete();
            PaymentDetail::where('prr_id', $prr_id)->delete();
            PaymentBill::where('prr_id', $prr_id)->delete();
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json($e->getMessage());
        }
        return json_encode(array('success' => true, 'message' => "Berhasil menghapus tagihan"));
    }

    public function logGenerate()
    {
        $data = MasterJob::with('detail','user')->get();
        return $data;
    }

    public function deleteBulk($f,$sp)
    {
        // dd($sp);
        $activeSchoolYearCode = $this->getActiveSchoolYearCode();
        $invoice = Payment::query()
        ->join('hr.ms_student','hr.ms_student.student_number','finance.payment_re_register.student_number')
        ->where('finance.payment_re_register.prr_school_year','=',$activeSchoolYearCode)
        ->where('finance.payment_re_register.deleted_at','=',null);

        if($f && $f != 0){
            $sp_in_faculty = Studyprogram::where('faculty_id',$f)->pluck('studyprogram_id')->toArray();
            $invoice = $invoice->whereIn('studyprogram_id',$sp_in_faculty);
        }else{
            $invoice = $invoice->where('studyprogram_id',$sp);
        }
        $invoice = $invoice->delete();

        return json_encode(array('success' => true, 'message' => "Berhasil menghapus tagihan"));
    }
}

// OLD CODE, MAYBE USEFULL SOMEDAY
// $components = $student->getComponent()
// ->where('path_id',$student->path_id)
// ->where('period_id',$student->period_id)
// ->where('msy_id',$student->msy_id)
// ->where('mlt_id',$student->mlt_id)
// ->get();

// if($components){
//     $prr_total = 0;
//     foreach($components as $item){
//         $prr_total = $prr_total+$item->cd_fee;
//     }
//     $payment = Payment::where('student_number',$student->student_number)->where('prr_school_year',$this->getActiveSchoolYearCode())->first();

//     DB::beginTransaction();
//     try{
//         if($payment){
//             $payment->prr_status = 'belum lunas';
//             $payment->prr_total = $prr_total;
//             $payment->prr_paid_net = $prr_total;
//             $payment->save();
//             PaymentDetail::where('prr_id', $payment->prr_id)->delete();
//         }else{
//             $payment = Payment::create([
//                 'prr_status' => 'belum lunas',
//                 'prr_total' => $prr_total,
//                 'prr_paid_net' => $prr_total,
//                 'student_number' => $student->student_number,
//                 'prr_school_year' => $this->getActiveSchoolYearCode(),
//             ]);

//         }

//         foreach($components as $item){
//             PaymentDetail::create([
//                 'prr_id' => $payment->prr_id,
//                 'prrd_component' => $item->component->msc_name,
//                 'prrd_amount' => $item->cd_fee
//             ]);
//         }

//         DB::commit();
//     }catch(\Exception $e){
//         DB::rollback();
//         return response()->json($e->getMessage());
//     }
// }
