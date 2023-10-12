<?php

namespace App\Traits\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

trait LoadDTColumnByRequest
{
    public function applyDTColumn($datatable, Request $request, $mapping)
    {
        if ( !$request->get('withLoadedColumn') ) return $datatable;

        $valid_columns = array_keys($mapping);
        $columns = [];
        foreach ($request->get('withLoadedColumn') as $col){
            if ( !in_array($col, $valid_columns) ) continue;
            $columns[] = $col;
        }

        foreach ($columns as $col) {
            $datatable->addColumn($col, function($model) use($col, $mapping) {
                $attrs = explode('.', $mapping[$col]);
                $value = $model;

                foreach ($attrs as $attr) {
                    $value = $value->{$attr};
                    if ($value == null) break;
                }

                if (str_starts_with($col, 'is_')) {
                    return boolval($value);
                }

                return $value;
            });
        }
    }
}
