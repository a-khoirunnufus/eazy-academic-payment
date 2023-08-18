<?php

namespace App\Http\Controllers\_Payment\Api\Discount;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\Discount;
use App\Models\Payment\DiscountReceiver;
use App\Http\Requests\Payment\Discount\DiscountReceiverNewStudentRequest;
use App\Models\Student;
use App\Models\Studyprogram;
use App\Models\PMB\Register;
use App\Models\Year;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DiscountReceiverNewStudentController extends Controller
{
    public function index(Request $request)
    {
        // $filters = $request->input('custom_filters');
        // $filters = array_filter($filters, function ($item) {
        //     return !is_null($item) && $item != '#ALL';
        // });

        $query = DiscountReceiver::query();
        $query = $query->where('reg_id', '!=', null);
        $query = $query->with('period','newStudent','discount');

        // if (isset($filters['period'])){
        //     $query->where('mdr_period', '=', $filters['period']);
        // }

        // if(isset($filters['discount_filter'])){
        //     $query->whereHas('discount', function($q) use($filters){
        //         $q->where('md_id', '=', $filters['discount_filter']);
        //     });
        // }

        // if(isset($filters['faculty_filter'])){
        //     $query->whereHas('student.studyProgram', function($q) use($filters){
        //         $q->where('faculty_id', '=', $filters['faculty_filter']);
        //     });
        // }

        // if(isset($filters['study_program_filter'])){
        //     $query->whereHas('student.studyProgram', function($q) use($filters){
        //         $q->where('studyprogram_id', '=', $filters['study_program_filter']);
        //     });
        // }

        $query = $query->orderBy('mdr_id')->get();

        // if(isset($filters['search_filter'])){
        //     $data = [];
        //     foreach($query as $item){
        //         // print_r($item);
        //         $isFound = false;

        //         if(strpos(strtolower($item->student->fullname), strtolower($filters['search_filter'])) !== false){
        //             if(!$isFound){
        //                 $isFound = true;
        //                 array_push($data, $item);
        //             }
        //         }

        //         if(strpos(strtolower($item->student->student_id), strtolower($filters['search_filter'])) !== false){
        //             if(!$isFound){
        //                 $isFound = true;
        //                 array_push($data, $item);
        //             }
        //         }

        //         if(strpos(strtolower($item->student->studyProgram->studyprogram_type . ' ' . $item->student->studyProgram->studyprogram_name), strtolower($filters['search_filter'])) !== false){
        //             if(!$isFound){
        //                 $isFound = true;
        //                 array_push($data, $item);
        //             }
        //         }

        //         if(strpos(strtolower($item->student->studyProgram->faculty->faculty_name), strtolower($filters['search_filter'])) !== false){
        //             if(!$isFound){
        //                 $isFound = true;
        //                 array_push($data, $item);
        //             }
        //         }

        //         if(strpos(strtolower($item->discount->md_name), strtolower($filters['search_filter'])) !== false){
        //             if(!$isFound){
        //                 $isFound = true;
        //                 array_push($data, $item);
        //             }
        //         }

        //         if(strpos(strtolower($item->period->msy_year.' '.($item->period->msy_semester == 1 ? 'Ganjil':'Genap')), strtolower($filters['search_filter'])) !== false){
        //             if(!$isFound){
        //                 $isFound = true;
        //                 array_push($data, $item);
        //             }
        //         }

        //         if(strpos(strtolower($item->mdr_nominal), strtolower($filters['search_filter'])) !== false){
        //             if(!$isFound){
        //                 $isFound = true;
        //                 array_push($data, $item);
        //             }
        //         }

        //         if(strpos(strtolower($item->mdr_status == 1 ? 'Aktif':'Tidak Aktif'), strtolower($filters['search_filter'])) !== false){
        //             if(!$isFound){
        //                 $isFound = true;
        //                 array_push($data, $item);
        //             }
        //         }
        //     }
        //     return datatables($data)->toJson();
        // }

        return datatables($query)->toJson();
    }

    public function discount()
    {
        $query = Discount::all();
        return $query;
    }

    public function student()
    {
        $query = Register::with('participant')->where('reg_major_pass','!=', null)->get();
        return $query;
    }

    public function period($md_id)
    {
        $data = Discount::with('periodStart','periodEnd')->findorfail($md_id);
        $start = $data->periodStart->msy_code;
        $end = $data->periodEnd->msy_code;
        $arr = [$start,$end];

        while($start < $end){
            $split = str_split($start, 4);
            $year = (int) $split[0];
            $sem = (int) $split[1];
            if($sem == 1){
                $start = $year.''.($sem+1);
            }else{
                $year = $year+1;
                $start = $year.'1';
            }
            $arr[] = $start;
        }
        $period = Year::whereIn('msy_code', $arr)->orderBy('msy_code')->get();
        return $period;
    }

    public function store(DiscountReceiverNewStudentRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try{
            $data = Discount::findOrFail($validated["md_id"]);
            if(array_key_exists("msc_id",$validated)){
                $receiver = DiscountReceiver::findOrFail($validated["msc_id"]);
                if($data->mdr_nominal != $validated['mdr_nominal']){
                    $realization = $data->md_realization-$receiver->mdr_nominal+$validated["mdr_nominal"];
                    $data->update(['md_realization' =>$realization]);
                }else{
                    $realization = $data->md_realization;
                }
                if($realization > $data->md_budget){
                    $text = "Budget Tidak Mencukupi";
                    return json_encode(array('success' => false, 'message' => $text));
                }else{
                    $receiver->update($validated);
                }
                $text = "Berhasil memperbarui penerima potongan";
            }else{
                $realization = $data->md_realization+$validated["mdr_nominal"];
                if($realization > $data->md_budget){
                    $text = "Budget Tidak Mencukupi";
                    return json_encode(array('success' => false, 'message' => $text));
                }else{
                    DiscountReceiver::create($validated);
                    $data->update(['md_realization' =>$realization]);
                }
                $text = "Berhasil menambahkan penerima potongan";
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
        DB::beginTransaction();
        try{
            $receiver = DiscountReceiver::findOrFail($id);
            $data = Discount::withTrashed()->findOrFail($receiver->md_id);
            $realization = $data->md_realization-$receiver->mdr_nominal;
            $data->update(['md_realization' =>$realization]);
            $receiver->delete();
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json($e->getMessage());
        }
        return json_encode(array('success' => true, 'message' => "Berhasil menghapus penerima potongan"));
    }

    public function studyProgram($id){
        $studyProgram = Studyprogram::where('faculty_id', '=', $id)->get();
        return $studyProgram;
    }

    public function exportData(Request $request)
    {
        $textData = $request->post('data');
        $data = json_decode($textData);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //header tabel
        $sheet->setCellValue('A1', 'Nim');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'Fakultas');
        $sheet->setCellValue('D1', 'Program Studi');
        $sheet->setCellValue('E1', 'Potongan');
        $sheet->setCellValue('F1', 'Periode');
        $sheet->setCellValue('G1', 'Nominal');
        $sheet->setCellValue('H1', 'Status');

        //content table
        $row = 2;
        foreach($data as $item){
            $sheet->setCellValue('A'.$row, $item->student->student_id);
            $sheet->setCellValue('B'.$row, $item->student->fullname);
            $sheet->setCellValue('C'.$row, $item->student->study_program->faculty->faculty_name);
            $sheet->setCellValue('D'.$row, $item->student->study_program->studyprogram_type.' '.$item->student->study_program->studyprogram_name);
            $sheet->setCellValue('E'.$row, $item->discount->md_name);
            $sheet->setCellValue('F'.$row, $item->period->msy_year.' '.($item->period->msy_semester == 1 ? 'Ganjil':'Genap'));
            $sheet->setCellValue('G'.$row, $item->mdr_nominal);
            $sheet->setCellValue('H'.$row, $item->mdr_status == 1 ? 'Aktif':'Tidak Aktif');

            $row++;
        }

        $response = response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="Laporan Program Penerima Potongan.xlsx"');
        $response->send();

    }
}
