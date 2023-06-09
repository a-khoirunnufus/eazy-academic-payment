<?php

namespace App\Services\ReRegistInvoice;

class CountGeneratedInvoice {
    public static function count($registrants)
    {
        $all_registrants = count($registrants);
        $generated_invoice_count = 0;

        foreach($registrants as $registrant) {
            if ($registrant->payment_reregist_invoice_status == 'Sudah Digenerate') {
                $generated_invoice_count++;
            }
        }

        if ($generated_invoice_count == 0) {
            return [
                'status' => 'not_generated',
                'text' => 'Belum Digenerate ('.$generated_invoice_count.'/'.$all_registrants.')',
            ];
        }
        elseif ($all_registrants != 0 && $generated_invoice_count < $all_registrants) {
            return [
                'status' => 'partial_generated',
                'text' => 'Telah Digenerate Sebagian ('.$generated_invoice_count.'/'.$all_registrants.')',
            ];
        }
        elseif ($all_registrants != 0 && $generated_invoice_count == $all_registrants) {
            return [
                'status' => 'done_generated',
                'text' => 'Telah Digenerate ('.$generated_invoice_count.'/'.$all_registrants.')',
            ];
        }
    }
}
