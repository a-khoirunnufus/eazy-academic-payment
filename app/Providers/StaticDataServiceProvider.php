<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class StaticDataServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $school_years = ['2021/2022', '2022/2023', '2023/2024', '2024/2025'];
        $faculties = ['Fakultas Informatika', 'Fakultas Elektro', 'Fakultas Rekayasa Industri', 'Fakultas Ekonomi dan Bisnis', 'Fakultas Ilmu Terapan', 'Fakultas Komunikasi dan Bisnis', 'Fakultas Industri Kreatif'];
        $study_programs = ['S1 Informatika', 'S1 Teknologi Infomasi', 'S1 Rekayasa Perangkat Lunak', 'S1 Sains Data', 'S1 PJJ Informatika'];
        $registration_periods = ['Periode Februari', 'Periode Juni'];
        $registration_paths = ['Jalur Mandiri', 'Jalur Rapor', 'Jalur USM', 'Jalur Test-Onsite'];
        $study_systems = ['Onsite', 'Online'];
        $invoice_components = ['Biaya Perkuliahan', 'Cuti', 'Denda', 'Daftar Ulang', 'Formulir'];
        $installments = ['Full 100% Pembayaran', '3 Kali Cicilan Pembayaran', '6 Kali Cicilan Pembayaran'];
        $courses = [
            ['code' => 'CSH1A2', 'name' => 'Pembentukan Karakter'],
            ['code' => 'LUH1A2', 'name' => 'Bahasa Indonesia'], 
            ['code' => 'MUH1B3', 'name' => 'Kalkulus IB'], 
            ['code' => 'DUH1A2', 'name' => 'Literasi TIK'], 
            ['code' => 'CSH1F2', 'name' => 'Pengantar Teknik Informatika'], 
            ['code' => 'CCH1A4', 'name' => 'Dasar Algoritma dan Pemrograman'], 
            ['code' => 'MSH1B3', 'name' => 'Logika Matematika A'],
        ];
        $semesters = ['Semester Ganjil', 'Semester Genap'];
        $study_levels = ['D3', 'S1', 'S2'];
        $invoice_types = ['Tagihan', 'Denda', 'Potongan'];

        View::share('static_school_years', $school_years);
        View::share('static_faculties', $faculties);
        View::share('static_study_programs', $study_programs);
        View::share('static_registration_periods', $registration_periods);
        View::share('static_registration_paths', $registration_paths);
        View::share('static_study_systems', $study_systems);
        View::share('static_invoice_components', $invoice_components);
        View::share('static_installments', $installments);
        View::share('static_courses', $courses);
        View::share('static_semesters', $semesters);
        View::share('static_study_levels', $study_levels);
        View::share('static_invoice_types', $invoice_types);
    }
}
