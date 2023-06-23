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
        $student_type = $request->input('type') ?? 'new_student';

        if ($student_type == 'new_student') {
            $user = $this->getStaticNewStudentUser();
        } elseif ($student_type == 'student') {
            $user = $this->getStaticStudentUser();
        }

        return view('pages._student.payment.index', compact('user'));
    }

    public function proceedPayment($prr_id, Request $request)
    {
        $student_type = $request->input('type') ?? 'new_student';

        if ($student_type == 'new_student') {
            $user = $this->getStaticNewStudentUser();
        } elseif ($student_type == 'student') {
            $user = $this->getStaticStudentUser();
        }

        $payment = Payment::find($prr_id);
        $payment_bill = PaymentBill::where('prr_id', '=', $prr_id)->first();
        $payment_method = MsPaymentMethod::where('mpm_key', '=', $payment->prr_method ?? '0')->first();

        return view('pages._student.payment.proceed-payment.index', compact('prr_id', 'user', 'payment', 'payment_bill', 'payment_method'));
    }
}
