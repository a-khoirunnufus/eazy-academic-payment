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
        $query = $query->with('period','student')->orderBy('mcs_id');
        return datatables($query)->toJson();
    }
}
