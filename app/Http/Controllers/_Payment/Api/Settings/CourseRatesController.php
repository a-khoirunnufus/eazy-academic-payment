<?php

namespace App\Http\Controllers\_Payment\Api\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Studyprogram;
use App\Models\Course;
use App\Models\Payment\CourseRate;
use App\Http\Requests\Payment\Settings\CourseRateRequest;
use App\Models\Faculty;
use DB;
use Exception;

class CourseRatesController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->input('custom_filter');
        // remove item with null value or #ALL value
        $filters = array_filter($filters, function($item){
            return !is_null($item) && $item != '#ALL';
        });

        // $query = CourseRate::query();
        $query = CourseRate::with('course');
        // $query = $query->with('course')->orderBy('mcr_id');
        // $query = $query->with('course');
        if(isset($filters['faculty_id'])){
            $query->whereHas('course.studyProgram', function($q) use ($filters) {
                $q->where('faculty_id', '=', $filters['faculty_id']);
            });
        }
        if(isset($filters['studyprogram_id'])){
            $query->whereHas('course', function($q) use ($filters){
                $q->where('studyprogram_id', '=', $filters['studyprogram_id']);
            });
        }
        $query = $query->orderBy('mcr_id');
        // $query = $this->loadRelation($query, $request, ['faculty']);
        // $query = $this->applyFilter($query, $request, [
        //     'studyprogram_active_status', 'faculty_id'
        // ]);
        // dd($query->get());
        return datatables($query)->toJson();
    }

    public function getStudyProgram($id = null)
    {
        if($id != null){
            $studyProgram = Studyprogram::where('faculty_id', '=', $id)->get();
            return $studyProgram->toJson();

        }
        $studyProgram = Studyprogram::all();
        return $studyProgram->toJson();
    }

    public function getMataKuliah($studyProgramId)
    {
        $data = Course::where('studyprogram_id', $studyProgramId)->orderBy('course_id')->get();
        return $data->toJson();
    }

    public function getCourseRateByCourseId($courseId)
    {
        $data = CourseRate::where('mcr_course_id', $courseId)->orderBy('mcr_id')->get();
        // dd($courseId);
        return $data->toJson();
    }

    public function store(CourseRateRequest $request)
    {
        $validated = $request->validated();
        // $count = count($validated->mcr_tingkat);
        // dd($validated);

        DB::beginTransaction();
        try {
            $count = count($validated['mcr_tingkat']);
            for ($i = 0; $i < $count; $i++) {
                if ($validated['mcr_id'][$i] == 0) {
                    CourseRate::create([
                        'mcr_course_id' => $validated['mcr_course_id'],
                        'mcr_tingkat' => $validated['mcr_tingkat'][$i],
                        'mcr_rate' => $validated['mcr_rate'][$i],
                        'mcr_active_status' => 1,
                        'mcr_is_package' => $validated['mcr_is_package'][$i],
                    ]);
                    $text = "Berhasil menambahkan tarif mata kuliah";
                } else {
                    $data = CourseRate::findorfail($validated['mcr_id'][$i]);
                    $data->update([
                        'mcr_course_id' => $validated['mcr_course_id'],
                        'mcr_tingkat' => $validated['mcr_tingkat'][$i],
                        'mcr_rate' => $validated['mcr_rate'][$i],
                        'mcr_active_status' => 1,
                        'mcr_is_package' => $validated['mcr_is_package'][$i],
                    ]);
                    $text = "Berhasil memperbarui tarif mata kuliah";
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json($e->getMessage());
        }
        return json_encode(array('success' => true, 'message' => $text));
        // $data = Course::where('studyprogram_id',$studyProgramId)->orderBy('course_id')->get();
        // return $data->toJson();
    }

    public function delete($id)
    {
        $data = CourseRate::findOrFail($id);
        $data->delete();

        return json_encode(array('success' => true, 'message' => "Berhasil menghapus tarif mata kuliah"));
    }

    public function import(Request $request)
    {
        $file = $request->file('file');

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

        $spreadsheet = $reader->load($file->getRealPath());
        $sheet = $spreadsheet->getSheetByName($spreadsheet->getSheetNames()[0]);
        $dataSheet = $sheet->toArray();

        $data = array();
        for($i = 2; $i < count($dataSheet); $i++){
            if($dataSheet[$i][0] !== NULL && $dataSheet[$i][1] !==NULL){
                $tarif = $dataSheet[$i][3] == NULL ? 0 : $dataSheet[$i][3];
                $paket = $dataSheet[$i][4] == NULL ? 0 : $dataSheet[$i][4];
                array_push($data, array(
                    "program_studi_id" => $dataSheet[$i][0],
                    "course_id" => $dataSheet[$i][1],
                    "tarif_per_tingkat" => array(array(
                        'tingkat' => $dataSheet[$i][2],
                        'tarif' => $tarif,
                        'paket' => $paket
                    ))
                    ));
            }else {
                if(count($data) > 0){
                    $tarif = $dataSheet[$i][3] == NULL ? 0 : $dataSheet[$i][3];
                    $paket = $dataSheet[$i][4] == NULL ? 0 : $dataSheet[$i][4];
                    array_push($data[count($data) - 1]["tarif_per_tingkat"], array(
                        'tingkat' => $dataSheet[$i][2],
                        'tarif' => $tarif,
                        'paket' => $paket
                    ));
                }
            }
        }

        // DB::beginTransaction();
        try{
            foreach($data as $row){
                foreach($row["tarif_per_tingkat"] as $item){
                    CourseRate::create([
                        'mcr_course_id' => $row["course_id"],
                        'mcr_tingkat' => $item['tingkat'],
                        'mcr_rate' => $item['tarif'],
                        'mcr_active_status' => 1,
                        'mcr_is_package' => $item['paket'],
                    ]);
                }
            }
        }catch(\Exception $e){
            // DB::rollback();
            return response()->json($e->getMessage());
        }
        return json_encode(array(
            'status' => true,
            'message' => "Succes Import Data"
        ));
    }
}
