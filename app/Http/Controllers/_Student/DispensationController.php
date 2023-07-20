<?php

namespace App\Http\Controllers\_Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Authentication\StaticStudentUser;

class DispensationController extends Controller
{
    use StaticStudentUser;

    public function getActiveSchoolYearCode(){
        return 20221;
    }
    public function getActiveSchoolYear(){
        return "2022/2023 - Ganjil";
    }

    public function index(Request $request)
    {
        $email = $request->query('email') ?? $this->example_s_user_email_hafizh;
        $type = $request->query('type') ?? 'student';

        $user = $this->getStaticUser($email, $type);

        if(!$user) {
            return 'User with email: '.$email.' not found!';
        }

        $year = $this->getActiveSchoolYear();
        $yearCode = $this->getActiveSchoolYearCode();

        return view('pages._student.dispensation.index', compact('user','year','yearCode'));
    }
}
