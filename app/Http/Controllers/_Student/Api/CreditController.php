<?php

namespace App\Http\Controllers\_Student\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student\CreditSubmission;
use App\Traits\Authentication\StaticStudentUser;
use App\Http\Requests\Student\CreditRequest;
use DB;
use Storage;

class CreditController extends Controller
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
        
        $query = CreditSubmission::query();
        $query = $query->with('period','student')->where('student_number',$user->student->student_number)->orderBy('mcs_id');
        return datatables($query)->toJson();
    }
    
    public function store(CreditRequest $request)
    {
        // $file = $request->file('mcs_proof');
        // dd($request);
        $validated = $request->validated();

        // $file = $validated->file('mcs_proof');
        // dd($validated);
        // dd($validated['mcs_proof']->getClientOriginalName());
        
        try{
            DB::beginTransaction();

            if(array_key_exists("msc_id",$validated)){
                $credit = CreditSubmission::findOrFail($validated["msc_id"]);
                $credit->update([
                    'mcs_phone' => $validated['mcs_phone'],
                    'mcs_email' => $validated['mcs_email'],
                    'mcs_reason' => $validated['mcs_reason'],
                    'mcs_method' => $validated['mcs_method'],
                    'mcs_proof_filename' => $validated['mcs_proof']->getClientOriginalName(),
                    'mcs_status' => 2,
                ]);
                $text = "Berhasil memperbarui pengajuan";
            }else{
                $credit = CreditSubmission::create([
                    'student_number' => $validated['student_number'],
                    'msy_id' => $validated['msy_id'],
                    'mcs_phone' => $validated['mcs_phone'],
                    'mcs_email' => $validated['mcs_email'],
                    'mcs_reason' => $validated['mcs_reason'],
                    'mcs_method' => $validated['mcs_method'],
                    'mcs_proof_filename' => $validated['mcs_proof']->getClientOriginalName(),
                    'mcs_status' => 2,
                ]);
                
                
                $text = "Berhasil membuat pengajuan";
            }

            if(config('app.disable_cloud_storage')) {
                $upload_success = '/';
            }else{
                $upload_success = Storage::disk('minio')->put('student/credit_submission_proof/'.$credit->mcs_id, $validated['mcs_proof']);
                // INI BUAT READ / GET IMAGE
                // $url = Storage::disk('minio_read')->temporaryUrl($query->mo_logo, \Carbon\Carbon::now()->addMinutes(60));
            }

            if(!$upload_success) {
                throw new \Exception('Failed uploading file!');
            }

            $credit->mcs_proof = $upload_success;
            $credit->update();

            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json($e->getMessage());
        }
        return json_encode(array('success' => true, 'message' => $text));
    }
    
    public function delete($id)
    {
        $data = CreditSubmission::findOrFail($id);
        $data->delete();

        return json_encode(array('success' => true, 'message' => "Berhasil menghapus pengajuan"));
    }

}
