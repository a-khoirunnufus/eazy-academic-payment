<?php

namespace App\Http\Controllers\_Payment\Api\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\CreditSchema;
use App\Models\Payment\CreditSchemaDetail;
use App\Http\Requests\Payment\Settings\CreditSchemaRequest;
use DB;

class CreditSchemaController extends Controller
{
    /**
     * Return datatables source for credit schema
     */
    public function index()
    {
        $data = CreditSchema::with('creditSchemaDetail')->get();

        return datatables($data)->toJSON();
    }

    public function show($id)
    {
        $data = CreditSchema::with('creditSchemaDetail')->where(['cs_id' => $id])->first();

        return response()->json($data, 200);
    }

    public function store(CreditSchemaRequest $request)
    {
        DB::beginTransaction();
        $validated = $request->validated();

        try {
            $credit_schema = CreditSchema::create([
                'cs_name' => $validated['cs_name'],
                'cs_valid' => $validated['cs_valid'],
            ]);
            foreach ($validated['csd_percentage'] as $key => $percent) {
                CreditSchemaDetail::create([
                    'csd_cs_id' => $credit_schema->cs_id,
                    'csd_order' => $key+1,
                    'csd_percentage' => $percent,
                ]);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            return $e;
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menambahkan skema cicilan',
        ]);
    }

    public function update($id, CreditSchemaRequest $request)
    {
        DB::beginTransaction();
        $validated = $request->validated();

        try {
            // update credit schema
            $credit_schema = CreditSchema::find($id);
            $credit_schema->cs_name = $validated['cs_name'];
            $credit_schema->cs_valid = $validated['cs_valid'];

            // delete old credit schema detail
            CreditSchemaDetail::where(['csd_cs_id' => $id])->delete();

            // create new credit schema detail
            foreach ($validated['csd_percentage'] as $key => $percent) {
                CreditSchemaDetail::create([
                    'csd_cs_id' => $credit_schema->cs_id,
                    'csd_order' => $key+1,
                    'csd_percentage' => $percent,
                ]);
            }

            $credit_schema->save();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            return $e;
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengupdate skema cicilan',
        ]);
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {
            $credit_schema = CreditSchema::find($id);
            CreditSchemaDetail::where(['csd_cs_id' => $id])->delete();
            $credit_schema->delete();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            return $e;
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menghapus skema cicilan',
        ]);
    }
}
