<?php

namespace App\Services\ReRegistInvoice;

use Illuminate\Support\Facades\DB;
use App\Contracts\GenerateReRegistInvoiceScope;
use App\Exceptions\GenerateInvoiceException;
use App\Services\ReRegistInvoice\GenerateOne;
use App\Services\Queries\ReRegistration\ReRegistInvWithFilter;
use App\Models\Year;

class GenerateByScope {

    public static function generate(int $invoice_period_code, GenerateReRegistInvoiceScope $scope, bool $skip_on_error = false)
    {
        $school_year_id = Year::where('msy_code', '=', $invoice_period_code)->first()?->msy_id ?? 0;

        // get registrants with filter and that has not invoice generated
        // with scope filters (faculty/studyprogram/path/period/lecture_type)
        // with register.ms_school_year and invoice_period matched
        $registrants = (new ReRegistInvWithFilter($scope->getFilters()))->query
            ->where('payment_reregist.prr_id', 'is', DB::raw('NULL'))
            ->where('register.ms_school_year_id', '=', $school_year_id)
            ->get();

        $generated_count = 0;

        // foreach registrant generate invoice
        foreach ($registrants as $registrant) {
            if ($skip_on_error) {
                try {
                    GenerateOne::generate($invoice_period_code, $registrant->registration_id);
                    $generated_count++;
                } catch (GenerateInvoiceException $ex) {
                    continue;
                }
            } else {
                GenerateOne::generate($invoice_period_code, $registrant->registration_id);
                $generated_count++;
            }
        }

        return $generated_count;
    }
}
