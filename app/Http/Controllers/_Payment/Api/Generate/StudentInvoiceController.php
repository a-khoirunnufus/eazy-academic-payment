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
use Carbon\Carbon;

class StudentInvoiceController extends Controller
{
    public function index($msy_id = 7){
        
        $query = Faculty::with('studyProgram')->orderBy('faculty_name')->get();
        $year = PeriodPath::join('masterdata.ms_period as mp','mp.period_id','masterdata.period_path.period_id')
        ->join('masterdata.ms_school_year as sy','sy.msy_id','mp.msy_id')
        ->select('sy.msy_id','sy.msy_year','sy.msy_semester')->distinct('mp.msy_id')->get();
        $rates = ComponentDetail::with('period','path','lectureType')
        ->selectRaw('mma_id,period_id,path_id,SUM(cd_fee) as total,msy_id,mlt_id')
        ->groupBy('mma_id','period_id','path_id','msy_id','mlt_id')->get();
        $result = collect();
        foreach($year as $y){
            foreach($query as $item){
                $collection = collect();
                $data = ['year' => $y,'faculty' => $item,'study_program' => null,'components' => null];
                $result->push($data);
                if($item->studyProgram){
                    foreach($item->studyProgram as $sp){
                        $rate = $rates->where('mma_id',$sp->studyprogram_id)->where('msy_id',$y->msy_id);
                        $data = ['year' => $y,'faculty' => null,'study_program' => $sp,'components' => $rate];
                        $result->push($data);
                    }
                }
            }
        }
        return datatables($result)->toJson();
    }

    public function detail(Request $request){
        // dd($request);
        $data['msy'] = $request->query()['msy'];
        $data['f'] = $request->query()['f'];
        $data['sp'] = $request->query()['sp'];
        $query = Student::query();
        $query = $query->with('lectureType','period')
        ->join('masterdata.ms_studyprogram as sp','sp.studyprogram_id','hr.ms_student.studyprogram_id')
        
        ->select('hr.ms_student.*');
        if($data['f'] != 0 && $data['f']){
            $query = $query->where('sp.faculty_id',$data['f']);
        }
        if($data['sp'] != 0 && $data['sp']){
            $query = $query->where('sp.studyprogram_id',$data['sp']);
        }
        if($data['msy'] != 0 && $data['msy']){
            $query = $query->where('msy_id',$data['msy']);
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
        $data['msy'] = $request->query()['msy'];
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
        $msy_year = Year::find($data['msy']);

        if($faculty){
            $header['faculty'] = $faculty->faculty_name;
        }else{
            $header['faculty'] = $studyProgram->faculty->faculty_name;
        }
        $header['study_program'] = ($data['sp'] != 0) ? $studyProgram->studyprogram_type.' '.$studyProgram->studyprogram_name : $studyProgram;
        $header['msy_year'] = $msy_year->msy_year;
        $header['active'] = $activeSchoolYear;

        // dd($query->get());
        return $header;
    }
}
