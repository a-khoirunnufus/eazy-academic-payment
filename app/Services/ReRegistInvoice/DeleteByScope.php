<?php

namespace App\Services\ReRegistInvoice;

use Illuminate\Support\Facades\DB;
use App\Contracts\GenerateReRegistInvoiceScope;
use App\Exceptions\DeleteInvoiceException;
use App\Services\ReRegistInvoice\DeleteOne;
use App\Services\Queries\ReRegistration\ReRegistInvWithFilter;
use App\Models\Year;

class DeleteByScope {

    public static function delete(int $invoice_period_code, GenerateReRegistInvoiceScope $scope, bool $skip_on_error = false)
    {
        $school_year_id = Year::where('msy_code', '=', $invoice_period_code)->first()?->msy_id ?? 0;

        // get registrants with filter and that has invoice generated
        // with scope filters (faculty/studyprogram/path/period/lecture_type)
        // with register.ms_school_year and invoice_period matched
        $registrants = (new ReRegistInvWithFilter($scope->getFilters()))->query
            ->whereNotNull('payment_reregist.prr_id')
            ->where('register.ms_school_year_id', '=', $school_year_id)
            ->get();

        $deleted_count = 0;

        // foreach registrant generate invoice
        foreach ($registrants as $registrant) {
            if ($skip_on_error) {
                try {
                    DeleteOne::delete($registrant->payment_reregist_id);
                    $deleted_count++;
                } catch (DeleteInvoiceException $ex) {
                    continue;
                }
            } else {
                DeleteOne::delete($registrant->payment_reregist_id);
                $deleted_count++;
            }
        }

        return $deleted_count;
    }
}
