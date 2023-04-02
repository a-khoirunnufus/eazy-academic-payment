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
                'entry_period' => '2022',
                'wave' => 'Gelombang 1',
                'registration_path' => 'Mandiri',
                'study_system' => 'SKS',
                'study_program' => 'S1 Informatika',
                'invoice_component' => 'Biaya Perkuliahan',
                'rate' => 7500000,
                'instalment' => 'Full 100%',
            ],
            [
                'id' => 2,
                'entry_period' => '2022',
                'wave' => 'Gelombang 1',
                'registration_path' => 'Mandiri',
                'study_system' => 'SKS',
                'study_program' => 'S1 Sistem Informasi',
                'invoice_component' => 'Biaya Perkuliahan',
                'rate' => 7500000,
                'instalment' => 'Full 100%',
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
                'course_code' => 'BA081',
                'course_name' => 'Matakuliah 1',
                'course_type' => 'Kuliah',
                'sks' => 2,
                'semester' => 1,
                'mandatory' => 'W',
                'is_package' => false,
                'rate' => 1000000,
            ],
            [
                'id' => 2,
                'course_code' => 'BA082',
                'course_name' => 'Matakuliah 2',
                'course_type' => 'Kuliah',
                'sks' => 2,
                'semester' => 1,
                'mandatory' => 'P',
                'is_package' => false,
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
                'invoice_type' => 'Formulir',
                'track' => 'Prestasi',
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
                'rule' => 'CUTI - Mengambil Cuti',
                'invoice_component' => 'CUTI',
                'instalment' => 'FULL',
                'minimum_paid' => 10,
                'is_active' => true,
            ],
            
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }

    public function registrantInvoice() {
        $data = [
            [
                'id' => 1,
                'study_program' => 'Sastra Jepang',
                'invoice_types_number' => 3,
                'invoice_type_1_name' => 'Jenis Tagihan 1',
                'invoice_type_1_nominal' => 15000000,
                'invoice_type_2_name' => 'Jenis Tagihan 2',
                'invoice_type_2_nominal' => 20000000,
                'invoice_type_3_name' => 'Jenis Tagihan 3',
                'invoice_type_3_nominal' => 25000000,
            ],
            [
                'id' => 2,
                'study_program' => 'Sastra Arab',
                'invoice_types_number' => 3,
                'invoice_type_1_name' => 'Jenis Tagihan 1',
                'invoice_type_1_nominal' => 20000000,
                'invoice_type_2_name' => 'Jenis Tagihan 2',
                'invoice_type_2_nominal' => 20000000,
                'invoice_type_3_name' => 'Jenis Tagihan 3',
                'invoice_type_3_nominal' => 20000000,
            ],
            [
                'id' => 3,
                'study_program' => 'Sastra China',
                'invoice_types_number' => 3,
                'invoice_type_1_name' => 'Jenis Tagihan 1',
                'invoice_type_1_nominal' => 17000000,
                'invoice_type_2_name' => 'Jenis Tagihan 2',
                'invoice_type_2_nominal' => 17000000,
                'invoice_type_3_name' => 'Jenis Tagihan 3',
                'invoice_type_3_nominal' => 17000000,
            ],
            [
                'id' => 4,
                'study_program' => 'Teknik Elektro',
                'invoice_types_number' => 3,
                'invoice_type_1_name' => 'Jenis Tagihan 1',
                'invoice_type_1_nominal' => 12000000,
                'invoice_type_2_name' => 'Jenis Tagihan 2',
                'invoice_type_2_nominal' => 12000000,
                'invoice_type_3_name' => 'Jenis Tagihan 3',
                'invoice_type_3_nominal' => 12000000,
            ],
            [
                'id' => 5,
                'study_program' => 'Teknik Informatika',
                'invoice_types_number' => 3,
                'invoice_type_1_name' => 'Jenis Tagihan 1',
                'invoice_type_1_nominal' => 14000000,
                'invoice_type_2_name' => 'Jenis Tagihan 2',
                'invoice_type_2_nominal' => 14000000,
                'invoice_type_3_name' => 'Jenis Tagihan 3',
                'invoice_type_3_nominal' => 14000000,
            ],
            
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }

    public function oldStudentInvoice() {
        $data = [
            [
                'unit_name' => 'Universitas Jaya Kusuma',
                'invoice' => 3000000,
                'penalty' => 500000,
                'discount' => 200000,
                'total' => 3300000
            ],
            [
                'unit_name' => 'Fakultas Informatika',
                'invoice' => 2000000,
                'penalty' => 500000,
                'discount' => 200000,
                'total' => 2300000
            ],
            [
                'unit_name' => 'Teknik Informatika',
                'invoice' => 1000000,
                'penalty' => 500000,
                'discount' => 200000,
                'total' => 1300000
            ],
            
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }

    public function newStudentInvoice() {
        $data = [
            [
                'unit_name' => 'Universitas Jaya Kusuma',
                'invoice' => 3000000,
                'penalty' => 500000,
                'discount' => 200000,
                'total' => 3300000
            ],
            [
                'unit_name' => 'Fakultas Informatika',
                'invoice' => 2000000,
                'penalty' => 500000,
                'discount' => 200000,
                'total' => 2300000
            ],
            [
                'unit_name' => 'Teknik Informatika',
                'invoice' => 1000000,
                'penalty' => 500000,
                'discount' => 200000,
                'total' => 1300000
            ],
            
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }
    
    public function studentInvoiceDetail() {
        $data = [
            [
                'id' => 1,
                'student_id' => 123123123,
                'student_name' => 'Rian',
                'invoice_detail' => [
                    'BPP' => 500000,
                    'Praktikum' => 200000,
                    'SKS' => 200000,
                    'Seragam' => 100000,
                ],
                'invoice_total' => 1000000,
                'penalty_detail' => [
                    'Denda 1' => 100000,
                ],
                'penalty_total' => 100000,
                'scholarship_detail' => [
                    'Beasiswa Djarum' => 200000
                ],
                'scholarship_total' => 200000,
                'discount_detail' => [
                    'Potongan 1' => 100000,
                ],
                'discount_total' => 100000,
                'student_status' => 'Aktif'
            ],
            [
                'id' => 2,
                'student_id' => 123123124,
                'student_name' => 'Budi',
                'invoice_detail' => [
                    'BPP' => 500000,
                    'Praktikum' => 200000,
                    'SKS' => 200000,
                    'Seragam' => 100000,
                ],
                'invoice_total' => 1000000,
                'penalty_detail' => [
                    'Denda 1' => 100000,
                ],
                'penalty_total' => 100000,
                'scholarship_detail' => [
                    'Beasiswa Djarum' => 200000
                ],
                'scholarship_total' => 200000,
                'discount_detail' => [
                    'Potongan 1' => 100000,
                ],
                'discount_total' => 100000,
                'student_status' => 'Aktif'
            ],
            [
                'id' => 3,
                'student_id' => 123123125,
                'student_name' => 'Cici',
                'invoice_detail' => [
                    'BPP' => 500000,
                    'Praktikum' => 200000,
                    'SKS' => 200000,
                    'Seragam' => 100000,
                ],
                'invoice_total' => 1000000,
                'penalty_detail' => [
                    'Denda 1' => 100000,
                ],
                'penalty_total' => 100000,
                'scholarship_detail' => [
                    'Beasiswa Djarum' => 200000
                ],
                'scholarship_total' => 200000,
                'discount_detail' => [
                    'Potongan 1' => 100000,
                ],
                'discount_total' => 100000,
                'student_status' => 'Aktif'
            ],
            
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }

    public function otherInvoice() {
        $data = [
            [
                'unit_name' => 'Universitas Jaya Kusuma',
                'invoice_component' => 'Biaya Wisuda',
                'invoice_total' => 200000000
            ],
            [
                'unit_name' => 'Fakultas Informatika',
                'invoice_component' => 'Biaya Wisuda',
                'invoice_total' => 200000000
            ],
            [
                'unit_name' => 'Teknik Informatika',
                'invoice_component' => 'Biaya Wisuda',
                'invoice_total' => 200000000
            ],
            
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }

    public function otherInvoiceDetail() {
        $data = [
            [
                'id' => 1,
                'student_id' => 123123123,
                'student_name' => 'Rian',
                'invoice_detail' => [
                    'Biaya Wisuda' => 100000,
                ],
                'invoice_total' => 1000000,
                'student_status' => 'Aktif'
            ],
            [
                'id' => 2,
                'student_id' => 123123124,
                'student_name' => 'Budi',
                'invoice_detail' => [
                    'Biaya Wisuda' => 100000,
                ],
                'invoice_total' => 1000000,
                'student_status' => 'Aktif'
            ],
            [
                'id' => 3,
                'student_id' => 123123125,
                'student_name' => 'Cici',
                'invoice_detail' => [
                    'Biaya Wisuda' => 100000,
                ],
                'invoice_total' => 1000000,
                'student_status' => 'Aktif'
            ],
            
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }

    public function studentStudentInvoice() {
        $data = [
            [
                'id' => 1,
                'invoice_code' => 'INV/20192/2010210',
                'period' => '2022/2023 - Genap',
                'month' => 1,
                'n_installment' => 1,
                'invoice_nominal' => 750000,
                'payment_nominal' => 750000,
                'status' => 'LUNAS',
                'payment_method' => [
                    'Metode' => 'VA BNI',
                    'Kode VA' => '002949125',
                    'Tanggal' => '25/03/2023 11:03:03',
                ]
            ],
            [
                'id' => 2,
                'invoice_code' => 'INV/20192/2010211',
                'period' => '2022/2023 - Ganjil',
                'month' => 1,
                'n_installment' => 1,
                'invoice_nominal' => 750000,
                'payment_nominal' => 750000,
                'status' => 'LUNAS',
                'payment_method' => [
                    'method_name' => 'VA BNI',
                    'va_code' => '002949125',
                    'date' => '25/03/2023 11:03:03',
                ]
            ],
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }
}
