<?php

namespace App\Traits\Models;

use Illuminate\Http\Request;

trait QueryFilterByRequest
{
    public function applyFilter($query, Request $request, $params)
    {
        foreach ($params as $item) {
            $valueKey = $item;

            $path = explode(".", $item);
            if(count($path) > 1)
                $valueKey = str_replace(".", "_", $item);

            $value = $request->get($valueKey);

            if(!$value)
                continue;

            if(count($path) == 1){
                // qualifyColumn for prevent ambigous column
                $query->where($query->qualifyColumn($item), $value);
                continue;
            }

            $relationPath = implode(".", array_slice($path, 0, count($path) - 1));
            $_item = $path[count($path) - 1];
            $query->whereHas($relationPath, function($_query) use($_item, $value){
               $_query->where($_item, $value);
            });
        }

        return $query;
    }

    public function applyScope($query, Request $request, $scopes)
    {
        if(!$request->get('withScopes'))
            return $query;

        foreach($request->get('withScopes') as $scope){
            if(!isset($scope['name']))
                continue;
            if(!in_array($scope['name'], $scopes))
                continue;

            $param = null;
            if(isset($scope['param']))
                $param = $scope['param'];

            $scopeArray = explode(".", $scope['name']);
            if(count($scopeArray) == 1){
                $query->{$scopeArray[0]}($param);
                continue;
            }
    
            $relationPath = implode(".", array_slice($scopeArray, 0, count($scopeArray) - 1));
            $query->whereHas($relationPath, function($q2) use($scopeArray, $param){
               $q2->{$scopeArray[count($scopeArray) - 1]}($param);
            });
        }

        return $query;
    }
}