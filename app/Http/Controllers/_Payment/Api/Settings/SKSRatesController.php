<?php

namespace App\Http\Controllers\_Payment\Api\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\Studyprogram;
use App\Models\Payment\Course;
use App\Models\Payment\CourseRate;
use App\Models\Payment\SKSRate;
use App\Http\Requests\Payment\Settings\SKSRateRequest;
use App\Models\Payment\Faculty;
use DB;
use Exception;
// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SKSRatesController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->input('custom_filter');
        // remove item with null value or #ALL value
        $filters = array_filter($filters, function ($item) {
            return !is_null($item) && $item != '#ALL';
        });

        $query = SKSRate::where('msr_active_status',1)->with('studyProgram');
        if (isset($filters['faculty_id'])) {
            $query->whereHas('studyProgram', function ($q) use ($filters) {
                $q->where('faculty_id', '=', $filters['faculty_id']);
            });
        }
        if (isset($filters['studyprogram_id'])) {
            $query->whereHas('studyProgram', function ($q) use ($filters, $query) {
                $q->where('studyprogram_id', '=', $filters['studyprogram_id']);
            });
        }
        if (isset($filters['filtering'])){
            $data = $query->orderBy('msr_id')->get();
            $filter_data = [];
            foreach($data as $item){
                $row = json_encode($item);
                if(strpos(strtolower($row), strtolower($filters['filtering']))){
                    array_push($filter_data, $item);
                }
            }
            return datatables($filter_data)->toJson();
        }
        $query = $query->orderBy('msr_id');
        return datatables($query->get())->toJson();
    }

    public function getStudyProgram($id = null)
    {
        if ($id != null) {
            $studyProgram = Studyprogram::where('faculty_id', '=', $id)->get();
            return $studyProgram->toJson();
        }
        $studyProgram = Studyprogram::all();
        return $studyProgram->toJson();
    }

    public function getSKSRateByStudyProgramId($studyProgramId)
    {
        $data = SKSRate::where('msr_studyprogram_id', $studyProgramId)->orderBy('msr_id')->get();
        return $data->toJson();
    }

    public function store(SKSRateRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $count = count($validated['msr_tingkat']);
            for ($i = 0; $i < $count; $i++) {
                if ($validated['id'][$i] == 0) {
                    $data = SKSRate::where('msr_studyprogram_id',$validated['msr_studyprogram_id'])->where('msr_tingkat',$validated['msr_tingkat'][$i])->first();
                    if($data){
                        $data->update([
                            'msr_studyprogram_id' => $validated['msr_studyprogram_id'],
                            'msr_tingkat' => $validated['msr_tingkat'][$i],
                            'msr_rate' => $validated['msr_rate'][$i],
                            'msr_active_status' => 1,
                            'msr_rate_practicum' => $validated['msr_rate_practicum'][$i],
                        ]);
                        $text = "Berhasil memperbarui tarif SKS";
                    }else{
                        SKSRate::create([
                            'msr_studyprogram_id' => $validated['msr_studyprogram_id'],
                            'msr_tingkat' => $validated['msr_tingkat'][$i],
                            'msr_rate' => $validated['msr_rate'][$i],
                            'msr_active_status' => 1,
                            'msr_rate_practicum' => $validated['msr_rate_practicum'][$i],
                        ]);
                        $text = "Berhasil menambahkan tarif SKS";
                    }
                } else {
                    $data = SKSRate::findorfail($validated['id'][$i]);
                    $data->update([
                        'msr_studyprogram_id' => $validated['msr_studyprogram_id'],
                        'msr_tingkat' => $validated['msr_tingkat'][$i],
                        'msr_rate' => $validated['msr_rate'][$i],
                        'msr_active_status' => 1,
                        'msr_rate_practicum' => $validated['msr_rate_practicum'][$i],
                    ]);
                    $text = "Berhasil memperbarui tarif SKS";
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json($e->getMessage());
        }
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function delete($id)
    {
        $data = SKSRate::findOrFail($id);
        $data->delete();

        return json_encode(array('success' => true, 'message' => "Berhasil menghapus tarif SKS"));
    }
}
