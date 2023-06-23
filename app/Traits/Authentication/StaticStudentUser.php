<?php

namespace App\Traits\Authentication;

use Illuminate\Support\Facades\DB;
use App\Models\PMB\User as NewStudentUser;
use App\Models\Masterdata\MsUser as StudentUser;

trait StaticStudentUser
{
    private $example_ns_user_id = 188;
    private $example_ns_user_email = 'omanaristarihoran33@gmail.com';
    private $example_ns_user_password = '@Pass1234';

    private $example_s_user_id = 1023;
    private $example_s_user_email = 'Gugun@gmail.com';
    private $example_s_user_password = null;

    private function getStaticNewStudentUser()
    {
        $user = NewStudentUser::with(['participant' => function($query) {
                $query->select('user_id', 'par_id', 'par_fullname', 'par_nik', 'par_phone');
            }])
            ->where('user_email', '=', $this->example_ns_user_email)
            ->first();

        return $user;
    }

    private function getStaticStudentUser()
    {
        $user = StudentUser::with(['student'])
            ->where('user_email', '=', $this->example_s_user_email)
            ->first();

        return $user;
    }
}
