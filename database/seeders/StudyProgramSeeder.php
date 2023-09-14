<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Payment\Studyprogram;

class StudyProgramSeeder extends Seeder
{
    public function run(): void
    {
        $studyprograms = [
            'Teknik Pertanian',
            'Teknologi Industri Pertanian',
            'Teknologi Pangan dan Hasil Pertanian',
            'Doktor Penyuluhan dan Komunikasi Pembangunan',
            'Doktor Perekonomian Islam dan Industri Halal',
            'Pembangunan Ekonomi Kewilayahan',
            'Teknik Pengelolaan dan Perawatan Alat Berat',
            'Biologi',
            'Doktor Ilmu Kedokteran dan Kesehatan',
            'Ilmu Kesehatan Anak',
            'Statistika',
            'Ekonomi Pertanian dan Agribisnis',
            'Teknik Geodesi',
            'Doktor Ilmu-ilmu Humaniora',
            'Magister Akuntansi',
            'Farmasi',
            'Magister Farmasi Klinik',
            'Teknik Industri',
            'Magister Ilmu dan Teknologi Pangan',
            'Magister Penyuluhan dan Komunikasi Pembangunan',
            'Magister Kajian Budaya dan Media',
            'Doktor Manajemen dan Kebijakan Publik',
            'Higiene Gigi',
            'Magister Ilmu Pendidikan Kedokteran dan Kesehatan',
            'Teknologi Rekayasa Elektro'
        ];

        foreach($studyprograms as $studyprogram){
            Studyprogram::updateOrCreate(
                ['name' => $studyprogram],
                ['updated_at' => date('Y-m-d H:i:s')]
            );
        }
    }
}
