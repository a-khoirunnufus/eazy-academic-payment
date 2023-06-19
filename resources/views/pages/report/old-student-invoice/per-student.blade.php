@extends('layouts.static_master')

@section('page_title', 'Laporan Pembayaran Tagihan Mahasiswa Lama')
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

    .toHistory:hover,
    .toHistory:hover small {
        cursor: pointer;
        color: #5399f5 !important;
    }

    .select-filtering {
        min-width: 150px !important;
    }
</style>
@endsection

@section('content')

@include('pages.report.old-student-invoice._shortcuts', ['active' => 'per-student'])

<div class="card">
    <div class="nav-tabs-shadow nav-align-top">
        <ul class="nav nav-tabs custom border-bottom" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-invoice-detail">Detail Tagihan Mahasiswa Lama</button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-payment-history" disabled>Riwayat Pembayaran</button>
            </li>
        </ul>
        <div class="tab-content">

            <!-- OLD STUDENT INVOICE DETAIL -->
            <div class="tab-pane fade show active" id="navs-invoice-detail" role="tabpanel">
                <div class="px-1 py-2 border-bottom">
                    <div class="d-flex">
                        <div class="select-filtering">
                            <label class="form-label">Angkatan</label>
                            <select class="form-select select2 select-filter" id="filterData">
                                <option value="#ALL">Semua Tahun</option>
                                @foreach($angkatan as $item)
                                <option value="{{ $item->tahun }}">{{ $item->tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="m-1 mb-0 align-self-end">
                            <button class="btn btn-primary" onclick="filter()">
                                <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                            </button>
                        </div>
                    </div>
                </div>
                <table id="old-student-invoice-detail-table" class="table table-striped">
                    <thead>
                        <tr>
                            <th rowspan="2">Program Studi / Fakultas</th>
                            <th rowspan="2">Nama / NIM</th>
                            <th rowspan="2">Total / Rincian Tagihan</th>
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
                            <th>Tagihan(A)</th>
                            <th>Denda(B)</th>
                            <th>Beasiswa(C)</th>
                            <th>Potongan(D)</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <!-- OLD STUDENT PAYMENT HISTORY -->
            <div class="tab-pane fade" id="navs-payment-history" role="tabpanel">
                <table id="old-student-payment-history-table" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nomor Tagihan</th>
                            <th>Biaya Admin</th>
                            <th>Jumlah</th>
                            <th>Batas Pembayaran</th>
                            <th>Tanggal Pembayaran</th>
                            <th>status</th>
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
    var dtDetail, dtHistory, student = null;
    $(document).ready(function() {
        select2Replace();
    });

    $(function() {
        _oldStudentInvoiceDetailTable.init();
        // _oldStudentPaymentHistoryTable.init()
    })

    const _oldStudentInvoiceDetailTable = {
        ..._datatable,
        init: function(byFilter = '#ALL', searchData = '#ALL') {
            dtDetail = this.instance = $('#old-student-invoice-detail-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL + '/api/report/old-student-invoice',
                    data: {
                        prodi_filter_angkatan: byFilter,
                        prodi_search_filter: searchData,
                        prodi: '{{$programStudy}}'
                    },
                },
                columns: [{
                        name: 'study_program_n_faculty',
                        render: (data, _, row) => {
                            return this.template.titleWithSubtitleCell(row.studyprogram.studyprogram_name, row.studyprogram.faculty[0].faculty_name);
                        }
                    },
                    {
                        name: 'student_name_n_id',
                        render: (data, _, row) => {
                            var elm = `<div class="toHistory" onclick="toHistory('${row.student_number}')">`;
                            elm += this.template.titleWithSubtitleCell(row.fullname, row.student_id);
                            elm += `</div>`;
                            return elm;
                        }
                    },
                    {
                        name: 'invoice',
                        render: (data, _, row) => {
                            var listData = []
                            var payment = row.payment.payment_detail;
                            for (var i = 0; i < payment.length; i++) {
                                listData.push({
                                    name: payment[i].prrd_component,
                                    nominal: payment[i].prrd_amount
                                });
                            }
                            return this.template.invoiceDetailCell(listData, row.payment.prr_amount);
                        }
                    },
                    {
                        name: 'invoice_a',
                        render: (data, _, row) => {
                            var listData = []
                            var payment = row.payment.payment_detail;
                            for (var i = 0; i < payment.length; i++) {
                                listData.push({
                                    name: payment[i].prrd_component,
                                    nominal: payment[i].prrd_amount
                                });
                            }
                            return this.template.invoiceDetailCell(listData, row.payment.prr_amount);
                        }
                    },
                    {
                        name: 'invoice_b',
                        render: (data, _, row) => {
                            var listData = [];
                            return this.template.invoiceDetailCell(listData, '0');
                        }
                    },
                    {
                        name: 'invoice_c',
                        render: (data, _, row) => {
                            var listData = [];
                            return this.template.invoiceDetailCell(listData, '0');
                        }
                    },
                    {
                        name: 'invoice_d',
                        render: (data, _, row) => {
                            var listData = [];
                            return this.template.invoiceDetailCell(listData, '0');
                        }
                    },
                    {
                        name: 'total_must_be_paid',
                        data: 'payment.prr_total',
                        render: (data) => {
                            return this.template.currencyCell(data, {
                                bold: true,
                                additionalClass: 'text-danger'
                            });
                        }
                    },
                    {
                        name: 'paid_off_total',
                        data: 'payment.prr_paid',
                        render: (data) => {
                            return this.template.currencyCell(data, {
                                bold: true,
                                additionalClass: 'text-success'
                            });
                        }
                    },
                    {
                        name: 'receivables_total',
                        render: (data, _, row) => {
                            var total = row.payment.prr_total - row.payment.prr_paid;
                            return this.template.currencyCell(total, {
                                bold: true,
                                minus: true,
                                additionalClass: 'text-warning'
                            });
                        }
                    },
                    {
                        name: 'status',
                        render: (data, _, row) => {
                            var total = row.payment.prr_total - row.payment.prr_paid;
                            var status = total > 0 ? "Belum Lunas" : "Lunas"
                            return this.template.badgeCell(status, 'primary');
                        }
                    },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom: '<"d-flex justify-content-between align-items-center header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"old-student-invoice-detail-actions">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" <".search_filter">lB> >' +
                    '>' +
                    '<"eazy-table-wrapper" t>' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function() {
                    $('.old-student-invoice-detail-actions').html(`
                        <h5 class="mb-0">Daftar Tagihan</h5>
                    `)
                    $('.search_filter').html(`
                    <div class="dataTables_filter">
                        <label><input type="text" id="searchFilterDetail" class="form-control" placeholder="Cari Data" onkeydown="searchDataDetail(event)"></label>
                    </div>
                    `)
                    feather.replace();
                }
            })
        },
        template: _datatableTemplates,
    }

    const _oldStudentPaymentHistoryTable = {
        ..._datatable,
        init: function(student_number, search = '#ALL') {
            dtHistory = this.instance = $('#old-student-payment-history-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL + '/api/report/old-student-invoice/student-history/' + student_number,
                    data: {
                        search_filter: search
                    }
                },
                columns: [{
                        name: 'payment_date',
                        data: 'prrb_invoice_num',
                        render: (data) => {
                            return this.template.defaultCell(data, {
                                bold: true
                            });
                        }
                    },
                    {
                        name: 'invoice_component',
                        data: 'prrb_admin_cost',
                        render: (data) => {
                            return this.template.currencyCell(data, {
                                bold: true
                            });
                        }
                    },
                    {
                        name: 'payment_nominal',
                        data: 'prrb_amount',
                        render: (data) => {
                            return this.template.currencyCell(data, {
                                bold: true
                            });
                        }
                    },
                    {
                        name: 'payment_expired_date',
                        data: 'prrb_expired_date',
                        render: (data) => {
                            return this.template.defaultCell(data, {
                                bold: true
                            });
                        }
                    },
                    {
                        name: 'payment_paid_date',
                        data: 'prrb_paid_date',
                        render: (data) => {
                            return this.template.defaultCell(data, {
                                bold: true
                            });
                        }
                    },
                    {
                        name: 'payment_status',
                        data: 'prrb_status',
                        render: (data) => {
                            return this.template.defaultCell(data, {
                                bold: true
                            });
                        }
                    }
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom: '<"d-flex justify-content-between align-items-center header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"old-student-payment-history-actions">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" <".search_filter_history">lB> >' +
                    '>' +
                    '<"eazy-table-wrapper" t>' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function() {
                    $('.old-student-payment-history-actions').html(`
                        <h5 class="mb-0">Daftar Riwayat Pembayaran</h5>
                    `)
                    $('.search_filter_history').html(`
                    <div class="dataTables_filter">
                        <label><input type="text" id="searchFilterHistory" class="form-control" placeholder="Cari Data" onkeydown="searchDataHistory(event)"></label>
                    </div>
                    `)
                    feather.replace();
                }
            })
        },
        template: _datatableTemplates,
    }

    function toHistory(student_number) {
        student = student_number;
        if (dtHistory == null) {
            _oldStudentPaymentHistoryTable.init(student_number);
        } else {
            dtHistory.clear().destroy()
            _oldStudentPaymentHistoryTable.init(student_number);
        }
        $('.nav-tabs button[data-bs-target="#navs-payment-history"]').tab('show');
    }

    function filter(){
        dtDetail.clear().destroy()
        _oldStudentInvoiceDetailTable.init($('select[id="filterData"]').val())
    }

    function searchDataDetail(event){
        if(event.key == 'Enter'){
            var find = $('#searchFilterDetail').val();
            $('#searchFilterDetail').val('');
            dtDetail.clear().destroy();
            _oldStudentInvoiceDetailTable.init($('select[id="filterData"]').val(), find);
        }
    }

    function searchDataHistory(event){
        if(event.key == 'Enter'){
            var find = $('#searchFilterHistory').val();
            $('#searchFilterHistory').val('');
            dtHistory.clear().destroy();
            _oldStudentPaymentHistoryTable.init(student, find);
        }
    }
</script>
@endsection