<?php

namespace App\Http\Controllers\_Student\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student\CreditSubmission;
use App\Traits\Authentication\StaticStudentUser;

class CreditController extends Controller
{
    use StaticStudentUser;
    
    public function index(Request $request)
    {
        $email = $request->query('email') ?? $this->example_s_user_email_hafizh;
        $type = $request->query('type') ?? 'student';

        $user = $this->getStaticUser($email, $type);

        if(!$user->student) {
            return 'User with email: '.$email.' not found!';
        }
        
        $query = CreditSubmission::query();
        $query = $query->with('period','student')->where('student_number',$user->student->student_number)->orderBy('mcs_id');
        return datatables($query)->toJson();
    }
}
