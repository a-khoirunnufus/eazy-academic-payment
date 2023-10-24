<?php

namespace App\Http\Controllers\_Payment\Api\Generate;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Payment\Studyprogram;
use App\Models\Payment\Faculty;
use App\Models\Payment\Student;
use App\Models\Payment\Payment;
use App\Jobs\Payment\GenerateBulkInvoice;
use App\Http\Requests\Payment\Generate\StudentInvoiceUpdateRequest;
use App\Traits\Payment\LogActivity;
use App\Traits\Payment\General;
use App\Enums\Payment\LogStatus;
use App\Services\Payment\StudentInvoice;
use Carbon\Carbon;
use Config;

class StudentInvoiceController extends Controller
{
    use LogActivity, General;

    private $is_admission = 0;
    /**
     * View Only
     */
    // DT Fakultas & Prodi
    public function index(Request $request)
    {
        $studentInvoice = new StudentInvoice();
        $schoolYearCode = $studentInvoice->getSchoolYear($request->prr_school_year);
        $getIndex = $studentInvoice->getIndex($schoolYearCode);
        return datatables($getIndex)->toJson();
    }

    // DT Per Prodi / Fakultas
    public function detail(Request $request)
    {
        $studentInvoice = new StudentInvoice();

        $list = ['f','sp','year'];
        $data = $studentInvoice->getDataQuery($request,$list);
        $schoolYearCode = $studentInvoice->getSchoolYear($request->query()['year']);
        $query = $studentInvoice->getDetailIndex($request,$schoolYearCode,$data);
        return datatables($query->get())->toJson();
    }

    // Header Per Prodi / Fakultas
    public function header(Request $request)
    {
        $studentInvoice = new StudentInvoice();
        $list = ['f','sp','year'];
        $data = $studentInvoice->getDataQuery($request,$list);
        $schoolYearCode = $studentInvoice->getSchoolYear($request->query()['year']);
        $header = $studentInvoice->getDetailHeader($schoolYearCode,$data);
        return $header;
    }

    // Header Fakultas & Prodi
    public function headerAll()
    {
        $studentInvoice = new StudentInvoice();
        $header = $studentInvoice->getHeader();
        return $header;
    }

    public function choice($f, $sp,$yearCode)
    {
        $studentInvoice = new StudentInvoice();
        $student = $studentInvoice->getChoiceWithScope($f, $sp,$yearCode);
        $student = $studentInvoice->setChoiceArray($student);
        return $student;
    }

    public function choiceAll()
    {
        $studentInvoice = new StudentInvoice();
        $student = $studentInvoice->getChoiceAll();
        $student = $studentInvoice->setChoiceArray($student);
        return $student;
    }

     /**
     * Function
     */
    public function studentGenerate(Request $request)
    {
        $studentInvoice = new StudentInvoice();
        $student = Student::with('getComponent')->findorfail($request['student_number']);
        $log = $this->addToLog('Generate Tagihan Mahasiswa Lama',$this->getAuthId(),LogStatus::Process,$request->url);
        $result = $studentInvoice->storeStudentGenerate($student,$log->log_id);
        $this->updateLogStatus($log,$result);
        return $result;
    }

    public function studentBulkGenerate(Request $request)
    {
        if ($request->generate_checkbox) {
            $log = $this->addToLog('Generate Bulk Tagihan Mahasiswa Lama',$this->getAuthId(),LogStatus::Process,$request->url);
            GenerateBulkInvoice::dispatch($request->generate_checkbox, $request->from,$log,$this->is_admission);
            return json_encode(array('success' => true, 'message' => "Generate Tagihan Sedang Diproses"));
        } else {
            return json_encode(array('success' => false, 'message' => "Belum ada data yang dipilih!"));
        }
    }

    public function deleteByUniversity(Request $request)
    {
        $log = $this->addToLog('Delete All Tagihan Mahasiswa Lama',$this->getAuthId(),LogStatus::Process,$request->url);
        $faculty = Faculty::all();
        foreach ($faculty as $item) {
            $this->deleteFacultyProcess($request,$item->faculty_id,$log);
        }
        $this->updateLogStatus($log,LogStatus::Success);
        $text = "Delete All Tagihan Mahasiswa Lama Berhasil";
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function deleteByFaculty(Request $request, $faculty_id)
    {
        $log = $this->addToLog('Delete Bulk Tagihan Mahasiswa Lama',$this->getAuthId(),LogStatus::Process,$request->url);
        $this->deleteFacultyProcess($request,$faculty_id,$log);
        $this->updateLogStatus($log,LogStatus::Success);
        $text = "Delete Bulk Tagihan Mahasiswa Lama Berhasil";
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function deleteFacultyProcess($request,$faculty_id,$log){
        $studyprogram = Studyprogram::where('faculty_id', '=', $faculty_id)->get();
        foreach ($studyprogram as $item) {
            $this->deleteProdiProcess($request,$item->studyprogram_id,$log);
        }
    }

    public function deleteByProdi(Request $request,$prodi_id)
    {
        $log = $this->addToLog('Delete Bulk Tagihan Mahasiswa Lama',$this->getAuthId(),LogStatus::Process,$request->url);
        $this->deleteProdiProcess($request,$prodi_id,$log);
        $this->updateLogStatus($log,LogStatus::Success);
        $text = "Delete Bulk Tagihan Mahasiswa Lama Berhasil";
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function deleteProdiProcess($request,$prodi_id,$log){
        $studentInvoice = new StudentInvoice();
        $payment = Payment::whereHas('student', function($q) use($prodi_id) {
            $q->where('studyprogram_id', '=', $prodi_id);
        })->get();
        foreach ($payment as $item) {
            $result = $studentInvoice->deleteProcess($request,$item->prr_id,$log->log_id);
        }
    }

    public function delete(Request $request,$prr_id)
    {
        // Deleting Detail invoice
        $studentInvoice = new StudentInvoice();
        $log = $this->addToLog('Delete Tagihan Mahasiswa Lama',$this->getAuthId(),LogStatus::Process,$request->url);
        $result = $studentInvoice->deleteProcess($request,$prr_id,$log->log_id);
        $this->updateLogStatus($log,$result);
        return $result;
    }

    public function regenerateTagihanByFaculty(Request $request, $faculty_id)
    {
        $log = $this->addToLog('Regenerate Bulk Tagihan Mahasiswa Lama',$this->getAuthId(),LogStatus::Process,$request->url);
        $studyprogram = Studyprogram::where('faculty_id', '=', $faculty_id)->get();
        foreach ($studyprogram as $item) {
            $this->regenerateProdiProcess($item->studyprogram_id,$log);
        }
        $this->updateLogStatus($log,LogStatus::Success);
        $text = "Regenerate Bulk Tagihan Mahasiswa Lama Berhasil";
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function regenerateTagihanByProdi(Request $request, $prodi_id)
    {
        $log = $this->addToLog('Regenerate Bulk Tagihan Mahasiswa Lama',$this->getAuthId(),LogStatus::Process,$request->url);
        $this->regenerateProdiProcess($prodi_id,$log);
        $this->updateLogStatus($log,LogStatus::Success);
        $text = "Regenerate Bulk Tagihan Mahasiswa Lama Berhasil";
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function regenerateTagihanByStudent(Request $request, $prr_id)
    {
        $studentInvoice = new StudentInvoice();
        $log = $this->addToLog('Regenerate Tagihan Mahasiswa Lama',$this->getAuthId(),LogStatus::Process,$request->url);
        $result = $studentInvoice->regenerateProcess($prr_id,$log->log_id);
        $this->updateLogStatus($log,$result);
        return $result;
    }

    public function regenerateProdiProcess($prodi_id,$log){
        $studentInvoice = new StudentInvoice();
        $payment = Payment::whereHas('student', function($q) use($prodi_id) {
            $q->where('studyprogram_id', '=', $prodi_id);
        })->get();
        foreach ($payment as $item) {
            $result = $studentInvoice->regenerateProcess($item->prr_id,$log->log_id);
        }
    }

    public function deleteStudentComponent(Request $request, $prrd_id)
    {
        $studentInvoice = new StudentInvoice();
        $result = $studentInvoice->deleteStudentComponentProcess($request, $prrd_id);
        return $result;
    }

    public function updateStudentComponent(StudentInvoiceUpdateRequest $request)
    {
        $studentInvoice = new StudentInvoice();
        $log = $this->addToLog('Update Komponen Tagihan Mahasiswa Lama',$this->getAuthId(),LogStatus::Process,$request->url);
        $result = $studentInvoice->updateStudentComponentProcess($request,$log->log_id);
        $this->updateLogStatus($log,$result);
        return $result;
    }
}
