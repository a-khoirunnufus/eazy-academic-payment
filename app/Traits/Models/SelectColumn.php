<?php

namespace App\Traits\Models;

trait SelectColumn
{
    public function selectColumn($query, $paths, $availableRelations, $additionalGroupBy)
    {
        $group_by_columns = [];

        foreach ($paths as $path) {
            $class_name = get_class($query->getModel());
            $model = $class_name::first();
            $model->load($availableRelations);

            $relations = explode('.', $path);
            $column = array_pop($relations);

            if (count($relations) == 0) {
                $query->addSelect($model->qualifyColumn($column));
                if (!in_array($model->qualifyColumn($model->getKeyName()), $group_by_columns)) {
                    array_push($group_by_columns, $model->qualifyColumn($model->getKeyName()));
                }
            }
            elseif (count($relations) == 1) {
                $model = $model->getRelationValue($relations[0]);
                // \Log::debug(['level 2', $model]);
                if (!$model) continue;
                $query->addSelect($model->qualifyColumn($column));
                $query->leftJoinRelation($relations[0]);
                if (!in_array($model->qualifyColumn($model->getKeyName()), $group_by_columns)) {
                    array_push($group_by_columns, $model->qualifyColumn($model->getKeyName()));
                }
            }
            else {
                $model = $model->getRelationValue($relations[0]);
                // \Log::debug(['level 3', $model]);
                for ($i=1; $i < count($relations) ; $i++) {
                    if (!$model) break;
                    $model = $model->getRelationValue($relations[$i]);
                }
                if (!$model) continue;
                $query->addSelect($model->qualifyColumn($column));
                $query->leftJoinRelation(implode('.', $relations));
                if (!in_array($model->qualifyColumn($model->getKeyName()), $group_by_columns)) {
                    array_push($group_by_columns, $model->qualifyColumn($model->getKeyName()));
                }
            }
        }

        $query->groupBy([...$group_by_columns, ...$additionalGroupBy]);
        // \Log::debug([...$group_by_columns, ...$additionalGroupBy]);
    }
}
