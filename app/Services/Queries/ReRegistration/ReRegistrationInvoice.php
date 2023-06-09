<?php

namespace App\Services\Queries\ReRegistration;

use Illuminate\Support\Facades\DB;
use App\Services\Queries\ReRegistration\ReRegistration;

class ReRegistrationInvoice extends ReRegistration {

    /**
     * Table Aliases:
     * pmb.register -> register
     * pmb.participant -> participant
     * masterdata.ms_school_year as school_year
     * pmb.ms_period -> period
     * pmb.ms_path -> path
     * masterdata.ms_studyprogram -> studyprogram
     * masterdata.ms_lecture_type -> lecture_type
     * masterdata.ms_faculties -> faculty
     * finance.payment_re_register -> payment_reregist
     */

    protected function queryBase()
    {
        parent::queryBase();

        // condition when prr record not exist(or deleted) still join prr as null value
        $query = $this->query->join('finance.payment_re_register as payment_reregist', function ($join) {
            $join->on('register.reg_id', '=', 'payment_reregist.reg_id');
            $join->on('payment_reregist.deleted_at', 'is', DB::raw('null'));
        }, null, null, 'left outer');

        $this->query = $query;
    }

    protected function getSelectColumns()
    {
        $default_selects = parent::getSelectColumns();

        $extended_selects = array_merge(
            $default_selects,
            [
                'payment_reregist.prr_id as payment_reregist_id',
                DB::raw("
                    CASE
                        WHEN payment_reregist.prr_id is null THEN 'Belum Digenerate'
                        ELSE 'Sudah Digenerate'
                    END as payment_reregist_invoice_status
                "),
                'payment_reregist.prr_total as payment_reregist_invoice_amount',
            ],
        );

        return $extended_selects;
    }
}
