<?php

namespace App\Traits\Authentication;

use Illuminate\Support\Facades\DB;

trait StaticStudentUser
{
    private $default_user_email = 'omanaristarihoran33@gmail.com';
    private $default_user_password = '@Pass1234';

    private function getStaticUser()
    {
        $user = DB::table('pmb.users as u')
            ->leftJoin('pmb.participant as p', 'p.user_id', '=', 'u.user_id')
            ->where('u.user_email', '=', $this->default_user_email)
            ->select(
                'u.user_email as email',
                'p.par_id as participant_id',
                'p.par_number as participant_number',
                'p.par_nik as nik',
                'p.par_fullname as fullname'
            )
            ->first();

        return $user;
    }
}
