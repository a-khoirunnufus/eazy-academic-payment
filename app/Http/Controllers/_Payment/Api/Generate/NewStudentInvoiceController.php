<?php

namespace App\Http\Controllers\_Payment\Api\Generate;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Masterdata\MsInstitution as Institution;
use App\Models\Payment\Studyprogram;
use App\Models\Payment\Faculty;
use App\Models\Payment\Year;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentDetail;
use App\Models\Payment\ComponentDetail;
use App\Models\PMB\Participant;
use App\Models\PMB\Setting;
use App\Exceptions\GenerateInvoiceException;
use App\Models\Payment\DiscountReceiver;
use App\Models\Payment\PaymentBill;
use App\Models\Payment\ScholarshipReceiver;
use App\Models\PMB\PaymentRegister;
use App\Models\PMB\PaymentRegisterDetail;
use App\Models\Payment\Student;
use App\Services\Queries\ReRegistration\ReRegistrationInvoice;
use App\Services\Queries\ReRegistration\GenerateInvoiceScopes\UniversityScope;
use App\Services\Queries\ReRegistration\GenerateInvoiceScopes\FacultyScope;
use App\Services\Queries\ReRegistration\GenerateInvoiceScopes\StudyprogramScope;
use App\Services\Queries\ReRegistration\GenerateInvoiceScopes\PathScope;
use App\Services\Queries\ReRegistration\GenerateInvoiceScopes\PeriodScope;
use App\Services\Queries\ReRegistration\GenerateInvoiceScopes\LectureTypeScope;
use App\Services\ReRegistInvoice\GenerateOne as GenerateOneInvoice;
use App\Services\ReRegistInvoice\GenerateByScope as GenerateInvoiceByScope;
use App\Services\ReRegistInvoice\GenerateAll as GenerateAllInvoice;
use App\Services\ReRegistInvoice\DeleteOne as DeleteOneInvoice;
use App\Services\ReRegistInvoice\DeleteByScope as DeleteInvoiceByScope;
use App\Services\ReRegistInvoice\DeleteAll as DeleteAllInvoice;
use App\Services\ReRegistInvoice\GenerateTreeComplete;
use App\Traits\Payment\LogActivity;
use App\Traits\Payment\General;
use App\Enums\Payment\LogStatus;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use PhpParser\Node\Expr\FuncCall;

use App\Http\Requests\Payment\Generate\StudentInvoiceUpdateRequest;
use App\Services\Payment\NewStudentInvoice;
use App\Jobs\Payment\GenerateBulkInvoice;
use App\Models\PMB\Register;

class NewStudentInvoiceController extends Controller
{
    use LogActivity, General;

    private $is_admission = 1;

    public function index(Request $request)
    {
        $studentInvoice = new NewStudentInvoice();
        $schoolYearCode = $studentInvoice->getSchoolYear($request->prr_school_year);
        $getIndex = $studentInvoice->getIndex($schoolYearCode);
        return datatables($getIndex)->toJson();
    }

    // Header Fakultas & Prodi
    public function headerAll()
    {
        $studentInvoice = new NewStudentInvoice();
        $header = $studentInvoice->getHeader();
        return $header;
    }

    public function choiceAll()
    {
        $studentInvoice = new NewStudentInvoice();
        $student = $studentInvoice->getChoiceAll();
        $student = $studentInvoice->setChoiceArray($student,1);
        return $student;
    }

    // DT Per Prodi / Fakultas
    public function detail(Request $request)
    {
        $studentInvoice = new NewStudentInvoice();

        $list = ['f','sp','year'];
        $data = $studentInvoice->getDataQuery($request,$list);
        $schoolYearCode = $studentInvoice->getSchoolYear($request->query()['year']);
        $query = $studentInvoice->getDetailIndex($request,$schoolYearCode,$data);
        return datatables($query->get())->toJson();
    }

    // Header Per Prodi / Fakultas
    public function header(Request $request)
    {
        $studentInvoice = new NewStudentInvoice();
        $list = ['f','sp','year'];
        $data = $studentInvoice->getDataQuery($request,$list);
        $schoolYearCode = $studentInvoice->getSchoolYear($request->query()['year']);
        $header = $studentInvoice->getDetailHeader($schoolYearCode,$data);
        return $header;
    }

    public function choice($f, $sp,$yearCode)
    {
        $studentInvoice = new NewStudentInvoice();
        $student = $studentInvoice->getChoiceWithScope($f, $sp,$yearCode);
        $student = $studentInvoice->setChoiceArray($student,0);
        return $student;
    }

     /**
     * Function
     */
    public function studentGenerate(Request $request)
    {
        $studentInvoice = new NewStudentInvoice();
        $student = Register::with('getComponent','participant')->findorfail($request['reg_id']);
        $log = $this->addToLog('Generate Tagihan Mahasiswa Baru',$this->getAuthId(),LogStatus::Process,$request->url);
        $result = $studentInvoice->storeStudentGenerate($student,$log->log_id);
        $this->updateLogStatus($log,$result);
        return $result;
    }

    public function studentBulkGenerate(Request $request)
    {
        if ($request->generate_checkbox) {
            $log = $this->addToLog('Generate Bulk Tagihan Mahasiswa Baru',$this->getAuthId(),LogStatus::Process,$request->url);
            GenerateBulkInvoice::dispatch($request->generate_checkbox, $request->from,$log,$this->is_admission);
            return json_encode(array('success' => true, 'message' => "Generate Tagihan Sedang Diproses"));
        } else {
            return json_encode(array('success' => false, 'message' => "Belum ada data yang dipilih!"));
        }
    }

    public function deleteByUniversity(Request $request)
    {
        $log = $this->addToLog('Delete All Tagihan Mahasiswa Baru',$this->getAuthId(),LogStatus::Process,$request->url);
        $faculty = Faculty::all();
        foreach ($faculty as $item) {
            $this->deleteFacultyProcess($request,$item->faculty_id,$log);
        }
        $this->updateLogStatus($log,LogStatus::Success);
        $text = "Delete All Tagihan Mahasiswa Baru Berhasil";
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function deleteByFaculty(Request $request, $faculty_id)
    {
        $log = $this->addToLog('Delete Bulk Tagihan Mahasiswa Baru',$this->getAuthId(),LogStatus::Process,$request->url);
        $this->deleteFacultyProcess($request,$faculty_id,$log);
        $this->updateLogStatus($log,LogStatus::Success);
        $text = "Delete Bulk Tagihan Mahasiswa Baru Berhasil";
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
        $log = $this->addToLog('Delete Bulk Tagihan Mahasiswa Baru',$this->getAuthId(),LogStatus::Process,$request->url);
        $this->deleteProdiProcess($request,$prodi_id,$log);
        $this->updateLogStatus($log,LogStatus::Success);
        $text = "Delete Bulk Tagihan Mahasiswa Baru Berhasil";
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function deleteProdiProcess($request,$prodi_id,$log){
        $studentInvoice = new NewStudentInvoice();
        $payment = Payment::whereHas('register', function($q) use($prodi_id) {
            $q->where('reg_major_pass', '=', $prodi_id);
        })->get();
        foreach ($payment as $item) {
            $result = $studentInvoice->deleteProcess($request,$item->prr_id,$log->log_id);
        }
    }

    public function delete(Request $request,$prr_id)
    {
        // Deleting Detail invoice
        $studentInvoice = new NewStudentInvoice();
        $log = $this->addToLog('Delete Tagihan Mahasiswa Baru',$this->getAuthId(),LogStatus::Process,$request->url);
        $result = $studentInvoice->deleteProcess($request,$prr_id,$log->log_id);
        $this->updateLogStatus($log,$result);
        return $result;
    }

    public function regenerateTagihanByFaculty(Request $request, $faculty_id)
    {
        $log = $this->addToLog('Regenerate Bulk Tagihan Mahasiswa Baru',$this->getAuthId(),LogStatus::Process,$request->url);
        $studyprogram = Studyprogram::where('faculty_id', '=', $faculty_id)->get();
        foreach ($studyprogram as $item) {
            $this->regenerateProdiProcess($item->studyprogram_id,$log);
        }
        $this->updateLogStatus($log,LogStatus::Success);
        $text = "Regenerate Bulk Tagihan Mahasiswa Baru Berhasil";
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function regenerateTagihanByProdi(Request $request, $prodi_id)
    {
        $log = $this->addToLog('Regenerate Bulk Tagihan Mahasiswa Baru',$this->getAuthId(),LogStatus::Process,$request->url);
        $this->regenerateProdiProcess($prodi_id,$log);
        $this->updateLogStatus($log,LogStatus::Success);
        $text = "Regenerate Bulk Tagihan Mahasiswa Baru Berhasil";
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function regenerateTagihanByStudent(Request $request, $prr_id)
    {
        $studentInvoice = new NewStudentInvoice();
        $log = $this->addToLog('Regenerate Tagihan Mahasiswa Baru',$this->getAuthId(),LogStatus::Process,$request->url);
        $result = $studentInvoice->regenerateProcess($prr_id,$log->log_id);
        $this->updateLogStatus($log,$result);
        return $result;
    }

    public function regenerateProdiProcess($prodi_id,$log){
        $studentInvoice = new NewStudentInvoice();
        $payment = Payment::whereHas('register', function($q) use($prodi_id) {
            $q->where('reg_major_pass', '=', $prodi_id);
        })->get();
        foreach ($payment as $item) {
            $result = $studentInvoice->regenerateProcess($item->prr_id,$log->log_id);
        }
    }

    public function deleteStudentComponent(Request $request, $prrd_id)
    {
        $studentInvoice = new NewStudentInvoice();
        $result = $studentInvoice->deleteStudentComponentProcess($request, $prrd_id);
        return $result;
    }

    public function updateStudentComponent(StudentInvoiceUpdateRequest $request)
    {
        $studentInvoice = new NewStudentInvoice();
        $log = $this->addToLog('Update Komponen Tagihan Mahasiswa Baru',$this->getAuthId(),LogStatus::Process,$request->url);
        $result = $studentInvoice->updateStudentComponentProcess($request,$log->log_id);
        $this->updateLogStatus($log,$result);
        return $result;
    }
}
