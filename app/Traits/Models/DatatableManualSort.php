<?php

namespace App\Traits\Models;
use Illuminate\Support\Arr;

trait DatatableManualSort {

    /**
     * @param Datatable $datatable          Datatable object
     * @param Request   $request            Request object
     * @param array     $sort_attributes    Valid column list to sorting
     *
     * @return array Datatable data source
     */
    public function applyManualSort($datatable, $request, $sort_attributes)
    {
        $datatable_array = $datatable
            ->order(function($q){}) // disable default ordering
            ->toArray();

        if ($request->get('order')) {
            $order = $request->get('order')[0];
            $order_column = $request->get('columns')[$order['column']]['data'];
            $order_dir = $order['dir'];

            // skip sorting if column not valid
            if ( !in_array($order_column, $sort_attributes) ) {
                return $datatable_array;
            }

            if ($order_dir == 'asc') {
                $sorted_datatable_data = array_values(Arr::sort($datatable_array['data'], function($value) use($order_column) {
                    $temp_value = $value;

                    foreach(explode('.', $order_column) as $attr) {
                        $temp_value = $temp_value[$attr];
                    }

                    return $temp_value;
                }));
            }
            elseif ($order_dir == 'desc') {
                $sorted_datatable_data = array_values(Arr::sortDesc($datatable_array['data'], function($value) use($order_column) {
                    $temp_value = $value;

                    foreach(explode('.', $order_column) as $attr) {
                        $temp_value = $temp_value[$attr];
                    }

                    return $temp_value;
                }));
            }

            $datatable_array['data'] = $sorted_datatable_data;
        }

        return $datatable_array;
    }
}
