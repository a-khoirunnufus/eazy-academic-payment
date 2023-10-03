<?php

namespace App\Traits\Models;

use Illuminate\Http\Request;

trait LoadDataAppendByRequest
{
    public function applyAppend($query, Request $request, $availableAttributes)
    {
        if(!$request->get('withAppend'))
            return $query;

        $appends = [];
        foreach($request->get('withAppend') as $attr){
            if(!in_array($attr, $availableAttributes))
                continue;
            $appends[] = $attr;
        }

        if(count($appends) > 0) {
            if ($query instanceof \Illuminate\Database\Eloquent\Collection) {
                return $query->each->setAppends($appends);
            }
            return $query->setAppends($appends);
        } else {
            return $query;
        }
    }
}
