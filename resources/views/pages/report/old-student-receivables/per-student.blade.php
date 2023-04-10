@extends('layouts.static_master')

@section('page_title', 'Laporan Piutang Mahasiswa Lama')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
    <style>
        .eazy-summary {
            display: flex;
            flex-direction: row;
            gap: 2rem;
            justify-content: space-between;
        }
        .eazy-summary__item {
            display: flex;
            flex-direction: row;
            align-items: center;
        }
        .eazy-summary__item .item__icon {
            color: blue;
            background-color: lightblue;
            border-radius: 50%;
            height: 56px;
            width: 56px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 1rem;
        }
        .eazy-summary__item .item__icon.item__icon--blue{
            color: #356CFF;
            background-color: #F0F4FF;
        }
        .eazy-summary__item .item__icon.item__icon--green{
            color: #0BA44C;
            background-color: #E1FFE0;
        }
        .eazy-summary__item .item__icon.item__icon--red{
            color: #FF4949;
            background-color: #FFF5F5;
        }
        .eazy-summary__item .item__icon svg {
            height: 30px;
            width: 30px;
        }
        .eazy-summary__item .item__text span:first-child {
            display: block;
            font-size: 1rem;
        }
        .eazy-summary__item .item__text span:last-child {
            display: block;
            font-size: 18px;
            font-weight: 700;
        }
    </style>
@endsection

@section('content')

@include('pages.report.old-student-receivables._shortcuts', ['active' => 'per-student'])

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
            <x-datatable-select-filter 
                title="Angkatan"
                elementId="filter-class-year"
                resourceName="class-year"
                value="code"
                labelTemplate=":name"
                :labelTemplateItems="array('name')"
            />
            <x-datatable-select-filter 
                title="Fakultas"
                elementId="filter-faculty"
                resourceName="faculty"
                value="id"
                labelTemplate=":name"
                :labelTemplateItems="array('name')"
            />
            <x-datatable-select-filter 
                title="Program Studi"
                elementId="filter-study-program"
                resourceName="study-program"
                value="id"
                labelTemplate=":type :name"
                :labelTemplateItems="array('type', 'name')"
            />
        </x-datatable-filter-wrapper>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div id="receivables-summary" class="eazy-summary">
            <div class="eazy-summary__item">
                <div class="item__icon item__icon--blue">
                    <i data-feather="activity"></i>
                </div>
                <div class="item__text">
                    <span>Jumlah Piutang Keseluruhan</span>
                    <span>Rp 100,000,000,00</span>
                </div>
            </div>
            <div class="eazy-summary__item">
                <div class="item__icon item__icon--green">
                    <i data-feather="credit-card"></i>
                </div>
                <div class="item__text">
                    <span>Jumlah Piutang Terbayar</span>
                    <span>Rp 50,000,000,00</span>
                </div>
            </div>
            <div class="eazy-summary__item">
                <div class="item__icon item__icon--red">
                    <i data-feather="percent"></i>
                </div>
                <div class="item__text">
                    <span>Total Sisa Tagihan Keseluruhan</span>
                    <span>Rp 50,000,000,00</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="old-student-receivables-table" class="table table-striped">
        <thead>
            <tr>
                <th rowspan="2">Program Studi / Fakultas</th>
                <th rowspan="2">Nama / NIM</th>
                <th rowspan="2">Rincian Tagihan</th>
                <th colspan="4" class="text-center">Jenis Tagihan</th>
                <th rowspan="2">
                    Total Harus Dibayar<br>
                    (A+B)-(C+D)
                </th>
                <th rowspan="2">Total Terbayar</th>
                <th rowspan="2">Sisa Tagihan</th>
                <th rowspan="2">Status</th>
            </tr>
            <tr>
                <th>Tagihan A</th>
                <th>Denda B</th>
                <th>Beasiswa C</th>
                <th>Potongan D</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection

@section('js_section')
<script>
    $(document).ready(function () {
        select2Replace();
    });

    $(function(){
        _oldStudentReceivablesTable.init();
    })

    const _oldStudentReceivablesTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#old-student-receivables-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/dt/report-old-student-receivables-per-student',
                },
                columns: [
                    {
                        name: 'study_program_n_faculty', 
                        render: (data, _, row) => {
                            return this.template.titleWithSubtitleCell(row.study_program, row.faculty);
                        }
                    },
                    {
                        name: 'student_name_n_id', 
                        render: (data, _, row) => {
                            return this.template.titleWithSubtitleCell(row.student_name, row.student_id);
                        }
                    },
                    {
                        name: 'invoice', 
                        render: (data, _, row) => {
                            return this.template.invoiceDetailCell(row.invoice_detail);
                        }    
                    },
                    {
                        name: 'invoice_a', 
                        render: (data, _, row) => {
                            return this.template.invoiceDetailCell(row.invoice_a_detail, row.invoice_a_total);
                        }    
                    },
                    {
                        name: 'invoice_b', 
                        render: (data, _, row) => {
                            return this.template.invoiceDetailCell(row.invoice_b_detail, row.invoice_b_total);
                        }    
                    },
                    {
                        name: 'invoice_c', 
                        render: (data, _, row) => {
                            return this.template.invoiceDetailCell(row.invoice_c_detail, row.invoice_c_total);
                        }    
                    },
                    {
                        name: 'invoice_d', 
                        render: (data, _, row) => {
                            return this.template.invoiceDetailCell(row.invoice_d_detail, row.invoice_d_total);
                        }    
                    },
                    {
                        name: 'total_must_be_paid',
                        data: 'total_must_be_paid',
                        render: (data) => {
                            return this.template.currencyCell(data, {bold: true, additionalClass: 'text-danger'});
                        }
                    },
                    {
                        name: 'paid_off_total',
                        data: 'paid_off_total',
                        render: (data) => {
                            return this.template.currencyCell(data, {bold: true, additionalClass: 'text-success'});
                        }
                    },
                    {
                        name: 'receivables_total',
                        data: 'receivables_total',
                        render: (data) => {
                            return this.template.currencyCell(data, {bold: true, minus: true, additionalClass: 'text-warning'});
                        }
                    },
                    {
                        name: 'status',
                        data: 'status',
                        render: (data) => {
                            return this.template.badgeCell(data, 'primary');
                        }
                    },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-center header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"old-student-receivables-actions">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>' +
                    '<"eazy-table-wrapper" t>' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function() {
                    $('.old-student-receivables-actions').html(`
                        <h5 class="mb-0">Daftar Piutang Mahasiswa Lama</h5>
                    `)
                    feather.replace();
                }
            })
        },
        template: _datatableTemplates,
    }
</script>
@endsection
