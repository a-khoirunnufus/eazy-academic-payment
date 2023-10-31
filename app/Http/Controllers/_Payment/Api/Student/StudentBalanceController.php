<?php

namespace App\Http\Controllers\_Payment\Api\Student;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
    use PaymentGeneral, LoadDataRelationByRequest, QueryFilterExtendByRequest, DatatableManualFilter;

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
        $query = DB::table('finance.vw_student_balance_withdraw_master')->orderBy('issued_time', 'desc');
        $query = $this->applyFilterWoFc($query, $request, ['amount', 'issuer_id', 'issued_time']);

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
            DB::beginTransaction();

            $user = Auth::user();
            if (!$user) throw new \Exception('User not authenticated', 1);

            $student = Student::where('student_id', $validated['student_id'])->first();
            $student_current_balance = $student->computed_current_balance;
            if ($student_current_balance < (int)$validated['sbw_amount']) {
                throw new Exception('Balance is not sufficient', 2);
            }

            $url_files = [];
            if (config('app.disable_cloud_storage')) {
                foreach ($validated['sbw_related_files'] as $file) {
                    $upload_success = Storage::disk('minio')->putFile('payment/student_balance_withdraw', $file);
                    if ($upload_success) {
                        array_push($url_files, $upload_success);
                    } else {
                        throw new \Exception('Failed uploading file!');
                    }
                }
            }

            $withdraw = StudentBalanceWithdraw::create([
                'student_number' => $student->student_number,
                'sbw_amount' => (int)$validated['sbw_amount'],
                'sbw_issued_by' => $user->user_id,
                'sbw_issued_time' => $this->getCurrentDateTime(),
                'sbw_related_files' => $url_files,
            ]);

            StudentBalanceTrans::create([
                'student_number' => $student->student_number,
                'sbt_opening_balance' => $student_current_balance,
                'sbt_amount' => (int)$validated['sbw_amount'],
                'sbtt_name' => 'withdraw',
                'sbtt_associate_id' => $withdraw->sbw_id,
                'sbt_closing_balance' => $student_current_balance - (int)$validated['sbw_amount'],
                'sbt_time' => $this->getCurrentDateTime(),
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            // throw $th;
            if (config('app.env') == 'production') {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan data',
                ], 500);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
                ], 500);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menambahkan data',
        ]);
    }
}
