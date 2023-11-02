<?php

namespace App\Services\Payment;

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
use App\Models\Payment\PaymentType;
use App\Models\Payment\PeriodPathMajor;
use App\Models\Payment\MasterJob;
use App\Jobs\Payment\GenerateInvoice;
use App\Jobs\Payment\GenerateBulkInvoice;
use App\Models\Payment\DiscountReceiver;
use App\Models\Payment\ScholarshipReceiver;
use App\Models\Payment\CreditSchema;
use App\Models\Payment\DispensationSubmission;
use App\Models\Payment\CreditSubmission;
use App\Models\PMB\PaymentRegisterDetail;
use App\Http\Requests\Payment\Generate\StudentInvoiceUpdateRequest;
use App\Traits\Payment\LogActivity;
use App\Traits\Payment\General;
use App\Enums\Payment\LogStatus;
use Carbon\Carbon;
use DB;

class StudentInvoice {
    use LogActivity, General;

    private $is_admission = 0;

    /**
     * Get invoice status of given student
     *
     * @param Student   $student            Student object
     * @param string    $school_year_code   School year code
     *
     * @return string|null  Return payment status {'lunas', 'belum lunas', 'kredit'},
     *                      if invoice not found return null instead.
     */

    public function getSchoolYear($prr_school_year){
        $schoolYearCode = $this->getActiveSchoolYearCode();
        if(isset($prr_school_year)){
            $schoolYearCode = $prr_school_year;
        }
        return $schoolYearCode;
    }

    public function getDataQuery($request,$list){
        foreach ($list as $item) {
            $data[$item] = $request->query()[$item];
        }
        return $data;
    }

    public function getIndex($schoolYearCode){
        $query = Faculty::with('studyProgram')->orderBy('faculty_name')->get();
        $student = Student::with(['payment' => function ($query) use ($schoolYearCode) {
            $query->where('prr_school_year', $schoolYearCode);
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
        return $result;
    }

    public function getDetailIndex($request,$schoolYearCode,$data){
        $query = Student::query();
        $query = $query->with('lectureType', 'period', 'path', 'year', 'studyProgram')
            ->with(['payment' => function ($query) use ($schoolYearCode) {
            $query->where('prr_school_year', $schoolYearCode);
        }])->join('masterdata.ms_studyprogram as sp', 'sp.studyprogram_id', 'hr.ms_student.studyprogram_id')
        ->leftJoin('finance.payment_re_register as prr', function ($join) use ($schoolYearCode) {
            $join->on('prr.student_number', '=', 'hr.ms_student.student_number');
            $join->where('prr.prr_school_year', '=', $schoolYearCode);
            $join->where('prr.deleted_at', '=', null);
        })
        ->select('hr.ms_student.*');
        if ($data['f'] != 0 && $data['f']) {
            $query = $query->where('sp.faculty_id', $data['f']);
        }
        if ($data['sp'] != 0 && $data['sp']) {
            $query = $query->where('sp.studyprogram_id', $data['sp']);
        }
        if ($request->query('yearFilter') !== "all") {
            $query = $query->where('msy_id', '=', $request->query('year'));
        }
        if ($request->query('path') !== "all") {
            $query = $query->where('path_id', '=', $request->query('path'));
        }
        if ($request->query('period') !== "all") {
            $query = $query->where('period_id', '=', $request->query('period'));
        }
        return $query;
    }

    public function getDetailHeader($schoolYearCode,$data){
        $formatSchoolYear = $this->fromCodeToWords($schoolYearCode);
        $faculty = Faculty::find($data['f']);
        if ($data['sp'] != 0) {
            $studyProgram = Studyprogram::with('faculty')->find($data['sp']);
        } else {
            $studyProgram = "-";
        }
        $date = Carbon::today()->toDateString();

        if ($faculty) {
            $header['faculty'] = $faculty->faculty_name;
        } else {
            $header['faculty'] = $studyProgram->faculty->faculty_name;
        }
        $header['study_program'] = ($data['sp'] != 0) ? $studyProgram->studyprogram_type . ' ' . $studyProgram->studyprogram_name : $studyProgram;
        $header['active'] = $formatSchoolYear;
        return $header;
    }

    public function getHeader(){
        $activeSchoolYear = $this->getActiveSchoolYear();

        $header['university'] = "Universitas Telkom";
        $header['active'] = $activeSchoolYear;

        return $header;
    }

    public function setChoiceArray($student){
        foreach ($student as $key => $s) {
            $getComponent = $s->getComponent()
                ->where('path_id', $s->path_id)
                ->where('period_id', $s->period_id)
                ->where('msy_id', $s->msy_id)
                ->where('mlt_id', $s->mlt_id)
                ->where('cd_is_admission', $this->is_admission)
                ->get();
            $student[$key]->setRelation('component_filter', $getComponent->values());
            $student[$key]->is_component = 1;
        }

        foreach ($student as $key => $s) {
            if($s->component_filter->isEmpty()){
                $s->is_component = 0;
            }
        }
        return $student;
    }

    public function getChoiceWithScope($f, $sp,$yearCode){
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
        return $student;
    }

    public function getChoiceAll(){
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
        return $student;
    }

    public function storeStudentGenerate($student,$log_id)
    {
        $components = $student->getComponent()
            ->where('path_id', $student->path_id)
            ->where('period_id', $student->period_id)
            ->where('msy_id', $student->msy_id)
            ->where('mlt_id', $student->mlt_id)
            ->where('mma_id', $student->studyprogram_id)
            ->where('cd_is_admission', $this->is_admission)
            ->get();
        if (!$components->isEmpty()) {
            $prr_total = 0;
            foreach ($components as $item) {
                $prr_total = $prr_total + $item->cd_fee;
            }
            DB::beginTransaction();
            try {
                $ppm = PeriodPathMajor::with('periodPath','majorLectureType')
                ->whereRelation('periodPath', 'path_id', $student->path_id)
                ->whereRelation('periodPath', 'period_id', $student->period_id)
                ->whereRelation('periodPath.period', 'msy_id', $student->msy_id)
                ->whereRelation('majorLectureType', 'mma_id', $student->studyprogram_id)
                ->whereRelation('majorLectureType', 'mlt_id', $student->mlt_id)->firstorfail();
                $paymentType = PaymentType::where('ppm_id',$ppm->ppm_id)->where('ptp_is_admission',$this->is_admission)->first();
                if($paymentType){
                    $type = $paymentType->msct_id;
                }else{
                    $type = $this->getCacheSetting('payment_type_default_cache');
                }
                $payment = Payment::create([
                    'prr_status' => 'belum lunas',
                    'prr_total' => $prr_total,
                    'prr_paid_net' => $prr_total,
                    'student_number' => $student->student_number,
                    'prr_type' => $type,
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
                $this->addToLogDetail($log_id,$this->getLogTitleStudent($student),LogStatus::Success);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                $this->addToLogDetail($log_id,$this->getLogTitleStudent($student,null,$e->getMessage()),LogStatus::Failed);
                return response()->json($e->getMessage());
            }
        } else {
            $text= 'Komponen Tagihan Tidak Ditemukan';
            $this->addToLogDetail($log_id,$this->getLogTitleStudent($student,null,$text),LogStatus::Failed);
            return json_encode(array('success' => false, 'message' => $text));
        }
        $text = "Berhasil generate tagihan mahasiswa " . $student->fullname;
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function storeBulkStudentGenerate($generate_checkbox, $from, $log)
    {
        foreach ($generate_checkbox as $item) {
            if ($item != "null") {
                // Parsing the key from string
                $store = explode("_", $item);
                if(count($store) == 2){
                    $faculty_id = ($store[0]) ? $store[0] : 0;
                    $studyprogram_id = ($store[1]) ? $store[1] : 0;
                }else{
                    $studyprogram_id = ($store[0]) ? $store[0] : 0;
                    $msy_id = ($store[1]) ? $store[1] : 0;
                    $path_id = ($store[2]) ? $store[2] : 0;
                    $period_id = ($store[3]) ? $store[3] : 0;
                    $mlt_id = ($store[4]) ? $store[4] : 0;
                }
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
                        GenerateInvoice::dispatch($student, $log,$this->is_admission);
                    }
                }
                GenerateInvoice::dispatch(null, $log,$this->is_admission);
            }
        }
        return true;
    }

    public function deleteProcess($request,$prr_id,$log_id)
    {
        // Deleting Detail invoice
        try {
            $detail = PaymentDetail::with('payment')->where('prr_id', $prr_id)->get();
            if ($detail) {
                foreach ($detail as $item) {
                    if ($item->type == 'discount' or $item->type == 'scholarship') {
                        $text = "Terdapat potongan, beasiswa, cicilan ataupun dispensasi yang sudah disetujui pada tagihan ini.";
                        $this->addToLogDetail($log_id,$this->getLogTitleStudent($item->payment->student,null,$text),LogStatus::Failed);
                        return json_encode(array('success' => false, 'message' => $text));
                    }
                }
            }
            // Deleting Detail Bill
            $data = Payment::with('student')->findorfail($prr_id);
            if(Carbon::createFromFormat('Y-m-d', $this->getCacheSetting('payment_delete_lock_cache')) <= Carbon::now()){
                $text = "Sudah melebihi batas waktu penghapusan data";
                $this->addToLogDetail($log_id,$this->getLogTitleStudent($data->student,null,$text),LogStatus::Failed);
                return json_encode(array('success' => false, 'message' => $text));
            }
            if ($data->prr_status == 'kredit') {
                $text = "Terdapat potongan, beasiswa, cicilan ataupun dispensasi yang sudah disetujui pada tagihan ini.";
                $this->addToLogDetail($log_id,$this->getLogTitleStudent($data->student,null,$text),LogStatus::Failed);
                return json_encode(array('success' => false, 'message' => $text));
            }
            DB::beginTransaction();
            $data->delete();
            PaymentDetail::where('prr_id', $prr_id)->delete();
            PaymentBill::where('prr_id', $prr_id)->delete();
            $this->addToLogDetail($log_id,$this->getLogTitleStudent($data->student),LogStatus::Success);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $this->addToLogDetail($log_id,$this->getLogTitleStudent($data->student,null,$e->getMessage()),LogStatus::Failed);
            return response()->json($e->getMessage());
        }
        return json_encode(array('success' => true, 'message' => "Berhasil menghapus tagihan"));
    }

    public function regenerateProcess($prr_id, $log_id){
        $data = Payment::with('student','paymentDetail','paymentBill')->findorfail($prr_id);
        if(Carbon::createFromFormat('Y-m-d', $this->getCacheSetting('payment_regenerate_lock_cache')) <= Carbon::now()){
            $text = "Sudah melebihi batas waktu regenerate data";
            $this->addToLogDetail($log_id,$this->getLogTitleStudent($data->student,null,$text),LogStatus::Failed);
            return json_encode(array('success' => false, 'message' => $text));
        }
        if($data->paymentBill){
            foreach($data->paymentBill as $item){
                if($item->prrb_status == 'lunas'){
                    $text= 'Terdapat tagihan yang sudah dilunasi';
                    $this->addToLogDetail($log_id,$this->getLogTitleStudent($data->student,null,$text),LogStatus::Failed);
                    return json_encode(array('success' => false, 'message' => $text));
                }
            }
        }
        $components = $data->student->getComponent()
        ->where('path_id', $data->student->path_id)
        ->where('period_id', $data->student->period_id)
        ->where('msy_id', $data->student->msy_id)
        ->where('mlt_id', $data->student->mlt_id)
        ->where('mma_id', $data->student->studyprogram_id)
        ->where('cd_is_admission', $this->is_admission)
        ->get();

        if ($components) {
            $prr_total = 0;
            foreach ($components as $item) {
                $prr_total = $prr_total + $item->cd_fee;
            }
            DB::beginTransaction();
            try {
                $ppm = PeriodPathMajor::with('periodPath','majorLectureType')
                ->whereRelation('periodPath', 'path_id', $data->student->path_id)
                ->whereRelation('periodPath', 'period_id', $data->student->period_id)
                ->whereRelation('periodPath.period', 'msy_id', $data->student->msy_id)
                ->whereRelation('majorLectureType', 'mma_id', $data->student->studyprogram_id)
                ->whereRelation('majorLectureType', 'mlt_id', $data->student->mlt_id)->firstorfail();
                $paymentType = PaymentType::where('ppm_id',$ppm->ppm_id)->where('ptp_is_admission',$this->is_admission)->first();
                if($paymentType){
                    $type = $paymentType->msct_id;
                }else{
                    $type = $this->getCacheSetting('payment_type_default_cache');
                }
                $payment = Payment::create([
                    'prr_status' => 'belum lunas',
                    'prr_total' => $prr_total,
                    'prr_paid_net' => $prr_total,
                    'prr_type' => $type,
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
                        $this->addToLogDetail($log_id,$this->getLogTitleStudent($data->student,null,'Proses regenerate pengajuan kredit gagal, harap melakukan approval ulang'),LogStatus::Failed);
                        }
                    }else{
                        $credit->update(['mcs_status'=> 0]);
                        $this->addToLogDetail($log_id,$this->getLogTitleStudent($data->student,null,'Proses regenerate pengajuan kredit gagal, harap melakukan approval ulang'),LogStatus::Failed);
                    }
                }
                // $data = Payment::findorfail($prr_id);
                // $data->delete();
                $this->addToLogDetail($log_id,$this->getLogTitleStudent($data->student,null,'Berhasil regenerate tagihan mahasiswa'),LogStatus::Success);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                $this->addToLogDetail($log_id,$this->getLogTitleStudent($data->student,null,$e->getMessage()),LogStatus::Failed);
                return response()->json($e->getMessage());
            }
            return json_encode(array('success' => true, 'message' => "Berhasil regenerate tagihan mahasiswa"));
        } else {
            $text= 'Komponen Tagihan Tidak Ditemukan';
            $this->addToLogDetail($log_id,$this->getLogTitleStudent($data->student,null,$text),LogStatus::Failed);
            return json_encode(array('success' => false, 'message' => $text));
        }
    }

    public function deleteStudentComponentProcess($request, $prrd_id)
    {
        $log = $this->addToLog('Delete Komponen Tagihan Mahasiswa Lama',$this->getAuthId(),LogStatus::Process,$request->url);
        $paymentDetail = PaymentDetail::with('payment')->findorfail($prrd_id);
        $payment = Payment::findorfail($paymentDetail->prr_id);
        try {
            DB::beginTransaction();
            $payment->prr_total = $payment->prr_total-$paymentDetail->prrd_amount;
            $payment->prr_paid_net = $payment->prr_paid_net-$paymentDetail->prrd_amount;
            $payment->save();
            PaymentDetail::where('prrd_id', $prrd_id)->delete();
            DB::commit();
            $this->addToLogDetail($log->log_id,$this->getLogTitleStudent($paymentDetail->payment->student),LogStatus::Success);
        } catch (\Exception $e) {
            DB::rollback();
            $this->addToLogDetail($log->log_id,$this->getLogTitleStudent($paymentDetail->payment->student,null,$e->getMessage()),LogStatus::Failed);
            return response()->json($e->getMessage());
        }
        $this->updateLogStatus($log,LogStatus::Success);
        return json_encode(array('success' => true, 'message' => "Berhasil menghapus komponen tagihan"));
    }

    public function updateStudentComponentProcess($request,$log_id){
        $validated = $request->validated();
        DB::beginTransaction();
        if(Carbon::createFromFormat('Y-m-d', $this->getCacheSetting('payment_edit_lock_cache')) <= Carbon::now()){
            $text = "Sudah melebihi batas waktu edit tagihan";
            $this->addToLogDetail($log_id,$this->getLogTitle($validated['title'],$text),LogStatus::Failed);
            return json_encode(array('success' => false, 'message' => $text));
        }
        try {
            if (isset($validated['prrd_id'])) {
                $count = count($validated['prrd_id']);
                for ($i=0; $i < $count; $i++) {
                    if($validated['prrd_id'][$i] == 0){
                        PaymentDetail::create([
                            'prr_id' => $validated['prr_id'][$i],
                            'prrd_component' => $validated['prrd_component'][$i],
                            'prrd_amount' => $validated['prrd_amount'][$i],
                            'is_plus' => 1,
                            'type' => 'component',
                        ]);
                        $payment = Payment::findorfail($validated['prr_id'][$i]);
                        $payment->prr_total = $payment->prr_total+$validated['prrd_amount'][$i];
                        $payment->prr_paid_net = $payment->prr_paid_net+$validated['prrd_amount'][$i];
                        $payment->save();
                        $this->addToLogDetail($log_id,$this->getLogTitle('Add '.$validated['prrd_component'][$i].' - Rp.'.$validated['prrd_amount'][$i].' at '.$validated['title']),LogStatus::Success);
                    } else {
                        $data = PaymentDetail::findorfail($validated['prrd_id'][$i]);
                        $payment = Payment::findorfail($validated['prr_id'][$i]);
                        $payment->prr_total = $payment->prr_total-$data->prrd_amount+$validated['prrd_amount'][$i];
                        $payment->prr_paid_net = $payment->prr_paid_net-$data->prrd_amount+$validated['prrd_amount'][$i];
                        $data->update([
                            'prrd_component' => $validated['prrd_component'][$i],
                            'prrd_amount' => $validated['prrd_amount'][$i]
                        ]);
                        $payment->save();
                        $this->addToLogDetail($log_id,$this->getLogTitle('Update '.$validated['prrd_component'][$i].' - Rp.'.$validated['prrd_amount'][$i].' at '.$validated['title']),LogStatus::Success);
                    }
                }
            }
            $text = "Berhasil memperbarui komponen tagihan mahasiswa";
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $this->addToLogDetail($log_id,$this->getLogTitle($validated['title'],$e->getMessage()),LogStatus::Failed);
            return response()->json($e->getMessage());
        }
        return json_encode(array('success' => true, 'message' => $text));
    }

    public static function status($student, $school_year_code)
    {
        $invoice = Payment::where('student_number', $student->student_number)
            ->where('prr_school_year', $school_year_code)
            ->first();

        if ($invoice == null) {
            return null;
        }

        return $invoice->computed_payment_status;
    }
}
