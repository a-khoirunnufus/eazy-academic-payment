<?php

namespace App\Traits\Models;

use Illuminate\Http\Request;

trait LoadDataRelationByRequest
{
    public function applyRelation($query, Request $request, $availableRelation)
    {
        if(!$request->get('withData'))
            return $query;

        $relations = [];
        foreach($request->get('withData') as $with){
            if(!in_array($with, $availableRelation))
                continue;
            $relations[] = $with;
        }

        if(count($relations) > 0)
            return $query->with($relations);
        else
            return $query;
    }
}