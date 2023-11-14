<?php

namespace App\Services\Payment;

use App\Models\Payment\Payment;

/**
 * Usage Example
 *
 * $status = (new PaymentStatus('1234', '20231'))->status();
 * $paid_percentage = (new PaymentStatus('1234', '20231'))->paid_percentage();
 */

class PaymentStatus
{

    private $invoices;

    /**
     * @param int $student_number
     * @param string $school_year_code
     */
    public function __construct(int $student_number, string $school_year_code) {

        $payments = Payment::with('paymentType')
            ->where([
                'student_number' => $student_number,
                'prr_school_year' => $school_year_code,
            ])
            ->whereHas('paymentType', function($q) {
                $q->where('msct_main_payment', '=', 1);
            })
            ->get();

        $this->invoices = $payments;
    }

    /**
     * get invoice payment status detail
     *
     * @return object $res      $res->result is payment status, value either 'lunas' or 'belum lunas'.
     */
    public function status_detail()
    {
        if ($this->validate()->status == 'invoices_not_found') {
            return (object)[
                'message' => 'invoices not found, status converted to paid off',
                'code' => 'invoices_not_found',
                'result' => 'lunas',
            ];
        }

        $is_paid_off = true;

        foreach ($this->invoices as $invoice) {
            $status = $invoice->prr_status;

            if ($status == 'belum lunas' || $status == 'kredit') {
                $is_paid_off = false;
                break;
            }
        }

        if (!$is_paid_off) {
            return (object)[
                'message' => 'invoice has not paid off',
                'code' => 'invoice_not_paid_off',
                'result' => 'belum lunas',
            ];
        }

        return (object)[
            'message' => 'invoice has paid',
            'code' => 'invoice_paid_off',
            'result' => 'lunas',
        ];
    }

    /**
     * get invoice payment status
     *
     * @return string   payment status, value either 'lunas' or 'belum lunas'.
     */
    public function status()
    {
        return $this->status_detail()->result;
    }

    /**
     * get invoice payment paid percentage detail
     *
     * @return object $res      $res->result is paid percentage, float data type, value
     *                          between 0 to 1 with 4 decimal places.
     */
    public function paid_percentage_detail()
    {
        if ($this->validate()->status == 'invoices_not_found') {
            return (object)[
                'message' => 'invoices not found, percentage set to 100%',
                'code' => 'invoices_not_found',
                'result' => (float)1,
            ];
        }

        $total_bill = 0;
        $total_paid = 0;

        foreach ($this->invoices as $invoice) {
            $total_bill += $invoice->prr_total;

            if ($invoice->prr_status == 'lunas') {
                $total_paid += $invoice->prr_total;
            } else {
                $total_paid += $invoice->computed_total_paid;
            }
        }

        $paid_percentage = round($total_paid/$total_bill, 4);

        return (object)[
            'message' => 'successfully calculate paid percentage',
            'code' => 'success',
            'result' => $paid_percentage,
        ];
    }

    /**
     * get invoice payment paid percentage
     *
     * @return float paid percentage, float data type, value between 0 to 1 with 4 decimal places.
     */
    public function paid_percentage()
    {
        return $this->paid_percentage_detail()->result;
    }

    /**
     * validate invoice
     */
    public function validate()
    {
        if (is_null($this->invoices)) {
            return (object)[
                'status' => 'invoices_not_found',
                'message' => 'invoices not found!',
            ];
        }

        return (object)['status' => 'invoices_found'];
    }

    public function get_invoices()
    {
        return $this->invoices;
    }
}
