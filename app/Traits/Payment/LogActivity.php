<?php

namespace App\Traits\Payment;

use App\Providers\RouteServiceProvider;
use App\Models\Payment\LogActivity as LogActivityModel;
use App\Models\Payment\LogActivityDetail as LogActivityDetailModel;
use App\Traits\Payment\General;
use App\Enums\Payment\LogStatus;

trait LogActivity
{
    use General;

    public function addToLog($activity,$user_id,$status,$route = 'default', $parameter = null)
    {
        $log = LogActivityModel::create([
            'log_activity' => $activity,
            'user_id' => $user_id,
            'log_status' => $status,
            'log_route' => $route ? $route : 'default',
            'log_route_parameter' => $parameter,
        ]);
        return $log;
    }

    public function addToLogDetail($log_id,$title,$status)
    {
        $log = LogActivityDetailModel::create([
            'log_id' => $log_id,
            'lad_title' => $title,
            'lad_status' => $status,
        ]);
        return $log;
    }

    public function logActivityLists($route = 'default')
    {
    	return LogActivityModel::with('detail', 'user')->where('log_route',$route)->latest()->paginate(10);
    }

    public function getLogTitleStudent($student = null, $newStudent = null,$message = null)
    {
        $name = $this->getStudentName($student,$newStudent);
        $number = $this->getStudentNumber($student,$newStudent);
        if($message){
            $text = $this->getMessage($message);
        }else{
            $text = "";
        }
        return $name.' ('.$number.')'.$text;
    }

    public function getLogTitle($data = null, $message = null)
    {
        $data = $data ? $data : "";
        $text = $message ? $this->getMessage($message) : "";
        return $data.$text;
    }

    public static function updateLogStatus($log,$result){
        if(is_object($result)){
            $log->log_status = $result;
        }else{
            if(json_decode($result)){
                $log->log_status = json_decode($result)->success ? LogStatus::Success : LogStatus::Failed;
            }else{
                $log->log_status = LogStatus::Failed;
            }
        }
        $log->update();
    }

}
