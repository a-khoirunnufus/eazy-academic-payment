<?php

namespace App\Traits\Payment;

use App\Providers\RouteServiceProvider;

trait General
{
    public function getAuthId(){
        return 1;
    }

    public function getActiveSchoolYearCode(){
        return 22231;
    }

    public function getStudentName($student, $newStudent){
        if($student){
            return $student->fullname;
        }

        if($newStudent){
            if($newStudent->participant){
                return $newStudent->participant->par_fullname;
            }
        }
        return 'Unknown';
    }

    public function getStudentNumber($student, $newStudent){
        if($student){
            return $student->student_id;
        }

        if($newStudent){
            return $newStudent->reg_number;
        }
        return 'Unknown';
    }

    public function getMessage($message){
        if($message){
            return ' - '.$message;
        }
        return 'Unknown';
    }

}
