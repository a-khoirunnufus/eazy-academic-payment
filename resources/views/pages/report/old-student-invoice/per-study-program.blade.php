@extends('layouts.static_master')

@section('page_title', 'Laporan Pembayaran Tagihan Mahasiswa Lama')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('content')

@include('pages.report.old-student-invoice._shortcuts', ['active' => 'per-study-program'])

<div class="card">
    <div class="card-body">
        <x-datatable-filter-wrapper oneRow handler="javascript:void(0)">
            <x-datatable-select-filter
                title="Tahun Akademik dan Semester"
                elementId="filter-school-year"
                resourceName="school-year"
                value="code"
                labelTemplate=":year Semester :semester"
                :labelTemplateItems="array('year', 'semester')"
            />
        </x-datatable-filter-wrapper>
    </div>
</div>

<div class="card">
    <table id="old-student-invoice-table" class="table table-striped">
        <thead>
            <tr>
                <th rowspan="2">Tahun Akademik</th>
                <th rowspan="2">Program Studi / Fakultas</th>
                <th rowspan="2">Mahasiswa</th>
                <th colspan="4" class="text-center">Rincian</th>
                <th rowspan="2">
                    Total Harus Dibayar<br>
                    (A+B)-(C+D)
                </th>
                <th rowspan="2">Terbayar</th>
                <th rowspan="2">Piutang</th>
            </tr>
            <tr>
                <th>Tagihan(A)</th>
                <th>Denda(B)</th>
                <th>Beasiswa(C)</th>
                <th>Potongan(D)</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <th colspan="3">Total Keseluruhan</th>
                <th>Rp 100,000,000,00</th>
                <th>Rp 100,000,000,00</th>
                <th>Rp 100,000,000,00</th>
                <th>Rp 100,000,000,00</th>
                <th>Rp 100,000,000,00</th>
                <th>Rp 100,000,000,00</th>
                <th>Rp 100,000,000,00</th>
            </tr>
        </tfoot>
    </table>
</div>
@endsection

@section('js_section')
<script>
    $(document).ready(function () {
        select2Replace();
    });

    $(function(){
        _oldStudentInvoiceTable.init()
    })

    const _oldStudentInvoiceTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#old-student-invoice-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/dt/report-old-student-invoice-per-study-program',
                },
                columns: [
                    {
                        name: 'academic_year',
                        render: (data, _, row) => {
                            return this.template.titleWithSubtitleCell(row.school_year, row.semester);
                        }
                    },
                    {
                        name: 'study_program_name',
                        data: 'study_program_name',
                        render: (data) => {
                            return this.template.buttonLinkCell(data, {link: _baseURL+'/report/old-student-invoice?type=student'});
                        }
                    },
                    {
                        name: 'student',
                        render: (data, _, row) => {
                            const listHeader = [
                                {label: 'Lunas', value: row.paid_off_count},
                                {label: 'Belum Lunas', value: row.not_paid_off_count}
                            ];
                            const listItem = [
                                {label: 'Jumlah Mahasiswa', value: row.student_count}
                            ];
                            return this.template.listDetailCellV2(listItem, listHeader);
                        }
                    },
                    {
                        name: 'invoice_a',
                        data: 'invoice_a',
                        render: (data) => {
                            return this.template.currencyCell(data);
                        }
                    },
                    {
                        name: 'invoice_b',
                        data: 'invoice_b',
                        render: (data) => {
                            return this.template.currencyCell(data);
                        }
                    },
                    {
                        name: 'invoice_c',
                        data: 'invoice_c',
                        render: (data) => {
                            return this.template.currencyCell(data);
                        }
                    },
                    {
                        name: 'invoice_d',
                        data: 'invoice_d',
                        render: (data) => {
                            return this.template.currencyCell(data);
                        }
                    },
                    {
                        name: 'invoice_total',
                        data: 'invoice_total',
                        render: (data) => {
                            return this.template.currencyCell(data, {bold: true});
                        }
                    },
                    {
                        name: 'paid_off_total',
                        data: 'paid_off_total',
                        render: (data) => {
                            return this.template.currencyCell(data, {bold: true});
                        }
                    },
                    {
                        name: 'receivables_total',
                        data: 'receivables_total',
                        render: (data) => {
                            return this.template.currencyCell(data, {bold: true});
                        }
                    }
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-center header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"old-student-invoice-actions">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>' +
                    '<"eazy-table-wrapper" t>' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function() {
                    $('.old-student-invoice-actions').html(`
                        <h5 class="mb-0">Daftar Tagihan</h5>
                    `)
                    feather.replace();
                }
            })
        },
        template: _datatableTemplates,
    }
</script>
@endsection
