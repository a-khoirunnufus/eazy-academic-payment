<?php

namespace App\Http\Controllers\_Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Authentication\StaticStudentUser;

class CreditController extends Controller
{
    use StaticStudentUser;

    public function index(Request $request)
    {
        $email = $request->query('email') ?? $this->example_s_user_email_hafizh;
        $type = $request->query('type') ?? 'student';

        $user = $this->getStaticUser($email, $type);

        if(!$user) {
            return 'User with email: '.$email.' not found!';
        }

        return view('pages._student.credit.index', compact('user'));
    }
}
