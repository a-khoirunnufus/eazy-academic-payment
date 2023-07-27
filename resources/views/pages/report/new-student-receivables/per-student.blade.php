@extends('layouts.static_master')

@section('page_title', 'Laporan Piutang Mahasiswa Baru')
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

    .eazy-summary__item .item__icon.item__icon--blue {
        color: #356CFF;
        background-color: #F0F4FF;
    }

    .eazy-summary__item .item__icon.item__icon--green {
        color: #0BA44C;
        background-color: #E1FFE0;
    }

    .eazy-summary__item .item__icon.item__icon--red {
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

    .toHistory:hover,
    .toHistory:hover small {
        cursor: pointer;
        color: #5399f5 !important;
    }

    .select-filtering {
        min-width: 150px !important;
    }

    .space {
        margin-left: 10px;
    }
</style>
@endsection

@section('content')

@include('pages.report.new-student-receivables._shortcuts', ['active' => 'per-student'])

<div class="card">
    <div class="card-body">
        <div class="d-flex">
            <div class="select-filtering">
                <label class="form-label">Angkatan</label>
                <select class="form-select select2 select-filter" id="filterData">
                    <option value="#ALL">Semua Tahun</option>
                    @foreach($angkatan as $item)
                    <option value="{{ $item->msy_id }}">{{ substr($item->msy_year, 0, 4).' Semester '.$item->msy_semester }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space select-filtering">
                <label class="form-label">Jalur Masuk</label>
                <select class="form-select select2 select-filter" id="pathData">
                    <option value="#ALL">Semua Jalur Masuk</option>
                    @foreach($jalur as $item)
                    <option value="{{ $item->path_id }}">{{ $item->path_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space select-filtering">
                <label class="form-label">Periode Masuk</label>
                <select class="form-select select2 select-filter" id="periodData">
                    <option value="#ALL">Semua Periode Masuk</option>
                    @foreach($periode as $item)
                    <option value="{{ $item->period_id }}">{{ $item->period_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space align-self-end">
                <button class="btn btn-primary" onclick="filter()">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
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
                    <span id="total_piutang">Rp 100,000,000,00</span>
                </div>
            </div>
            <div class="eazy-summary__item">
                <div class="item__icon item__icon--green">
                    <i data-feather="credit-card"></i>
                </div>
                <div class="item__text">
                    <span>Jumlah Piutang Terbayar</span>
                    <span id="piutang_terbayar">Rp 50,000,000,00</span>
                </div>
            </div>
            <div class="eazy-summary__item">
                <div class="item__icon item__icon--red">
                    <i data-feather="percent"></i>
                </div>
                <div class="item__text">
                    <span>Total Sisa Tagihan Keseluruhan</span>
                    <span id="sisa_piutang">Rp 50,000,000,00</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="new-student-invoice-detail-table" class="table table-striped">
        <thead>
            <tr>
                <th rowspan="2">Program Studi / Fakultas</th>
                <th rowspan="2">Nama / NIK</th>
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

<div class="modal" id="historPaymentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Riwayat Pembayaran</h5>
            </div>
            <div class="modal-body">
                <table id="new-student-payment-history-table" class="table table-striped">
                    <thead>
                        <th>Nomor Tagihan</th>
                        <th>Biaya Admin</th>
                        <th>Jumlah</th>
                        <th>Batas Pembayaran</th>
                        <th>Tanggal Pembayaran</th>
                        <th>status</th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js_section')
<script>
    var all_total_tagihan = 0;
    var all_total_denda = 0;
    var all_total_beasiswa = 0;
    var all_total_potongan = 0;
    var all_total_terbayar = 0;
    var all_total_harus_bayar = 0;
    var all_total_piutang = 0;

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
        init: function(byFilter = '#ALL', path = '#ALL', period = '#ALL', searchData = '#ALL') {
            all_total_tagihan = 0;
            all_total_denda = 0;
            all_total_beasiswa = 0;
            all_total_potongan = 0;
            all_total_terbayar = 0;
            all_total_harus_bayar = 0;
            all_total_piutang = 0;

            dtDetail = this.instance = $('#new-student-invoice-detail-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL + '/api/report/new-student-invoice',
                    data: {
                        prodi_filter_angkatan: byFilter,
                        prodi_search_filter: searchData,
                        prodi: '{{$programStudy}}',
                        prodi_path_filter: path,
                        prodi_period_filter: period,
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
                            var elm = `<div class="toHistory" onclick="toHistory('${row.par_id}')">`;
                            elm += this.template.titleWithSubtitleCell(row.par_fullname, row.par_nik);
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

                            all_total_harus_bayar += row.payment.prr_total;
                            all_total_terbayar += row.payment.prr_paid;
                            all_total_piutang += (all_total_harus_bayar - all_total_terbayar);
                            $('#total_piutang').html(this.template.currencyCell(all_total_harus_bayar));
                            $('#piutang_terbayar').html(this.template.currencyCell(all_total_terbayar));
                            $('#sisa_piutang').html(all_total_piutang);
                            
                            return this.template.invoiceDetailCell(listData, row.payment.prr_amount);
                        }
                    },
                    {
                        name: 'invoice_a',
                        render: (data, _, row) => {
                            var listData = []
                            var payment = row.payment.payment_detail;
                            var totalHarga = 0;
                            for (var i = 0; i < payment.length; i++) {
                                if (payment[i].type == 'component') {
                                    listData.push({
                                        name: payment[i].prrd_component,
                                        nominal: payment[i].prrd_amount
                                    });
                                    totalHarga += payment[i].prrd_amount;
                                }
                            }
                            return this.template.invoiceDetailCell(listData, '' + totalHarga);
                        }
                    },
                    {
                        name: 'invoice_b',
                        render: (data, _, row) => {
                            var listData = []
                            var payment = row.payment.payment_detail;
                            var totalHarga = 0;
                            for (var i = 0; i < payment.length; i++) {
                                if (payment[i].type == 'denda') {
                                    listData.push({
                                        name: payment[i].prrd_component,
                                        nominal: payment[i].prrd_amount
                                    });
                                    totalHarga += payment[i].prrd_amount;
                                }
                            }
                            return this.template.invoiceDetailCell(listData, '' + totalHarga);
                        }
                    },
                    {
                        name: 'invoice_c',
                        render: (data, _, row) => {
                            var listData = []
                            var payment = row.payment.payment_detail;
                            var totalHarga = 0;
                            for (var i = 0; i < payment.length; i++) {
                                if (payment[i].type == 'scholarship') {
                                    listData.push({
                                        name: payment[i].prrd_component,
                                        nominal: payment[i].prrd_amount
                                    });
                                    totalHarga += payment[i].prrd_amount;
                                }
                            }
                            return this.template.invoiceDetailCell(listData, '' + totalHarga);
                        }
                    },
                    {
                        name: 'invoice_d',
                        render: (data, _, row) => {
                            var listData = []
                            var payment = row.payment.payment_detail;
                            var totalHarga = 0;
                            for (var i = 0; i < payment.length; i++) {
                                if (payment[i].type == 'discount') {
                                    listData.push({
                                        name: payment[i].prrd_component,
                                        nominal: payment[i].prrd_amount
                                    });
                                    totalHarga += payment[i].prrd_amount;
                                }
                            }
                            return this.template.invoiceDetailCell(listData, '' + totalHarga);
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
                buttons: [{
                    text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file font-small-4 me-50"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>Excel</span>',
                    className: 'btn btn-outline-secondary',
                    action: function(e, dt, node, config) {
                        window.open(
                            _baseURL +
                            '/report/new-student-invoice/download-perstudent?' +
                            'prodi_filter_angkatan=' + encodeURIComponent(byFilter) + '&' +
                            'prodi_search_filter=' + encodeURIComponent(searchData) + '&' +
                            'prodi=' + encodeURIComponent('{{$programStudy}}') + '&' +
                            'prodi_path_filter=' + encodeURIComponent(path) + '&' +
                            'prodi_period_filter=' + encodeURIComponent(period) + '&' +
                            'student_export=' + encodeURIComponent('new')
                        )
                    }
                }],
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
            dtHistory = this.instance = $('#new-student-payment-history-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL + '/api/report/new-student-invoice/student-history/' + student_number,
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
        // $('.nav-tabs button[data-bs-target="#navs-payment-history"]').tab('show');
        $('#historPaymentModal').modal('toggle');
    }

    function filter() {
        var angkatan = $('select[id="filterData"]').val();
        var jalur = $('select[id="pathData"]').val();
        var periode = $('select[id="periodData"]').val();
        dtDetail.clear().destroy()
        _oldStudentInvoiceDetailTable.init(angkatan, jalur, periode)
    }

    function searchDataDetail(event) {
        if (event.key == 'Enter') {
            var angkatan = $('select[id="filterData"]').val();
            var jalur = $('select[id="pathData"]').val();
            var periode = $('select[id="periodData"]').val();
            var find = $('#searchFilterDetail').val();
            $('#searchFilterDetail').val('');
            dtDetail.clear().destroy();
            _oldStudentInvoiceDetailTable.init(angkatan, jalur, periode, find)
        }
    }

    function searchDataHistory(event) {
        if (event.key == 'Enter') {
            var find = $('#searchFilterHistory').val();
            $('#searchFilterHistory').val('');
            dtHistory.clear().destroy();
            _oldStudentPaymentHistoryTable.init(student, find);
        }
    }
</script>
@endsection