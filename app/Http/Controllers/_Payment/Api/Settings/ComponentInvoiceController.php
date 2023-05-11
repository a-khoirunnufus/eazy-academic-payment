<?php

namespace App\Http\Controllers\_Payment\Api\Settings;

use Illuminate\Http\Request;
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

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx',
        ]);

        $import = new InvoiceComponentsImport();
        $import->import($request->file('excel_file'));

        $failures = $import->failures();
        $is_failures = $failures->count() > 0;
        $error_url = '';
        if ($is_failures) {
            $failures_processed = [];
            foreach ($failures as $failure) {
                $row = $failure->row(); // row that went wrong
                // $attribute = $failure->attribute(); // either heading key (if using heading row concern) or column index
                $errors = $failure->errors(); // Actual error messages from Laravel validator
                // $values = $failure->values(); // The values of the row that has failed.
                $failures_processed[] = [
                    'nomor_baris' => $row,
                    'keterangan' => implode(', ', Arr::flatten($errors)),
                ];
            }
            $exportFailures = new ArrayExport($failures_processed);
            $filename = 'excel-import-errors-'.time().'.xlsx';
            $path_arr = ['app', 'public', 'excel-logs', $filename];
            $path = join(DIRECTORY_SEPARATOR, $path_arr);
            $exportFailures->store($path);
            $error_url = url('api/download?storage=local&type=excel-log&filename='.$filename);
        }

        $res = [
            'success' => true,
            'message' => 'Berhasil import '.$import->imported_rows.' komponen tagihan.'
                .($is_failures ? ' Terdapat beberapa error, keterangan error akan otomatis didownload.' : ''),
        ];

        if ($is_failures) $res['error_url'] = $error_url;

        return response()->json($res, 200);
    }
}
