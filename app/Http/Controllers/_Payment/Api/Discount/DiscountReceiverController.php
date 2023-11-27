<?php

namespace App\Http\Controllers\_Payment\API\Discount;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\Payment\Discount;
use App\Models\Payment\DiscountReceiver;
use App\Http\Requests\Payment\Discount\DiscountReceiverRequest;
use App\Http\Requests\Payment\Discount\DiscountReceiverBatchRequest;
use App\Models\Payment\Student;
use App\Models\Payment\Studyprogram;
use App\Models\Payment\Year;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Traits\Models\DatatableManualFilter;

class DiscountReceiverController extends Controller
{
    use DatatableManualFilter;

    public function index(Request $request)
    {
        $query = DiscountReceiver::with(['period', 'discount.periodStart', 'discount.periodEnd', 'student.studyProgram.faculty'])
            ->where('reg_id', '=', null);

        $datatable = datatables($query);

        $this->applyManualFilter(
            $datatable,
            $request,
            [
                // filter attributes
                'mdr_period',
                'md_id',
                'student.studyProgram.faculty_id',
                'student.studyprogram_id',
            ],
            [
                // search attributes
                'student.fullname',
                'student.student_id',
            ],
        );

        return $datatable->toJson();
    }

    public function discount()
    {
        $query = Discount::all();
        return $query;
    }

    public function student()
    {
        $query = Student::all();
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

    public function store(DiscountReceiverRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try{
            $data = Discount::findOrFail($validated["md_id"]);
            if(array_key_exists("msc_id",$validated)){
                $receiver = DiscountReceiver::findOrFail($validated["msc_id"]);
                if($receiver->mdr_status_generate == 1){
                    $text = "Data telah digenerate";
                    return json_encode(array('success' => false, 'message' => $text));
                }
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

    public function validateBatch(Request $request)
    {
        $receiver_count = count($request->get('student_number') ?? []);

        $validator = Validator::make(
            $request->all(),
            [
                'student_number' => 'required|array',
                'student_number.*' => Rule::exists(Student::class, 'student_number'),
                'md_id' => 'required|array|size:'.$receiver_count,
                'md_id.*' => Rule::exists(Discount::class, 'md_id'),
                'mdr_period' => 'required|array|size:'.$receiver_count,
                'mdr_period.*' => Rule::exists(Year::class, 'msy_id'),
                'mdr_nominal' => 'required|array|size:'.$receiver_count,
                'mdr_nominal.*' => 'numeric',
                'mdr_status' => 'required|array|size:'.$receiver_count,
                'mdr_status.*' => 'in:0,1',
            ],
            [],
            [
                'student_number' => 'NIM Mahasiswa',
                'md_id' => 'Potongan',
                'mdr_period' => 'Periode',
                'mdr_nominal' => 'Nominal',
                'mdr_status' => 'Status',
                'student_number.*' => 'NIM Mahasiswa',
                'md_id.*' => 'Potongan',
                'mdr_period.*' => 'Periode',
                'mdr_nominal.*' => 'Nominal',
                'mdr_status.*' => 'Status',
            ]
        );

        $validation_errors = [];

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            foreach ($errors as $key => $values) {
                $attr = explode('.', $key);
                $row_idx = (int)$attr[1] + 1;
                foreach ($values as $value) {
                    $validation_errors['row_'.$row_idx][] = $value;
                }
            }
            return response()->json($validation_errors);
        }

        $validated = $validator->validated();

        // validation
        for ($i=0; $i < $receiver_count; $i++) {

            $errors = [];

            // budget checking
            $discount = Discount::find($validated["md_id"][$i]);
            $available_budget = $discount->md_budget - $discount->md_realization;
            if ($available_budget < (int)$validated['mdr_nominal'][$i]) {
                $errors[] = 'Budget potongan tidak cukup!';
            }

            // duplicate data
            $discount_receiver = DiscountReceiver::where([
                'md_id' => $validated['md_id'][$i],
                'student_number' => $validated['student_number'][$i],
                'mdr_period' => $validated['mdr_period'][$i],
            ])->exists();
            if ($discount_receiver) {
                $errors[] = 'Data duplikat!';
            }

            if (count($errors) > 0) {
                $validation_errors['row_'.($i+1)] = $errors;
            }
        }

        return response()->json($validation_errors);
    }

    public function storeBatch(DiscountReceiverBatchRequest $request)
    {
        /**
         * [
         *  [
         *      'md_id' => 5,
         *      'student_number' => 1874,
         *      'mdr_period' => 1,
         *      'mdr_nominal' => 1000000,
         *      'mdr_status' => 1,
         *  ],
         *  ...
         * ]
         */

        $validated = $request->validated();
        $receiver_count = count($validated['student_number']);

        try {
            DB::beginTransaction();

            // validation: budget checking
            for ($i=0; $i < $receiver_count; $i++) {

                $discount = Discount::find($validated["md_id"][$i]);
                $available_budget = $discount->md_budget - $discount->md_realization;
                $is_applied = $available_budget >= (int)$validated['mdr_nominal'][$i];
                if (!$is_applied) {
                    throw new \Exception('Budget tidak cukup untuk potongan: '.$discount->md_name, 1);
                }

                DiscountReceiver::create([
                    'student_number' => $validated['student_number'][$i],
                    'md_id' => $validated['md_id'][$i],
                    'mdr_period' => $validated['mdr_period'][$i],
                    'mdr_nominal' => $validated['mdr_nominal'][$i],
                    'mdr_status' => $validated['mdr_status'][$i],
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();

            if ($th->getCode() == 1) {
                return response()->json([
                    'message' => $th->getMessage(),
                    'success' => false,
                ], 422);
            } else {
                throw $th;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menambahkan penerima potongan',
        ]);

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
