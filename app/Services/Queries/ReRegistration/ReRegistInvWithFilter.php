<?php

namespace App\Services\Queries\ReRegistration;

use Illuminate\Support\Facades\DB;
use App\Services\Queries\ReRegistration\ReRegistration;

class ReRegistInvWithFilter extends ReRegistrationInvoice {

    private $filters;

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

    public function __construct(array $filters) {
        $this->filters = $filters;
        parent::__construct();
    }

    protected function queryFilter()
    {
        parent::queryFilter();

        $query = $this->query;
        foreach ($this->filters as $filter) {
            $query->where($filter['key'], '=', $filter['value']);
        }

        $this->query = $query;
    }
}
