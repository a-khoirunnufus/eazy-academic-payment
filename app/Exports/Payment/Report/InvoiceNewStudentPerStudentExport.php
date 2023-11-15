<?php

namespace App\Exports\Payment\Report;

use Maatwebsite\Excel\Concerns\FromQuery;
use Illuminate\Support\Facades\DB;
use App\Models\Payment\Settings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Support\Responsable;

class InvoiceNewStudentPerStudentExport implements FromQuery, WithHeadings, Responsable
{
    use Exportable;

    private $table_source;
    private $filters;

    public function __construct($options)
    {
        $this->fileName = $options['file_name'];
        $this->writerType = $options['writer_type'];
        $this->headers = $options['headers'];
        $this->filters = $options['filters'];

        if ($options['source'] == 'finance') {
            $this->table_source = 'finance.vw_invoice_new_student_finance_master';
        }
        elseif ($options['source'] == 'admission') {
            $this->table_source = 'finance.vw_invoice_new_student_admission_master';
        }
    }

    public function query()
    {
        $query = DB::table($this->table_source)
            ->select([
                'registration_year_name',
                'registration_period_name',
                'registration_path_name',
                DB::raw("registration_major_type || ' ' || registration_major_name || ' ' || registration_major_lecture_type_name"),
                'registration_faculty_name',
                'invoice_id',
                'registrant_number',
                'registrant_fullname',
                'invoice_component_total_amount',
                'invoice_penalty_total_amount',
                'invoice_scholarship_total_amount',
                'invoice_discount_total_amount',
                'payment_admin_cost',
                'invoice_nominal_total',
                'payment_total_paid',
                'payment_total_unpaid',
                'payment_status',
            ])
            ->orderBy('invoice_id');

        foreach ($this->filters as $filter) {
            $column = $filter['column'];
            $operator = $filter['operator'];
            $value = $filter['value'];
            $query->where($column, $operator, $value);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Tahun Akademik Pendaftaran',
            'Periode Pendaftaran',
            'Jalur Pendaftaran',
            'Program Studi',
            'Fakultas',
            'Nomor Tagihan',
            'Nomor Pendaftar',
            'Nama Mahasiswa',
            'Nominal Total Komponen',
            'Nominal Total Denda',
            'Nominal Total Beasiswa',
            'Nominal Total Potongan',
            'Biaya Admin',
            'Nominal Final Tagihan',
            'Total Terbayar',
            'Total Belum Terbayar',
            'Status Pembayaran',
        ];
    }
}