<?php

namespace App\Http\Controllers\_Student\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student\DispensationSubmission;
use App\Traits\Authentication\StaticStudentUser;
use App\Http\Requests\Student\DispensationRequest;
use App\Models\Payment\Payment;
use DB;
use Storage;

class DispensationController extends Controller
{
    use StaticStudentUser;
    
    public function index(Request $request)
    {
        $email = $request->query('email') ?? $this->example_s_user_email_hafizh;
        $type = $request->query('type') ?? 'student';

        $user = $this->getStaticUser($email, $type);

        if(!$user->student) {
            return 'User with email: '.$email.' not found!';
        }
        
        $query = DispensationSubmission::query();
        $query = $query->with('period','student')->where('student_number',$user->student->student_number)->orderBy('mds_id');
        return datatables($query)->toJson();
    }
    
    public function store(DispensationRequest $request)
    {
        $validated = $request->validated();

        $payment = Payment::where('student_number',$validated['student_number'])->where('prr_school_year',$validated['mds_school_year'])->first();
        if(!$payment){
            return json_encode(array('success' => false, 'message' => 'Data tagihan tidak ditemukan'));
        }
        try{
            DB::beginTransaction();

            if(array_key_exists("msc_id",$validated)){
                $dispensation = DispensationSubmission::findOrFail($validated["msc_id"]);
                $dispensation->update([
                    'mds_phone' => $validated['mds_phone'],
                    'mds_email' => $validated['mds_email'],
                    'mds_reason' => $validated['mds_reason'],
                    'mds_proof_filename' => $validated['mds_proof']->getClientOriginalName(),
                    'mds_status' => 2,
                ]);
                $text = "Berhasil memperbarui pengajuan";
            }else{
                $dispensation = DispensationSubmission::create([
                    'student_number' => $validated['student_number'],
                    'mds_school_year' => $validated['mds_school_year'],
                    'mds_phone' => $validated['mds_phone'],
                    'mds_email' => $validated['mds_email'],
                    'mds_reason' => $validated['mds_reason'],
                    'mds_proof_filename' => $validated['mds_proof']->getClientOriginalName(),
                    'mds_status' => 2,
                ]);
                
                
                $text = "Berhasil membuat pengajuan";
            }

            if(config('app.disable_cloud_storage')) {
                $upload_success = '/';
            }else{
                $upload_success = Storage::disk('minio')->put('student/dispensation_submission_proof/'.$dispensation->mds_id, $validated['mds_proof']);
                // INI BUAT READ / GET IMAGE
                // $url = Storage::disk('minio_read')->temporaryUrl($query->mo_logo, \Carbon\Carbon::now()->addMinutes(60));
            }

            if(!$upload_success) {
                throw new \Exception('Failed uploading file!');
            }

            $dispensation->mds_proof = $upload_success;
            $dispensation->update();

            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json($e->getMessage());
        }
        return json_encode(array('success' => true, 'message' => $text));
    }
    
    public function delete($id)
    {
        $data = DispensationSubmission::findOrFail($id);
        $data->delete();

        return json_encode(array('success' => true, 'message' => "Berhasil menghapus pengajuan"));
    }

    public function getSpesific($prr_id){
        $data = DispensationSubmission::where('prr_id', $prr_id)
                ->where('mds_status', 1)
                ->whereNull('deleted_at')
                ->first();
        
        return json_encode($data, JSON_PRETTY_PRINT);
    }

}
