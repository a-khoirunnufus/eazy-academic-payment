<?php

namespace App\Http\Controllers\_Payment\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AcademicRulesApi extends Controller
{
    //
    public function academicRules()
    {
        $data = [
            [
                'id' => 1,
                'period' => '2023/2024',
                'rule_name' => 'Mengambil Cuti',
                'invoice_component' => 'Cuti',
                'instalment' => 'Full 100% Pembayaran',
                'minimum_paid_percent' => 10,
                'is_active' => true,
            ],
            [
                'id' => 1,
                'period' => '2023/2024',
                'rule_name' => 'Mengambil Cuti',
                'invoice_component' => 'Cuti',
                'instalment' => '3 Kali Pembayaran',
                'minimum_paid_percent' => 10,
                'is_active' => false,
            ],
            
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }

    public function addData(Request $request){
        return var_dump($request->entry_period);
    }
}
