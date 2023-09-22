<?php

namespace App\Services;

use App\Models\SchoolYear;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class SchoolYearService
{
    protected static $activeDataCacheKey = "active-year-cache";

    public static function getActiveByDate($date = null)
    {
        if($date != null)
            return SchoolYear::activeByDate($date)
                ->first()?->toArray() ?? null;

        // implement TTL for current active school year
        return self::getCachedActiveData();
    }

    public static function getActiveByDateRange($start_date, $end_date)
    {
        return SchoolYear::activeByDateRange($start_date, $end_date)
            ->first();
    }
    
    public static function getCachedActiveData()
    {
        if(!Cache::has(self::$activeDataCacheKey))
            self::setCachedActiveData(self::getActiveByDate(date('Y-m-d H:i:s')));

        return Cache::get(self::$activeDataCacheKey);
    }

    public static function setCachedActiveData($value)
    {
        // Calculate the remaining time until the end of the day
        $now = Carbon::now();
        $endOfDay = $now->copy()->endOfDay();
        $remainingTime = $now->diffInSeconds($endOfDay);
        // set cache
        Cache::put(self::$activeDataCacheKey, $value, $remainingTime);
    }

    public static function updateCachedActiveData()
    {
        $data = self::getActiveByDate(date('Y-m-d H:i:s'));
        self::setCachedActiveData($data);
    }

    public static function fromCodeToYearAndSemester($school_year_code)
    {
        $year = substr($school_year_code, 0, 4);
        $semester = substr($school_year_code, 4, 1);

        return [
            'year' => $year."/".((int)$year + 1),
            'semester' => $semester
        ];
    }
}