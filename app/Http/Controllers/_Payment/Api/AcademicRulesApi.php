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
        $data = DB::select('
        select distinct
            mra_id as id,
            sy.school_year as period,
            mr.mr_name as rule_name,
            mc.msc_name as invoice_component,
            cs.cs_name as instalment,
            mra_minimum as minimum_paid_percent,
            mra_active_status as is_active
        from finance.ms_rule_academics mra
        join academic.school_year sy
            on mra.mra_school_year_code = sy.school_year_code
        join finance.ms_rules mr 
            on mra.mra_mr_id = mr.mr_id
        join finance.ms_component mc 
            on mra.mra_msc_id = mc.msc_id
        join finance.credit_schema cs 
            on mra.mra_cs_id = cs.cs_id
        ');

        $datatable = datatables($data);

        return $datatable->toJSON();
    }

    public function addData(AcademicRulesRequest $request){
        $validated = $request->validated();

        try{
            DB::insert('
                insert into finance.ms_rule_academics(mra_school_year_code, mra_mr_id, mra_msc_id, mra_cs_id, mra_active_status, mra_minimum, created_at)
                values(?,?,?,?,?,?,?)
            ',[
                $validated['periode'],$validated['aturan'],$validated['komponen'],$validated['cicilan'],$validated['is_active'],$validated['minimum_paid'], date("Y-m-d H:i:s")
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
            DB::update('
            update finance.ms_rule_academics 
            set 
                mra_school_year_code = ?, 
                mra_mr_id = ?, 
                mra_msc_id = ?, 
                mra_cs_id = ?, 
                mra_active_status = ?, 
                mra_minimum = ?,
                updated_at = ?
            where mra_id = ?
            ',[$validated['periode'],$validated['aturan'],$validated['komponen'],$validated['cicilan'],$validated['is_active'],$validated['minimum_paid'], date("Y-m-d H:i:s"), $id]);
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
