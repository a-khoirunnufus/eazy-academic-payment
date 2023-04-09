<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TempResourceController extends Controller
{
    public function schoolYear()
    {
        $data = DB::table('masterdata.ms_school_year')
            ->select(
                'msy_id as id', 
                'msy_year as year', 
                'msy_semester as semester', 
                'msy_code as code',
                'msy_status as status'
            )
            ->orderBy('year', 'asc')
            ->orderBy('semester', 'asc')
            ->get();
        
        return response()->json($data, 200);
    }

    public function classYear()
    {
        $data = [
            ['id' => 1, 'name' => 'Angkatan 2018', 'code' => '2018'],
            ['id' => 2, 'name' => 'Angkatan 2019', 'code' => '2019'],
            ['id' => 3, 'name' => 'Angkatan 2020', 'code' => '2020'],
            ['id' => 4, 'name' => 'Angkatan 2021', 'code' => '2021'],
            ['id' => 5, 'name' => 'Angkatan 2022', 'code' => '2022'],
            ['id' => 6, 'name' => 'Angkatan 2023', 'code' => '2023'],
        ];
        
        return response()->json($data, 200);
    }

    public function faculty()
    {
        $data = DB::table('masterdata.ms_faculties')
            ->select(
                'faculty_id as id', 
                'faculty_name as name',
            )
            ->where('institution_id', 7)
            ->orderBy('name', 'asc')
            ->get();
        
        return response()->json($data, 200);
    }

    public function studyProgram()
    {
        $data = DB::table('masterdata.ms_studyprogram')
            ->select(
                'studyprogram_id as id', 
                'studyprogram_name as name',
                'studyprogram_type as type'
            )
            ->where('institution_id', 7)
            ->orderBy('type', 'asc')
            ->orderBy('name', 'asc')
            ->get();
        
        return response()->json($data, 200);
    }

    /**
     * DATATABLE SOURCE
     */

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

    public function reportOldStudentInvoicePerStudyProgram() {
        $data = [
            [
                'school_year' => '2022/2023',
                'semester' => 'Semester Genap',
                'study_program_name' => 'S1 Rekayasa Perangakat Lunak',
                'paid_off_count' => 100,
                'not_paid_off_count' => 100,
                'student_count' => 200,
                'invoice_a' => 1000000,
                'invoice_b' => 21000000,
                'invoice_c' => 11000000,
                'invoice_d' => 11000000,
                'invoice_total' => 100000000,
                'paid_off_total' => 100000000,
                'receivables_total' => 2000000,
            ],
            [
                'school_year' => '2022/2023',
                'semester' => 'Semester Genap',
                'study_program_name' => 'S1 Data Sains',
                'paid_off_count' => 100,
                'not_paid_off_count' => 100,
                'student_count' => 200,
                'invoice_a' => 1000000,
                'invoice_b' => 21000000,
                'invoice_c' => 11000000,
                'invoice_d' => 11000000,
                'invoice_total' => 100000000,
                'paid_off_total' => 100000000,
                'receivables_total' => 2000000,
            ],
            [
                'school_year' => '2022/2023',
                'semester' => 'Semester Genap',
                'study_program_name' => 'S1 Teknologi Informasi',
                'paid_off_count' => 100,
                'not_paid_off_count' => 100,
                'student_count' => 200,
                'invoice_a' => 1000000,
                'invoice_b' => 21000000,
                'invoice_c' => 11000000,
                'invoice_d' => 11000000,
                'invoice_total' => 100000000,
                'paid_off_total' => 100000000,
                'receivables_total' => 2000000,
            ],
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }

    public function reportOldStudentInvoicePerStudent() {
        $data = [
            [
                'faculty' => 'Fakultas Informatika',
                'study_program' => 'S1 Rekayasa Perangkat Lunak',
                'student_name' => 'Ahmad Lubis Joko Tingkir',
                'student_id' => '12324124112',
                'invoice_detail' => [
                    ['name' => 'BPP', 'nominal' => 7500000],
                    ['name' => 'Praktikum', 'nominal' => 200000],
                    ['name' => 'SKS', 'nominal' => 200000],
                    ['name' => 'Seragam', 'nominal' => 100000],
                ],
                'invoice_total' => 8000000,
                'invoice_a_detail' => [
                    ['name' => 'Rincian Lainnya', 'nominal' => 0],
                    ['name' => 'Rincian Lainnya', 'nominal' => 0],
                ],
                'invoice_a_total' => 8000000,
                'invoice_b_detail' => [
                    ['name' => 'Denda 1', 'nominal' => 2000000],
                    ['name' => 'Denda 2', 'nominal' => 2000000],
                ],
                'invoice_b_total' => 4000000,
                'invoice_c_detail' => [
                    ['name' => 'Djarum', 'nominal' => 2000000],
                    ['name' => 'Alumni', 'nominal' => 2000000],
                ],
                'invoice_c_total' => 4000000,
                'invoice_d_detail' => [
                    ['name' => 'Rincian 1', 'nominal' => 2000000],
                    ['name' => 'Rincian 2', 'nominal' => 2000000],
                ],
                'invoice_d_total' => 4000000,
                'total_must_be_paid' => 100000000,
                'paid_off_total' => 100000000,
                'receivables_total' => 2000000,
                'status' => 'Kredit',
            ],
            [
                'faculty' => 'Fakultas Informatika',
                'study_program' => 'S1 Informatika',
                'student_name' => 'Cucut Mahyadi Jamaluddin',
                'student_id' => '12324124113',
                'invoice_detail' => [
                    ['name' => 'BPP', 'nominal' => 7500000],
                    ['name' => 'Praktikum', 'nominal' => 200000],
                    ['name' => 'SKS', 'nominal' => 200000],
                    ['name' => 'Seragam', 'nominal' => 100000],
                ],
                'invoice_total' => 8000000,
                'invoice_a_detail' => [
                    ['name' => 'Rincian Lainnya', 'nominal' => 0],
                    ['name' => 'Rincian Lainnya', 'nominal' => 0],
                ],
                'invoice_a_total' => 8000000,
                'invoice_b_detail' => [
                    ['name' => 'Denda 1', 'nominal' => 2000000],
                    ['name' => 'Denda 2', 'nominal' => 2000000],
                ],
                'invoice_b_total' => 4000000,
                'invoice_c_detail' => [
                    ['name' => 'Djarum', 'nominal' => 2000000],
                    ['name' => 'Alumni', 'nominal' => 2000000],
                ],
                'invoice_c_total' => 4000000,
                'invoice_d_detail' => [
                    ['name' => 'Rincian 1', 'nominal' => 2000000],
                    ['name' => 'Rincian 2', 'nominal' => 2000000],
                ],
                'invoice_d_total' => 4000000,
                'total_must_be_paid' => 100000000,
                'paid_off_total' => 100000000,
                'receivables_total' => 2000000,
                'status' => 'Kredit',
            ],
            [
                'faculty' => 'Fakultas Informatika',
                'study_program' => 'S1 Data Sains',
                'student_name' => 'Mauskana Koamsika Laskus',
                'student_id' => '12324124114',
                'invoice_detail' => [
                    ['name' => 'BPP', 'nominal' => 7500000],
                    ['name' => 'Praktikum', 'nominal' => 200000],
                    ['name' => 'SKS', 'nominal' => 200000],
                    ['name' => 'Seragam', 'nominal' => 100000],
                ],
                'invoice_total' => 8000000,
                'invoice_a_detail' => [
                    ['name' => 'Rincian Lainnya', 'nominal' => 0],
                    ['name' => 'Rincian Lainnya', 'nominal' => 0],
                ],
                'invoice_a_total' => 8000000,
                'invoice_b_detail' => [
                    ['name' => 'Denda 1', 'nominal' => 2000000],
                    ['name' => 'Denda 2', 'nominal' => 2000000],
                ],
                'invoice_b_total' => 4000000,
                'invoice_c_detail' => [
                    ['name' => 'Djarum', 'nominal' => 2000000],
                    ['name' => 'Alumni', 'nominal' => 2000000],
                ],
                'invoice_c_total' => 4000000,
                'invoice_d_detail' => [
                    ['name' => 'Rincian 1', 'nominal' => 2000000],
                    ['name' => 'Rincian 2', 'nominal' => 2000000],
                ],
                'invoice_d_total' => 4000000,
                'total_must_be_paid' => 100000000,
                'paid_off_total' => 100000000,
                'receivables_total' => 2000000,
                'status' => 'Kredit',
            ],
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }

    public function reportOldStudentPaymentHistory() {
        $data = [
            [
                'payment_date' => '2022-03-20',
                'invoice_component' => 'BPP',
                'payment_nominal' => 10000000,
                'payment_method_name' => 'VA BNI',
                'payment_method_detail' => [
                    ['label' => 'Kode', 'value' => '002201923123'],
                    ['label' => 'Tanggal', 'value' => '01-02-2023 / 11:05:00'],
                ],
            ],
            [
                'payment_date' => '2022-03-21',
                'invoice_component' => 'BPP',
                'payment_nominal' => 10000000,
                'payment_method_name' => 'VA BNI',
                'payment_method_detail' => [
                    ['label' => 'Kode', 'value' => '002201923123'],
                    ['label' => 'Tanggal', 'value' => '01-02-2023 / 11:05:00'],
                ],
            ],
            [
                'payment_date' => '2022-03-22',
                'invoice_component' => 'BPP',
                'payment_nominal' => 10000000,
                'payment_method_name' => 'VA BNI',
                'payment_method_detail' => [
                    ['label' => 'Kode', 'value' => '002201923123'],
                    ['label' => 'Tanggal', 'value' => '01-02-2023 / 11:05:00'],
                ],
            ]
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }
}
