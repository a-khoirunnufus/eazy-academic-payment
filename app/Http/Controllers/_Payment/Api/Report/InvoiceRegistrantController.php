<?php

namespace App\Http\Controllers\_Payment\Api\Report;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\PMB\Register;
use App\Traits\Models\QueryFilterExtendByRequest;

class InvoiceRegistrantController extends Controller
{
    use QueryFilterExtendByRequest;

    public function datatable(Request $request)
    {
        \Log::debug($request->all());
        $validated = $request->validate([
            'school_year' => 'required',
        ]);

        $query = DB::table('finance.vw_invoice_registration_master')
            ->where('registration_year_id', $validated['school_year']);

        $this->applyFilterWoFc(
            $query,
            $request,
            [
                'registration_year_id',
                'registration_period_id',
                'registration_path_id',
                'registration_majors',
                'invoice_nominal_gross',
                'invoice_nominal_nett',
                'payment_status',
            ]
        );

        \Log::debug($query->toSql());

        $datatable = datatables($query);

        return $datatable->toJson();
    }

    public function refresh()
    {
        try {
            \DB::statement('REFRESH MATERIALIZED VIEW finance.vw_invoice_registration_master');
        } catch (\Throwable $th) {
            throw $th;
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil memperbaharui data.'
        ]);
    }

    function studentRegistrant(Request $request){
        $data = Register::with('participant', 'studyProgram', 'lectureType', 'period', 'path' ,'paymentRegister', 'year');

        if($request->get('angkatan', '#ALL') !== '#ALL'){
            $data = $data->whereHas('year', function($q) use($request) {
                $q->where('msy_id', '=', $request->get('angkatan'));
            });
        }

        if($request->get('path', '#ALL') !== '#ALL'){
            $data = $data->whereHas('path', function($q) use($request) {
                $q->where('path_id', '=', $request->get('path'));
            });
        }

        if($request->get('period', '#ALL') !== '#ALL'){
            $data = $data->whereHas('period', function($q) use($request){
                $q->where('period_id', '=', $request->get('period'));
            });
        }

        return DataTables($data->get())->toJson();
    }
}
