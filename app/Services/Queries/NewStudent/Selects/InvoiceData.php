<?php

namespace App\Services\Queries\NewStudent\Selects;

use Illuminate\Support\Facades\DB;
use App\Contracts\Queries\NewStudentSelect;

class InvoiceData implements NewStudentSelect
{
    public function expressions(): array
    {
        return [
            // add invoice period
            'prr.prr_id as payment_re_register_id',
            DB::raw("
                CASE
                    WHEN prr.prr_id is null THEN 'Belum Digenerate'
                    ELSE 'Sudah Digenerate'
                END as invoice_status
            "),
            'prr.prr_total as invoice_amount',
        ];
    }
}
