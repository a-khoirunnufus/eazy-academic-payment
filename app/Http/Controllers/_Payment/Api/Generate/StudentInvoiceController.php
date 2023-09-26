<?php

namespace App\Http\Controllers\_Payment\Api\Generate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\Studyprogram;
use App\Models\Payment\Faculty;
use App\Models\Payment\PeriodPath;
use App\Models\Payment\Student;
use App\Models\Payment\ActiveYear;
use App\Models\Payment\Year;
use App\Models\Payment\ComponentDetail;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentBill;
use App\Models\Payment\PaymentDetail;
use App\Models\Payment\MasterJob;
use App\Jobs\Payment\GenerateInvoice;
use App\Jobs\Payment\GenerateBulkInvoice;
use App\Models\Payment\DiscountReceiver;
use App\Models\Payment\ScholarshipReceiver;
use App\Models\Payment\CreditSchema;
use App\Models\Student\DispensationSubmission;
use App\Models\Student\CreditSubmission;
use App\Models\PMB\PaymentRegisterDetail;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\DB;
use App\Traits\Payment\LogActivity;
use App\Traits\Payment\General;
use App\Enums\Payment\LogStatus;
use Config;

class StudentInvoiceController extends Controller
{
    use LogActivity, General;

    /**
     * View Only
     */
    // DT Fakultas & Prodi
    public function index(Request $request)
    {
        $activeSchoolYearCode = $this->getActiveSchoolYearCode();
        $query = Faculty::with('studyProgram')->orderBy('faculty_name')->get();
        $student = Student::with(['payment' => function ($query) use ($activeSchoolYearCode) {
            $query->where('prr_school_year', $activeSchoolYearCode);
        }])->get();


        $result = collect();
        foreach ($query as $item) {
            $collection = collect();
            $arrSp = [];
            $total_invoice = 0;
            $total_generate = 0;
            if ($item->studyProgram) {
                foreach ($item->studyProgram as $sp) {
                    $arrSp[] = $sp->studyprogram_id;
                }
                $filter = $student->whereIn('studyprogram_id', $arrSp);
                $total_student = $student->whereIn('studyprogram_id', $arrSp)->count();
                foreach ($filter as $t) {
                    if ($t->payment) {
                        $total_invoice = $total_invoice + $t->payment->prr_total;
                        $total_generate = $total_generate + 1;
                    }
                }
            }
            $data = ['faculty' => $item, 'study_program' => null, 'total_student' => $total_student, 'total_invoice' => $total_invoice, 'total_generate' => $total_generate];
            $result->push($data);
            if ($item->studyProgram) {
                foreach ($item->studyProgram as $sp) {
                    $filter = $student->whereIn('studyprogram_id', $sp->studyprogram_id);
                    $total_student = $student->whereIn('studyprogram_id', $sp->studyprogram_id)->count();
                    $total_invoice = 0;
                    $total_generate = 0;
                    foreach ($filter as $t) {
                        if ($t->payment) {
                            $total_invoice = $total_invoice + $t->payment->prr_total;
                            $total_generate = $total_generate + 1;
                        }
                    }
                    $data = ['faculty' => null, 'study_program' => $sp, 'total_student' => $total_student, 'total_invoice' => $total_invoice, 'total_generate' => $total_generate];
                    $result->push($data);
                }
            }
        }
        return datatables($result)->toJson();
    }

    // DT Per Prodi / Fakultas
    public function detail(Request $request)
    {
        // dd($request);
        $data['f'] = $request->query()['f'];
        $data['sp'] = $request->query()['sp'];
        $query = Student::query();
        $query = $query->with('lectureType', 'period', 'payment', 'path', 'year', 'studyProgram')
            ->join('masterdata.ms_studyprogram as sp', 'sp.studyprogram_id', 'hr.ms_student.studyprogram_id')

            ->select('hr.ms_student.*');
        if ($data['f'] != 0 && $data['f']) {
            $query = $query->where('sp.faculty_id', $data['f']);
        }
        if ($data['sp'] != 0 && $data['sp']) {
            $query = $query->where('sp.studyprogram_id', $data['sp']);
        }
        if ($request->query('year') !== "all") {
            $query = $query->where('msy_id', '=', $request->query('year'));
            // $query = $query->whereIn('studyprogram_id', $year);
        }
        if ($request->query('path') !== "all") {
            $query = $query->where('path_id', '=', $request->query('path'));
        }
        if ($request->query('period') !== "all") {
            $query = $query->where('period_id', '=', $request->query('period'));
        }
        // dd($query->get());
        return datatables($query->get())->toJson();
    }

    // Header Per Prodi / Fakultas
    public function header(Request $request)
    {
        // dd($request);
        $data['f'] = $request->query()['f'];
        $data['sp'] = $request->query()['sp'];

        // dd($data);
        $faculty = Faculty::find($data['f']);
        if ($data['sp'] != 0) {
            $studyProgram = Studyprogram::with('faculty')->find($data['sp']);
        } else {
            $studyProgram = "-";
        }
        $date = Carbon::today()->toDateString();
        $activeSchoolYear = $this->getActiveSchoolYear();

        if ($faculty) {
            $header['faculty'] = $faculty->faculty_name;
        } else {
            $header['faculty'] = $studyProgram->faculty->faculty_name;
        }
        $header['study_program'] = ($data['sp'] != 0) ? $studyProgram->studyprogram_type . ' ' . $studyProgram->studyprogram_name : $studyProgram;
        $header['active'] = $activeSchoolYear;

        // dd($query->get());
        return $header;
    }

    // Header Fakultas & Prodi
    public function headerAll()
    {
        $activeSchoolYear = $this->getActiveSchoolYear();

        $header['university'] = "Universitas Telkom";
        $header['active'] = $activeSchoolYear;

        return $header;
    }

    public function choice($f, $sp)
    {
        // dd($f);
        $activeSchoolYearCode = $this->getActiveSchoolYearCode();
        $student = Student::query()
            ->with('studyProgram', 'lectureType', 'period', 'path', 'year', 'getComponent')
            ->leftJoin('finance.payment_re_register', function ($join) use ($activeSchoolYearCode) {
                $join->on('finance.payment_re_register.student_number', '=', 'hr.ms_student.student_number');
                $join->where('finance.payment_re_register.prr_school_year', '=', $activeSchoolYearCode);
                $join->where('finance.payment_re_register.deleted_at', '=', null);
            });

        if ($f && $f != 0) {
            $sp_in_faculty = Studyprogram::where('faculty_id', $f)->pluck('studyprogram_id')->toArray();
            $student = $student->whereIn('studyprogram_id', $sp_in_faculty);
        } else {
            $student = $student->where('studyprogram_id', $sp);
        }
        $student = $student
            ->where('student_type_id', 1)
            ->select('hr.ms_student.mlt_id', 'hr.ms_student.path_id', 'hr.ms_student.period_id', 'hr.ms_student.msy_id', 'hr.ms_student.studyprogram_id', DB::raw('count(hr.ms_student.*) as total_student'), DB::raw('count(prr_id) as total_generate'))
            ->groupBy('hr.ms_student.mlt_id', 'hr.ms_student.path_id', 'hr.ms_student.period_id', 'hr.ms_student.msy_id', 'hr.ms_student.studyprogram_id')
            ->get();

        foreach ($student as $key => $s) {
            $getComponent = $s->getComponent()
                ->where('path_id', $s->path_id)
                ->where('period_id', $s->period_id)
                ->where('msy_id', $s->msy_id)
                ->where('mlt_id', $s->mlt_id)
                ->get();
            $student[$key]->setRelation('component_filter', $getComponent->values());
        }

        return $student;
    }

    public function choiceAll()
    {
        // dd($f);
        $activeSchoolYearCode = $this->getActiveSchoolYearCode();
        $student = Student::query()
            ->with('studyProgram', 'getComponent')
            ->join('masterdata.ms_studyprogram', 'masterdata.ms_studyprogram.studyprogram_id', 'hr.ms_student.studyprogram_id')
            ->join('masterdata.ms_faculties', 'masterdata.ms_faculties.faculty_id', 'masterdata.ms_studyprogram.faculty_id')
            ->leftJoin('finance.payment_re_register', function ($join) use ($activeSchoolYearCode) {
                $join->on('finance.payment_re_register.student_number', '=', 'hr.ms_student.student_number');
                $join->where('finance.payment_re_register.prr_school_year', '=', $activeSchoolYearCode);
                $join->where('finance.payment_re_register.deleted_at', '=', null);
            });

        $student = $student
            ->where('student_type_id', 1)
            ->select('hr.ms_student.studyprogram_id', 'masterdata.ms_faculties.faculty_id', DB::raw('count(hr.ms_student.*) as total_student'), DB::raw('count(prr_id) as total_generate'))
            ->groupBy('hr.ms_student.studyprogram_id', 'masterdata.ms_faculties.faculty_id')
            ->get();

        foreach ($student as $key => $s) {
            $getComponent = $s->getComponent()
                ->where('path_id', $s->path_id)
                ->where('period_id', $s->period_id)
                ->where('msy_id', $s->msy_id)
                ->where('mlt_id', $s->mlt_id)
                ->get();
            $student[$key]->setRelation('component_filter', $getComponent->values());
        }

        return $student;
    }

     /**
     * Function
     */
    public function studentGenerate(Request $request)
    {
        $student = Student::with('getComponent')->findorfail($request['student_number']);
        $log = $this->addToLog('Generate Tagihan Mahasiswa Lama',$this->getAuthId(),LogStatus::Process,$request->url);
        $result = $this->storeStudentGenerate($student,$log->log_id);
        $this->updateLogStatus($log,$result);
        return $result;
    }

    public function storeStudentGenerate($student,$log_id)
    {
        $components = $student->getComponent()
            ->where('path_id', $student->path_id)
            ->where('period_id', $student->period_id)
            ->where('msy_id', $student->msy_id)
            ->where('mlt_id', $student->mlt_id)
            ->get();

        if ($components) {
            $prr_total = 0;
            foreach ($components as $item) {
                $prr_total = $prr_total + $item->cd_fee;
            }
            DB::beginTransaction();
            try {
                $payment = Payment::create([
                    'prr_status' => 'belum lunas',
                    'prr_total' => $prr_total,
                    'prr_paid_net' => $prr_total,
                    'student_number' => $student->student_number,
                    'prr_school_year' => $this->getActiveSchoolYearCode(),
                ]);

                foreach ($components as $item) {
                    PaymentDetail::create([
                        'prr_id' => $payment->prr_id,
                        'prrd_component' => $item->component->msc_name,
                        'prrd_amount' => $item->cd_fee,
                        'is_plus' => 1,
                        'type' => 'component',
                    ]);
                }
                $this->addToLogDetail($log_id,$this->getLogTitle($student),LogStatus::Success);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                $this->addToLogDetail($log_id,$this->getLogTitle($student,null,$e->getMessage()),LogStatus::Failed);
                return response()->json($e->getMessage());
            }
        } else {
            $text= 'Komponen Tagihan Tidak Ditemukan';
            $this->addToLogDetail($log_id,$this->getLogTitle($student,null,$text),LogStatus::Failed);
            return json_encode(array('success' => false, 'message' => $text));
        }
        $text = "Berhasil generate tagihan mahasiswa " . $student->fullname;
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function studentBulkGenerate(Request $request)
    {
        if ($request->generate_checkbox) {
            $log = $this->addToLog('Generate Bulk Tagihan Mahasiswa Lama',$this->getAuthId(),LogStatus::Process,$request->url);
            GenerateBulkInvoice::dispatch($request->generate_checkbox, $request->from,$log)->onQueue('bulk');
            return json_encode(array('success' => true, 'message' => "Generate Tagihan Sedang Diproses"));
        } else {
            return json_encode(array('success' => false, 'message' => "Belum ada data yang dipilih!"));
        }
    }

    public function storeBulkStudentGenerate($generate_checkbox, $from, $log)
    {
        foreach ($generate_checkbox as $item) {
            if ($item != "null") {
                // Parsing the key from string
                $store = explode("_", $item);
                $studyprogram_id = ($store[0]) ? $store[0] : 0;
                $msy_id = ($store[1]) ? $store[1] : 0;
                $path_id = ($store[2]) ? $store[2] : 0;
                $period_id = ($store[3]) ? $store[3] : 0;
                $mlt_id = ($store[4]) ? $store[4] : 0;

                // Starting Query
                $students = Student::query();
                $students = $students->with('getComponent')
                    ->where('studyprogram_id', $studyprogram_id);

                // If from details, not index, using more parameters
                if ($from == 'detail') {
                    $students = $students
                        ->where('msy_id', $msy_id)
                        ->where('path_id', $path_id)
                        ->where('period_id', $period_id)
                        ->where('mlt_id', $mlt_id);
                }
                $students = $students->get();

                // Loop for each student
                foreach ($students as $student) {
                    $payment = Payment::where('student_number', $student->student_number)->where('prr_school_year', $this->getActiveSchoolYearCode())->first();
                    if (!$payment) {
                        GenerateInvoice::dispatch($student, $log)->onQueue('invoice');
                    }
                }
                GenerateInvoice::dispatch(null, $log)->onQueue('invoice');
            }
        }
        return true;
    }

    public function delete(Request $request,$prr_id)
    {
        // Deleting Detail invoice
        $log = $this->addToLog('Delete Tagihan Mahasiswa Lama',$this->getAuthId(),LogStatus::Process,$request->url);
        $result = $this->deleteProcess($request,$prr_id,$log->log_id);
        $this->updateLogStatus($log,$result);
        return $result;
    }

    public function deleteProcess(Request $request,$prr_id,$log_id)
    {
        // Deleting Detail invoice
        try {
            $detail = PaymentDetail::with('payment')->where('prr_id', $prr_id)->get();
            if ($detail) {
                foreach ($detail as $item) {
                    if ($item->type == 'discount' or $item->type == 'scholarship') {
                        $text = "Terdapat potongan, beasiswa, cicilan ataupun dispensasi yang sudah disetujui pada tagihan ini.";
                        $this->addToLogDetail($log_id,$this->getLogTitle($item->payment->student,null,$text),LogStatus::Failed);
                        return json_encode(array('success' => false, 'message' => $text));
                    }
                }
            }
            // Deleting Detail Bill
            $data = Payment::with('student')->findorfail($prr_id);
            if(Carbon::createFromFormat('Y-m-d', Config::get('app.payment_delete_lock')) <= Carbon::now()){
                $text = "Sudah melebihi batas waktu penghapusan data";
                $this->addToLogDetail($log_id,$this->getLogTitle($data->student,null,$text),LogStatus::Failed);
                return json_encode(array('success' => false, 'message' => $text));
            }
            if ($data->prr_status == 'kredit') {
                $text = "Terdapat potongan, beasiswa, cicilan ataupun dispensasi yang sudah disetujui pada tagihan ini.";
                $this->addToLogDetail($log_id,$this->getLogTitle($data->student,null,$text),LogStatus::Failed);
                return json_encode(array('success' => false, 'message' => $text));
            }
            DB::beginTransaction();
            $data->delete();
            PaymentDetail::where('prr_id', $prr_id)->delete();
            PaymentBill::where('prr_id', $prr_id)->delete();
            $this->addToLogDetail($log_id,$this->getLogTitle($data->student),LogStatus::Success);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $this->addToLogDetail($log_id,$this->getLogTitle($data->student,null,$e->getMessage()),LogStatus::Failed);
            return response()->json($e->getMessage());
        }
        return json_encode(array('success' => true, 'message' => "Berhasil menghapus tagihan"));
    }


    // Mas sukri
    public function deleteByProdi($prodi_id)
    {
        return json_encode($this->deleteTagihanByProdi($prodi_id), JSON_PRETTY_PRINT);
    }

    public function regenerateByProdi($prodi_id)
    {
        return json_encode($this->regenerateTagihanByProdi($prodi_id), JSON_PRETTY_PRINT);
    }

    public function deleteInvoiceUniv()
    {
        $faculty = Faculty::all('faculty_id');

        $dataSuccess = 0;
        $dataFailed = 0;
        foreach ($faculty as $list) {
            $target_faculty = json_decode($this->deleteByFaculty($list->faculty_id));

            if (!$target_faculty['status']) {
                return json_encode($target_faculty, JSON_PRETTY_PRINT);
            }
            $dataSuccess += $target_faculty['data_success'];
            $dataFailed += $target_faculty['data_failed'];
        }

        return array(
            'status' => true,
            'msg' => 'data sukses: ' . $dataSuccess . ', data gagal: ' . $dataFailed,
            'data_success' => $dataSuccess,
            'data_failed' => $dataFailed
        );
    }

    public function deleteByFaculty($faculty_id)
    {
        $studyprogram = Studyprogram::where('faculty_id', '=', $faculty_id)->get();

        $dataSuccess = 0;
        $dataFailed = 0;
        foreach ($studyprogram as $item) {
            $target_prodi = json_decode($this->deleteByProdi($item->studyprogram_id), true);

            if (!$target_prodi['status']) {
                return json_encode($target_prodi, JSON_PRETTY_PRINT);
            }

            $dataSuccess += $target_prodi['data_success'];
            $dataFailed += $target_prodi['data_failed'];
        }

        return array(
            'status' => true,
            'msg' => 'data sukses: ' . $dataSuccess . ', data gagal: ' . $dataFailed,
            'data_success' => $dataSuccess,
            'data_failed' => $dataFailed
        );
    }

    public function regenerateByFaculty($faculty_id)
    {
        $studyprogram = Studyprogram::where('faculty_id', '=', $faculty_id)->get();

        foreach ($studyprogram as $item) {
            $target_prodi = json_decode($this->regenerateByProdi($item->studyprogram_id), true);

            if (!$target_prodi['status']) {
                return json_encode($target_prodi, JSON_PRETTY_PRINT);
            }
        }

        return array(
            'status' => true,
            'msg' => 'Berhasil menggenerate ulang'
        );
    }


    public function deleteBulk($f, $sp)
    {
        // dd($sp);
        $activeSchoolYearCode = $this->getActiveSchoolYearCode();
        $invoice = Payment::query()
            ->join('hr.ms_student', 'hr.ms_student.student_number', 'finance.payment_re_register.student_number')
            ->where('finance.payment_re_register.prr_school_year', '=', $activeSchoolYearCode)
            ->where('finance.payment_re_register.deleted_at', '=', null);

        if ($f && $f != 0) {
            $sp_in_faculty = Studyprogram::where('faculty_id', $f)->pluck('studyprogram_id')->toArray();
            $invoice = $invoice->whereIn('studyprogram_id', $sp_in_faculty);
        } else {
            $invoice = $invoice->where('studyprogram_id', $sp);
        }
        $invoice = $invoice->delete();

        return json_encode(array('success' => true, 'message' => "Berhasil menghapus tagihan"));
    }

    public function deleteTagihanByProdi($prodi_id)
    {
        $dataSuccess = 0;
        $dataFailed = 0;
        try {
            $data = DB::table('finance.payment_re_register as prr')
                ->select('prr.prr_id')
                ->join('hr.ms_student as student', 'student.student_number', '=', 'prr.student_number')
                ->where('student.studyprogram_id', '=', $prodi_id)
                ->whereNull('prr.deleted_at')
                ->get();

            /*
                hapus data jika tidak terdapat pembayaran yang sudah dilakukan
                dan juga beasiswa dan potongan yang belum digenerate
            */
            foreach ($data as $item) {
                $target_detail = DB::table('finance.payment_re_register_detail')
                    ->select('prrd_id')
                    ->where('prr_id', '=', $item->prr_id)
                    ->whereNull('deleted_at')
                    ->whereIn('type', ['scholarship', 'discount'])
                    ->get();

                $target_bill = DB::table('finance.payment_re_register_bill')
                    ->select('prrb_id')
                    ->where('prr_id', '=', $item->prr_id)
                    ->whereNull('deleted_at')
                    ->get();

                if (count($target_detail) > 0 || count($target_bill) > 0) {
                    $dataFailed++;
                } else {
                    $target_detail_update = DB::table('finance.payment_re_register')
                        ->where('prr_id', '=', $item->prr_id)
                        ->update(['deleted_at' => date("Y-m-d H:i:s")]);

                    $dataSuccess++;
                }
            }
        } catch (QueryException $e) {
            return array(
                'status' => false,
                'msg' => 'error system: ' . $e->getMessage(),
                'data_success' => $dataSuccess,
                'data_failed' => $dataFailed
            );
        }

        return array(
            'status' => true,
            'msg' => 'data sukses: ' . $dataSuccess . ', data gagal: ' . $dataFailed,
            'data_success' => $dataSuccess,
            'data_failed' => $dataFailed
        );
    }

    public function regenerateTagihanByProdi($prodi_id)
    {
        try {
            $prr = DB::table('finance.payment_re_register as prr')
                ->select('prr.prr_id', 'prr.student_number')
                ->join('hr.ms_student as student', 'student.student_number', '=', 'prr.student_number')
                ->whereNull('prr.deleted_at')
                ->where('student.studyprogram_id', '=', $prodi_id)
                ->get();

            foreach ($prr as $list) {
                $payment_bill = DB::table('finance.payment_re_register_detail as prrd')
                    ->where('prr_id', '=', $list->prr_id)
                    ->whereNull('deleted_at')
                    ->update(['deleted_at' => date("Y-m-d H:i:s")]);

                $component = DB::table('finance.component_detail as cd')
                    ->select('c.msc_name as component_name', 'cd.cd_fee as fee')
                    ->join('hr.ms_student as student', function ($join) {
                        $join->on('student.studyprogram_id', '=', 'cd.mma_id')
                            ->on('student.period_id', '=', 'cd.period_id')
                            ->on('student.path_id', '=', 'cd.path_id')
                            ->on('student.msy_id', '=', 'cd.msy_id');
                    })
                    ->join('finance.ms_component as c', 'c.msc_id', '=', 'cd.msc_id')
                    ->where('student.student_number', '=', $list->student_number)
                    ->get();

                foreach ($component as $item) {
                    $prrd = PaymentDetail::create([
                        'prr_id' => $list->prr_id,
                        'prrd_component' => $item->component_name,
                        'prrd_amount' => $item->fee,
                        'created_at' => date("Y-m-d H:i:s"),
                        'is_plus' => 1,
                        'type' => 'component'
                    ]);
                }

                $scholarship = DB::table('finance.ms_scholarship_receiver as msr')
                    ->join('finance.ms_scholarship as scholarship', 'scholarship.ms_id', '=', 'msr.ms_id')
                    ->where('student_number', '=', $list->student_number)
                    ->where('msr_status_generate', '=', 1)
                    ->where('msr_status', '=', '1')
                    ->where('prr_id', '=', $list->prr_id)
                    ->whereNull('msr.deleted_at')
                    ->get('msr.msr_nominal as fee', 'scholarship.ms_name as component_name');

                foreach ($scholarship as $item) {
                    $prrd = PaymentDetail::create([
                        'prr_id' => $list->prr_id,
                        'prrd_component' => $item->component_name,
                        'prrd_amount' => $item->fee,
                        'created_at' => date("Y-m-d H:i:s"),
                        'is_plus' => 0,
                        'type' => 'scholarship'
                    ]);
                }

                $discount = DB::table('finance.ms_discount_receiver as mdr')
                    ->join('finance.ms_discount as md', 'md.md_id', '=', 'mdr.md_id')
                    ->where('student_number', '=', $list->student_number)
                    ->where('mdr_status_generate', '=', 1)
                    ->where('mdr_status', '=', '1')
                    ->where('prr_id', '=', $list->prr_id)
                    ->whereNull('mdr.deleted_at')
                    ->get('mdr_nominal as fee', 'md.md_name as component_name');

                foreach ($discount as $item) {
                    $prrd = PaymentDetail::create([
                        'prr_id' => $list->prr_id,
                        'prrd_component' => $item->component_name,
                        'prrd_amount' => $item->fee,
                        'created_at' => date("Y-m-d H:i:s"),
                        'is_plus' => 0,
                        'type' => 'discount'
                    ]);
                }
            }
        } catch (QueryException $e) {
            return array(
                'status' => false,
                'msg' => 'error system: ' . $e->getMessage()
            );
        }

        return array(
            'status' => true,
            'msg' => 'Berhasil menggenerate ulang'
        );
    }

    public function regenerateTagihanByStudent(Request $request, $prr_id)
    {
        $log = $this->addToLog('Regenerate Tagihan Mahasiswa Lama',$this->getAuthId(),LogStatus::Process,$request->url);
        $result = $this->regenerateProcess($prr_id,$log->log_id);
        $this->updateLogStatus($log,$result);
        return $result;
    }

    public function regenerateProcess($prr_id, $log_id){
        $data = Payment::with('student','paymentDetail','paymentBill')->findorfail($prr_id);
        if($data->paymentBill){
            foreach($data->paymentBill as $item){
                if($item->prrb_status == 'lunas'){
                    $text= 'Terdapat tagihan yang sudah dilunasi';
                    $this->addToLogDetail($log_id,$this->getLogTitle($data->student,null,$text),LogStatus::Failed);
                    return json_encode(array('success' => false, 'message' => $text));
                }
            }
        }
        $components = $data->student->getComponent()
        ->where('path_id', $data->student->path_id)
        ->where('period_id', $data->student->period_id)
        ->where('msy_id', $data->student->msy_id)
        ->where('mlt_id', $data->student->mlt_id)
        ->get();

        if ($components) {
            $prr_total = 0;
            foreach ($components as $item) {
                $prr_total = $prr_total + $item->cd_fee;
            }
            DB::beginTransaction();
            try {
                $payment = Payment::create([
                    'prr_status' => 'belum lunas',
                    'prr_total' => $prr_total,
                    'prr_paid_net' => $prr_total,
                    'student_number' => $data->student->student_number,
                    'prr_school_year' => $this->getActiveSchoolYearCode(),
                    'prr_dispensation_date' => $data->prr_dispensation_date,
                ]);

                foreach ($components as $item) {
                    PaymentDetail::create([
                        'prr_id' => $payment->prr_id,
                        'prrd_component' => $item->component->msc_name,
                        'prrd_amount' => $item->cd_fee,
                        'is_plus' => 1,
                        'type' => 'component',
                    ]);
                }
                if($data->paymentDetail){
                    foreach($data->paymentDetail as $item){
                        if($item->type == 'scholarship' || $item->type == 'discount'){
                            PaymentDetail::create([
                                'prr_id' => $payment->prr_id,
                                'prrd_component' => $item->prrd_component,
                                'prrd_amount' => $item->prrd_amount,
                                'is_plus' => $item->is_plus,
                                'type' => $item->type,
                                'reference_table' => $item->reference_table,
                                'reference_id' => $item->reference_id,
                            ]);
                            $prr_total = $prr_total - $item->prrd_amount;
                            $payment->update(['prr_total' => $prr_total,'prr_paid_net' => $prr_total]);
                        }
                    }
                }
                $scholarship = ScholarshipReceiver::where('prr_id',$prr_id)->get();
                if($scholarship){
                    foreach($scholarship as $item){
                        ScholarshipReceiver::where('msr_id',$item->msr_id)->update(['prr_id'=> $payment->prr_id]);
                    }
                }

                $discount = DiscountReceiver::where('prr_id',$prr_id)->get();
                if($discount){
                    foreach($discount as $item){
                        DiscountReceiver::where('mdr_id',$item->mdr_id)->update(['prr_id'=> $payment->prr_id]);
                    }
                }

                $dispensation = DispensationSubmission::where('prr_id',$prr_id)->first();
                if($dispensation){
                    $dispensation->update(['prr_id'=> $payment->prr_id]);
                }

                $credit = CreditSubmission::where('prr_id',$prr_id)->first();
                if($credit){
                    if($credit->cs_id){
                        $credit->update(['prr_id'=> $payment->prr_id]);
                        $schema = CreditSchema::with('creditSchemaDetail')->findorfail($credit->cs_id);
                        if($schema->creditSchemaDetail){
                            foreach($schema->creditSchemaDetail as $item){
                                $temp_amount = $prr_total*$item->csd_percentage/100;
                                PaymentBill::create([
                                    'prr_id' => $payment->prr_id,
                                    'prrb_status' => 'belum lunas',
                                    'prrb_due_date' => $item->creditSchemaDeadline->cse_deadline,
                                    'prrb_amount' => $temp_amount,
                                    'prrb_order' => $item->csd_order,
                                ]);
                            }
                        }else{
                        $credit->update(['mcs_status'=> 0]);
                        $this->addToLogDetail($log_id,$this->getLogTitle($data->student,null,'Proses regenerate pengajuan kredit gagal, harap melakukan approval ulang'),LogStatus::Failed);
                        }
                    }else{
                        $credit->update(['mcs_status'=> 0]);
                        $this->addToLogDetail($log_id,$this->getLogTitle($data->student,null,'Proses regenerate pengajuan kredit gagal, harap melakukan approval ulang'),LogStatus::Failed);
                    }
                }
                $this->addToLogDetail($log_id,$this->getLogTitle($data->student,null,'Berhasil regenerate tagihan mahasiswa'),LogStatus::Success);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                $this->addToLogDetail($log_id,$this->getLogTitle($data->student,null,$e->getMessage()),LogStatus::Failed);
                return response()->json($e->getMessage());
            }
            return json_encode(array('success' => true, 'message' => "Berhasil regenerate tagihan mahasiswa"));
        } else {
            $text= 'Komponen Tagihan Tidak Ditemukan';
            $this->addToLogDetail($log_id,$this->getLogTitle($data->student,null,$text),LogStatus::Failed);
            return json_encode(array('success' => false, 'message' => $text));
        }
    }
}

// OLD CODE, MAYBE USEFULL SOMEDAY
// $components = $student->getComponent()
// ->where('path_id',$student->path_id)
// ->where('period_id',$student->period_id)
// ->where('msy_id',$student->msy_id)
// ->where('mlt_id',$student->mlt_id)
// ->get();

// if($components){
//     $prr_total = 0;
//     foreach($components as $item){
//         $prr_total = $prr_total+$item->cd_fee;
//     }
//     $payment = Payment::where('student_number',$student->student_number)->where('prr_school_year',$this->getActiveSchoolYearCode())->first();

//     DB::beginTransaction();
//     try{
//         if($payment){
//             $payment->prr_status = 'belum lunas';
//             $payment->prr_total = $prr_total;
//             $payment->prr_paid_net = $prr_total;
//             $payment->save();
//             PaymentDetail::where('prr_id', $payment->prr_id)->delete();
//         }else{
//             $payment = Payment::create([
//                 'prr_status' => 'belum lunas',
//                 'prr_total' => $prr_total,
//                 'prr_paid_net' => $prr_total,
//                 'student_number' => $student->student_number,
//                 'prr_school_year' => $this->getActiveSchoolYearCode(),
//             ]);

//         }

//         foreach($components as $item){
//             PaymentDetail::create([
//                 'prr_id' => $payment->prr_id,
//                 'prrd_component' => $item->component->msc_name,
//                 'prrd_amount' => $item->cd_fee
//             ]);
//         }

//         DB::commit();
//     }catch(\Exception $e){
//         DB::rollback();
//         return response()->json($e->getMessage());
//     }
// }
