<?php

namespace App\Traits\Payment;

use App\Providers\RouteServiceProvider;
use App\Models\Payment\LogActivity as LogActivityModel;
use App\Models\Payment\LogActivityDetail as LogActivityDetailModel;

trait LogActivity
{
    public static function addToLog($activity,$user_id,$status,$route = 'default', $parameter = null)
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

    public static function addToLogDetail($log_id,$title,$status)
    {
        $log = LogActivityDetailModel::create([
            'log_id' => $log_id,
            'lad_title' => $title,
            'lad_status' => $status,
        ]);
        return $log;
    }

    public static function logActivityLists($route = 'default')
    {
    	return LogActivityModel::with('detail', 'user')->where('log_route',$route)->latest()->paginate(10);
    }
}
