<?php

namespace App\Traits\Models\Scopes;

trait SchoolYear
{
    public function scopeActiveByDate($q, $date = null)
    {
        if(is_null($date)){
            $date = date('Y-m-d');
        }
        $q->whereDate('msy_start_date', '<=', $date)
          ->whereDate('msy_end_date', '>=', $date);
    }

    public function scopeActiveByDateRange($q, $start, $end)
    {
        $q->whereDate('msy_start_date', '<=', $start)
          ->whereDate('msy_end_date', '>=', $end);
    }
}
