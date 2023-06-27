<?php

namespace App\Http\Controllers\_Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentBill;
use App\Models\Masterdata\MsPaymentMethod;
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
        $email = $request->query('email') ?? $this->example_ns_user_email;
        $type = $request->query('type') ?? 'new_student';

        $user = $this->getStaticUser($email, $type);

        if(!$user) {
            return 'User with email: '.$email.' not found!';
        }

        return view('pages._student.payment.index', compact('user'));
    }

    public function proceedPayment($prr_id, Request $request)
    {
        $email = $request->query('email') ?? $this->example_ns_user_email;
        $type = $request->query('type') ?? 'new_student';

        $user = $this->getStaticUser($email, $type);

        if(!$user) {
            return 'User with email: '.$email.' not found!';
        }

        return view('pages._student.proceed-payment.index', compact('prr_id', 'user'));
    }
}
