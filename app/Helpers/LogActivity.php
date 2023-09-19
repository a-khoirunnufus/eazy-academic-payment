<?php


namespace App\Helpers;
use Request;
use App\LogActivity as LogActivityModel;


class LogActivity
{


    public static function addToLog($activity,$user_id,$status,$route = null, $parameter = null)
    {
        LogActivityModel::create([
            'log_activity' => $activity,
            'user_id' => $user_id,
            'log_status' => $status,
            'log_route' => $route,
            'log_route_parameter' => $parameter,
        ]);
    }


    public static function logActivityLists()
    {
    	return LogActivityModel::latest()->get();
    }


}
