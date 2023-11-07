<?php

namespace App\Http\Controllers\_Payment\Api\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\Studyprogram;
use App\Models\Payment\Course;
use App\Models\Payment\CourseRate;
use App\Http\Requests\Payment\Settings\CourseRateRequest;
use App\Models\Payment\Faculty;
use DB;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CourseRatesController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->input('custom_filter');
        // remove item with null value or #ALL value
        $filters = array_filter($filters, function ($item) {
            return !is_null($item) && $item != '#ALL';
        });

        // $query = CourseRate::query();
        $query = CourseRate::whereNotNull('mcr_studyprogram_id')->with('course', 'studyProgram');
        // $query = $query->with('course')->orderBy('mcr_id');
        // $query = $query->with('course');
        if (isset($filters['faculty_id'])) {
            $query->whereHas('studyProgram', function ($q) use ($filters) {
                $q->where('faculty_id', '=', $filters['faculty_id']);
            });
        }
        if (isset($filters['studyprogram_id'])) {
            $query->whereHas('course', function ($q) use ($filters, $query) {
                $q->where('studyprogram_id', '=', $filters['studyprogram_id']);
            });
        }
        if (isset($filters['filtering'])){
            $data = $query->orderBy('mcr_id')->get();
            $filter_data = [];
            foreach($data as $item){
                $row = json_encode($item);
                if(strpos(strtolower($row), strtolower($filters['filtering']))){
                    array_push($filter_data, $item);
                }
            }
            return datatables($filter_data)->toJson();
        }
        // $query->whereNotNull('mcr_studyprogram_id');
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
        if ($id != null) {
            $studyProgram = Studyprogram::where('faculty_id', '=', $id)->get();
        } else {
            $studyProgram = Studyprogram::all();
        }

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
                    $data = CourseRate::where('mcr_studyprogram_id',$validated['mcr_studyprogram_id'])
                    ->where('mcr_course_id',$validated['mcr_course_id'])
                    ->where('mcr_tingkat',$validated['mcr_tingkat'][$i])->first();
                    if($data){
                        $data->update([
                            'mcr_course_id' => $validated['mcr_course_id'],
                            'mcr_tingkat' => $validated['mcr_tingkat'][$i],
                            'mcr_rate' => $validated['mcr_rate'][$i],
                            'mcr_active_status' => 1,
                            'mcr_is_package' => $validated['mcr_is_package'][$i],
                            'mcr_studyprogram_id' => $validated['mcr_studyprogram_id'],
                        ]);
                        $text = "Berhasil memperbarui tarif mata kuliah";
                    }else{
                        CourseRate::create([
                            'mcr_course_id' => $validated['mcr_course_id'],
                            'mcr_tingkat' => $validated['mcr_tingkat'][$i],
                            'mcr_rate' => $validated['mcr_rate'][$i],
                            'mcr_active_status' => 1,
                            'mcr_is_package' => $validated['mcr_is_package'][$i],
                            'mcr_studyprogram_id' => $validated['mcr_studyprogram_id'],
                        ]);
                        $text = "Berhasil menambahkan tarif mata kuliah";
                    }
                } else {
                    $data = CourseRate::findorfail($validated['mcr_id'][$i]);
                    $data->update([
                        'mcr_course_id' => $validated['mcr_course_id'],
                        'mcr_tingkat' => $validated['mcr_tingkat'][$i],
                        'mcr_rate' => $validated['mcr_rate'][$i],
                        'mcr_active_status' => 1,
                        'mcr_is_package' => $validated['mcr_is_package'][$i],
                        'mcr_studyprogram_id' => $validated['mcr_studyprogram_id'],
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
        for ($i = 2; $i < count($dataSheet); $i++) {
            if ($dataSheet[$i][0] !== NULL && $dataSheet[$i][1] !== NULL) {
                $tarif = $dataSheet[$i][3] == NULL ? 0 : $dataSheet[$i][3];
                $paket = $dataSheet[$i][4] == NULL ? 0 : $dataSheet[$i][4];
                array_push($data, array(
                    "program_studi_id" => explode("-", $dataSheet[$i][0])[0],
                    "course_id" => explode("-", $dataSheet[$i][1])[0],
                    "tarif_per_tingkat" => array(array(
                        'tingkat' => $dataSheet[$i][2],
                        'tarif' => $tarif,
                        'paket' => explode("-", $paket)[0]
                    ))
                ));
            } else {
                if (count($data) > 0) {
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
        try {
            foreach ($data as $row) {
                foreach ($row["tarif_per_tingkat"] as $item) {
                    CourseRate::create([
                        'mcr_course_id' => $row["course_id"],
                        'mcr_tingkat' => $item['tingkat'],
                        'mcr_rate' => $item['tarif'],
                        'mcr_active_status' => 1,
                        'mcr_is_package' => $item['paket'],
                        'mcr_studyprogram_id' => $row['program_studi_id']
                    ]);
                }
            }
        } catch (\Exception $e) {
            // DB::rollback();
            return response()->json($e->getMessage());
        }
        return json_encode(array(
            'status' => true,
            'message' => "Succes Import Data"
        ));
    }

    public function preview(Request $request)
    {
        $file = $request->file('file');

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

        $spreadsheet = $reader->load($file->getRealPath());
        $sheet = $spreadsheet->getSheetByName($spreadsheet->getSheetNames()[0]);
        $dataSheet = $sheet->toArray();

        $data = array();
        for ($i = 2; $i < count($dataSheet); $i++) {
            if ($dataSheet[$i][0] !== NULL && $dataSheet[$i][1] !== NULL) {
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
            } else {
                if (count($data) > 0) {
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

        return json_encode($data, JSON_PRETTY_PRINT);
    }

    public function template()
    {
        $course = Course::limit(200)->get();
        $listCourse = array();
        foreach($course as $item){
            array_push($listCourse, $item->course_id."-".$item->subject_name);
        }
        $listCourse = '"'.implode(",", $listCourse).'"';
        // var_dump($listCourse);

        $studyprogram = Studyprogram::limit(200)->get();
        $listStudyProgram = array();
        foreach($studyprogram as $item){
            array_push($listStudyProgram, $item->studyprogram_id."-".$item->studyprogram_name);
        }
        $listStudyProgram = '"'.implode(",", $listStudyProgram).'"';
        // var_dump($listStudyProgram);

        $spreadsheet = new Spreadsheet();
        $sheet_info = new Worksheet($spreadsheet, 'Info');
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Program Studi');
        $sheet_info->setCellValue('A1', 'Program Studi');
        $sheet->mergeCells('A1:A2');
        $sheet->setCellValue('B1', 'Mata Kuliah');
        $sheet_info->setCellValue('B1', 'Mata Kuliah');
        $sheet->mergeCells('B1:B2');
        $sheet->setCellValue('C1', 'Tarif per Tingkat');
        $sheet->mergeCells('C1:E1');
        $sheet->setCellValue('C2', "Tingkat");
        $sheet->setCellValue('D2', 'Tarif');
        $sheet->setCellValue('E2', 'Paket');

        $start_row = 2;
        foreach(Course::all(['course_id', 'subject_name']) as $item){
            $sheet_info->setCellValue('B'.$start_row, $item->course_id."-".$item->subject_name);
            $start_row++;
        }
        $mata_kuliah = $start_row;

        $start_row = 2;
        foreach(Studyprogram::all(['studyprogram_id','studyprogram_name']) as $item){
            $sheet_info->setCellValue('A'.$start_row, $item->studyprogram_id."-".$item->studyprogram_name);
            $start_row++;
        }
        $programstudy = $start_row;

        for($i = 3; $i < 100; $i++){
            $validation1 = $sheet->getCell('A'.$i)->getDataValidation();
            $validation1->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            // $validation1->setFormula1(''.$listStudyProgram);
            $validation1->setFormula1('Info!$A$2:$A$'.$programstudy);
            $validation1->setAllowBlank(false);
            $validation1->setShowDropDown(true);
            $validation1->setShowInputMessage(true);
            $validation1->setPromptTitle('Note');
            $validation1->setPrompt('Must select one from the drop down options.');
            $validation1->setShowErrorMessage(true);
            $validation1->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validation1->setErrorTitle('Invalid option');
            $validation1->setError('Select one from the drop down list.');

            $validation2 = $sheet->getCell('B'.$i)->getDataValidation();
            $validation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            // $validation2->setFormula1(''.$listCourse);
            $validation2->setFormula1('Info!$B$2:$B$'.$mata_kuliah);
            $validation2->setAllowBlank(false);
            $validation2->setShowDropDown(true);
            $validation2->setShowInputMessage(true);
            $validation2->setPromptTitle('Note');
            $validation2->setPrompt('Must select one from the drop down options.');
            $validation2->setShowErrorMessage(true);
            $validation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validation2->setErrorTitle('Invalid option');
            $validation2->setError('Select one from the drop down list.');

            $validation3 = $sheet->getCell('C'.$i)->getDataValidation();
            $validation3->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation3->setFormula1('"1,2,3,4"');
            $validation3->setAllowBlank(false);
            $validation3->setShowDropDown(true);
            $validation3->setShowInputMessage(true);
            $validation3->setPromptTitle('Note');
            $validation3->setPrompt('Must select one from the drop down options.');
            $validation3->setShowErrorMessage(true);
            $validation3->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validation3->setErrorTitle('Invalid option');
            $validation3->setError('Select one from the drop down list.');

            $validation4 = $sheet->getCell('E'.$i)->getDataValidation();
            $validation4->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation4->setFormula1('"1-IYA,0-TIDAK"');
            $validation4->setAllowBlank(false);
            $validation4->setShowDropDown(true);
            $validation4->setShowInputMessage(true);
            $validation4->setPromptTitle('Note');
            $validation4->setPrompt('Must select one from the drop down options.');
            $validation4->setShowErrorMessage(true);
            $validation4->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validation4->setErrorTitle('Invalid option');
            $validation4->setError('Select one from the drop down list.');
        }
        // $writer = new Xlsx($spreadsheet);
        //     $writer->save('php://output');
        $spreadsheet->addSheet($sheet_info, 1);

        $response = response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="template tarif permata kuliah.xlsx"');
        $response->send();
    }
}
