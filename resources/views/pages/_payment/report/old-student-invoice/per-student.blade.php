@extends('tpl.vuexy.master-payment')

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

    .space {
        margin-left: 10px;
    }

    table.dataTable thead th {
        white-space: nowrap
    }

    table.dataTable thead [rowspan="2"].sorting:before,
    table.dataTable thead [rowspan="2"].sorting_asc:before,
    table.dataTable thead [rowspan="2"].sorting_desc:before {
        top: 1.75rem;
    }
    table.dataTable thead [rowspan="2"].sorting:after,
    table.dataTable thead [rowspan="2"].sorting_asc:after,
    table.dataTable thead [rowspan="2"].sorting_desc:after {
        top: unset;
        bottom: 1.75rem;
    }

    table.dataTable.align-top td {
        vertical-align: top;
    }
</style>
@endsection

@section('content')

@include('pages._payment.report.old-student-invoice._shortcuts', ['active' => 'per-student'])

<div class="card">
    <div class="card-body">
        <div class="datatable-filter multiple-row">
            <x-select-option
                title="Tahun Akademik"
                select-id="school-year-filter"
                resource-url="/api/payment/resource/school-year"
                value="msy_code"
                :default-value="$year->msy_code"
                :default-label="$year->msy_year.' '.($year->msy_semester == 1 ? 'Ganjil' : ($year->msy_semester == 2 ? 'Genap' : 'Antara'))"
                label-template=":msy_year :msy_semester"
                :label-template-items="['msy_year', [
                    'key' => 'msy_semester',
                    'mapping' => [
                        '1' => 'Ganjil',
                        '2' => 'Genap',
                        '3' => 'Antara',
                    ],
                ]]"
                without-all-option="1"
            />
            <div>
                <label class="form-label">Angkatan</label>
                <select id="student-batch-filter" class="form-select"></select>
            </div>
            <x-select-option
                title="Periode Masuk"
                select-id="period-filter"
                resource-url="/api/payment/resource/registration-period"
                value="period_id"
                label-template=":period_name"
                :label-template-items="['period_name']"
            />
            <x-select-option
                title="Jalur Masuk"
                select-id="path-filter"
                resource-url="/api/payment/resource/registration-path"
                value="path_id"
                label-template=":path_name"
                :label-template-items="['path_name']"
            />
            <x-select-option
                title="Fakultas"
                select-id="faculty-filter"
                resource-url="/api/payment/resource/faculty"
                value="faculty_id"
                label-template=":faculty_name"
                :label-template-items="['faculty_name']"
            />
            <div>
                <label class="form-label">Program Studi</label>
                <select id="studyprogram-filter" class="form-select">
                    @if($studyprogram)
                        <option value="{{ $studyprogram->studyprogram_id }}" selected>{{ strtoupper($studyprogram->studyprogram_type) }} {{ $studyprogram->studyprogram_name }}</option>
                    @endif
                </select>
            </div>
            <div>
                <label class="form-label">Status Tagihan</label>
                <select id="status-filter" class="form-select select2">
                    <option value="#ALL" selected>Semua Status Tagihan</option>
                    <option value="lunas">Lunas</option>
                    <option value="belum lunas">Belum Lunas</option>
                    <option value="kredit">Kredit</option>
                </select>
            </div>
            <div class="d-flex align-items-end">
                <button onclick="_oldStudentInvoiceDetailTable.filter()" class="btn btn-info text-nowrap">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="old-student-invoice-detail-table" class="table table-striped align-top">
        <thead>
            <tr>
                <th rowspan="2">Nomor Tagihan</th>
                <th rowspan="2">Tahun Akademik</th>
                <th rowspan="2">Nama (NIM)<br>Angkatan<br>Periode Masuk<br>Jalur Masuk</th>
                <th rowspan="2">Program Studi<br>Fakultas</th>
                <th colspan="4" class="text-center">Rincian</th>
                <th rowspan="2">
                    Total Final Tagihan<br>
                    (A+B)-(C+D)
                </th>
                <th rowspan="2">Terbayar</th>
                <th rowspan="2">Piutang</th>
                <th rowspan="2">Status Tagihan</th>
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
                <th colspan="4">Total Keseluruhan</th>
                <th id="sum-invoice-component"></th>
                <th id="sum-invoice-penalty"></th>
                <th id="sum-invoice-scholarship"></th>
                <th id="sum-invoice-discount"></th>
                <th id="sum-final-bill"></th>
                <th id="sum-total-paid"></th>
                <th id="sum-total-not-paid"></th>
                <th id="sum-invoice-summary"></th>
            </tr>
        </tfoot>
    </table>
</div>

<div class="modal" id="historPaymentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Riwayat Pembayaran</h5>
            </div>
            <div class="modal-body">
                <table id="old-student-payment-history-table" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nomor Cicilan</th>
                            <th>Jumlah</th>
                            <th>Batas Pembayaran</th>
                            <th>Tanggal Pembayaran</th>
                            <th>status</th>
                        </tr>
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
    $(function() {
        _oldStudentInvoiceDetailTable.init();
    })

    const _oldStudentInvoiceDetailTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#old-student-invoice-detail-table').DataTable({
                ajax: {
                    url: _baseURL + '/api/report/old-student-invoice/student',
                    data: function(d) {
                        d.school_year = assignFilter('#school-year-filter');
                        d.filters = [
                            {
                                column: 'student.student_school_year',
                                operator: 'ilike',
                                value: assignFilter('#student-batch-filter', '%'),
                            },
                            {
                                column: 'student.period_id',
                                operator: '=',
                                value: assignFilter('#period-filter'),
                            },
                            {
                                column: 'student.path_id',
                                operator: '=',
                                value: assignFilter('#path-filter'),
                            },
                            {
                                column: 'student.studyProgram.faculty_id',
                                operator: '=',
                                value: assignFilter('#faculty-filter'),
                            },
                            {
                                column: 'student.studyprogram_id',
                                operator: '=',
                                value: assignFilter('#studyprogram-filter'),
                            },
                            {
                                column: 'prr_status',
                                operator: '=',
                                value: assignFilter('#status-filter'),
                            },
                        ];
                    },
                },
                stateSave: false,
                order: [],
                columns: [
                    //0
                    {
                        data: 'prr_id',
                        render: (data, _, row) => {
                            return this.template.buttonLinkCell(
                                row.prr_id,
                                {onclickFunc: `toHistory(${row.student_number})`},
                                {additionalClass: 'text-center'}
                            );
                        }
                    },
                    // 1
                    {
                        data: 'prr_school_year',
                        render: (data, _, row) => {
                            return this.template.titleWithSubtitleCell(
                                row.year.msy_year,
                                row.year.msy_semester == 1 ? 'Ganjil'
                                    : row.year.msy_semester == 2 ? 'Genap'
                                        : 'Antara'
                            );
                        }
                    },
                    // 2
                    {
                        data: 'student.fullname',
                        render: (data, _, row) => {
                            return this.template.listCell([
                                {text: `${row.student.fullname} (${row.student.student_id})`, bold: true, small: false, nowrap: true},
                                {text: 'Angkatan '+row.student.student_school_year.toString().substring(0, 4), bold: false, small: true, nowrap: true},
                                {text: row.student.period.period_name, bold: false, small: true, nowrap: true},
                                {text: row.student.path.path_name, bold: false, small: true, nowrap: true},
                            ]);
                        }
                    },
                    // 3
                    {
                        data: 'student.study_program.studyprogram_name',
                        render: (data, _, row) => {
                            return this.template.titleWithSubtitleCell(
                                `${row.student.study_program.studyprogram_name} (${row.student.study_program.studyprogram_type.toUpperCase()})`,
                                row.student.study_program.faculty.faculty_name
                            );
                        }
                    },
                    // 4
                    {
                        data: 'invoice_component.total',
                        render: (data, _, row) => {
                            let list = row.invoice_component.list;
                            if (typeof list == 'object') {
                                list = Object.values(list);
                            }

                            return this.template.listCell([
                                ...list.map(item => ({
                                    text: this.template.titleWithSubtitleCell(item.prrd_component, Rupiah.format(item.prrd_amount)),
                                    bold: false,
                                    small: true,
                                    nowrap: true
                                }))
                                ,
                                {
                                    text: this.template.titleWithSubtitleCell('Total', Rupiah.format(row.invoice_component.total)),
                                    bold: true,
                                    small: false,
                                    nowrap: true
                                }
                            ]);
                        }
                    },
                    // 5
                    {
                        data: 'invoice_penalty.total',
                        render: (data, _, row) => {
                            let list = row.invoice_penalty.list;
                            if (typeof list == 'object') {
                                list = Object.values(list);
                            }

                            return this.template.listCell([
                                ...list.map(item => ({
                                    text: this.template.titleWithSubtitleCell(item.prrd_component, Rupiah.format(item.prrd_amount)),
                                    bold: false,
                                    small: true,
                                    nowrap: true
                                }))
                                ,
                                {
                                    text: this.template.titleWithSubtitleCell('Total', Rupiah.format(row.invoice_penalty.total)),
                                    bold: true,
                                    small: false,
                                    nowrap: true
                                }
                            ]);
                        }
                    },
                    // 6
                    {
                        data: 'invoice_scholarship.total',
                        render: (data, _, row) => {
                            let list = row.invoice_scholarship.list;
                            if (typeof list == 'object') {
                                list = Object.values(list);
                            }

                            return this.template.listCell([
                                ...list.map(item => ({
                                    text: this.template.titleWithSubtitleCell(item.prrd_component, Rupiah.format(item.prrd_amount)),
                                    bold: false,
                                    small: true,
                                    nowrap: true
                                }))
                                ,
                                {
                                    text: this.template.titleWithSubtitleCell('Total', Rupiah.format(row.invoice_scholarship.total)),
                                    bold: true,
                                    small: false,
                                    nowrap: true
                                }
                            ]);
                        }
                    },
                    // 7
                    {
                        data: 'invoice_discount.total',
                        render: (data, _, row) => {
                            let list = row.invoice_discount.list;
                            if (typeof list == 'object') {
                                list = Object.values(list);
                            }

                            return this.template.listCell([
                                ...list.map(item => ({
                                    text: this.template.titleWithSubtitleCell(item.prrd_component, Rupiah.format(item.prrd_amount)),
                                    bold: false,
                                    small: true,
                                    nowrap: true
                                }))
                                ,
                                {
                                    text: this.template.titleWithSubtitleCell('Total', Rupiah.format(row.invoice_discount.total)),
                                    bold: true,
                                    small: false,
                                    nowrap: true
                                }
                            ]);
                        }
                    },
                    // 8
                    {
                        data: 'final_bill',
                        render: (data) => {
                            return this.template.currencyCell(data, {bold: true, additionalClass: 'text-primary'});
                        }
                    },
                    // 9
                    {
                        data: 'total_paid',
                        render: (data) => {
                            return this.template.currencyCell(data, {bold: true, additionalClass: 'text-success'});
                        }
                    },
                    // 10
                    {
                        data: 'total_not_paid',
                        render: (data) => {
                            return this.template.currencyCell(data, {bold: true, additionalClass: 'text-danger'});
                        }
                    },
                    // 11
                    {
                        data: 'prr_status',
                        render: (data) => {
                            let bsColor = 'secondary';
                            if (data == 'lunas') bsColor = 'success';
                            if (data == 'belum lunas') bsColor = 'danger';
                            if (data == 'kredit') bsColor = 'warning';
                            return this.template.badgeCell(data, bsColor);
                        }
                    },
                    {
                        title: 'Nomor Tagihan',
                        data: 'prr_id',
                        visible: false,
                    },
                    {
                        title: 'Tahun Akademik',
                        visible: false,
                        render: (data, _, row) => {
                            return `${row.year.msy_year} ${
                                row.year.msy_semester == 1 ? 'Ganjil'
                                    : row.year.msy_semester == 2 ? 'Genap'
                                        : 'Antara'
                            }`;
                        }
                    },
                    {
                        title: 'Nama Mahasiswa',
                        data: 'student.fullname',
                        visible: false,
                    },
                    {
                        title: 'NIM Mahasiswa',
                        data: 'student.student_id',
                        visible: false,
                    },
                    {
                        title: 'Total Nominal Tagihan',
                        data: 'invoice_component.total',
                        visible: false,
                    },
                    {
                        title: 'Total Nominal Denda',
                        data: 'invoice_penalty.total',
                        visible: false,
                    },
                    {
                        title: 'Total Nominal Beasiswa',
                        data: 'invoice_scholarship.total',
                        visible: false,
                    },
                    {
                        title: 'Total Nominal Potongan',
                        data: 'invoice_discount.total',
                        visible: false,
                    },
                    {
                        title: 'Total Nominal Final Tagihan',
                        data: 'final_bill',
                        visible: false,
                    },
                    {
                        title: 'Nominal Terbayar',
                        data: 'total_paid',
                        visible: false,
                    },
                    {
                        title: 'Nominal Belum Dibayar',
                        data: 'total_not_paid',
                        visible: false,
                    },
                    {
                        title: 'Status Pembayaran',
                        data: 'prr_status',
                        visible: false,
                    },
                ],
                drawCallback: (settings) => {
                    const data = $('#old-student-invoice-detail-table').dataTable().api().rows({page:'current'}).data().toArray();
                    this.renderFooter(data);
                    feather.replace();
                },
                scrollX: true,
                scrollY: "60vh",
                scrollCollapse: true,
                language: {
                    search: '_INPUT_',
                    searchPlaceholder: "Cari Data",
                    lengthMenu: '_MENU_',
                    paginate: { 'first': 'First', 'last': 'Last', 'next': 'Next', 'previous': 'Prev' },
                    processing: "Loading...",
                    emptyTable: "Tidak ada data",
                    infoEmpty:  "Menampilkan 0",
                    lengthMenu: "_MENU_",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    infoFiltered: "(difilter dari _MAX_ entri)",
                    zeroRecords: "Tidak ditemukan data yang cocok"
                },
                dom: '<"d-flex justify-content-between align-items-center header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"old-student-invoice-actions">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>' +
                    'tr' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                buttons: _datatableBtnExportTemplate({
                    btnTypes: ['excel', 'csv'],
                    exportColumns: [12,13,14,15,16,17,18,19,20,21,22,23]
                }),
                initComplete: function() {}
            });

            this.implementSearchDelay();
        },
        template: _datatableTemplates,
        renderFooter: function(data) {
            let sumInvoiceComponent = 0;
            let sumInvoicePenalty = 0;
            let sumInvoiceScholarship = 0;
            let sumInvoiceDiscount = 0;
            let sumFinalBill = 0;
            let sumTotalPaid = 0;
            let sumTotalNotPaid = 0;
            let sumInvoiceSummaryTotal = 0;
            let sumInvoiceSummaryPaid = 0;
            let sumInvoiceSummaryNotPaid = 0;

            data.forEach((payment) => {
                sumInvoiceComponent += payment.invoice_component.total;
                sumInvoicePenalty += payment.invoice_penalty.total;
                sumInvoiceScholarship += payment.invoice_scholarship.total;
                sumInvoiceDiscount += payment.invoice_discount.total;
                sumFinalBill += payment.final_bill;
                sumTotalPaid += payment.total_paid;
                sumTotalNotPaid += payment.total_not_paid;

                if (payment.prr_status == 'lunas') {
                    sumInvoiceSummaryTotal++;
                    sumInvoiceSummaryPaid++;
                }

                if (payment.prr_status == 'belum lunas' || payment.prr_status == 'kredit') {
                    sumInvoiceSummaryTotal++;
                    sumInvoiceSummaryNotPaid++;
                }
            });

            $('.dataTables_scrollFoot #sum-invoice-component').html(
                _datatableTemplates.currencyCell(sumInvoiceComponent)
            );

            $('.dataTables_scrollFoot #sum-invoice-penalty').html(
                _datatableTemplates.currencyCell(sumInvoicePenalty)
            );

            $('.dataTables_scrollFoot #sum-invoice-scholarship').html(
                _datatableTemplates.currencyCell(sumInvoiceScholarship)
            );

            $('.dataTables_scrollFoot #sum-invoice-discount').html(
                _datatableTemplates.currencyCell(sumInvoiceDiscount)
            );

            $('.dataTables_scrollFoot #sum-final-bill').html(
                _datatableTemplates.currencyCell(sumFinalBill, {bold: true, additionalClass: 'text-primary'})
            );

            $('.dataTables_scrollFoot #sum-total-paid').html(
                _datatableTemplates.currencyCell(sumTotalPaid, {bold: true, additionalClass: 'text-success'})
            );

            $('.dataTables_scrollFoot #sum-total-not-paid').html(
                _datatableTemplates.currencyCell(sumTotalNotPaid, {bold: true, additionalClass: 'text-danger'})
            );

            $('.dataTables_scrollFoot #sum-invoice-summary').html(
                _datatableTemplates.defaultCell(`L: ${sumInvoiceSummaryPaid}, BL: ${sumInvoiceSummaryNotPaid}, T: ${sumInvoiceSummaryTotal}`)
            );
        },
        filter: function() {
            this.reload();
        },
        search: function(e) {
            if (e.key == 'Enter')
                this.reload();
        },
    }

    let dtHistory = null;
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
                order: [[0, 'asc']],
                stateSave: false,
                columns: [
                    {
                        data: 'prrb_id',
                        render: (data) => {
                            return this.template.defaultCell(data, {
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
                        data: 'prrb_due_date',
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
                            if(!data) return '-';
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
                buttons: _datatableBtnExportTemplate({
                    btnTypes: ['print', 'csv', 'excel', 'pdf', 'copy'],
                    exportColumns: [0, 1, 2, 3, 4]
                }),
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

    function searchDataHistory(event) {
        if (event.key == 'Enter') {
            var find = $('#searchFilterHistory').val();
            $('#searchFilterHistory').val('');
            dtHistory.clear().destroy();
            _oldStudentPaymentHistoryTable.init(student, find);
        }
    }

    function assignFilter(selector, postfix = null) {
        let value = $(selector).val();

        if (value === '#ALL')
            return null;

        if (value && postfix)
            value = `${value}${postfix}`;

        return value;
    }
</script>
@endsection

@push('laravel-component-setup')
    <script>
        $(function() {
            setupFilters.studentBatch();
            setupFilters.studyprogram();
        });

        const setupFilters = {
            studentBatch: async function() {
                const schoolYears = await $.get({
                    async: true,
                    url: `${_baseURL}/api/payment/resource/school-year`,
                    data: {semester: '1'},
                });

                const studentBatchArr = schoolYears.map(item => item.msy_year.substring(0, 4));

                const formatted = studentBatchArr.map(item => ({id: item, text: item}));

                $('#student-batch-filter').select2({
                    data: [
                        {id: '#ALL', text: "Semua Angkatan"},
                        ...formatted,
                    ],
                    minimumResultsForSearch: 6,
                });
            },
            studyprogram: async function() {
                const data = await $.get({
                    async: true,
                    url: `${_baseURL}/api/payment/resource/studyprogram`,
                });

                const formatted = data.map(item => {
                    return {
                        id: item.studyprogram_id,
                        text: item.studyprogram_type.toUpperCase() + ' ' + item.studyprogram_name,
                    };
                });

                $('#studyprogram-filter').select2({
                    data: [
                        {id: '#ALL', text: "Semua Program Studi"},
                        ...formatted,
                    ],
                    minimumResultsForSearch: 6,
                });

                $('#faculty-filter').change(async function() {
                    const facultyId = this.value;
                    const studyprograms = await $.get({
                        async: true,
                        url: `${_baseURL}/api/payment/resource/studyprogram`,
                        data: {
                            faculty: facultyId != '#ALL' ? facultyId : null,
                        },
                        processData: true,
                    });
                    const options = [
                        new Option('Semua Program Studi', '#ALL', false, false),
                        ...studyprograms.map(item => {
                            return new Option(
                                item.studyprogram_type.toUpperCase() + ' ' + item.studyprogram_name,
                                item.studyprogram_id,
                                false,
                                false,
                            );
                        })
                    ];
                    $('#studyprogram-filter').empty().append(options).trigger('change');
                });
            }
        }
    </script>
@endpush
