<?php

namespace App\Traits\Models;

use Illuminate\Http\Request;
use App\Traits\Models\QueryFilterByRequest;

trait QueryFilterExtendByRequest
{
    use QueryFilterByRequest;

    public function applyFilterWithOperator($query, Request $request, $params)
    {
        $filters = collect($request->get('withFilter'));

        foreach ($params as $item) {
            $filter = $filters->where('column', $item)->first();
            if (!$filter) continue;

            $column = $filter['column'];
            $operator = $filter['operator'];
            $value = $filter['value'];

            $path = explode(".", $column);
            if(count($path) == 1){
                // qualifyColumn for prevent ambigous column
                $query->where($query->qualifyColumn($column), $operator, $value);
                continue;
            }

            $relationPath = implode(".", array_slice($path, 0, count($path) - 1));
            $_column = $path[count($path) - 1];

            $query->whereHas($relationPath, function($_query) use($_column, $operator, $value){
               $_query->where($_column, $operator, $value);
            });
        }

        return $query;
    }

    // apply filter with operator fixed column
    public function applyFilterWoFc($query, Request $request, $params)
    {
        $filters = collect($request->get('withFilter'));

        foreach ($params as $item) {
            $filter = $filters->where('column', $item)->first();
            if (!$filter) continue;

            $column = $filter['column'];
            $operator = $filter['operator'];
            $value = $filter['value'];

            $path = explode(".", $column);
            $query->where($column, $operator, $value);
        }
    }
}
