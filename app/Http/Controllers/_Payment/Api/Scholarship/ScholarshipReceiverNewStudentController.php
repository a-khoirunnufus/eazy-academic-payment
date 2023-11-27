<?php

namespace App\Http\Controllers\_Payment\Api\Scholarship;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Payment\Scholarship;
use App\Models\Payment\ScholarshipReceiver;
use App\Http\Requests\Payment\Scholarship\ScholarshipReceiverNewStudentRequest;
use App\Http\Requests\Payment\Scholarship\ScholarshipReceiverNewStudentBatchRequest;
use App\Models\Payment\Student;
use App\Models\PMB\Register;
use App\Models\Payment\Studyprogram;
use App\Models\Payment\Year;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Traits\Models\DatatableManualFilter;

class ScholarshipReceiverNewStudentController extends Controller
{
    use DatatableManualFilter;

    public function index(Request $request)
    {
        $query = ScholarshipReceiver::with(['period', 'scholarship.periodStart', 'scholarship.periodEnd', 'newStudent.studyProgram.faculty', 'newStudent.participant'])
            ->where('reg_id', '!=', null);

        $datatable = datatables($query);

        $this->applyManualFilter(
            $datatable,
            $request,
            [
                // filter attributes
                'msr_period',
                'ms_id',
                'newStudent.studyProgram.faculty_id',
                'newStudent.studyProgram.studyprogram_id',
            ],
            [
                // search attributes
                'newStudent.participant.par_fullname',
                'newStudent.reg_number',
            ],
        );

        return $datatable->toJson();
    }

    public function scholarship()
    {
        $query = Scholarship::all();
        return $query;
    }

    public function student()
    {
        $query = Register::with('participant')->where('reg_major_pass','!=', null)->get();
        return $query;
    }

    public function study_program(Request $request)
    {
        $query = Studyprogram::where('faculty_id', $request->get('id'));
        return $query->get();
    }

    public function period($ms_id)
    {
        $data = Scholarship::with('periodStart', 'periodEnd')->findorfail($ms_id);
        $start = $data->periodStart->msy_code;
        $end = $data->periodEnd->msy_code;
        $arr = [$start, $end];

        while ($start < $end) {
            $split = str_split($start, 4);
            $year = (int) $split[0];
            $sem = (int) $split[1];
            if ($sem == 1) {
                $start = $year . '' . ($sem + 1);
            } else {
                $year = $year + 1;
                $start = $year . '1';
            }
            $arr[] = $start;
        }
        $period = Year::whereIn('msy_code', $arr)->orderBy('msy_code')->get();
        return $period;
    }

    public function store(ScholarshipReceiverNewStudentRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $data = Scholarship::findOrFail($validated["ms_id"]);
            if (array_key_exists("msc_id", $validated)) {
                $receiver = ScholarshipReceiver::findOrFail($validated["msc_id"]);
                if($receiver->msr_status_generate == 1){
                    $text = "Data telah digenerate";
                    return json_encode(array('success' => false, 'message' => $text));
                }
                if ($data->msr_nominal != $validated['msr_nominal']) {
                    $realization = $data->ms_realization - $receiver->msr_nominal + $validated["msr_nominal"];
                    $data->update(['ms_realization' => $realization]);
                } else {
                    $realization = $data->ms_realization;
                }
                if ($realization > $data->ms_budget) {
                    $text = "Budget Tidak Mencukupi";
                    return json_encode(array('success' => false, 'message' => $text));
                } else {
                    $receiver->update($validated);
                }
                $text = "Berhasil memperbarui penerima beasiswa";
            } else {
                $realization = $data->ms_realization + $validated["msr_nominal"];
                if ($realization > $data->ms_budget) {
                    $text = "Budget Tidak Mencukupi";
                    return json_encode(array('success' => false, 'message' => $text));
                } else {
                    ScholarshipReceiver::create($validated);
                    $data->update(['ms_realization' => $realization]);
                }
                $text = "Berhasil menambahkan penerima beasiswa";
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json($e->getMessage());
        }
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function validateBatch(Request $request)
    {
        $receiver_count = count($request->get('reg_id') ?? []);

        $validator = Validator::make(
            $request->all(),
            [
                'reg_id' => 'required|array',
                'reg_id.*' => Rule::exists(Register::class, 'reg_id'),
                'ms_id' => 'required|array|size:'.$receiver_count,
                'ms_id.*' => Rule::exists(Scholarship::class, 'ms_id'),
                'msr_period' => 'required|array|size:'.$receiver_count,
                'msr_period.*' => Rule::exists(Year::class, 'msy_id'),
                'msr_nominal' => 'required|array|size:'.$receiver_count,
                'msr_nominal.*' => 'numeric',
                'msr_status' => 'required|array|size:'.$receiver_count,
                'msr_status.*' => 'in:0,1',
            ],
            [],
            [
                'reg_id' => 'Mahasiswa',
                'ms_id' => 'Beasiswa',
                'msr_period' => 'Periode',
                'msr_nominal' => 'Nominal',
                'msr_status' => 'Status',
                'reg_id.*' => 'Mahasiswa',
                'ms_id.*' => 'Beasiswa',
                'msr_period.*' => 'Periode',
                'msr_nominal.*' => 'Nominal',
                'msr_status.*' => 'Status',
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
            $scholarship = Scholarship::find($validated["ms_id"][$i]);
            $available_budget = $scholarship->ms_budget - $scholarship->ms_realization;
            if ($available_budget < $validated['msr_nominal'][$i]) {
                $errors[] = 'Budget beasiswa tidak cukup!';
            }

            // duplicate data
            $scholarship_receiver = ScholarshipReceiver::where([
                'ms_id' => $validated['ms_id'][$i],
                'reg_id' => $validated['reg_id'][$i],
                'msr_period' => $validated['msr_period'][$i],
            ])->exists();
            if ($scholarship_receiver) {
                $errors[] = 'Data duplikat!';
            }

            if (count($errors) > 0) {
                $validation_errors['row_'.($i+1)] = $errors;
            }
        }

        return response()->json($validation_errors);
    }

    public function storeBatch(ScholarshipReceiverNewStudentBatchRequest $request)
    {
        /**
         * [
         *  [
         *      'ms_id' => 5,
         *      'reg_id' => 1874,
         *      'msr_period' => 1,
         *      'msr_nominal' => 1000000,
         *      'msr_status' => 1,
         *  ],
         *  ...
         * ]
         */

        $validated = $request->validated();
        $receiver_count = count($validated['reg_id']);

        try {
            DB::beginTransaction();

            // validation: budget checking
            for ($i=0; $i < $receiver_count; $i++) {

                $scholarship = Scholarship::find($validated["ms_id"][$i]);
                $available_budget = $scholarship->ms_budget - $scholarship->ms_realization;
                $is_applied = $available_budget >= (int)$validated['msr_nominal'][$i];
                if (!$is_applied) {
                    throw new \Exception('Budget tidak cukup untuk beasiswa: '.$scholarship->ms_name, 1);
                }

                ScholarshipReceiver::create([
                    'reg_id' => $validated['reg_id'][$i],
                    'ms_id' => $validated['ms_id'][$i],
                    'msr_period' => $validated['msr_period'][$i],
                    'msr_nominal' => $validated['msr_nominal'][$i],
                    'msr_status' => $validated['msr_status'][$i],
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
            'message' => 'Berhasil menambahkan penerima beasiswa',
        ]);

    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $receiver = ScholarshipReceiver::findOrFail($id);
            $data = Scholarship::withTrashed()->findOrFail($receiver->ms_id);
            $realization = $data->ms_realization - $receiver->msr_nominal;
            $data->update(['ms_realization' => $realization]);
            $receiver->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json($e->getMessage());
        }
        return json_encode(array('success' => true, 'message' => "Berhasil menghapus penerima beasiswa"));
    }

    public function exportData(Request $request)
    {
        $textData = $request->post('data');
        $data = json_decode($textData);
        // var_dump($textData);
        // var_dump($data);


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //header table
        $sheet->setCellValue('A1', 'NIM');
        $sheet->setCellValue('B1', 'Mahasiswa');
        $sheet->setCellValue('C1', 'Fakultas');
        $sheet->setCellValue('D1', 'Program Studi');
        $sheet->setCellValue('E1', 'Nama Beasiswa');
        $sheet->setCellValue('F1', 'Instansi/Perusahaan');
        $sheet->setCellValue('G1', 'PIC');
        $sheet->setCellValue('H1', 'Periode');
        $sheet->setCellValue('I1', 'Nominal');
        $sheet->setCellValue('J1', 'Status');

        // data table
        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item->student->student_id);
            $sheet->setCellValue('B' . $row, $item->student->fullname);
            $sheet->setCellValue('C' . $row, $item->student->study_program->faculty->faculty_name);
            $sheet->setCellValue('D' . $row, $item->student->study_program->studyprogram_type . ' ' . $item->student->study_program->studyprogram_name);
            $sheet->setCellValue('E' . $row, $item->scholarship->ms_name);
            $sheet->setCellValue('F' . $row, $item->scholarship->ms_from);
            $sheet->setCellValue('G' . $row, $item->scholarship->ms_from_name);
            $sheet->setCellValue('H' . $row, $item->period->msy_year . ' ' . ($item->period->msy_semester == 1 ? 'Ganjil' : 'Genap'));
            $sheet->setCellValue('I' . $row, $item->msr_nominal);
            $sheet->setCellValue('J' . $row, ($item->msr_status == 1 ? "Aktif" : "Tidak Aktif"));
            $row++;
        }

        foreach(range('A', 'J') as $colId){
            $sheet->getColumnDimension($colId)->setAutoSize(true);
        }

        $response = response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="Laporan Mahasiswa Penerima Beasiswa.xlsx"');
        $response->send();
    }
}
