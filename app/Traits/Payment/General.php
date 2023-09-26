<?php

namespace App\Traits\Payment;

use App\Providers\RouteServiceProvider;
use App\Services\SchoolYearService;
use Auth;
use App\Models\Payment\Student;

trait General
{
    public function getAuthId(){
        return 1;
    }

    public function getActiveSchoolYear()
    {
        return "2022/2023 - Ganjil";
    }

    public function getActiveSchoolYearCode()
    {
        $schoolYear = SchoolYearService::getActiveByDate();
        return $schoolYear['msy_code'];
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

    private function getStudentData()
    {
        // Error getting associate data
        // $student = Auth::user()->getAssociateData('student'); // error

        // Manually get student model
        ['associate_identifier' => $student_number] = Auth::user()->getLoadedData()['student'];

        $student = Student::with(['studyProgram', 'studyProgram.faculty'])
            ->where('student_number', $student_number)
            ->first();

        return $student;
    }
}
