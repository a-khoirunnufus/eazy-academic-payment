<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment\Faculty;
use Illuminate\Http\Request;
use App\Models\Payment\ComponentType;
use Illuminate\Support\Facades\DB;
use App\Models\Payment\Studyprogram;
use App\Models\Payment\PeriodPath;
use App\Models\Payment\Period;
use App\Models\Payment\Path;
use App\Models\Payment\Settings;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function component()
    {
        return view('pages._payment.settings.component.index');
    }

    public function index()
    {
        $settings = Settings::all();
        $types = ComponentType::where('msct_is_show',1)->where('msct_main_payment',1)->orderBy('msct_id')->get();
        return view('pages._payment.settings.index', compact('settings','types'));
    }

    public function update(Request $request)
    {
        unset($request['_token']);
        foreach ($request->all() as $key => $item) {
            Settings::where('name', $key)->update(
                ['value' => $item]
            );
        }
        Cache::flush();
        return redirect()->back()->with(['message'=>'Update settings berhasil!','tabId'=>$request['tabId']]);
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

    public function sksrates()
    {
        $studyProgram = Studyprogram::all();
        $faculty = Faculty::all();
        return view('pages._payment.settings.sksrates.index', compact('studyProgram', 'faculty'));
    }

    public function otherRates()
    {
        // $studyProgram = Studyprogram::all();
        $types = ComponentType::where('msct_is_show',1)->where('msct_main_payment',0)->orderBy('msct_id')->get();
        $periode = DB::select("SELECT msy_year, msy_code from masterdata.ms_school_year");
        $jalur_pendaftaran = DB::select("SELECT path_id, path_name from pmb.ms_path");
        $gelombang = DB::select("SELECT period_id, period_name from pmb.ms_period");
        return view('pages._payment.settings.other-rates.index', compact("periode", "jalur_pendaftaran", "gelombang","types"));
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
        $types = ComponentType::where('msct_is_show',1)->orderBy('msct_id')->get();

        return view('pages._payment.settings.paymentrates.detail',compact('data','id','types'));
    }

    public function creditSchema()
    {
        return view('pages._payment.settings.credit-schema.index');
    }

    public function registrationForm(){
        $periode = DB::select("SELECT msy_year, msy_code from masterdata.ms_school_year");
        $jalur_pendaftaran = DB::select("SELECT path_id, path_name from pmb.ms_path");
        $gelombang = DB::select("SELECT period_id, period_name from pmb.ms_period");

        return view('pages._payment.settings.registration-form.index', compact("periode", "jalur_pendaftaran", "gelombang"));
    }
}
