<?php

namespace App\Http\Controllers\_Payment\Api\Settings;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\Settings\ComponentRequest;
use App\Models\Payment\ComponentType;
use App\Models\Payment\Component;
use App\Traits\Models\QueryFilterByRequest;
use App\Traits\Models\LoadDataRelationByRequest;
use App\Imports\InvoiceComponentsImport;
use App\Exports\ArrayExport;
use DB;

class ComponentInvoiceController extends Controller
{
    use QueryFilterByRequest, LoadDataRelationByRequest;

    public function index(Request $request)
    {
        $query = Component::query();
        $query = $query->orderBy('msc_id');
        // $query = $this->loadRelation($query, $request, ['faculty']);
        // $query = $this->applyFilter($query, $request, [
        //     'studyprogram_active_status', 'faculty_id'
        // ]);
        return datatables($query)->toJson();
    }

    public function getComponentType()
    {
        $data = ComponentType::orderBy('msct_id')->get();
        return $data->toJson();
    }

    public function store(ComponentRequest $request)
    {
        $validated = $request->validated();
        $arr = ['msc_is_student','msc_is_new_student','msc_is_participant'];
        foreach($arr as $item){
            if(array_key_exists($item,$validated)){
                $validated[$item] = 1;
            }else{
                $validated[$item] = 0;
            }
        }

        DB::beginTransaction();
        try{
            if(array_key_exists("msc_id",$validated)){
                $data = Component::findOrFail($validated["msc_id"]);
                $data->update($validated);
                $text = "Berhasil memperbarui komponen tagihan";
            }else{
                Component::create($validated + [
                    'active_status' => 1
                ]);
                $text = "Berhasil menambahkan komponen tagihan";
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json($e->getMessage());
        }
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function delete($id)
    {
        $data = Component::findOrFail($id);
        $data->delete();

        return json_encode(array('success' => true, 'message' => "Berhasil menghapus komponen tagihan"));
    }

    public function uploadFileForImport(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|mimes:xlsx'
        ]);

        // define import_id
        $import_id = DB::select("select nextval('temp.finance_import_component_import_id_num_seq')")[0]->nextval;

        try {
            $import = new InvoiceComponentsImport($import_id);
            $import->import($request->file('file'));
        } catch (\Throwable $th) {
            Log::debug($th->getMessage());
            return response()->json([
                'success' => false,
                // 'message' => 'Terjadi Kesalahan!',
                'message' => $th->getMessage(),
            ], 500);
        }

        // send import id
        return response()->json([
            'success' => true,
            'message' => 'Selesai memproses file.',
            'payload' => [
                'import_id' => $import_id,
            ],
        ], 200);
    }

    public function dtImportPreview(Request $request)
    {
        $import_id = $request->input('custom_payload')['import_id'];

        $data = DB::table('temp.finance_import_component')
            ->where('import_id', '=', $import_id)
            ->get();

        return datatables($data)->toJson();
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'import_id' => 'required',
        ]);

        $data = DB::table('temp.finance_import_component as fic')
            ->leftJoin('finance.ms_component_type as msct', 'msct.msct_name', '=', 'fic.component_type')
            ->select(
                'fic.component_name',
                'fic.component_description',
                'msct.msct_id',
                'fic.is_student',
                'fic.is_new_student',
                'fic.is_participant',
                'fic.component_active_status'
            )
            ->where('fic.import_id', '=', $validated['import_id'])
            ->where('fic.status', '=', 'valid')
            ->get();

        try{
            DB::beginTransaction();

            foreach ($data as $item) {
                Component::create([
                    'msc_name' => $item->component_name,
                    'msc_description' => $item->component_description,
                    'msct_id' => $item->msct_id,
                    'msc_is_student' => $item->is_student,
                    'msc_is_new_student' => $item->is_new_student,
                    'msc_is_participant' => $item->is_participant,
                    'active_status' => $item->component_active_status
                ]);
            }

            // clear temp import data
            DB::table('temp.finance_import_component')
                ->where('import_id', '=', $validated['import_id'])
                ->delete();

            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json([
                'success' => false,
                // 'message' => 'Gagal import komponen tagihan.',
                'message' => $e->getMessage(),
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil import komponen tagihan.',
        ], 200);
    }
}
