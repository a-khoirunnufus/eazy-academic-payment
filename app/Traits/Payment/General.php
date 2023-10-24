<?php

namespace App\Traits\Payment;

use App\Providers\RouteServiceProvider;
use App\Services\SchoolYearService;
use Illuminate\Support\Facades\Cache;
use Auth;
use App\Models\Payment\Student;
use App\Models\Payment\Settings;
use Carbon\Carbon;

trait General
{
    public function getAuthId(){
        return 1;
    }

    public function getActiveSchoolYear()
    {
        return "2022/2023 - Ganjil";
        $schoolYear = SchoolYearService::getActiveByDate();
        return $schoolYear['msy_code'];
    }

    public function getActiveSchoolYearCode()
    {
        $schoolYear = SchoolYearService::getActiveByDate();
        return $schoolYear['msy_code'];
    }

    public function getActiveSchoolYearId()
    {
        $schoolYear = SchoolYearService::getActiveByDate();
        return $schoolYear['msy_id'];
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
        $student = Auth::user()->getAssociateData('student');
        // load faculty & studyprogram data
        $student->load('studyProgram.faculty');

        // Manually get student model
        // ['associate_identifier' => $student_number] = Auth::user()->getLoadedData()['student'];

        // $student = Student::with(['studyProgram', 'studyProgram.faculty'])
        //     ->where('student_number', $student_number)
        //     ->first();

        return $student;
    }

    private function getCurrentDateTime()
    {
        return Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s O');
    }

    public function getCacheSetting($name){
        if (Cache::has($name)) {
            return Cache::get($name);
        } else {
            $value = Settings::where('name',$name)->pluck('value')[0];
            Cache::put($name, $value, 30*60 );
            return $value;
        }
    }

    public static function fromCodeToWords($school_year_code)
    {
        $year = substr($school_year_code, 0, 4);
        $semester = substr($school_year_code, 4, 1);

        return "Semester ".($semester == 1 ? "Ganjil":"Genap") ." ".$year."/".((int)$year + 1);
    }
}
