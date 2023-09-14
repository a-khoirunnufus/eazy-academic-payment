@extends('tpl.vuexy.master-payment')

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

    #forExport_wrapper {
        display: none !important;
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
            <!-- <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-payment-history">Riwayat Pembayaran</button>
            </li> -->
        </ul>
        <div class="tab-content">

            <!-- REGISTRANT INVOICE DETAIL -->
            <div class="tab-pane fade show active" id="navs-invoice-detail" role="tabpanel">
                <div class="px-1 py-2 border-bottom">
                    <div class="d-flex">
                        <div class="select-filtering">
                            <label class="form-label">Angkatan</label>
                            <select class="form-select select2 select-filter" id="filterData">
                                <option value="#ALL">Semua Tahun</option>
                                @foreach($angkatan as $item)
                                <option value="{{ $item->msy_id }}">{{ $item->msy_year.' '.($item->msy_semester == 2 ? 'Genap' : 'Ganjil') }}</option>
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
                            <button class="btn btn-info" onclick="filter()">
                                <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                            </button>
                        </div>
                    </div>
                </div>
                <table id="registrant-invoice-detail-table" class="table table-striped">
                    <thead>
                        <tr>
                            <th rowspan="2">Nama</th>
                            <th rowspan="2">Jalur / Periode</th>
                            <th rowspan="2">Metode Pembayaran</th>
                            <th rowspan="2">Nomor Tagihan</th>
                            <th rowspan="2">Tanggal Pembayaran</th>
                            <th colspan="2" class="text-center">Jenis Tagihan</th>
                            <th rowspan="2">
                                Total Harus Dibayar<br>
                                (A-B)
                            </th>
                            <th rowspan="2">Status</th>
                        </tr>
                        <tr>
                            <th>Tagihan(A)</th>
                            <th>Potongan(B)</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <table id="forExport" class="table table-striped">
                    <thead>
                        <tr>
                            <th rowspan="2">Nama</th>
                            <th rowspan="2">Jalur</th>
                            <th rowspan="2">periode</th>
                            <th rowspan="2">Metode Pembayaran</th>
                            <th rowspan="2">Nomor Tagihan</th>
                            <th rowspan="2">Tanggal Pembayaran</th>
                            <th colspan="2">Jenis Tagihan</th>
                            <th rowspan="2">status</th>
                        </tr>
                        <tr>
                            <th>Nama Tagihan</th>
                            <th>Nominal</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <!-- REGISTRANT PAYMENT HISTORY -->
            <!-- <div class="tab-pane fade" id="navs-payment-history" role="tabpanel">
                <table id="registrant-payment-history-table" class="table table-striped">
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
            </div> -->
        </div>
    </div>
</div>
@endsection

@section('js_section')
<script>
    var dtDetail, dtHistory, student = null;

    var dtExport = $('#forExport').DataTable({
        paging: false,
        dom: 'Bfrtip',
        buttons: [
            'copy',
            'excel',
            'csv',
            {
                extend: 'pdf',
                orientation: 'landscape',
                pageSize: 'LEGAL'
            }
        ]
    });

    $(document).ready(function() {
        select2Replace();
    });

    $(function() {
        _oldStudentInvoiceDetailTable.init();
        _oldStudentPaymentHistoryTable.init()
    })

    const _oldStudentInvoiceDetailTable = {
        ..._datatable,
        init: function(byFilter = '#ALL', path = '#ALL', period = '#ALL', searchData = '#ALL') {
            var jsonData = [];
            dtDetail = this.instance = $('#registrant-invoice-detail-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL + '/api/report/registrant-invoice',
                    data: {
                        angkatan: byFilter,
                        path: path,
                        period: period,
                        search: searchData,
                    },
                    dataSrc: function(json) {
                        var data = [];
                        for (var i = 0; i < json.data.length; i++) {
                            if (json.data[i].payment === null) {
                                console.log(i + " Value is null");
                            } else {
                                if (searchData != '#ALL') {
                                    var row = json.data[i];
                                    var isFound = false;

                                    if (!isFound && '' + row.participant.par_fullname.toLowerCase().search(searchData.toLowerCase()) >= 0) {
                                        data.push(json.data[i]);
                                        isFound = true;
                                    }

                                    if (!isFound && '' + row.path.path_name.toLowerCase().search(searchData.toLowerCase()) >= 0) {
                                        data.push(json.data[i]);
                                        isFound = true;
                                    }

                                    if (!isFound && '' + row.period.period_name.toLowerCase().search(searchData.toLowerCase()) >= 0) {
                                        data.push(json.data[i]);
                                        isFound = true;
                                    }

                                    if (!isFound && '' + row.payment.payment_reg_method.toLowerCase().search(searchData.toLowerCase()) >= 0) {
                                        data.push(json.data[i]);
                                        isFound = true;
                                    }

                                    if (!isFound && '' + row.payment.payment_reg_total.toString().toLowerCase().search(searchData.toLowerCase()) >= 0) {
                                        data.push(json.data[i]);
                                        isFound = true;
                                    }

                                    if (!isFound && '' + row.payment.payment_reg_status.toLowerCase().search(searchData.toLowerCase()) >= 0) {
                                        data.push(json.data[i]);
                                        isFound = true;
                                    }

                                    if (!isFound) {
                                        var start = 0;
                                        while (!isFound && start < row.payment.payment_register_detail.length) {
                                            if (!isFound && '' + row.payment.payment_register_detail[start].payment_rd_amount.toString().toLowerCase().search(searchData.toLowerCase()) >= 0) {
                                                data.push(json.data[i]);
                                                isFound = true;
                                            }

                                            if (!isFound && '' + row.payment.payment_register_detail[start].payment_rd_component.toLowerCase().search(searchData.toLowerCase()) >= 0) {
                                                data.push(json.data[i]);
                                                isFound = true;
                                            }
                                            start++;
                                        }
                                    }
                                } else {
                                    data.push(json.data[i]);
                                }
                            }
                        }
                        jsonData = data;
                        json.data = data;
                        return json.data;
                    }
                },
                columns: [{
                        name: 'student_name',
                        data: 'participant.par_fullname',
                        render: (data) => {
                            return this.template.defaultCell(data, {
                                bold: true
                            });
                        }
                    },
                    {
                        name: 'path_n_period',
                        render: (data, _, row) => {
                            return this.template.titleWithSubtitleCell(row.path.path_name, row.period.period_name);
                        }
                    },
                    {
                        name: 'payment',
                        render: (data, _, row) => {
                            try {
                                return row.payment.payment_reg_method;
                            } catch (err) {
                                return "";
                            }
                        }
                    },
                    {
                        name: 'invoice_number',
                        data: 'payment.payment_reg_invoice_num'
                    },
                    {
                        name: 'paid_date',
                        data: 'payment.payment_reg_paid_date'
                    },
                    {
                        name: 'invoice_a',
                        render: (data, _, row) => {
                            var listData = [];
                            var payment = row.payment.payment_register_detail;
                            for (var i = 0; i < payment.length; i++) {
                                if (payment[i].payment_rd_component != "biaya discount") {
                                    listData.push({
                                        name: payment[i].payment_rd_component,
                                        nominal: payment[i].payment_rd_amount
                                    })
                                }
                            }
                            return this.template.invoiceDetailCell(listData, row.payment.payment_reg_total);
                        }
                    },
                    {
                        name: 'invoice_b',
                        render: (data, _, row) => {
                            var listData = [];
                            var payment = row.payment.payment_register_detail;
                            var total = 0;
                            for (var i = 0; i < payment.length; i++) {
                                if (payment[i].payment_rd_component == "biaya discount") {
                                    listData.push({
                                        name: payment[i].payment_rd_component,
                                        nominal: payment[i].payment_rd_amount
                                    })
                                    total += payment[i].payment_rd_amount;
                                }
                            }
                            return this.template.invoiceDetailCell(listData, '' + total);
                        }
                    },
                    {
                        name: 'total_must_be_paid',
                        data: 'total_must_be_paid',
                        render: (data, _, row) => {
                            return this.template.currencyCell(row.payment.payment_reg_total, {
                                bold: true,
                                additionalClass: 'text-danger'
                            });
                        }
                    },
                    {
                        name: 'status',
                        data: 'payment.payment_reg_status',
                        render: (data) => {
                            if(data == 'lunas'){
                                return this.template.badgeCell(data, 'success');
                            }else{
                                return this.template.badgeCell(data, 'danger');
                            }
                        }
                    },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom: '<"d-flex justify-content-between align-items-center header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"registrant-invoice-detail-actions">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end"  <".search_filter">lB> >' +
                    '>' +
                    '<"eazy-table-wrapper" t>' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                buttons: [{
                        extend: 'collection',
                        text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-external-link font-small-4 me-50"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>Export</span>',
                        className: 'btn btn-outline-secondary dropdown-toggle',
                        buttons: [{
                                text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clipboard font-small-4 me-50"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>Pdf</span>',
                                className: 'dropdown-item',
                                action: function(e, dt, node, config){
                                    getPdf();
                                }
                            },
                            {
                                text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file font-small-4 me-50"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>Excel</span>',
                                className: 'dropdown-item',
                                action: function(e, dt, node, config){
                                    getExcel();
                                }
                            },
                            {
                                text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text font-small-4 me-50"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>Csv</span>',
                                className: 'dropdown-item',
                                action: function(e, dt, node, config){
                                    getCsv();
                                }
                            },
                            {
                                text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-copy font-small-4 me-50"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>Copy</span>',
                                className: 'dropdown-item',
                                action: function(e, dt, node, config){
                                    getCopy();
                                }
                            }
                        ]
                    },
                ],
                initComplete: function() {
                    $('.registrant-invoice-detail-actions').html(`
                        <h5 class="mb-0">Daftar Tagihan</h5>
                    `)
                    $('.search_filter').html(`
                    <div class="dataTables_filter">
                        <label><input type="text" id="searchFilterDetail" class="form-control" placeholder="Cari Data" onkeydown="searchDataDetail(event)"></label>
                    </div>
                    `)
                    feather.replace();
                    setExportDataTableExport(jsonData);
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
                    url: _baseURL + '/api/dt/report-registrant-payment-history',
                },
                columns: [{
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
                            return this.template.defaultCell(data, {
                                bold: true
                            });
                        }
                    },
                    {
                        name: 'payment_nominal',
                        data: 'payment_nominal',
                        render: (data) => {
                            return this.template.currencyCell(data, {
                                bold: true
                            });
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
                dom: '<"d-flex justify-content-between align-items-center header-actions mx-0 row"' +
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

    function setExportDataTableExport(data){
        dtExport.clear().destroy();
        var listData = [];

        data.forEach((row) => {
            row.payment.payment_register_detail.forEach((list) => {
                listData.push({
                    par_fullname: row.participant.par_fullname,
                    path_name: row.path.path_name,
                    period_name: row.period.period_name,
                    payment_reg_method: row.payment.payment_reg_method,
                    payment_reg_invoice_num: row.payment.payment_reg_invoice_num,
                    payment_reg_paid_date: row.payment.payment_reg_paid_date,
                    payment_rd_component: list.payment_rd_component,
                    payment_rd_amount: list.payment_rd_amount,
                    payment_reg_status: row.payment.payment_reg_status
                })
            })
        })

        dtExport = $('#forExport').DataTable({
            paging: false,
            dom: 'Bfrtip',
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                {
                    extend: 'pdf',
                    orientation: 'landscape',
                    pageSize: 'LEGAL'
                }
            ],
            data: listData,
            serverSide: false,
            columns: [
                { data: 'par_fullname' },
                { data: 'path_name' },
                { data: 'period_name' },
                { data: 'payment_reg_method' },
                { data: 'payment_reg_invoice_num' },
                { data: 'payment_reg_paid_date' },
                { data: 'payment_rd_component' },
                { data: 'payment_rd_amount' },
                { data: 'payment_reg_status' },
            ]
        });
    }

    function getPdf(){
        var pdfButton = $('#forExport_wrapper .buttons-pdf');
        pdfButton.click();
    }

    function getExcel(){
        var pdfButton = $('#forExport_wrapper .buttons-excel');
        pdfButton.click();
    }

    function getCsv(){
        var pdfButton = $('#forExport_wrapper .buttons-csv');
        pdfButton.click();
    }

    function getCopy(){
        var pdfButton = $('#forExport_wrapper .buttons-copy');
        pdfButton.click();
    }
</script>
@endsection
