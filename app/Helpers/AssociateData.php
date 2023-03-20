<?php

namespace App\Helpers;

class AssociateData
{
    protected static $aliases = [
        'system' => null
    ];

    protected static $identifier = [
        'system' => null
    ];

    public static function getAssociateList()
    {
        $list = [];
        foreach(self::$identifier as $key => $item){
            $list[$key] = trans("associatedata.".$key);
        }
        return $list;
    }

    public static function getAssociatedData($modelAlias, $identifier)
    {
        $modelAliasArr = explode(":", $modelAlias);
        if(!isset(self::$aliases[$modelAliasArr[0]]))
            return null;

        if(is_null(self::$aliases[$modelAliasArr[0]]))
            return null;

        $model = self::$aliases[$modelAliasArr[0]]::query();
        if(count($modelAliasArr) == 2){
            $scope = $modelAliasArr[1];
            $model->{$scope}();
        }

        $identifierColumn = self::$identifier[$modelAlias];

        return $model->where($identifierColumn, $identifier)->first();
    }
}