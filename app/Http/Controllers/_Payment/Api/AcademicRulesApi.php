<?php

namespace App\Http\Controllers\_Payment\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\Settings\AcademicRulesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

class AcademicRulesApi extends Controller
{
    //
    public function academicRules()
    {
        $data = DB::table('finance.ms_rule_academics as mra')
                ->select(
                    'mra_id as id',
                    'sy.school_year as period',
                    'mr.mr_name as rule_name',
                    'mc.msc_name as invoice_component',
                    'cs.cs_name as instalment',
                    'mra_minimum as minimum_paid_percent',
                    'mra_active_status as is_active'
                )
                ->join('academic.school_year as sy', 'mra.mra_school_year_code', '=', 'sy.school_year_code')
                ->join('finance.ms_rules as mr', 'mra.mra_mr_id', '=', 'mr.mr_id')
                ->join('finance.ms_component as mc', 'mra.mra_msc_id', '=', 'mc.msc_id')
                ->join('finance.credit_schema as cs', 'mra.mra_cs_id', '=', 'cs.cs_id')
                ->distinct()->get();

        $datatable = datatables($data);

        return $datatable->toJSON();
    }

    public function addData(AcademicRulesRequest $request){
        $validated = $request->validated();

        try{
            DB::table('finance.ms_rule_academics')
                ->insert([
                   'mra_school_year_code' => $validated['periode'],
                   'mra_mr_id' => $validated['aturan'],
                   'mra_msc_id' => $validated['komponen'],
                   'mra_cs_id' => $validated['cicilan'],
                   'mra_active_status' => $validated['is_active'],
                   'mra_minimum' => $validated['minimum_paid'],
                   'created_at' => date("Y-m-d H:i:s")
                ]);
        }catch(\Throwable $e){
            return $e;
        }
        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menambahkan Aturan Akademik',
        ]);
    }

    public function getDataById($id){
        $data = DB::select('select * from finance.ms_rule_academics where mra_id = ?',[$id]);
        return $data[0];
    }

    public function editData($id, AcademicRulesRequest $request){
        $validated = $request->validated();
        
        try{
            DB::table('finance.ms_rule_academics')
            ->whereRaw('mra_id = ?', [$id])
            ->update([
                'mra_school_year_code' => DB::raw($validated['periode']),
                'mra_mr_id' => DB::raw($validated['aturan']),
                'mra_msc_id' => DB::raw($validated['komponen']),
                'mra_cs_id' => DB::raw($validated['cicilan']),
                'mra_active_status' => DB::raw($validated['is_active']),
                'mra_minimum' => DB::raw($validated['minimum_paid']),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
        }catch(\Throwable $e){
            return $e;
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Memperbaharui Aturan Akademik',
        ]);
    }

    public function deleteData($id){
        $row = DB::delete('delete from finance.ms_rule_academics where mra_id = ?', [$id]);
        if($row > 0){
            return response()->json([
                'success' => true,
                'message' => 'Berhasil Menghapus Aturan Akademik',
            ]);
        }else {
            return response()->json([
                'success' => false,
                'message' => 'Gagal Menghapus Aturan Akademik',
            ]);
        }
    }
}
