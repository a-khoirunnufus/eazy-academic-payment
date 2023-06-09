@extends('layouts.static_master')


@section('page_title', 'Detail Tagihan Mahasiswa Baru')
@section('sidebar-size', 'collapsed')
@section('url_back', route('payment.generate.student-invoice'))

@section('css_section')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
    <link rel="stylesheet" href="{{ url('css/jstree-custom-table.css') }}" />

    <style>
        .eazy-table-wrapper {
            min-height: 300px;
            width: 100%;
            overflow-x: auto;
        }

        .eazy-table-info {
            display: inline-block;
        }
        .eazy-table-info td {
            padding: 2.5px 0;
        }
        .eazy-table-info td:first-child {
            padding-right: 1rem;
        }
        #invoiceDetailModal #invoice-not-generated.hide,
        #invoiceDetailModal #invoice-data.hide,
        #invoiceDetailModal #invoice-detail.hide,
        #invoiceDetailModal #transaction-history.hide {
            display: none;
        }
    </style>
@endsection

@section('content')

@include('pages._payment.generate._shortcuts', ['active' => 'new-student-invoice'])

<div class="card">
    <div class="card-body">
        <div class="d-flex" style="gap: 2rem">
            <div class="flex-grow-1">
                <span class="text-secondary d-block" style="margin-bottom: 7px">Periode Tagihan</span>
                <h5 class="fw-bolder" id="info-invoice-period">
                    {{ $invoice_period->msy_year}} Semester {{ $invoice_period->msy_semester }}
                </h5>
            </div>
            <div class="flex-grow-1">
                <span class="text-secondary d-block" style="margin-bottom: 7px">Fakultas</span>
                @if($faculty)
                    <h5 class="fw-bolder" id="info-faculty">{{ $faculty->faculty_name }}</h5>
                @else
                    <h5>-</h5>
                @endif
            </div>
            <div class="flex-grow-1">
                <span class="text-secondary d-block" style="margin-bottom: 7px">Program Studi</span>
                @if($studyprogram)
                    <h5 class="fw-bolder" id="info-studyprogram">{{ strtoupper($studyprogram->studyprogram_type).' '.$studyprogram->studyprogram_name }}</h5>
                @else
                    <h5>-</h5>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="student-invoice-detail-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Tahun Ajaran</th>
                <th>Nama / Nomor Pendaftaran</th>
                <th>Jalur / Periode Pendaftaran</th>
                <th>Program Studi / Jenis Perkuliahan</th>
                <th>Jenis Perkuliahan</th>
                <th class="text-center">Status Tagihan</th>
                <th>Jumlah Tagihan</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Modal Generate Tagihan -->
<div class="modal fade" id="generateInvoiceModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-white" style="padding: 2rem 3rem">
                <h4 class="modal-title fw-bolder" id="generateInvoiceModalLabel">Generate Tagihan Mahasiswa Baru</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 pt-0">
                <div class="d-flex flex-row" style="gap: 3rem">
                    <div class="d-flex flex-column mb-2" style="gap: 5px">
                        <small class="d-block">Periode Tagihan</small>
                        <span class="fw-bolder" id="info-invoice-period">N/A</span>
                    </div>
                    @if($faculty)
                        <div class="d-flex flex-column mb-2" style="gap: 5px">
                            <small class="d-block">Fakultas</small>
                            <span class="fw-bolder">{{ $faculty->faculty_name }}</span>
                        </div>
                    @endif
                    @if($studyprogram)
                        <div class="d-flex flex-column mb-2" style="gap: 5px">
                            <small class="d-block">Program Studi</small>
                            <span class="fw-bolder">{{ $studyprogram->studyprogram_name }}</span>
                        </div>
                    @endif
                </div>
                <div class="jstree-table-wrapper">
                    <div style="width: 1185px; margin: 0 auto;">
                        <div id="tree-table-header" class="d-flex align-items-center bg-light border-top border-start border-end" style="height: 40px; width: 1185px;">
                            <div style="width: 80px"></div>
                            <div class="flex-grow-1 fw-bolder text-uppercase" style="width: 619px">Scope</div>
                            <div class="fw-bolder text-uppercase" style="width: 200px">Status Generate</div>
                            <div class="fw-bolder text-uppercase" style="width: 284px">Status Komponen Tagihan</div>
                        </div>
                        <div id="tree-generate-invoice"></div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Batal</button>
                    <button onclick="" class="btn btn-primary">Generate</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Detail Modal -->
<div class="modal fade" id="invoiceDetailModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-white" style="padding: 2rem 3rem 3rem 3rem">
                <h4 class="modal-title fw-bolder" id="invoiceDetailModalLabel">Detail Mahasiswa dan Tagihan</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 pt-0">
                <div id="student-data" class="mb-3">
                    <h4 class="fw-bolder mb-1">Data Mahasiwa</h4>
                    <div class="d-flex flex-row justify-content-between mb-4" style="gap: 2rem">
                        <div class="d-flex flex-column" style="gap: 5px">
                            <small class="d-block">Nama Lengkap dan Nomor Pendaftaran</small>
                            <span class="fw-bolder" id="detail-student-fullname">...</span>
                            <span class="text-secondary d-block" id="detail-registration-number">...</span>
                        </div>
                        <div class="d-flex flex-column" style="gap: 5px">
                            <small class="d-block">Jalur dan Periode Pendaftaran</small>
                            <span class="fw-bolder" id="detail-path">...</span>
                            <span class="text-secondary d-block" id="detail-period">...</span>
                        </div>
                        <div class="d-flex flex-column" style="gap: 5px">
                            <small class="d-block">Program Studi dan Jenis Perkuliahan</small>
                            <span class="fw-bolder" id="detail-studyprogram">...</span>
                            <span class="text-secondary d-block" id="detail-lecture-type">...</span>
                        </div>
                    </div>
                </div>

                <div id="invoice-not-generated" class="hide">
                    <div class="alert alert-warning p-1">
                        <i data-feather="alert-circle"></i>&nbsp;&nbsp;Tagihan untuk mahasiswa ini belum digenerate.
                    </div>
                </div>

                <div id="invoice-data" class="mb-3 hide">
                    <h4 class="fw-bolder mb-1">Data Invoice</h4>
                    <table class="eazy-table-info">
                        <tr>
                            <td>Nomor Invoice</td>
                            <td>
                                <span class="fw-bold">:&nbsp;&nbsp;<span id="detail-invoice-number">...</span><span>
                            </td>
                        </tr>
                        <tr>
                            <td>Digenerate Pada</td>
                            <td>
                                <span class="fw-bold">:&nbsp;&nbsp;<span id="detail-invoice-created">...</span></span>
                            </td>
                        </tr>
                        <tr>
                            <td>Status Pembayaran</td>
                            <td>
                                <span class="fw-bold">:&nbsp;&nbsp;<span id="detail-invoice-status">...</span></span>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="invoice-detail" class="mb-3 hide">
                    <h4 class="fw-bolder mb-2">Rincian Tagihan</h4>
                    <table id="table-detail-invoice-component" class="table table-bordered">
                        <thead>
                            <tr class="bg-light">
                                <th class="text-center">Komponen Tagihan</th>
                                <th class="text-center">Harga</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div id="transaction-history" class="hide">
                    <h4 class="fw-bolder mb-2">Riwayat Transaksi</h4>
                    <table class="table table-bordered" id="paymentBill">
                        <thead>
                            <tr>
                                <th class="text-center">Invoice ID</th>
                                <th class="text-center">Expired Date</th>
                                <th class="text-center">Amount</th>
                                <th class="text-center">Fee</th>
                                <th class="text-center">Paid Date</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="6">
                                    Belum ada transaksi
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('js_section')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>

<script>
    DataTable.render.ellipsis = function ( cutoff ) {
        return function ( data, type, row ) {
            if ( type === 'display' ) {
                var str = data.toString(); // cast numbers

                return str.length < cutoff ?
                    str :
                    str.substr(0, cutoff-1) +'&#8230;';
            }

            // Search, order and type can use the original data
            return data;
        };
    };

    const invoicePeriodCode = "{{ $invoice_period->msy_code }}";
    const scope = "{{ $scope }}";
    const facultyId = "{{ $faculty ? $faculty->faculty_id : 0 }}";
    const studyprogramId = "{{ $studyprogram ? $studyprogram->studyprogram_id : 0 }}";

    $(function(){
        _newStudentInvoiceDetailTable.init()
    })

    const _newStudentInvoiceDetailTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#student-invoice-detail-table').DataTable({
                serverSide: true,
                ajax: {
                    url: `${_baseURL}/api/payment/generate/new-student-invoice/detail`
                        +`?invoice_period_code=${invoicePeriodCode}`
                        +`&scope=${scope}${scope == 'faculty' ? '&faculty_id='+facultyId : '&studyprogram_id='+studyprogramId}`,
                },
                stateSave: false,
                columns: [
                    {
                        name: 'action',
                        data: 'participant_id',
                        orderable: false,
                        searchable: false,
                        render: (data, _, row) => {
                            return this.template.rowAction(row.payment_reregist_invoice_status);
                        }
                    },
                    {
                        name: 'school_year_year',
                        data: 'school_year_year',
                        render: (data, _, row) => {
                            return this.template.defaultCell(data);
                        }
                    },
                    {
                        name: 'participant_fullname',
                        data: 'participant_fullname',
                        render: (data, _, row) => {
                            return this.template.titleWithSubtitleCell(row.participant_fullname, row.registration_number);
                        }
                    },
                    {
                        name: 'registration_path_name',
                        data: 'registration_path_name',
                        render: (data, _, row) => {
                            return this.template.titleWithSubtitleCell(row.registration_path_name, row.registration_period_name);
                        }
                    },
                    {
                        name: 'studyprogram_name',
                        data: 'studyprogram_name',
                        visible: scope != 'studyprogram',
                        render: (data, _, row) => {
                            return this.template.titleWithSubtitleCell(
                                row.studyprogram_name ?? 'N/A',
                                row.lecture_type_name ?? 'N/A'
                            );
                        }
                    },
                    {
                        name: 'lecture_type_name',
                        data: 'lecture_type_name',
                        visible: scope != 'all' && scope != 'faculty',
                        render: (data) => {
                            return this.template.defaultCell(data ?? 'N/A');
                        }
                    },
                    {
                        name: 'payment_reregist_invoice_status',
                        data: 'payment_reregist_invoice_status',
                        render: (data) => {
                            return this.template.badgeCell(
                                data,
                                data == 'Sudah Digenerate' ? 'success' : 'secondary'
                            );
                        }
                    },
                    {
                        name: 'payment_reregist_invoice_amount',
                        data: 'payment_reregist_invoice_amount',
                        render: (data) => {
                            return this.template.currencyCell(data ?? 0);
                        }
                    },
                    // search and export columns
                    {
                        title: 'Nama Mahasiswa',
                        data: 'participant_fullname',
                        visible: false,
                    },
                    {
                        title: 'Nomor Pendaftaran',
                        data: 'registration_number',
                        visible: false,
                    },
                    {
                        title: 'Jalur Pendaftaran',
                        data: 'registration_path_name',
                        visible: false,
                    },
                    {
                        title: 'Periode Pendaftaran',
                        data: 'registration_period_name',
                        visible: false,
                    },
                    {
                        title: 'Program Studi',
                        data: 'studyprogram_name',
                        visible: false,
                    },
                    {
                        title: 'Jenis Perkuliahan',
                        data: 'lecture_type_name',
                        visible: false,
                    },
                    {
                        title: 'Jumlah Tagihan',
                        data: 'payment_reregist_invoice_amount',
                        visible: false,
                    },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"student-invoice-detail-actions d-flex align-items-end">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>' +
                    '<"eazy-table-wrapper" t>' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                buttons: [
                    {
                        extend: 'collection',
                        className: 'btn btn-outline-secondary dropdown-toggle',
                        text: feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + 'Export',
                        buttons: [
                            {
                                extend: 'print',
                                text: feather.icons['printer'].toSvg({class: 'font-small-4 me-50'}) + 'Print',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [8,9,10,11,12,13,6,7]
                                }
                            },
                            {
                                extend: 'csv',
                                text: feather.icons['file-text'].toSvg({class: 'font-small-4 me-50'}) + 'Csv',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [8,9,10,11,12,13,6,14]
                                }
                            },
                            {
                                extend: 'excel',
                                text: feather.icons['file'].toSvg({class: 'font-small-4 me-50'}) + 'Excel',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [8,9,10,11,12,13,6,14]
                                }
                            },
                            {
                                extend: 'pdf',
                                text: feather.icons['clipboard'].toSvg({class: 'font-small-4 me-50'}) + 'Pdf',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [8,9,10,11,12,13,6,7]
                                }
                            },
                            {
                                extend: 'copy',
                                text: feather.icons['copy'].toSvg({class: 'font-small-4 me-50'}) + 'Copy',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [8,9,10,11,12,13,6,14]
                                }
                            }
                        ],
                    }
                ],
                initComplete: function() {
                    $('.student-invoice-detail-actions').html(`
                        <div style="margin-bottom: 7px">
                            <button onclick="TreeGenerate.openModal()" class="btn btn-primary">
                                Generate Tagihan
                            </button>
                        </div>
                    `)
                    feather.replace()
                }
            });
            this.implementSearchDelay();
        },
        template: {
            rowAction: function(invoiceStatus) {
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a onclick="_invoiceDetailModalActions.open(event)" class="dropdown-item"><i data-feather="eye"></i>&nbsp;&nbsp;Detail Mahasiswa & Tagihan</a>
                            <a
                                onclick="_newStudentInvoiceDetailTableAction.generateInvoice(event)"
                                class="dropdown-item ${invoiceStatus == 'Sudah Digenerate' ? 'disabled' : ''}"
                            >
                                <i data-feather="mail"></i>&nbsp;&nbsp;Generate Tagihan
                            </a>
                            <a
                                onclick="_newStudentInvoiceDetailTableAction.deleteInvoice(event)"
                                class="dropdown-item ${invoiceStatus == 'Belum Digenerate' ? 'disabled' : ''}"
                            >
                                <i data-feather="trash"></i>&nbsp;&nbsp;Delete Tagihan
                            </a>
                        </div>
                    </div>
                `
            },
            titleWithSubtitleCell: _datatableTemplates.titleWithSubtitleCell,
            defaultCell: _datatableTemplates.defaultCell,
            badgeCell: _datatableTemplates.badgeCell,
            currencyCell: _datatableTemplates.currencyCell,
        }
    }

    const _newStudentInvoiceDetailTableAction = {
        tableRef: _newStudentInvoiceDetailTable,
        generateInvoice: function(e) {
            const data = this.tableRef.getRowData(e.currentTarget);
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin generate tagihan mahasiswa '+data.participant_fullname+' ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#356CFF',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Generate',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(
                        // url
                        _baseURL + '/api/payment/generate/new-student-invoice/generate-one',
                        // data send
                        {
                            invoice_period_code: invoicePeriodCode,
                            register_id: data.registration_id,
                        },
                        // data receive
                        (data) => {
                            if (data.success) {
                                _toastr.success(data.message, 'Sukses');
                            } else {
                                _toastr.error(data.message, 'Gagal');
                            }
                            _newStudentInvoiceDetailTable.reload();
                        }
                    ).fail((error) => {
                        _responseHandler.generalFailResponse(error);
                    });
                }
            })
        },
        deleteInvoice: function(e) {
            const data = this.tableRef.getRowData(e.currentTarget);
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin generate tagihan mahasiswa '+data.participant_fullname+' ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#356CFF',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Generate',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(
                        // url
                        _baseURL + '/api/payment/generate/new-student-invoice/delete-one',
                        // data send
                        {
                            payment_reregist_id: data.payment_reregist_id,
                        },
                        // data receive
                        (data) => {
                            if (data.success) {
                                _toastr.success(data.message, 'Sukses');
                            } else {
                                _toastr.error(data.message, 'Gagal');
                            }
                            _newStudentInvoiceDetailTable.reload();
                        }
                    ).fail((error) => {
                        _responseHandler.generalFailResponse(error);
                    });
                }
            });
        },
    }

    const GenerateInvoiceModal = new bootstrap.Modal(document.getElementById('generateInvoiceModal'));

    const TreeGenerate = {
        selector: '#tree-generate-invoice',
        openModal: function() {
            $('#generateInvoiceModal #info-invoice-period').text("{{ $invoice_period->msy_year.' Semester '.$invoice_period->msy_semester }}");
            this.initTree();
            $(this.selector).on("loaded.jstree", () => { this.appendColumn(this.selector) });
            $(this.selector).on("before_open.jstree", () => { this.appendColumn(this.selector) });
            GenerateInvoiceModal.show();
        },
        initTree: async function() {
            const {data} = await $.ajax({
                async: true,
                url: `${_baseURL}/api/payment/generate/new-student-invoice/get-tree-generate-${scope}`
                    +`?invoice_period_code=${invoicePeriodCode}`
                    +`${
                        scope == 'faculty' ?
                            `&scope=faculty&faculty_id=${facultyId}`
                            : scope == 'studyprogram' ?
                                `&scope=studyprogram&faculty_id=${facultyId}&studyprogram_id=${studyprogramId}`
                                : ''
                    }`,
                type: 'get',
            });

            return $(this.selector).jstree({
                'core' : {
                    'data' : data.tree,
                    "themes":{
                        "icons":false
                    }
                },
                "checkbox" : {
                    "keep_selected_style" : false
                },
                "plugins" : [ "checkbox", "wholerow" ],
            });
        },
        appendColumn: function(selector) {
            $(selector+' .jstree-anchor').each(function() {
                if ($(this).children().length <= 2) {
                    const nodeId = $(this).parents('li').attr('id');
                    const node = $(selector).jstree('get_node', nodeId);
                    const children = $(this).children();
                    console.log(node);
                    $(this).empty();
                    $(this).append(children.get(0));
                    $(this).append(children.get(1));
                    $(this).append(`<div class="text"><span>${node.text}</span></div>`);
                    $(this).append(`<div style="display: flex; justify-content: flex-end;">
                        <div style="width: 200px">${node.data.status_generated.text}</div>
                        <div style="width: 280px">
                            ${
                                node.data.status_invoice_component == "not_defined"
                                    ? 'Komponen Tagihan Belum Diset!' : ''
                            }
                        </div>
                    </div>`);
                }
            });
        }
    }

    const InvoiceDetailModal = new bootstrap.Modal(document.getElementById('invoiceDetailModal'));

    const _invoiceDetailModalActions = {
        open: async function(e) {
            this.resetData();

            const studentData = _newStudentInvoiceDetailTable.getRowData(e.currentTarget);

            // Student Data
            $('#detail-student-fullname').text(studentData.participant_fullname);
            $('#detail-registration-number').text(studentData.registration_number);
            $('#detail-path').text(studentData.registration_path_name);
            $('#detail-period').text(studentData.registration_period_name);
            $('#detail-studyprogram').text(studentData.studyprogram_name);
            $('#detail-lecture-type').text(studentData.lecture_type_name);

            // invoice already generated
            if (studentData.payment_reregist_id) {
                const {data: invoiceData} = await $.ajax({
                    async: true,
                    url: _baseURL+'/api/payment/generate/new-student-invoice/show-invoice/'+studentData.payment_reregist_id,
                    type: 'get',
                });
                const {data: invoiceComponentData} = await $.ajax({
                    async: true,
                    url: _baseURL+'/api/payment/generate/new-student-invoice/show-invoice-component/'+studentData.payment_reregist_id,
                    type: 'get',
                });
                // TODO: get total terbayar dan total diterima

                $('#invoice-data').removeClass('hide');
                $('#invoice-detail').removeClass('hide');
                $('#transaction-history').removeClass('hide');

                // Invoice Data
                $('#detail-invoice-number').text(invoiceData.prr_id);
                $('#detail-invoice-created').text(moment(invoiceData.created_at).format('DD MMMM YYYY, HH:mm'));
                $('#detail-invoice-status').html(
                    invoiceData.prr_status == 'lunas' ?
                        '<div class="badge bg-success" style="font-size: inherit">Lunas</div>'
                        : '<div class="badge bg-danger" style="font-size: inherit">Belum Lunas</div>'
                );

                let detailInvoiceComponentHtml = invoiceComponentData.map((item) => {
                    return `
                        <tr>
                            <td class="text-center">${item.prrd_component}</td>
                            <td class="text-center">${Rupiah.format(item.prrd_amount)}</td>
                        </tr>
                    `;
                });
                detailInvoiceComponentHtml += `
                    <tr class="bg-light">
                        <td class="text-center fw-bolder">Total Tagihan Mahasiswa</td>
                        <td class="text-center fw-bolder">${Rupiah.format(studentData.payment_reregist_invoice_amount)}</td>
                    </tr>
                    <tr class="bg-light">
                        <td class="text-center fw-bolder">Jumlah Terbayar</td>
                        <td class="text-center fw-bolder">N/A</td>
                    </tr>
                    <tr class="bg-light">
                        <td class="text-center fw-bolder">Total yang Diterima</td>
                        <td class="text-center fw-bolder">N/A</td>
                    </tr>
                `;

                // Invoice Component Data
                $('#table-detail-invoice-component tbody').html(detailInvoiceComponentHtml);
            }
            // invoice not generated
            else {
                $('#invoice-not-generated').removeClass('hide');
            }

            InvoiceDetailModal.show();
        },
        resetData: function() {
            $('#detail-student-fullname').text('...');
            $('#detail-student-nik').text('...');
            $('#detail-path').text('...');
            $('#detail-period').text('...');
            $('#detail-studyprogram').text('...');
            $('#detail-lecture-type').text('...');
            $('#detail-invoice-number').text('...');
            $('#detail-invoice-created').text('...');
            $('#table-detail-invoice-component tbody').html('');
            $('#detail-invoice-amount').text('...');

            $('#invoice-not-generated').addClass('hide');
            $('#invoice-data').addClass('hide');
            $('#invoice-detail').addClass('hide');
            $('#transaction-history').addClass('hide');
        }
    }

</script>
@endsection
