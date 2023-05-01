<?php

namespace App\Http\Controllers\_Payment\Api\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Studyprogram;
use App\Models\Course;
use App\Models\Payment\CourseRate;
use App\Http\Requests\Payment\Settings\CourseRateRequest;
use DB;

class CourseRatesController extends Controller
{
    public function index(Request $request)
    {
        $query = CourseRate::query();
        $query = $query->with('course')->orderBy('mcr_id');
        // $query = $this->loadRelation($query, $request, ['faculty']);
        // $query = $this->applyFilter($query, $request, [
        //     'studyprogram_active_status', 'faculty_id'
        // ]);
        // dd($query->get());
        return datatables($query)->toJson();
    }

    public function getStudyProgram()
    {
        $studyProgram = Studyprogram::all();
        return $studyProgram->toJson();
    }

    public function getMataKuliah($studyProgramId)
    {
        $data = Course::where('studyprogram_id',$studyProgramId)->orderBy('course_id')->get();
        return $data->toJson();
    }
    
    public function getCourseRateByCourseId($courseId)
    {
        $data = CourseRate::where('mcr_course_id',$courseId)->orderBy('mcr_id')->get();
        // dd($courseId);
        return $data->toJson();
    }
    
    public function store(CourseRateRequest $request)
    {
        $validated = $request->validated();
        // $count = count($validated->mcr_tingkat);
        // dd($validated);

        DB::beginTransaction();
        try{
            $count = count($validated['mcr_tingkat']);
            for ($i=0; $i < $count; $i++) { 
                if($validated['mcr_id'][$i] == 0){
                    CourseRate::create([
                        'mcr_course_id' => $validated['mcr_course_id'],
                        'mcr_tingkat' => $validated['mcr_tingkat'][$i],
                        'mcr_rate' => $validated['mcr_rate'][$i],
                        'mcr_active_status' => 1,
                        'mcr_is_package' => $validated['mcr_is_package'][$i],
                    ]);
                    $text = "Berhasil menambahkan tarif mata kuliah";
                }else{
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
        }catch(\Exception $e){
            DB::rollback();
            return response()->json($e->getMessage());
        }
        return json_encode(array('success' => true, 'message' => $text));
        // $data = Course::where('studyprogram_id',$studyProgramId)->orderBy('course_id')->get();
        // return $data->toJson();
    }
}
