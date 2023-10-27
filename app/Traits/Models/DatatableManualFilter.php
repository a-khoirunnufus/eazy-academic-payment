<?php

namespace App\Traits\Models;

trait DatatableManualFilter {

    public function applyManualFilter($datatable, $request, $filter_attributes, $search_attributes)
    {
        $raw_filters = $request->get('filters') ?? [];
        $filters = [];
        foreach ($raw_filters as $filter) {
            if (
                in_array($filter['column'], $filter_attributes)
                && ( $filter['value'] != '#ALL' && $filter['value'] !== null )
            ) {
                $filters[] = $filter;
            }
        }

        $datatable->filter(function ($query) use($filters, $search_attributes) {

            /**
             * FILTER
             */
            foreach ($filters as $filter) {
                $item = $filter['column'];
                $operator = $filter['operator'];
                $value = $filter['value'];

                $path = explode(".", $item);
                if(count($path) == 1){
                    // qualifyColumn for prevent ambigous column
                    $query->where($query->qualifyColumn($item), $operator, $value);
                    continue;
                }

                $relationPath = implode(".", array_slice($path, 0, count($path) - 1));
                $_item = $path[count($path) - 1];

                $query->whereHas($relationPath, function($_query) use($_item, $operator, $value){
                   $_query->where($_item, $operator, $value);
                });
            }


            /**
             * SEARCH
             */
            if (request()->get('search')['value'] !== null) {
                $query->where(function($query) use ($search_attributes){
                    foreach ($search_attributes as $idx => $attr) {

                        $relation = explode('.', $attr);
                        $col = array_pop($relation);
                        if (count($relation) > 0) {
                            $relation = implode('.', $relation);
                        } else {
                            $relation = null;
                        }

                        if ($idx == 0) {
                            if ($relation) {
                                $query->whereHas($relation, function($q) use($col) {
                                    $q->where($col, 'ilike', "%" . request()->get('search')['value'] . "%");
                                });
                            } else {
                                $query->where($col, 'ilike', "%" . request()->get('search')['value'] . "%");
                            }
                        } else {
                            if ($relation) {
                                $query->orWhereHas($relation, function($q) use($col) {
                                    $q->where($col, 'ilike', "%" . request()->get('search')['value'] . "%");
                                });
                            } else {
                                $query->orWhere($col, 'ilike', "%" . request()->get('search')['value'] . "%");
                            }
                        }
                    }
                });
            }
        });
    }

    public function applyManualFilterWoFc($datatable, $request, $filter_columns, $search_columns)
    {
        $filters = $request->get('filters') ?? [];

        $datatable->filter(function ($query) use($filters, $search_columns) {

            /**
             * FILTER
             */
            foreach ($filters as $filter) {
                $column = $filter['column'];
                $operator = $filter['operator'];
                $value = $filter['value'];
                $query->where($column, $operator, $value);
            }

            /**
             * SEARCH
             */
            if (request()->get('search')['value'] !== null) {
                $query->where(function($query) use ($search_columns){
                    foreach ($search_columns as $idx => $col) {
                        if ($idx == 0) {
                            $query->where($col, 'ilike', "%" . request()->get('search')['value'] . "%");
                        } else {
                            $query->orWhere($col, 'ilike', "%" . request()->get('search')['value'] . "%");
                        }
                    }
                });
            }
        });
    }
}
