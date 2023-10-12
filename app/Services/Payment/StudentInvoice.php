<?php

namespace App\Services\Payment;

use App\Models\Payment\Payment;

class StudentInvoice {

    /**
     * Get invoice status of given student
     *
     * @param Student   $student            Student object
     * @param string    $school_year_code   School year code
     *
     * @return string|null  Return payment status {'lunas', 'belum lunas', 'kredit'},
     *                      if invoice not found return null instead.
     */
    public static function status($student, $school_year_code)
    {
        $invoice = Payment::where('student_number', $student->student_number)
            ->where('prr_school_year', $school_year_code)
            ->first();

        if ($invoice == null) {
            return null;
        }

        return $invoice->computed_payment_status;
    }
}
