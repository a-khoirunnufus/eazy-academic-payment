<?php

namespace App\Http\Controllers\_Student\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TempResourceController extends Controller
{
    public function invoice() {
        $data = [
            [
                'id' => 1,
                'period' => '2022/2023',
                'semester' => 'Semester Genap',
                'invoice_code' => 'INV/20192/2010210',
                'month' => 'Januari - Pebruari',
                'nth_installment' => 1,
                'invoice_detail' => [
                    ['name' => 'BPP', 'nominal' => 7500000],
                    ['name' => 'Praktikum', 'nominal' => 200000],
                    ['name' => 'SKS', 'nominal' => 200000],
                    ['name' => 'Seragam', 'nominal' => 100000],
                ],
                'invoice_total' => 8000000,
                'discount_detail' => [
                    ['name' => 'Potongan 1', 'nominal' => 100000],
                    ['name' => 'Potongan 2', 'nominal' => 100000],
                ],
                'discount_total' => 200000,
                'scholarship_detail' => [
                    ['name' => 'Djarum', 'nominal' => 3000000],
                    ['name' => 'Alumni', 'nominal' => 2500000],
                ],
                'scholarship_total' => 5500000,
                'all_invoice_total' => 2300000,
                'payment_total' => 2300000,
            ],
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }

    public function payment() {
        $data = [
            [
                'id' => 1,
                'period' => '2022/2023',
                'semester' => 'Semester Genap',
                'invoice_code' => 'INV/20192/2010210',
                'month' => 'Januari - Pebruari',
                'nth_installment' => 1,
                'payment_method_name' => 'VA BNI',
                'payment_method_detail' => [
                    ['label' => 'Kode', 'value' => '002201923123'],
                    ['label' => 'Tanggal', 'value' => '01-02-2023 / 11:05:00'],
                ],
                'invoice_total' => 2300000,
                'payment_total' => 2300000,
                'is_paid_off' => true,
            ],
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }
}
