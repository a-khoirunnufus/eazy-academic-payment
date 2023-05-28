<?php

namespace App\Exports\Sheets;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class SettingFeeTemplateSheetExport implements FromView, WithTitle
{
    private $sheet_type;
    private $sheet_title;
    private $period;
    private $path;
    private $academic_year;
    private $studyprogram;
    private $lecture_type;
    private $encrypted_string;
    private $data;

    public function __construct(
        string $sheet_type,
        string $sheet_title,
        string $academic_year,
        string $period,
        string $path,
        string $studyprogram,
        string $lecture_type,
        array $guides,
        string $encrypted_string,
        array $data
    ) {
        $this->sheet_type = $sheet_type;
        $this->sheet_title = $sheet_title;
        $this->academic_year = $academic_year;
        $this->period = $period;
        $this->path = $path;
        $this->studyprogram = $studyprogram;
        $this->lecture_type = $lecture_type;
        $this->guides = $guides;
        $this->encrypted_string = $encrypted_string;
        $this->data = $data;
    }

    public function view(): View
    {
        $title = 'TABEL ';
        if ($this->sheet_type == 'component_fee') {
            $title .= 'KOMPONEN TAGIHAN';
        } elseif ($this->sheet_type == 'credit_schema') {
            $title .= 'SKEMA CICILAN';
        }

        return view('exports.component-fee', [
            'title' => $title,
            'academic_year' => $this->academic_year,
            'period' => $this->period,
            'path' => $this->path,
            'studyprogram' => $this->studyprogram,
            'lecture_type' => $this->lecture_type,
            'guides' => $this->guides,
            'encrypted_string' => $this->encrypted_string,
            'data' => $this->data
        ]);
    }

    // TODO: ADA BATAS PANJANG NAMA SHEET
    public function title(): string
    {
        return $this->sheet_title;
    }
}
