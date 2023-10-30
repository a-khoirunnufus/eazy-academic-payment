<?php

namespace App\Http\Controllers\_Payment\Api\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StudentBalanceWithdraw\StoreRequest;
use App\Models\Payment\Student;
use App\Models\Payment\StudentBalanceTrans;
use App\Models\Payment\StudentBalanceWithdraw;
use App\Traits\Payment\General as PaymentGeneral;
use App\Traits\Models\LoadDataRelationByRequest;
use App\Traits\Models\QueryFilterExtendByRequest;
use App\Traits\Models\DatatableManualFilter;

class StudentBalanceController extends Controller
{
    use PaymentGeneral;
    use LoadDataRelationByRequest;
    use QueryFilterExtendByRequest;
    use DatatableManualFilter;

    public function studentListDatatable(Request $request)
    {
        $query = \DB::table('finance.vw_student_balance_master');

        $datatable = datatables($query);

        $this->applyManualFilterWoFc(
            $datatable,
            $request,
            [
                // filter attributes
                'msy_year',
                'period_id',
                'path_id',
                'faculty_id',
                'studyprogram_id',
                'mlt_id',
                'current_balance'
            ],
            [
                // search attributes
                'fullname',
                'student_id',
            ],
        );

        return $datatable->toJson();
    }

    public function studentListIndex(Request $request)
    {
        $search = $request->get('search');

        $query = \DB::table('finance.vw_student_balance_master');

        if ($search) {
            $query->where('fullname', 'ilike', '%'.$search.'%');
            $query->orWhere('student_id', 'ilike', '%'.$search.'%');
        }

        $data = $query->orderBy('fullname')->paginate(30);

        return response()->json($data->toArray());
    }

    public function refreshStudentList()
    {
        try {
            \DB::statement('REFRESH MATERIALIZED VIEW finance.vw_student_balance_master');
        } catch (\Throwable $th) {
            throw $th;
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil memperbaharui data.'
        ]);
    }

    public function transactionDatatable(Request $request)
    {
        $query = StudentBalanceTrans::orderBy('sbt_time', 'asc');
        $query = $this->applyRelation($query, $request, ['student', 'type']);
        $query = $this->applyFilterWithOperator($query, $request, [
            'student_number',
            'sbtt_type',
            'sbt_time',
        ]);

        $datatable = datatables($query);

        return $datatable->toJson();
    }

    public function transactionShow(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required_without:transaction_id',
            'transaction_id' => 'required_without:student_id',
        ]);

        $student = Student::where('student_id', $validated['student_id'])->first();

        if (!$student) abort(404);

        if ($validated['student_id']) {
            $transaction = StudentBalanceTrans::where('student_number', $student->student_number)
                ->orderBy('sbt_time', 'desc')
                ->first();
        }
        elseif ($validated['transaction_id']) {
            $transaction = StudentBalanceTrans::find($validated['transaction_id']);
        }

        return response()->json($transaction->toArray());
    }

    public function withdrawDatatable(Request $request)
    {
        $query = StudentBalanceWithdraw::orderBy('sbw_issued_time', 'asc');
        $query = $this->applyRelation($query, $request, ['student', 'issuer']);
        $query = $this->applyFilterWithOperator($query, $request, [
            'student_number',
            'sbw_amount',
            'sbw_issued_by',
            'sbw_issued_time',
        ]);

        $datatable = datatables($query);

        return $datatable->toJson();
    }

    public function withdrawShow($id)
    {
        $query = StudentBalanceWithdraw::where('sbw_id', $id);
        $query = $this->applyRelation($query, $request, ['student', 'issuer']);

        return response()->json($query->first()->toArray());
    }

    public function withdrawStore(StoreRequest $request)
    {
        $validated = $request->validated();

        try {
            StudentBalanceWithdraw::create([
                ...$validated,
                'sbw_issued_time' => $this->getCurentDateTime()
            ]);
        } catch (\Throwable $th) {
            if (config('app.env') == 'production') {
                return response()->json([
                    'success' => true,
                    'message' => 'Gagal menambahkan data',
                ]);
            } else {
                throw $th;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menambahkan data',
        ]);
    }
}
