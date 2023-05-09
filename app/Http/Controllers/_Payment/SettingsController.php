<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\ComponentType;
use Illuminate\Support\Facades\DB;
use App\Models\Studyprogram;
use App\Models\PeriodPath;

class SettingsController extends Controller
{
    public function component()
    {
        return view('pages._payment.settings.component.index');
    }
    
    public function subjectrates()
    {
        $studyProgram = Studyprogram::all();
        return view('pages._payment.settings.subjectrates.index', compact('studyProgram'));
    }
    
    public function paymentrates()
    {
        return view('pages._payment.settings.paymentrates.index');
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
