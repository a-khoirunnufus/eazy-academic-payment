<?php

namespace App\Http\Controllers\_Payment\Api\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\Student;

class StudentController extends Controller
{
    public function show($student_id)
    {
        $student = Student::where('student_id', $student_id)->first();

        if (!$student) {
            return response()->json(['error' => 'Student data not found'], 404);
        }

        return response()->json($student, 200);
    }
}
