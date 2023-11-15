@extends('tpl.vuexy.master-payment')

@section('page_title', 'Laporan Pembayaran Tagihan Mahasiswa Baru')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
<style>
    table.dataTable thead th {
        white-space: nowrap
    }
    table.dtr-details-custom td {
        padding: 10px 0;
    }
    table.dtr-details-custom td > * {
        padding-left: 0;
        padding-right: 0;
    }
    .dtr-bs-modal .modal-dialog {
        max-width: max-content;
    }

    .buttons-columnVisibility {
        display: block;
        width: 100%;
        padding: 0.65rem 1.28rem;
        clear: both;
        font-weight: 400;
        color: #6e6b7b;
        text-align: inherit;
        white-space: nowrap;
        background-color: transparent;
        border: 0;
    }
    .buttons-columnVisibility.active:after {
        position: absolute;
        right: 1em;
        content: "✓";
        color: inherit;
    }
    .buttons-columnVisibility:hover {
        color: #356cff;
        background-color: #356cff1f;
    }

    .dt-button.buttons-collection {
        display: inline-block;
        font-weight: 400;
        line-height: 1;
        text-align: center;
        vertical-align: middle;
        cursor: pointer;
        user-select: none;
        background-color: transparent;
        padding: 0.786rem 1.5rem;
        font-size: 1rem;
        border-radius: 0.358rem;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, background 0s, border 0s;
        box-shadow: none;
        font-weight: 500;
        border: 1px solid #d8d6de !important;
        color: #82868b;
    }

    .custom-actions {
        margin: 1rem 0 0.5rem 0;
    }
</style>
@endsection

@section('content')

@include('pages._payment.report.new-student-invoice._shortcuts', ['active' => 'per-student'])

<div class="card">
    <div class="card-body">
        <div class="datatable-filter multiple-row">
            <x-select-option
                title="Tahun Akademik Pendaftaran"
                select-id="school-year-filter"
                resource-url="/api/payment/resource/school-year"
                value="msy_id"
                :default-value="$year->msy_id"
                :default-label="$year->msy_year.' '.($year->msy_semester == 1 ? 'Ganjil' : ($year->msy_semester == 2 ? 'Genap' : 'Antara')).' (Aktif)'"
                label-template=":msy_year :msy_semester"
                :label-template-items="['msy_year', [
                    'key' => 'msy_semester',
                    'mapping' => [
                        '1' => 'Ganjil',
                        '2' => 'Genap',
                        '3' => 'Antara',
                    ],
                ]]"
                without-all-option="0"
            />
            <x-select-option
                title="Periode Masuk"
                select-id="period-filter"
                resource-url="/api/payment/resource/registration-period"
                value="period_id"
                :default-value="$period?->period_id"
                :default-label="$period?->period_name"
                label-template=":period_name"
                :label-template-items="['period_name']"
            />
            <x-select-option
                title="Jalur Masuk"
                select-id="path-filter"
                resource-url="/api/payment/resource/registration-path"
                value="path_id"
                :default-value="$path?->path_id"
                :default-label="$path?->path_name"
                label-template=":path_name"
                :label-template-items="['path_name']"
            />
            <x-select-option
                title="Fakultas"
                select-id="faculty-filter"
                resource-url="/api/payment/resource/faculty"
                value="faculty_id"
                :default-value="$faculty?->faculty_id"
                :default-label="$faculty?->faculty_name"
                label-template=":faculty_name"
                :label-template-items="['faculty_name']"
            />
            <div>
                <label class="form-label">Program Studi</label>
                <select id="studyprogram-filter" class="form-select">
                    @if($studyprogram)
                        <option value="{{ $studyprogram->studyprogram_id }}" selected>
                            {{ strtoupper($studyprogram->studyprogram_type).' '.$studyprogram->studyprogram_name }}
                        </option>
                    @endif
                </select>
            </div>
            <x-select-option
                title="Tipe Perkuliahan"
                select-id="lecture-type-filter"
                resource-url="/api/payment/resource/lecture-type"
                value="mlt_id"
                :default-value="$lecture_type?->mlt_id"
                :default-label="$lecture_type?->mlt_name"
                label-template=":mlt_name"
                :label-template-items="['mlt_name']"
            />
            <div>
                <label class="form-label">Status Pembayaran</label>
                <select id="status-filter" class="form-select select2">
                    <option value="#ALL" selected>Semua Status Pembayaran</option>
                    <option value="lunas">Lunas</option>
                    <option value="belum lunas">Belum Lunas</option>
                    <option value="kredit">Kredit</option>
                </select>
            </div>
            <div class="d-flex align-items-end">
                <button onclick="_newStudentInvoiceDetailTable.reload()" class="btn btn-info text-nowrap">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="new-student-invoice-detail-table" class="table table-striped" style="width: 100%">
        <thead>
            <tr>
                <th>Aksi</th>
                <th>Tahun Akademik Pendaftaran</th>
                <th>Periode / Jalur Pendaftaran</th>
                <th>Program Studi / Fakultas</th>
                <th>Nomor Tagihan</th>
                <th>Nama / Nomor Pendaftar</th>
                <th>Detail Komponen Tagihan</th>
                <th>Total Komponen Tagihan</th>
                <th>Detail Denda</th>
                <th>Total Denda</th>
                <th>Detail Beasiswa</th>
                <th>Total Beasiswa</th>
                <th>Detail Potongan</th>
                <th>Total Potongan</th>
                <th>Biaya Admin</th>
                <th>Total Final Tagihan</th>
                <th>Terbayar</th>
                <th>Piutang</th>
                <th>Status Pembayaran</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <th colspan="6">Total Keseluruhan</th>
                <th></th>
                <th id="sum-invoice-component"></th>
                <th></th>
                <th id="sum-invoice-penalty"></th>
                <th></th>
                <th id="sum-invoice-scholarship"></th>
                <th></th>
                <th id="sum-invoice-discount"></th>
                <th id="sum-admin-cost"></th>
                <th id="sum-final-bill"></th>
                <th id="sum-total-paid"></th>
                <th id="sum-total-not-paid"></th>
                <th id="sum-invoice-summary"></th>
            </tr>
        </tfoot>
    </table>
</div>

<div class="modal" id="payment-bill-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: max-content">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cicilan Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <table id="payment-bill-table" class="table table-striped">
                <thead>
                    <tr>
                        <th>Nomor Cicilan</th>
                        <th>Nominal</th>
                        <th>Tenggat Pembayaran</th>
                        <th>Tanggal Pembayaran</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade dtr-bs-modal" id="row-detail-modal" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Tagihan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="custom-body"></div>
        </div>
    </div>
</div>
@endsection

@section('js_section')
<script src="{{ url('plugins/datatable/buttons.colVis.min.js') }}"></script>
<script src="{{ url('plugins/datatable/column().title().min.js') }}"></script>
<script>
    $(function() {
        _newStudentInvoiceDetailTable.init();

        $('#new-student-invoice-detail-table').on( 'column-visibility.dt', function ( e, settings, column, state ) {
            if ([4,6,8,10,11,12,13,14,15].includes(column)) {
                const data = $('#new-student-invoice-detail-table').dataTable().api().rows({page:'current'}).data().toArray();
                _newStudentInvoiceDetailTable.renderFooter(data);
            }
            // console.log('Column '+ column +' has changed to '+ (state ? 'visible' : 'hidden'));
        });
    })

    const _newStudentInvoiceDetailTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#new-student-invoice-detail-table').DataTable({
                ajax: {
                    url: _baseURL + '/api/payment/report/new-student-invoice/student/datatable',
                    data: (d) => {
                        const filters = this.getFilters();

                        if (filters.length > 0) {
                            d.withFilter = filters;
                        }
                    },
                },
                stateSave: false,
                order: [[4, 'asc']],
                columnDefs: [
                    {
                        visible: false,
                        targets: this.getColumnsIndexesByName([
                            'registration_year_name',
                            'registration_period_path',
                            'registration_major_faculty',
                            'invoice_component_items',
                            'invoice_component_total_amount',
                            'invoice_penalty_items',
                            'invoice_penalty_total_amount',
                            'invoice_scholarship_items',
                            'invoice_scholarship_total_amount',
                            'invoice_discount_items',
                            'invoice_discount_total_amount',
                            'payment_admin_cost',
                            'inv.registrant_fullname',
                            'inv.registrant_number',
                        ]),
                    },
                    {
                        searchable: true,
                        targets: this.getColumnsIndexesByName([
                            'inv.registrant_fullname',
                            'inv.registrant_number',
                        ]),
                    },
                    { searchable: false, targets: '_all' },
                    {
                        orderable: true,
                        targets: this.getColumnsIndexesByName([
                            'registration_year_name',
                            'registration_major_faculty',
                            'invoice_id',
                            'registrant',
                            'invoice_component_total_amount',
                            'invoice_penalty_total_amount',
                            'invoice_scholarship_total_amount',
                            'invoice_discount_total_amount',
                            'payment_admin_cost',
                            'invoice_nominal_total',
                            'payment_total_paid',
                            'payment_total_unpaid',
                        ]),
                    },
                    { orderable: false, targets: '_all' },
                ],
                columns: [
                    // 0
                    {
                        data: 'invoice_id',
                        render: (data, _, row) => {
                            return this.template.rowAction();
                        }
                    },
                    // 1
                    {
                        data: 'registration_year_name',
                        render: (data) => {
                            return this.template.defaultCell(data);
                        }
                    },
                    // 2
                    {
                        data: 'registration_period_name',
                        render: (data, _, row) => {
                            return this.template.listCell([
                                {text: row.registration_period_name, bold: true, small: false, nowrap: true},
                                {text: row.registration_path_name, bold: false, small: true, nowrap: true},
                            ]);
                        }
                    },
                    // 3
                    {
                        data: 'registration_major_name',
                        render: (data, _, row) => {
                            return this.template.listCell([
                                {text: `${row.registration_major_name} (${row.registration_major_type.toUpperCase()} ${row.registration_major_lecture_type_name})`, bold: true, small: false, nowrap: true},
                                {text: row.registration_faculty_name, bold: false, small: true, nowrap: true},
                            ]);
                        }
                    },
                    // 4
                    {
                        data: 'invoice_id',
                        render: (data, _, row) => {
                            return this.template.defaultCell(data);
                        }
                    },
                    // 5
                    {
                        data: 'registrant_fullname',
                        render: (data, _, row) => {
                            return this.template.listCell([
                                {text: row.registrant_fullname, bold: true, small: false, nowrap: true},
                                {text: row.registrant_number, bold: false, small: true, nowrap: true},
                            ]);
                        }
                    },
                    // 6
                    {
                        data: 'invoice_component_items',
                        render: (data, _, row) => {
                            if (!data) return this.template.defaultCell('-');

                            const list = JSON.parse(unescapeHtml(data));

                            if (list.length == 0) return this.template.defaultCell('-');

                            return this.template.listCell(
                                list.map(item => ({
                                    text: this.template.titleWithSubtitleCell(item.name, Rupiah.format(item.amount)),
                                    bold: false,
                                    small: false,
                                    nowrap: true
                                }))
                            );
                        }
                    },
                    // 7
                    {
                        data: 'invoice_component_total_amount',
                        render: (data, _, row) => {
                            if (!data) data = 0;

                            return this.template.currencyCell(data);
                        }
                    },
                    // 8
                    {
                        data: 'invoice_penalty_items',
                        render: (data, _, row) => {
                            if (!data) return this.template.defaultCell('-');

                            const list = JSON.parse(unescapeHtml(data));

                            if (list.length == 0) return this.template.defaultCell('-');

                            return this.template.listCell(
                                list.map(item => ({
                                    text: this.template.titleWithSubtitleCell(item.name, Rupiah.format(item.amount)),
                                    bold: false,
                                    small: false,
                                    nowrap: true
                                }))
                            );
                        }
                    },
                    // 9
                    {
                        data: 'invoice_penalty_total_amount',
                        render: (data, _, row) => {
                            if (!data) data = 0;

                            return this.template.currencyCell(data);
                        }
                    },
                    // 10
                    {
                        data: 'invoice_scholarship_items',
                        render: (data, _, row) => {
                            if (!data) return this.template.defaultCell('-');

                            const list = JSON.parse(unescapeHtml(data));

                            if (list.length == 0) return this.template.defaultCell('-');

                            return this.template.listCell(
                                list.map(item => ({
                                    text: this.template.titleWithSubtitleCell(item.name, Rupiah.format(item.amount)),
                                    bold: false,
                                    small: false,
                                    nowrap: true
                                }))
                            );
                        }
                    },
                    // 11
                    {
                        data: 'invoice_scholarship_total_amount',
                        render: (data, _, row) => {
                            if (!data) data = 0;

                            return this.template.currencyCell(data);
                        }
                    },
                    // 12
                    {
                        data: 'invoice_discount_items',
                        render: (data, _, row) => {
                            if (!data) return this.template.defaultCell('-');

                            const list = JSON.parse(unescapeHtml(data));

                            if (list.length == 0) return this.template.defaultCell('-');

                            return this.template.listCell(
                                list.map(item => ({
                                    text: this.template.titleWithSubtitleCell(item.name, Rupiah.format(item.amount)),
                                    bold: false,
                                    small: false,
                                    nowrap: true
                                }))
                            );
                        }
                    },
                    // 13
                    {
                        data: 'invoice_discount_total_amount',
                        render: (data, _, row) => {
                            if (!data) data = 0;

                            return this.template.currencyCell(data);
                        }
                    },
                    // 14
                    {
                        data: 'payment_admin_cost',
                        render: (data) => {
                            return this.template.currencyCell(data);
                        }
                    },
                    // 15
                    {
                        data: 'invoice_nominal_total',
                        render: (data) => {
                            return this.template.currencyCell(data, {bold: true, additionalClass: 'text-primary'});
                        }
                    },
                    // 16
                    {
                        data: 'payment_total_paid',
                        render: (data) => {
                            return this.template.currencyCell(data, {bold: true, additionalClass: 'text-success'});
                        }
                    },
                    // 17
                    {
                        data: 'payment_total_unpaid',
                        render: (data, _, row) => {
                            return this.template.currencyCell(data, {bold: true, additionalClass: 'text-danger'});
                        }
                    },
                    // 18
                    {
                        data: 'payment_status',
                        render: (data) => {
                            let bsColor = 'secondary';
                            if (data == 'lunas') bsColor = 'success';
                            if (data == 'belum lunas') bsColor = 'danger';
                            if (data == 'kredit') bsColor = 'warning';
                            return this.template.badgeCell(data, bsColor, {centered: false});
                        }
                    },
                    // searchable columns
                    { data: 'registrant_fullname', searchable: true },
                    { data: 'registrant_number', searchable: true },
                ],
                drawCallback: (settings) => {
                    this.renderRefreshSection();
                    const data = $('#new-student-invoice-detail-table').dataTable().api().rows({page:'current'}).data().toArray();
                    this.renderFooter(data);
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
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"custom-actions">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>' +
                    'tr' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                buttons: [
                    {
                        extend: 'colvis',
                        columns: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18],
                    },
                    {
                        extend: 'collection',
                        className: 'btn btn-outline-secondary dropdown-toggle',
                        text: feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + 'Export',
                        buttons: [
                            {
                                text: feather.icons['file-text'].toSvg({class: 'font-small-4 me-50'}) + 'Csv',
                                className: 'dropdown-item',
                                action: () => {
                                    window.open(
                                        _baseURL +
                                        '/api/payment/report/new-student-invoice/student/export?' +
                                        'type=csv&' +
                                        $.param({ filter: this.getFilters() })
                                    );
                                }
                            },
                            {
                                text: feather.icons['file'].toSvg({class: 'font-small-4 me-50'}) + 'Excel',
                                className: 'dropdown-item',
                                action: () => {
                                    window.open(
                                        _baseURL +
                                        '/api/payment/report/new-student-invoice/student/export?' +
                                        'type=excel&' +
                                        $.param({ filter: this.getFilters() })
                                    );
                                }
                            },
                        ],
                    }
                ],
                initComplete: () => {
                    $('.buttons-colvis').addClass('btn btn-outline-secondary dropdown-toggle');
                    $('.buttons-colvis > span').text('Tampilkan kolom');
                }
            });

            this.implementSearchDelay();
        },
        template: {
            ..._datatableTemplates,
            rowAction: function() {
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a onclick="_newStudentInvoiceDetailTable.showRowDetailModal(event)" class="dropdown-item"><i data-feather="eye"></i>&nbsp; Detail</a>
                            <a onclick="_newStudentInvoiceDetailTable.showBillDetailModal(event)" class="dropdown-item"><i data-feather="file-text"></i>&nbsp; Pembayaran Cicilan</a>
                        </div>
                    </div>
                `
            },
        },
        renderFooter: function(data) {
            let sumInvoiceComponent = 0;
            let sumInvoicePenalty = 0;
            let sumInvoiceScholarship = 0;
            let sumInvoiceDiscount = 0;
            let sumAdminCost = 0;
            let sumFinalBill = 0;
            let sumTotalPaid = 0;
            let sumTotalNotPaid = 0;
            let sumInvoiceSummaryTotal = 0;
            let sumInvoiceSummaryPaid = 0;
            let sumInvoiceSummaryNotPaid = 0;


            data.forEach((row) => {

                sumInvoiceComponent += parseInt(row.invoice_component_total_amount);
                sumInvoicePenalty += parseInt(row.invoice_penalty_total_amount);
                sumInvoiceScholarship += parseInt(row.invoice_scholarship_total_amount);
                sumInvoiceDiscount += parseInt(row.invoice_discount_total_amount);
                sumAdminCost += parseInt(row.payment_admin_cost);
                sumFinalBill += parseInt(row.invoice_nominal_total);
                sumTotalPaid += parseInt(row.payment_total_paid);
                sumTotalNotPaid += parseInt(row.payment_total_unpaid);

                sumInvoiceSummaryTotal++;
                if (row.payment_status == 'lunas') {
                    sumInvoiceSummaryPaid++;
                }
                if (row.payment_status == 'belum lunas' || row.payment_status == 'kredit') {
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

            $('.dataTables_scrollFoot #sum-admin-cost').html(
                _datatableTemplates.currencyCell(sumAdminCost)
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
                _datatableTemplates.defaultCell(`T: ${sumInvoiceSummaryTotal}, L: ${sumInvoiceSummaryPaid}, BL: ${sumInvoiceSummaryNotPaid}`)
            );
        },
        renderRefreshSection: function() {
            $.get(_baseURL + '/api/payment/report/new-student-invoice/student/refresh-info')
                .done(res => {
                    $('.custom-actions').html(`
                        <div>
                            <button onclick="refreshData()" class="btn btn-outline-secondary btn-sm" style="margin-bottom: 5px">
                                <i data-feather="refresh-cw"></i> &nbsp; Refresh Data
                            </button>
                            <small class="d-block">Terakhir diperbaharui pada ${moment(res.last_refresh_time).format('DD/MM/YYYY HH:mm')}</small>
                        </div>
                    `);
                    feather.replace();
                });
        },
        getColumnsIndexesByName: (nameArr) => {
            const masterCol = [
                'action',
                'registration_year_name',
                'registration_period_path',
                'registration_major_faculty',
                'invoice_id',
                'registrant',
                'invoice_component_items',
                'invoice_component_total_amount',
                'invoice_penalty_items',
                'invoice_penalty_total_amount',
                'invoice_scholarship_items',
                'invoice_scholarship_total_amount',
                'invoice_discount_items',
                'invoice_discount_total_amount',
                'payment_admin_cost',
                'invoice_nominal_total',
                'payment_total_paid',
                'payment_total_unpaid',
                'payment_status',
                'inv.registrant_fullname',
                'inv.registrant_number',
                'inv.payment_status',
                'inv.registration_period_name',
                'inv.registration_path_name',
                'inv.registration_faculty_name',
                'inv.registration_major_name',
                'inv.registration_major_type',
                'inv.registration_major_lecture_type_name',
            ];

            return nameArr.map(name => {
                return masterCol.indexOf(name);
            });
        },
        getFilters: () => {
            let filters = [];

            if (assignFilter('#school-year-filter')) {
                filters.push({
                    column: 'registration_year_id',
                    operator: '=',
                    value: assignFilter('#school-year-filter'),
                });
            }

            if (assignFilter('#period-filter')) {
                filters.push({
                    column: 'registration_period_id',
                    operator: '=',
                    value: assignFilter('#period-filter'),
                });
            }

            if (assignFilter('#path-filter')) {
                filters.push({
                    column: 'registration_path_id',
                    operator: '=',
                    value: assignFilter('#path-filter'),
                });
            }

            if (assignFilter('#faculty-filter')) {
                filters.push({
                    column: 'registration_faculty_id',
                    operator: '=',
                    value: assignFilter('#faculty-filter'),
                });
            }

            if (assignFilter('#studyprogram-filter')) {
                filters.push({
                    column: 'registration_major_id',
                    operator: '=',
                    value: assignFilter('#studyprogram-filter'),
                });
            }

            if (assignFilter('#lecture-type-filter')) {
                filters.push({
                    column: 'registration_major_lecture_type_id',
                    operator: '=',
                    value: assignFilter('#lecture-type-filter'),
                });
            }

            if (assignFilter('#status-filter')) {
                filters.push({
                    column: 'payment_status',
                    operator: '=',
                    value: assignFilter('#status-filter'),
                });
            }

            return filters;
        },
        showRowDetailModal: function(e) {
            const row = this.instance.row($(e.currentTarget).parents('tr'));
            const rowIdx = row.index();

            let html = '';

            row.columns().every(function(colIdx) {
                if (colIdx == 0 || colIdx > 18) return;

                const title = this.title();
                const data = this.nodes()[rowIdx].innerHTML;

                html += `
                    <tr data-dt-row="${rowIdx}" data-dt-column="${colIdx}">
                        <td class="align-top px-1" style="width: 200px">${title}</td>
                        <td class="align-top px-0" style="width: 5px">:</td>
                        <td class="align-top px-1" style="min-width: 400px; max-width: fit-content">${data}</td>
                    </tr>
                `;
            });

            html = $('<table class="table table-bordered dtr-details-custom mb-0" />').append(html);

            $('#row-detail-modal .custom-body').html(html);
            $('#row-detail-modal').modal('show');
        },
        showBillDetailModal: function(e) {
            const data = _newStudentInvoiceDetailTable.instance.row($(e.currentTarget).parents('tr')).data();

            selectedInvoiceId = data.invoice_id;

            if (_billTable.instance == null) {
                _billTable.init();
            } else {
                _billTable.reload();
            }

            $('#payment-bill-modal').modal('show');
        }
    }

    let selectedInvoiceId = null;
    const _billTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#payment-bill-table').DataTable({
                ajax: {
                    url: _baseURL + '/api/payment/report/new-student-invoice/bill/datatable',
                    data: function(d) {
                        d.invoice_id = selectedInvoiceId;
                    }
                },
                order: [[0, 'asc']],
                stateSave: false,
                columns: [
                    {
                        data: 'prrb_id',
                        render: (data) => {
                            return this.template.defaultCell(data);
                        }
                    },
                    {
                        data: 'prrb_amount',
                        render: (data) => {
                            return this.template.currencyCell(data);
                        }
                    },
                    {
                        data: 'prrb_due_date',
                        render: (data) => {
                            return this.template.dateTimeCell(data);
                        }
                    },
                    {
                        data: 'prrb_paid_date',
                        render: (data) => {
                            if(!data) return '-';
                            return this.template.dateTimeCell(data);
                        }
                    },
                    {
                        name: 'payment_status',
                        data: 'prrb_status',
                        render: (data) => {
                            let bsColor = 'secondary';
                            if (data == 'lunas') bsColor = 'success';
                            if (data == 'belum lunas') bsColor = 'danger';
                            if (data == 'kredit') bsColor = 'warning';
                            return this.template.badgeCell(data, bsColor, {centered: false});
                        }
                    }
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom: '<"d-flex justify-content-end mx-1"B>tr',
                buttons: _datatableBtnExportTemplate({
                    btnTypes: ['print', 'csv', 'excel', 'pdf', 'copy'],
                    exportColumns: [0, 1, 2, 3, 4]
                }),
                initComplete: function() {}
            });
        },
        template: _datatableTemplates,
    }

    function assignFilter(selector, postfix = null) {
        let value = $(selector).val();

        if (value === '#ALL')
            return null;

        if (value && postfix)
            value = `${value}${postfix}`;

        return value;
    }

    async function refreshData() {
        const res = await $.ajax({
            async: true,
            url: `${_baseURL}/api/payment/report/new-student-invoice/student/refresh`,
            type: 'get',
        });

        if (res.success) {
            _toastr.success(res.message, 'Sukses');
            _newStudentInvoiceDetailTable.reload();
            _newStudentInvoiceDetailTable.renderRefreshSection();
        } else {
            _toastr.error(res.message, 'Gagal');
        }
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
