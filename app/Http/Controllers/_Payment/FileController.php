<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student\CreditSubmission;
use App\Models\Student\DispensationSubmission;
use Storage;
use Carbon;

class FileController extends Controller
{
    public function getFile($from, $id){

        if($from == "student-credit"){
            $data = CreditSubmission::findorfail($id);
            try{
                $url = Storage::disk("minio_read")->temporaryUrl($data->mcs_proof, \Carbon\Carbon::now()->addMinutes(60));
            }catch(\Exception $e){
                return abort(404); 
            }
        }else if($from == "student-dispensation"){
            $data = DispensationSubmission::findorfail($id);
            try{
                $url = Storage::disk("minio_read")->temporaryUrl($data->mds_proof, \Carbon\Carbon::now()->addMinutes(60));
            }catch(\Exception $e){
                return abort(404); 
            }
        }else{
            $url = "";
        }

        return redirect($url);
    }
}
