<?php

namespace App\Http\Controllers\_Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Traits\Authentication\StaticStudentUser;

class PaymentController extends Controller
{
    /**
     * @var $default_user_email
     * @func getStaticUser()
     */
    use StaticStudentUser;

    public function index(Request $request)
    {
        $user = $this->getStaticUser();

        return view('pages._student.payment.index', compact('user'));
    }

    public function proceedPayment($prr_id, Request $request)
    {
        $user = $this->getStaticUser();

        return view('pages._student.payment.proceed-payment', compact('user'));
    }
}
