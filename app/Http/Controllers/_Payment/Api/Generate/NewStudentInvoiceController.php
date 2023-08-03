<?php

namespace App\Http\Controllers\_Payment\Api\Generate;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Masterdata\MsInstitution as Institution;
use App\Models\Studyprogram;
use App\Models\Faculty;
use App\Models\Year;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentDetail;
use App\Models\Payment\ComponentDetail;
use App\Models\PMB\Participant;
use App\Models\PMB\Register;
use App\Models\PMB\Setting;
use App\Exceptions\GenerateInvoiceException;
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

class NewStudentInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'invoice_period_code' => 'required',
        ]);
        $school_year_id = Year::where('msy_code', '=', $validated['invoice_period_code'])->first()?->msy_id ?? 0;

        $faculty_w_studyprogram = Faculty::with(['studyProgram' => function ($query) {
                // $query->select('studyprogram_id', 'studyprogram_name');
                $query->orderBy('studyprogram_type', 'asc');
                $query->orderBy('studyprogram_name', 'asc');
            }])
            // ->select('faculty_id', 'faculty_name')
            // ->where('institution_id', '=', Institution::$defaultInstitutionId)
            ->orderBy('faculty_name', 'asc')
            ->get();

        $students = (new ReRegistrationInvoice())->query
            ->where('register.ms_school_year_id', '=', $school_year_id)
            ->get()
            ->toArray();

        $data = [];
        foreach($faculty_w_studyprogram as $faculty){
            foreach (['faculty', 'studyprogram'] as $unit_type) {
                if ($unit_type == 'faculty') {
                    $student_count = 0;
                    $invoice_amount = 0;
                    $generated_invoice = 0;
                    foreach ($students as $student) {
                        if ($student->faculty_id == $faculty->faculty_id) {
                            $student_count++;
                            $invoice_amount += intval($student->payment_reregist_invoice_amount);
                            if ($student->payment_reregist_invoice_status == 'Sudah Digenerate') $generated_invoice++;
                        }
                    }

                    $generated_status = '';
                    $generated_msg = '';
                    if ($generated_invoice == 0) {
                        $generated_status = 'not_yet';
                        $generated_msg = 'Belum Digenerate ('.$generated_invoice.'/'.$student_count.')';
                    } elseif ($generated_invoice != 0 && ($generated_invoice == $student_count)) {
                        $generated_status = 'done';
                        $generated_msg = 'Sudah Digenerate ('.$generated_invoice.'/'.$student_count.')';
                    } elseif ($generated_invoice != 0) {
                        $generated_status = 'partial';
                        $generated_msg = 'Sebagian Telah Digenerate ('.$generated_invoice.'/'.$student_count.')';
                    }

                    $data[] = [
                        'unit_type' => 'faculty',
                        'unit_id' => $faculty->faculty_id,
                        'unit_name' => $faculty->faculty_name,
                        'student_count' => $student_count,
                        'invoice_total_amount' => $invoice_amount,
                        'generated_status' => $generated_status,
                        'generated_msg' => $generated_msg,
                    ];
                }

                if ($unit_type == 'studyprogram') {
                    foreach ($faculty->studyProgram as $studyprogram) {
                        $student_count = 0;
                        $invoice_amount = 0;
                        $generated_invoice = 0;
                        foreach ($students as $student) {
                            if ($student->studyprogram_id == $studyprogram->studyprogram_id) {
                                $student_count++;
                                $invoice_amount += intval($student->payment_reregist_invoice_amount);
                                if ($student->payment_reregist_invoice_status == 'Sudah Digenerate') $generated_invoice++;
                            }
                        }

                        $generated_status = '';
                        $generated_msg = '';
                        if ($generated_invoice == 0) {
                            $generated_status = 'not_yet';
                            $generated_msg = 'Belum Digenerate ('.$generated_invoice.'/'.$student_count.')';
                        } elseif ($generated_invoice != 0 && ($generated_invoice == $student_count)) {
                            $generated_status = 'done';
                            $generated_msg = 'Sudah Digenerate ('.$generated_invoice.'/'.$student_count.')';
                        } elseif ($generated_invoice != 0) {
                            $generated_status = 'partial';
                            $generated_msg = 'Sebagian Telah Digenerate ('.$generated_invoice.'/'.$student_count.')';
                        }

                        $data[] = [
                            'unit_type' => 'studyprogram',
                            'unit_id' => $studyprogram->studyprogram_id,
                            'unit_name' => strtoupper($studyprogram->studyprogram_type).' '.$studyprogram->studyprogram_name,
                            'student_count' => $student_count,
                            'invoice_total_amount' => $invoice_amount,
                            'generated_status' => $generated_status,
                            'generated_msg' => $generated_msg,
                            'faculty_id' => $faculty->faculty_id,
                        ];
                    }
                }
            }
        }

        return datatables($data)->toJSON();
    }

    public function detail(Request $request)
    {
        $validated = $request->validate([
            'invoice_period_code' => 'required',
            'scope' => 'required|in:all,faculty,studyprogram',
            'faculty_id' => 'required_if:scope,faculty',
            'studyprogram_id' => 'required_if:scope,studyprogram',
        ]);
        $school_year_id = Year::where('msy_code', '=', $validated['invoice_period_code'])->first()?->msy_id ?? 0;

        $filters = [
            ['register.ms_school_year_id', '=', $school_year_id],
        ];
        if ($validated['scope'] == 'faculty') {
            $filters[] = ['faculty.faculty_id', '=', $validated['faculty_id']];
        } elseif ($validated['scope'] == 'studyprogram') {
            $filters[] = ['studyprogram.studyprogram_id', '=', $validated['studyprogram_id']];
        }

        $data = (new ReRegistrationInvoice())->query->where($filters)->get();

        return datatables($data)->toJSON();
    }

    public function invoiceDetail($prr_id)
    {
        $data = DB::table('finance.payment_re_register')
            ->where('prr_id', '=', $prr_id)
            ->whereNull('deleted_at')
            ->first();

        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }

    public function invoiceComponentDetail($prr_id)
    {
        $data = DB::table('finance.payment_re_register_detail')
            ->where('prr_id', '=', $prr_id)
            ->whereNull('deleted_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }

    /**
     * Generate invoice for one participant.
     */
    public function generateOne(Request $request)
    {
        $validated = $request->validate([
            'invoice_period_code' => 'required',
            'register_id' => 'required',
        ]);

        try {
            GenerateOneInvoice::generate($validated['invoice_period_code'], $validated['register_id']);
        } catch (GenerateInvoiceException $ex) {
            return response()->json([
                'success' => false,
                'message' => config('app.env') != 'production' ?
                    $ex->getMessage()
                    : 'Terjadi kesalahan pada sistem, silahkan hubungi administrator.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil generate tagihan mahasiswa.',
        ], 200);
    }

    /**
     * Delete invoice for one participant.
     */
    public function deleteOne(Request $request)
    {
        $validated = $request->validate([
            'payment_reregist_id' => 'required',
        ]);

        try {
            DeleteOneInvoice::delete($validated['payment_reregist_id']);
        } catch (DeleteInvoiceException $ex) {
            return response()->json([
                'success' => false,
                'message' => config('app.env') != 'production' ?
                    $ex->getMessage()
                    : 'Terjadi kesalahan pada sistem, silahkan hubungi administrator.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menghapus tagihan mahasiswa.',
        ], 200);
    }

    public function generateAll(Request $request)
    {
        $validated = $request->validate([
            'invoice_period_code' => 'required',
        ]);

        $generated_count = GenerateAllInvoice::generate($validated['invoice_period_code'], true);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil generate '.$generated_count.' tagihan mahasiswa.',
        ], 200);
    }

    public function deleteAll(Request $request)
    {
        $validated = $request->validate([
            'invoice_period_code' => 'required',
        ]);

        $deleted_count = DeleteAllInvoice::delete($validated['invoice_period_code'], true);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menghapus '.$deleted_count.' tagihan mahasiswa.',
        ], 200);
    }

    public function generateByScope(Request $request)
    {
        $validated = $request->validate([
            'invoice_period_code' => 'required',
            'scope' => 'required|in:faculty,studyprogram,path,period,lecture_type',
            'faculty_id' => 'required_if:scope,faculty|required_if:scope,studyprogram|required_if:scope,path|required_if:scope,period|required_if:scope,lecture_type',
            'studyprogram_id' => 'required_if:scope,studyprogram|required_if:scope,path|required_if:scope,period|required_if:scope,lecture_type',
            'path_id' => 'required_if:scope,path|required_if:scope,period|required_if:scope,lecture_type',
            'period_id' => 'required_if:scope,period|required_if:scope,lecture_type',
            'lecture_type_id' => 'required_if:scope,lecture_type',
        ]);

        $scope = null;

        if ($validated['scope'] == 'faculty') {
            $scope = new FacultyScope($validated['faculty_id']);
        }
        elseif ($validated['scope'] == 'studyprogram') {
            $scope = new StudyprogramScope($validated['faculty_id'], $validated['studyprogram_id']);
        }
        elseif ($validated['scope'] == 'path') {
            $scope = new PathScope($validated['faculty_id'], $validated['studyprogram_id'], $validated['path_id']);
        }
        elseif ($validated['scope'] == 'period') {
            $scope = new PeriodScope($validated['faculty_id'], $validated['studyprogram_id'], $validated['path_id'], $validated['period_id']);
        }
        elseif ($validated['scope'] == 'lecture_type') {
            $scope = new LectureTypeScope($validated['faculty_id'], $validated['studyprogram_id'], $validated['path_id'], $validated['period_id'], $validated['lecture_type_id']);
        }

        $generated_count = GenerateInvoiceByScope::generate($validated['invoice_period_code'], $scope, true);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil generate '.$generated_count.' tagihan mahasiswa.',
        ], 200);
    }

    public function generateByScopes(Request $request)
    {
        $validated = $request->validate([
            'invoice_period_code' => 'required',
            'generate_data' => 'required|array',
        ]);

        $generated_count = 0;

        foreach($validated['generate_data'] as $data) {
            $scopeObj = null;

            if ($data['scope'] == 'university') {
                $scopeObj = new UniversityScope();
            }
            elseif ($data['scope'] == 'faculty') {
                $scopeObj = new FacultyScope($data['faculty_id']);
            }
            elseif ($data['scope'] == 'studyprogram') {
                $scopeObj = new StudyprogramScope($data['faculty_id'], $data['studyprogram_id']);
            }
            elseif ($data['scope'] == 'path') {
                $scopeObj = new PathScope($data['faculty_id'], $data['studyprogram_id'], $data['path_id']);
            }
            elseif ($data['scope'] == 'period') {
                $scopeObj = new PeriodScope($data['faculty_id'], $data['studyprogram_id'], $data['path_id'], $data['period_id']);
            }
            elseif ($data['scope'] == 'lecture_type') {
                $scopeObj = new LectureTypeScope($data['faculty_id'], $data['studyprogram_id'], $data['path_id'], $data['period_id'], $data['lecture_type_id']);
            }

            $generated_count += GenerateInvoiceByScope::generate($validated['invoice_period_code'], $scopeObj, true);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil generate '.$generated_count.' tagihan mahasiswa.',
        ], 200);
    }

    public function deleteByScope(Request $request)
    {
        $validated = $request->validate([
            'invoice_period_code' => 'required',
            'scope' => 'required|in:faculty,studyprogram,path,period,lecture_type',
            'faculty_id' => 'required_if:scope,faculty|required_if:scope,studyprogram|required_if:scope,path|required_if:scope,period|required_if:scope,lecture_type',
            'studyprogram_id' => 'required_if:scope,studyprogram|required_if:scope,path|required_if:scope,period|required_if:scope,lecture_type',
            'path_id' => 'required_if:scope,path|required_if:scope,period|required_if:scope,lecture_type',
            'period_id' => 'required_if:scope,period|required_if:scope,lecture_type',
            'lecture_type_id' => 'required_if:scope,lecture_type',
        ]);

        $scope = null;
        $deleted_count = 0;

        switch ($validated['scope']) {
            case 'faculty':
                $scope = new FacultyScope($validated['faculty_id']);
                $deleted_count = DeleteInvoiceByScope::delete($validated['invoice_period_code'], $scope, true);
                break;

            case 'studyprogram':
                $scope = new StudyprogramScope($validated['faculty_id'], $validated['studyprogram_id']);
                $deleted_count = DeleteInvoiceByScope::delete($validated['invoice_period_code'], $scope, true);
                break;

            case 'path':
                $scope = new PathScope($validated['faculty_id'], $validated['studyprogram_id'], $validated['path_id']);
                $deleted_count = DeleteInvoiceByScope::delete($validated['invoice_period_code'], $scope, true);
                break;

            case 'period':
                $scope = new PeriodScope($validated['faculty_id'], $validated['studyprogram_id'], $validated['path_id'], $validated['period_id']);
                $deleted_count = DeleteInvoiceByScope::delete($validated['invoice_period_code'], $scope, true);
                break;

            case 'lecture_type':
                $scope = new LectureTypeScope($validated['faculty_id'], $validated['studyprogram_id'], $validated['path_id'], $validated['period_id'], $validated['lecture_type_id']);
                $deleted_count = DeleteInvoiceByScope::delete($validated['invoice_period_code'], $scope, true);
                break;

            default:
                break;
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil hapus '.$deleted_count.' tagihan mahasiswa.',
        ], 200);
    }

    public function getTreeGenerate(Request $request)
    {
        $invoice_period_code = $request->input('invoice_period_code');
        $school_year_id = Year::where('msy_code', '=', $invoice_period_code)->first()?->msy_id ?? 0;

        $path_base = (new ReRegistrationInvoice(true))->query;

        if($invoice_period_code != null) {
            $path_base = $path_base->where('register.ms_school_year_id', '=', $school_year_id);
        }

        $path_base = $path_base->select(
                DB::raw("'faculty_' || faculty.faculty_id as faculty_id"),
                DB::raw("'studyprogram_' || studyprogram.studyprogram_id as studyprogram_id"),
                DB::raw("'path_' || path.path_id as path_id"),
                DB::raw("'period_' || period.period_id as period_id"),
                DB::raw("'lecturetype_' || lecture_type.mlt_id as lecturetype_id"),
            )
            ->distinct()
            ->groupBy(
                'faculty.faculty_id',
                'studyprogram.studyprogram_id',
                'path.path_id',
                'period.period_id',
                'lecture_type.mlt_id'
            )
            ->get()
            ->toArray();

        $paths = array_map(function($item) {
            return $item->faculty_id.'/'.$item->studyprogram_id.'/'.$item->path_id.'/'.$item->period_id.'/'.$item->lecturetype_id;
        }, $path_base);

        $registrants = (new ReRegistrationInvoice())->query;

        if($invoice_period_code != null) {
            $registrants = $registrants->where('register.ms_school_year_id', '=', $school_year_id);
        }

        $registrants = $registrants->get()->toArray();

        $tree = (new GenerateTreeComplete($paths, $registrants))->generate();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil generate tree stucture.',
            'data' => [
                'tree' => $tree,
            ],
        ], 200);
    }

    public function getTreeGenerateUniversity(Request $request)
    {
        $validated = $request->validate([
            'invoice_period_code' => 'required',
        ]);
        $school_year_id = Year::where('msy_code', '=', $validated['invoice_period_code'])->first()?->msy_id ?? 0;

        $path_base = (new ReRegistrationInvoice(true))->query
            ->where('register.ms_school_year_id', '=', $school_year_id)
            ->select(
                DB::raw("'faculty_' || faculty.faculty_id as faculty_id"),
                DB::raw("'studyprogram_' || studyprogram.studyprogram_id as studyprogram_id"),
            )
            ->distinct()
            ->groupBy(
                'faculty.faculty_id',
                'studyprogram.studyprogram_id',
            )
            ->get()
            ->toArray();

        $paths = array_map(function($item) {
            return $item->faculty_id.'/'.$item->studyprogram_id;
        }, $path_base);

        $registrants = (new ReRegistrationInvoice())->query
            ->where('register.ms_school_year_id', '=', $school_year_id)
            ->get()
            ->toArray();

        // Log::debug([$paths, $registrants]);

        $tree = (new GenerateTreeComplete($paths, $registrants))->generateByUniversity();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil generate tree stucture.',
            'data' => [
                'tree' => $tree,
            ],
        ], 200);
    }

    public function getTreeGenerateFaculty(Request $request)
    {
        $validated = $request->validate([
            'invoice_period_code' => 'required',
            'faculty_id' => 'required',
        ]);
        $school_year_id = Year::where('msy_code', '=', $validated['invoice_period_code'])->first()?->msy_id ?? 0;

        $path_base = (new ReRegistrationInvoice(true))->query
            ->where('register.ms_school_year_id', '=', $school_year_id)
            ->where('faculty.faculty_id', '=', $validated['faculty_id'])
            ->select(
                DB::raw("'studyprogram_' || studyprogram.studyprogram_id as studyprogram_id"),
                DB::raw("'path_' || path.path_id as path_id"),
                DB::raw("'period_' || period.period_id as period_id"),
                DB::raw("'lecturetype_' || lecture_type.mlt_id as lecturetype_id"),
            )
            ->distinct()
            ->groupBy(
                'studyprogram.studyprogram_id',
                'path.path_id',
                'period.period_id',
                'lecture_type.mlt_id'
            )
            ->get()
            ->toArray();

        $paths = array_map(function($item) {
            return $item->studyprogram_id.'/'.$item->path_id.'/'.$item->period_id.'/'.$item->lecturetype_id;
        }, $path_base);

        $registrants = (new ReRegistrationInvoice())->query
            ->where('register.ms_school_year_id', '=', $school_year_id)
            ->where('faculty.faculty_id', '=', $validated['faculty_id'])
            ->get()
            ->toArray();

        $tree = (new GenerateTreeComplete($paths, $registrants))->generateByFaculty();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil generate tree stucture.',
            'data' => [
                'tree' => $tree,
            ],
        ], 200);
    }

    public function getTreeGenerateStudyprogram(Request $request)
    {
        $validated = $request->validate([
            'invoice_period_code' => 'required',
            'faculty_id' => 'required',
            'studyprogram_id' => 'required',
        ]);
        $school_year_id = Year::where('msy_code', '=', $validated['invoice_period_code'])->first()?->msy_id ?? 0;

        $path_base = (new ReRegistrationInvoice(true))->query
            ->where('register.ms_school_year_id', '=', $school_year_id)
            ->where('faculty.faculty_id', '=', $validated['faculty_id'])
            ->where('studyprogram.studyprogram_id', '=', $validated['studyprogram_id'])
            ->select(
                DB::raw("'path_' || path.path_id as path_id"),
                DB::raw("'period_' || period.period_id as period_id"),
                DB::raw("'lecturetype_' || lecture_type.mlt_id as lecturetype_id"),
            )
            ->distinct()
            ->groupBy(
                'path.path_id',
                'period.period_id',
                'lecture_type.mlt_id'
            )
            ->get()
            ->toArray();

        $paths = array_map(function($item) {
            return $item->path_id.'/'.$item->period_id.'/'.$item->lecturetype_id;
        }, $path_base);

        $registrants = (new ReRegistrationInvoice())->query
            ->where('register.ms_school_year_id', '=', $school_year_id)
            ->where('faculty.faculty_id', '=', $validated['faculty_id'])
            ->where('studyprogram.studyprogram_id', '=', $validated['studyprogram_id'])
            ->get()
            ->toArray();

        $tree = (new GenerateTreeComplete($paths, $registrants, 'studyprogram'))->generateByStudyprogram();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil generate tree stucture.',
            'data' => [
                'tree' => $tree,
            ],
        ], 200);
    }
}
