@extends('layouts.static_master')

@section('page_title', 'Laporan Pembayaran Tagihan Pendaftar')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
    <style>
        .nav-tabs.custom .nav-item {
            flex-grow: 1;
        }
        .nav-tabs.custom .nav-link {
            width: -webkit-fill-available !important;
            height: 50px !important;
        }
        .nav-tabs.custom .nav-link.active {
            background-color: #f2f2f2 !important;
        }
    </style>
@endsection

@section('content')
<div class="card">
    <div class="nav-tabs-shadow nav-align-top">
        <ul class="nav nav-tabs custom border-bottom" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-invoice-detail">Detail Tagihan Pendaftar</button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-payment-history">Riwayat Pembayaran</button>
            </li>
        </ul>
        <div class="tab-content">

            <!-- REGISTRANT INVOICE DETAIL -->
            <div class="tab-pane fade show active" id="navs-invoice-detail" role="tabpanel">
                <div class="px-1 py-2 border-bottom">
                    <x-datatable-filter-wrapper oneRow handler="foo()">
                        <x-datatable-select-filter 
                            title="Tahun Akademik dan Semester"
                            elementId="filter-school-year"
                            resourceName="school-year"
                            value="code"
                            labelTemplate=":year Semester :semester"
                            :labelTemplateItems="array('year', 'semester')"
                        />
                        <x-datatable-select-filter 
                            title="Jalur Pendaftaran"
                            elementId="filter-registration-path"
                            resourceName="registration-path"
                            value="id"
                            labelTemplate=":name"
                            :labelTemplateItems="array('name')"
                        />
                        <x-datatable-select-filter 
                            title="Periode Pendaftaran"
                            elementId="filter-registration-period"
                            resourceName="registration-period"
                            value="id"
                            labelTemplate=":name"
                            :labelTemplateItems="array('name')"
                        />
                    </x-datatable-filter-wrapper>
                </div>
                <table id="registrant-invoice-detail-table" class="table table-striped">
                    <thead>
                        <tr>
                            <th rowspan="2">Nama</th>
                            <th rowspan="2">Jalur / Periode</th>
                            <th rowspan="2">Metode Pembayaran</th>
                            <th colspan="2">Jenis Tagihan</th>
                            <th rowspan="2">
                                Total Harus Dibayar<br>
                                (A+B)-(C+D)
                            </th>
                            <th rowspan="2">Sisa Tagihan</th>
                            <th rowspan="2">Status</th>
                        </tr>
                        <tr>
                            <th>Tagihan A</th>
                            <th>Denda B</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <!-- REGISTRANT PAYMENT HISTORY -->
            <div class="tab-pane fade" id="navs-payment-history" role="tabpanel">
                <table id="registrant-payment-history-table" class="table table-striped" >
                    <thead>
                        <tr>
                            <th>Tanggal Pembayaran</th>
                            <th>Komponen Tagihan</th>
                            <th>Nominal Pembayaran</th>
                            <th>Metode Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js_section')
<script>
    function foo() {
        console.log('bar');
    }

    $(document).ready(function () {
        select2Replace();
    });

    $(function(){
        _oldStudentInvoiceDetailTable.init();
        _oldStudentPaymentHistoryTable.init()
    })

    const _oldStudentInvoiceDetailTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#registrant-invoice-detail-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/dt/report-registrant-invoice-per-student',
                },
                columns: [
                    {
                        name: 'student_name',
                        data: 'student_name', 
                        render: (data) => {
                            return this.template.defaultCell(data, {bold: true});
                        }
                    },
                    {
                        name: 'path_n_period',
                        render: (data, _, row) => {
                            return this.template.titleWithSubtitleCell(row.path, row.period);
                        }
                    },
                    {
                        name: 'payment', 
                        render: (data, _, row) => {
                            return this.template.listDetailCell(row.payment_method_detail, row.payment_method_name);
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
                        name: 'total_must_be_paid',
                        data: 'total_must_be_paid',
                        render: (data) => {
                            return this.template.currencyCell(data, {bold: true, additionalClass: 'text-danger'});
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
                            return this.template.badgeCell(data, 'success');
                        }
                    },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-center header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"registrant-invoice-detail-actions">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>' +
                    '<"eazy-table-wrapper" t>' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function() {
                    $('.registrant-invoice-detail-actions').html(`
                        <h5 class="mb-0">Daftar Tagihan</h5>
                    `)
                    feather.replace();
                }
            })
        },
        template: _datatableTemplates,
    }

    const _oldStudentPaymentHistoryTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#registrant-payment-history-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/dt/report-registrant-payment-history',
                },
                columns: [
                    {
                        name: 'payment_date', 
                        data: 'payment_date',
                        render: (data) => {
                            return this.template.dateCell(data);
                        }
                    },
                    {
                        name: 'invoice_component', 
                        data: 'invoice_component',
                        render: (data) => {
                            return this.template.defaultCell(data, {bold: true});
                        }
                    },
                    {
                        name: 'payment_nominal', 
                        data: 'payment_nominal',
                        render: (data) => {
                            return this.template.currencyCell(data, {bold: true});
                        }
                    },
                    {
                        name: 'payment', 
                        render: (data, _, row) => {
                            return this.template.listDetailCell(row.payment_method_detail, row.payment_method_name);
                        }    
                    },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-center header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"registrant-payment-history-actions">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>' +
                    '<"eazy-table-wrapper" t>' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function() {
                    $('.registrant-payment-history-actions').html(`
                        <h5 class="mb-0">Daftar Riwayat Pembayaran</h5>
                    `)
                    feather.replace();
                }
            })
        },
        template: _datatableTemplates,
    }
</script>
@endsection
