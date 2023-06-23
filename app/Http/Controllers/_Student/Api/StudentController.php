<?php

namespace App\Http\Controllers\_Student\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PMB\Participant as NewStudent;
use App\Models\HR\MsStudent as Student;

class StudentController extends Controller
{
    public function show(Request $request)
    {
        /**
         * TODO:
         * - protect sensitive data sending to client
         */

        $validated = $request->validate([
            'student_type' => 'required|in:new_student,student',
            'par_id' => 'required_if:student_type,new_student',
            'student_id' => 'required_if:student_type,student',
        ]);

        $student = null;
        if ($validated['student_type'] == 'new_student') {
            $student = NewStudent::where('par_id', '=', $validated['par_id'])
                ->select('par_fullname', 'par_nik', 'par_phone')
                ->first()
                ->toArray();
        } elseif ($validated['student_type'] == 'student') {
            $student = Student::with(['studyprogram', 'studyprogram.faculty'])
                ->where('student_id', '=', $validated['student_id'])
                ->first()
                ->toArray();
        }

        if (!$student) {
            return response()->json(['error' => 'Student data not found'], 404);
        }

        return response()->json($student, 200);
    }
}
