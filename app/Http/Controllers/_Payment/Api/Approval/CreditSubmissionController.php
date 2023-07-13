<?php

namespace App\Http\Controllers\_Payment\API\Approval;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student\CreditSubmission;

class CreditSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = CreditSubmission::query();
        $query = $query->with('period','student','payment')->whereHas('payment', function($q){
            $q->whereColumn('finance.payment_re_register.prr_school_year', 'finance.ms_credit_submission.mcs_school_year');
        })->orderBy('finance.ms_credit_submission.mcs_id');
        // dd($query->get());
        return datatables($query)->toJson();
    }
}
