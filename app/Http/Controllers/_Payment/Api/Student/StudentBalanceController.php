<?php

namespace App\Http\Controllers\_Payment\Api\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Payment\StudentBalanceTrans;

class StudentBalanceController extends Controller
{
    public function balance(Request $request)
    {
        // NOTE:
        // - not working for new student case

        $validated = $request->validate([
            'student_number' => 'required',
        ]);

        $balance = StudentBalanceTrans::where('student_number', $validated['student_number'])
            ->orderBy('sbt_time', 'desc')
            ->first()
            ?->sbt_closing_balance
            ?? 0;

        return response()->json([
            'balance' => $balance
        ]);
    }

    public function dtTransaction(Request $request)
    {
        $validated = $request->validate([
            'student_number' => 'required',
        ]);

        $query = StudentBalanceTrans::with('type')
            ->where('student_number', $validated['student_number'])
            ->orderBy('sbt_time', 'asc');

        return datatables($query)->toJson();
    }
}
