<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use Illuminate\Http\Request;
use App\Models\Payment\ComponentType;
use Illuminate\Support\Facades\DB;
use App\Models\Studyprogram;
use App\Models\PeriodPath;
use App\Models\Period;
use App\Models\Path;

class SettingsController extends Controller
{
    public function component()
    {
        return view('pages._payment.settings.component.index');
    }

    public function subjectrates()
    {
        $studyProgram = Studyprogram::all();
        $faculty = Faculty::all();
        return view('pages._payment.settings.subjectrates.index', compact('studyProgram', 'faculty'));
    }

    public function paymentrates()
    {
        $school_years = DB::table('masterdata.ms_school_year')
            ->select(
                'msy_id as id',
                DB::raw("
                    CASE
                        WHEN msy_semester = '1' THEN msy_year || ' - Ganjil'
                        WHEN msy_semester = '2' THEN msy_year || ' - Genap'
                        ELSE msy_year || ' - ' || msy_semester
                    END AS text
                ")
            )
            ->orderBy('msy_year', 'ASC')
            ->orderBy('msy_semester', 'ASC')
            ->get();
        $periods = Period::all();
        $paths = Path::all();

        return view('pages._payment.settings.paymentrates.index', compact('school_years', 'periods', 'paths'));
    }

    public function paymentratesdetail($id)
    {
        $data = PeriodPath::with('path','period')->findorfail($id);
        $data['periode'] = "";
        $data['jalur'] = "";
        $data['tahun'] = "";
        if($data->path){
            $data['jalur'] = $data->path->path_name;
            
        }
        if($data->period){
            $data['periode'] = $data->period->period_name;
            if($data->period->schoolyear){
                if($data->period->schoolyear->msy_semester == 1){
                    $semester = "Ganjil";
                }else{
                    $semester = "Genap";
                }
                $data['tahun'] = $data->period->schoolyear->msy_year.' - '.$semester;
            }
        }

        return view('pages._payment.settings.paymentrates.detail',compact('data','id'));
    }
    
    public function creditSchema()
    {
        return view('pages._payment.settings.credit-schema.index');
    }

    public function registrationForm(){
        $periode = DB::select("SELECT msy_year, msy_code from masterdata.ms_school_year");
        $jalur_pendaftaran = DB::select("SELECT path_id, path_name from pmb.ms_path");
        $gelombang = DB::select("SELECT period_id, period_name from pmb.ms_period");

        return view('pages.setting.registration-form', compact("periode", "jalur_pendaftaran", "gelombang"));
    }
}
