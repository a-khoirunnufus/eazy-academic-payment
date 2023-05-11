<?php

namespace App\Http\Controllers\_Payment\Api\Generate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use DB;

class NewStudentInvoiceController extends Controller
{
    public function index()
    {
        $data = DB::table('pmb.participant as p')
                    ->leftJoin('pmb.register as r', 'p.par_id', '=', 'r.par_id')
                    ->select(
                        'p.par_fullname as fullname',
                        'p.par_nik as nik',
                        'p.par_phone as phone',
                        'p.par_birthday as birthday',
                        'p.par_birthplace as birthplace',
                        'p.par_gender as gender',
                        'p.par_religion as religion'
                    )
                    ->whereNotNull('r.reg_major_pass')
                    ->whereNotNull('r.reg_major_lecture_type_pass')
                    ->where('r.reg_status_pass', '=', 1)
                    ->where('r.re_register_status', '=', 0)
                    ->where('p.par_active_status', '=', 1)
                    ->get();

        return datatables($data)->toJSON();
    }
}
