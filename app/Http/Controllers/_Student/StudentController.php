<?php

namespace App\Http\Controllers\_Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $student_number = 1862;
        if(isset($request->query()['student_number'])){
            $q = $request->query()['student_number'];
        }else{
            $q = $student_number;
        }
        $data = Student::findorfail($q);
        return view('pages._student.index',compact('data'));
    }
}
