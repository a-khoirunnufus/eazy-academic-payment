<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TempResourceController extends Controller
{
    public function invoiceComponent()
    {
        $data = [
            [
                "id" => 1,
                "code" => "CT", 
                "name" => "CUTI",
                "old_student" => true,
                "new_student" => true,
                "registrant" => false,
            ],
            [
                "id" => 2,
                "code" => "DD", 
                "name" => "DENDA",
                "old_student" => true,
                "new_student" => true,
                "registrant" => false,
            ],
            [
                "id" => 3,
                "code" => "MS", 
                "name" => "Daftar Ulang",
                "old_student" => false,
                "new_student" => false,
                "registrant" => true,
            ],
            [
                "id" => 4,
                "code" => "FRM", 
                "name" => "Formulir",
                "old_student" => false,
                "new_student" => false,
                "registrant" => true,
            ],
            [
                "id" => 5,
                "code" => "BPP", 
                "name" => "Biaya Perkuliahan",
                "old_student" => true,
                "new_student" => true,
                "registrant" => true,
            ],
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }

    public function instalmentTemplate()
    {
        $data = [
            [
                'id' => 1,
                'schema_name' => 'Full 100%',
                'n_payment' => 1,
                'validity' => 'Valid',
            ],
            [
                'id' => 2,
                'schema_name' => 'Cicilan 3X',
                'n_payment' => 3,
                'validity' => 'Valid',
            ],
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }

    public function rates()
    {
        $data = [
            [
                'id' => 1,
                'entry_period' => '2023/2024',
                'faculty' => 'Informatika',
                'study_program' => 'S1 Informatika',
                'wave' => 'Periode Juni',
                'registration_path' => 'Mandiri',
                'study_system' => 'Onsite',
                'invoice_component' => 'Biaya Perkuliahan',
                'rate' => 7500000,
                'instalment' => 'Full 100% Pembayaran',
            ],
            [
                'id' => 2,
                'entry_period' => '2023/2024',
                'faculty' => 'Informatika',
                'study_program' => 'S1 Sistem Informasi',
                'wave' => 'Periode Juni',
                'registration_path' => 'Mandiri',
                'study_system' => 'Onsite',
                'invoice_component' => 'Biaya Perkuliahan',
                'rate' => 7500000,
                'instalment' => '3 Kali Pembayaran',
            ],
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }

    public function ratesPerCourse()
    {
        $data = [
            [
                'id' => 1,
                'course_code' => 'CSH1A2',
                'course_name' => 'Pembentukan Karakter',
                'course_type' => 'Kuliah',
                'sks' => 2,
                'semester' => 1,
                'mandatory' => 'W',
                'is_package' => true,
                'rate' => 1000000,
            ],
            [
                'id' => 2,
                'course_code' => 'LUH1A2',
                'course_name' => 'Bahasa Indonesia',
                'course_type' => 'Kuliah',
                'sks' => 2,
                'semester' => 1,
                'mandatory' => 'W',
                'is_package' => true,
                'rate' => 1000000,
            ],
            [
                'id' => 3,
                'course_code' => 'MUH1B3',
                'course_name' => 'Kalkulus IB',
                'course_type' => 'Kuliah',
                'sks' => 3,
                'semester' => 1,
                'mandatory' => 'W',
                'is_package' => true,
                'rate' => 1000000,
            ],
            [
                'id' => 4,
                'course_code' => 'DUH1A2',
                'course_name' => 'Literasi TIK',
                'course_type' => 'Kuliah',
                'sks' => 2,
                'semester' => 1,
                'mandatory' => 'W',
                'is_package' => true,
                'rate' => 1000000,
            ],
            [
                'id' => 5,
                'course_code' => 'CSH1F2',
                'course_name' => 'Pengantar Teknik Informatika',
                'course_type' => 'Kuliah',
                'sks' => 2,
                'semester' => 1,
                'mandatory' => 'W',
                'is_package' => true,
                'rate' => 1000000,
            ],
            [
                'id' => 6,
                'course_code' => 'CCH1A4',
                'course_name' => 'Dasar Algoritma dan Pemrograman',
                'course_type' => 'Kuliah',
                'sks' => 4,
                'semester' => 1,
                'mandatory' => 'W',
                'is_package' => true,
                'rate' => 1000000,
            ],
            [
                'id' => 7,
                'course_code' => 'MSH1B3',
                'course_name' => 'Logika Matematika A',
                'course_type' => 'Kuliah',
                'sks' => 3,
                'semester' => 1,
                'mandatory' => 'W',
                'is_package' => true,
                'rate' => 1000000,
            ],
            
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }

    public function registrationForm()
    {
        $data = [
            [
                'id' => 1,
                'period' => '2023/2024',
                'invoice_type' => 'Formulir',
                'track' => 'Jalur Mandiri',
                'wave' => 'Periode Juni',
                'rate' => 150000,
            ],
            [
                'id' => 2,
                'period' => '2023/2024',
                'invoice_type' => 'Formulir',
                'track' => 'Jalur Mandiri',
                'wave' => 'Periode Februari',
                'rate' => 150000,
            ],
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }

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

    public function registrantInvoice() {
        $data = [
            [
                'id' => 1,
                'period' => '2022/2023',
                'semester' => 'Semester Genap',
                'faculty' => 'Fakultas Informatika',
                'study_program' => 'S1 Informatika',
                'invoice_nominal' => 10000000,
                'penalty_nominal' => 21000,
                'discount_nominal' => 11000,
            ],
            [
                'id' => 2,
                'period' => '2022/2023',
                'semester' => 'Semester Genap',
                'faculty' => 'Fakultas Informatika',
                'study_program' => 'S1 Rekayasa Perangkat Lunak',
                'invoice_nominal' => 10000000,
                'penalty_nominal' => 21000,
                'discount_nominal' => 11000,
            ],
            [
                'id' => 3,
                'period' => '2022/2023',
                'semester' => 'Semester Genap',
                'faculty' => 'Fakultas Informatika',
                'study_program' => 'S1 Data Sains',
                'invoice_nominal' => 10000000,
                'penalty_nominal' => 21000,
                'discount_nominal' => 11000,
            ],
            
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }

    public function oldStudentInvoice() {
        $data = [
            [
                'period' => '2022/2023',
                'semester' => 'Semester Genap',
                'unit_name' => 'Fakultas Informatika',
                'is_child' => false,
                'invoice' => 3000000,
                'penalty' => 500000,
                'discount' => 200000,
                'total' => 3300000
            ],
            [
                'period' => '2022/2023',
                'semester' => 'Semester Genap',
                'unit_name' => 'S1 Informatika',
                'is_child' => true,
                'invoice' => 3000000,
                'penalty' => 500000,
                'discount' => 200000,
                'total' => 3300000
            ],
            [
                'period' => '2022/2023',
                'semester' => 'Semester Genap',
                'unit_name' => 'S1 Rekayasa Perangkat Lunak',
                'is_child' => true,
                'invoice' => 3000000,
                'penalty' => 500000,
                'discount' => 200000,
                'total' => 3300000
            ],
            [
                'period' => '2022/2023',
                'semester' => 'Semester Genap',
                'unit_name' => 'S1 Data Science',
                'is_child' => true,
                'invoice' => 3000000,
                'penalty' => 500000,
                'discount' => 200000,
                'total' => 3300000
            ],
            [
                'period' => '2022/2023',
                'semester' => 'Semester Genap',
                'unit_name' => 'Fakultas Ekonomi Bisnis',
                'is_child' => false,
                'invoice' => 3000000,
                'penalty' => 500000,
                'discount' => 200000,
                'total' => 3300000
            ],
            [
                'period' => '2022/2023',
                'semester' => 'Semester Genap',
                'unit_name' => 'S1 Manajemen Bisis dan Teknologi',
                'is_child' => true,
                'invoice' => 3000000,
                'penalty' => 500000,
                'discount' => 200000,
                'total' => 3300000
            ],
            
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }

    public function newStudentInvoice() {
        $data = [
            [
                'period' => '2022/2023',
                'semester' => 'Semester Genap',
                'unit_name' => 'Fakultas Informatika',
                'is_child' => false,
                'invoice' => 3000000,
                'penalty' => 500000,
                'discount' => 200000,
                'total' => 3300000
            ],
            [
                'period' => '2022/2023',
                'semester' => 'Semester Genap',
                'unit_name' => 'S1 Informatika',
                'is_child' => true,
                'invoice' => 3000000,
                'penalty' => 500000,
                'discount' => 200000,
                'total' => 3300000
            ],
            [
                'period' => '2022/2023',
                'semester' => 'Semester Genap',
                'unit_name' => 'S1 Rekayasa Perangkat Lunak',
                'is_child' => true,
                'invoice' => 3000000,
                'penalty' => 500000,
                'discount' => 200000,
                'total' => 3300000
            ],
            [
                'period' => '2022/2023',
                'semester' => 'Semester Genap',
                'unit_name' => 'S1 Data Science',
                'is_child' => true,
                'invoice' => 3000000,
                'penalty' => 500000,
                'discount' => 200000,
                'total' => 3300000
            ],
            [
                'period' => '2022/2023',
                'semester' => 'Semester Genap',
                'unit_name' => 'Fakultas Ekonomi Bisnis',
                'is_child' => false,
                'invoice' => 3000000,
                'penalty' => 500000,
                'discount' => 200000,
                'total' => 3300000
            ],
            [
                'period' => '2022/2023',
                'semester' => 'Semester Genap',
                'unit_name' => 'S1 Manajemen Bisis dan Teknologi',
                'is_child' => true,
                'invoice' => 3000000,
                'penalty' => 500000,
                'discount' => 200000,
                'total' => 3300000
            ],
            
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }
    
    public function studentInvoiceDetail() {
        $data = [
            [
                'id' => 1,
                'student_id' => '1234124112',
                'student_name' => 'Ahmad Lubis Joko Tingkir',
                'invoice_detail' => [
                    ['name' => 'BPP', 'nominal' => 7500000],
                    ['name' => 'Praktikum', 'nominal' => 200000],
                    ['name' => 'SKS', 'nominal' => 200000],
                    ['name' => 'Seragam', 'nominal' => 100000],
                ],
                'invoice_total' => 8000000,
                'penalty_detail' => [
                    ['name' => 'Denda 1', 'nominal' => 100000],
                    ['name' => 'Denda 2', 'nominal' => 100000],
                    ['name' => 'Denda 3', 'nominal' => 100000],
                    ['name' => 'Denda 4', 'nominal' => 100000],
                ],
                'penalty_total' => 400000,
                'scholarship_detail' => [
                    ['name' => 'Djarum', 'nominal' => 3000000],
                    ['name' => 'Alumni', 'nominal' => 2500000],
                ],
                'scholarship_total' => 5500000,
                'discount_detail' => [
                    ['name' => 'Potongan 1', 'nominal' => 100000],
                    ['name' => 'Potongan 2', 'nominal' => 100000],
                ],
                'discount_total' => 200000,
                'student_status' => 'active'
            ],
            [
                'id' => 2,
                'student_id' => '1234124113',
                'student_name' => 'Cucut Mahyadi Jamaluddin',
                'invoice_detail' => [
                    ['name' => 'BPP', 'nominal' => 7500000],
                    ['name' => 'Praktikum', 'nominal' => 200000],
                    ['name' => 'SKS', 'nominal' => 200000],
                    ['name' => 'Seragam', 'nominal' => 100000],
                ],
                'invoice_total' => 8000000,
                'penalty_detail' => [
                    ['name' => 'Denda 1', 'nominal' => 100000],
                    ['name' => 'Denda 2', 'nominal' => 100000],
                    ['name' => 'Denda 3', 'nominal' => 100000],
                    ['name' => 'Denda 4', 'nominal' => 100000],
                ],
                'penalty_total' => 400000,
                'scholarship_detail' => [
                    ['name' => 'Djarum', 'nominal' => 3000000],
                    ['name' => 'Alumni', 'nominal' => 2500000],
                ],
                'scholarship_total' => 5500000,
                'discount_detail' => [
                    ['name' => 'Potongan 1', 'nominal' => 100000],
                    ['name' => 'Potongan 2', 'nominal' => 100000],
                ],
                'discount_total' => 200000,
                'student_status' => 'active'
            ],
            [
                'id' => 3,
                'student_id' => '1234124113',
                'student_name' => 'Mauskana Koamsika Laskus',
                'invoice_detail' => [
                    ['name' => 'BPP', 'nominal' => 7500000],
                    ['name' => 'Praktikum', 'nominal' => 200000],
                    ['name' => 'SKS', 'nominal' => 200000],
                    ['name' => 'Seragam', 'nominal' => 100000],
                ],
                'invoice_total' => 8000000,
                'penalty_detail' => [
                    ['name' => 'Denda 1', 'nominal' => 100000],
                    ['name' => 'Denda 2', 'nominal' => 100000],
                    ['name' => 'Denda 3', 'nominal' => 100000],
                    ['name' => 'Denda 4', 'nominal' => 100000],
                ],
                'penalty_total' => 400000,
                'scholarship_detail' => [
                    ['name' => 'Djarum', 'nominal' => 3000000],
                    ['name' => 'Alumni', 'nominal' => 2500000],
                ],
                'scholarship_total' => 5500000,
                'discount_detail' => [
                    ['name' => 'Potongan 1', 'nominal' => 100000],
                    ['name' => 'Potongan 2', 'nominal' => 100000],
                ],
                'discount_total' => 200000,
                'student_status' => 'active'
            ],
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }

    public function otherInvoice() {
        $data = [
            [
                'id' => 1,
                'unit_name' => 'Fakultas Informatika',
                'is_child' => false,
                'invoice_component' => 'Cuti',
                'invoice_total' => 1000000
            ],
            [
                'id' => 2,
                'unit_name' => 'S1 Informatika',
                'is_child' => true,
                'invoice_component' => 'Cuti',
                'invoice_total' => 1000000
            ],
            [
                'id' => 3,
                'unit_name' => 'S1 Rekayasa Perangkat Lunak',
                'is_child' => true,
                'invoice_component' => 'Cuti',
                'invoice_total' => 1000000
            ],
            [
                'id' => 4,
                'unit_name' => 'S1 Data Sains',
                'is_child' => true,
                'invoice_component' => 'Cuti',
                'invoice_total' => 1000000
            ],
            [
                'id' => 5,
                'unit_name' => 'Fakultas Ekonomi Bisnis',
                'is_child' => false,
                'invoice_component' => 'Cuti',
                'invoice_total' => 1000000
            ],
            [
                'id' => 6,
                'unit_name' => 'S1 Manajemen Bisnis dan Teknologi',
                'is_child' => true,
                'invoice_component' => 'Cuti',
                'invoice_total' => 1000000
            ],
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }

    public function otherInvoiceDetail() {
        $data = [
            [
                'id' => 1,
                'student_id' => '1234124112',
                'student_name' => 'Ahmad Lubis Joko Tingkir',
                'invoice_detail' => [
                    ['name' => 'Tagihan 1', 'nominal' => 1000000],
                    ['name' => 'Tagihan 2', 'nominal' => 200000],
                    ['name' => 'Tagihan 3', 'nominal' => 200000],
                ],
                'invoice_total' => 1400000,
                'student_status' => 'active'
            ],
            [
                'id' => 2,
                'student_id' => '1234124113',
                'student_name' => 'Cucut Mahyadi Jamaluddin',
                'invoice_detail' => [
                    ['name' => 'Tagihan 1', 'nominal' => 1000000],
                    ['name' => 'Tagihan 2', 'nominal' => 200000],
                    ['name' => 'Tagihan 3', 'nominal' => 200000],
                ],
                'invoice_total' => 1400000,
                'student_status' => 'active'
            ],
            [
                'id' => 3,
                'student_id' => '1234124113',
                'student_name' => 'Mauskana Koamsika Laskus',
                'invoice_detail' => [
                    ['name' => 'Tagihan 1', 'nominal' => 1000000],
                    ['name' => 'Tagihan 2', 'nominal' => 200000],
                    ['name' => 'Tagihan 3', 'nominal' => 200000],
                ],
                'invoice_total' => 1400000,
                'student_status' => 'active'
            ],
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }
}
