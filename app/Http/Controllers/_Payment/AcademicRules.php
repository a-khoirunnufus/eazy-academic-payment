<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicRules extends Controller
{
    //
    public function index(){
        $periode_masuk = DB::select('select school_year, school_year_code from academic.school_year');
        $aturan_akademik = DB::select('select mr_id, mr_name from finance.ms_rules');
        $component = DB::select("select msc_id, msc_name from finance.ms_component");
        $credit_schema = DB::select("select cs_id, cs_name from finance.credit_schema");

        return view('pages.setting.academic-rules', compact("periode_masuk", "aturan_akademik", "component", "credit_schema"));
    }
}
