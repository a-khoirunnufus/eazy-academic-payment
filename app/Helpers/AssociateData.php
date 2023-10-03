<?php

namespace App\Helpers;

use App\Models\UserAssociateModel;

class AssociateData
{
    protected static $aliases = [
        'employee' => \App\Models\MsEmployee::class,
        'student' => \App\Models\Payment\Student::class,
        'studyprogram' => \App\Models\Payment\StudyProgram::class,
        'faculty' => \App\Models\MsFaculties::class,
        'system' => null
    ];

    protected static $identifier = [
        'employee' => 'emp_num',
        'employee:isLecturer' => 'lecturer_code',
        'student' => 'student_number',
        'student:isAlumni' => 'student_number',
        'studyprogram' => 'studyprogram_id',
        'faculty' => 'faculty_id',
        'system' => null
    ];

    public static function getAssociateList()
    {
        $list = [];
        foreach (self::$identifier as $key => $item) {
            $list[$key] = trans("associatedata." . $key);
        }
        return $list;
    }

    public static function getFillableListByUser()
    {
        $list = [];
        foreach (self::$identifier as $key => $item) {
            $list[$key] = trans("associatedata." . $key);
        }

        unset($list["employee"]);
        unset($list["employee:isLecturer"]);
        unset($list["student"]);
        unset($list["student:isAlumni"]);

        return $list;
    }

    public static function isRequireIdentifier($model)
    {
        if(!isset(self::$identifier[$model]))
            return false;

        return self::$identifier[$model] != null;
    }

    public static function getAssociatedData($modelAlias, $identifier)
    {
        $modelAliasArr = explode(":", $modelAlias);
        if (!isset(self::$aliases[$modelAliasArr[0]]))
            return null;

        if (is_null(self::$aliases[$modelAliasArr[0]]))
            return null;

        $model = self::$aliases[$modelAliasArr[0]]::query();
        if (count($modelAliasArr) == 2) {
            $scope = $modelAliasArr[1];
            $model->{$scope}();
        }

        $identifierColumn = self::$identifier[$modelAlias];

        return $model->where($identifierColumn, $identifier)->first();
    }

    public static function getSimplifiedAssocData($modelAlias, $identifier)
    {
        $data = self::getAssociatedData($modelAlias, $identifier);

        $text = null;
        if($modelAlias == "student") {
            $text = $data->student_id." - ".$data->fullname;
        } else if ($modelAlias == "studyprogram") {
            $text = $data->studyprogram_type." - ".$data->studyprogram_name;
        } else if ($modelAlias == "faculty") {
            $text = $data->faculty_name;
        }

        return [
            "id" => $identifier,
            "text" => $text
        ];
    }

    public static function getFromUserId($user_id, $modelAlias)
    {
        $identifier = UserAssociateModel::where('model', 'student')
            ->where('user_id', $user_id)
            ->first()?->associate_identifier;
        if($identifier == null)
            return null;

        return self::getAssociatedData($modelAlias, $identifier);
    }
}
