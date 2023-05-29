<?php

namespace App\Services\Queries\NewStudent\Selects;

use Illuminate\Support\Facades\DB;
use App\Contracts\Queries\NewStudentSelect;

class InvoiceData implements NewStudentSelect
{
    public function expressions(): array
    {
        return [
            'prr.prr_id as payment_re_register_id',
            DB::raw("
                CASE
                    WHEN prr.prr_id is null THEN 'not_generated'
                    ELSE 'generated'
                END as invoice_status
            "),
            'prr.prr_total as invoice_amount',
        ];
    }
}
