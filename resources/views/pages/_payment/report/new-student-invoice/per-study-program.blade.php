@extends('tpl.vuexy.master-payment')

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

@section('page_title', 'Laporan Pembayaran Tagihan Mahasiswa Baru')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('content')

@include('pages._payment.report.new-student-invoice._shortcuts', ['active' => 'per-study-program'])

<div class="card">
    <div class="card-body">
        <div class="datatable-filter multiple-row">
            <x-select-option
                title="Tahun Akademik"
                select-id="school-year-filter"
                resource-url="/api/payment/resource/school-year"
                value="msy_id"
                :default-value="$current_year->msy_id"
                :default-label="$current_year->msy_year.' '.($current_year->msy_semester == 1 ? 'Ganjil' : ($current_year->msy_semester == 2 ? 'Genap' : 'Antara')).' (Aktif)'"
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
                <select id="studyprogram-filter" class="form-select"></select>
            </div>
            <x-select-option
                title="Tipe Perkuliahan"
                select-id="lecture-type-filter"
                resource-url="/api/payment/resource/lecture-type"
                value="mlt_id"
                label-template=":mlt_name"
                :label-template-items="['mlt_name']"
            />
            <div class="d-flex align-items-end">
                <button onclick="_newStudentInvoiceTable.reload()" class="btn btn-info text-nowrap">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="new-student-invoice-table" class="table table-striped align-top">
        <thead>
            <tr>
                <th>Aksi</th>
                <th>Tahun Akademik Pendaftaran</th>
                <th>Periode / Jalur Pendaftaran</th>
                <th>Program Studi / Fakultas</th>
                <th>Total Komponen Tagihan</th>
                <th>Total Denda</th>
                <th>Total Beasiswa</th>
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
                <th colspan="4">Total Keseluruhan</th>
                <th id="sum-invoice-component"></th>
                <th id="sum-invoice-penalty"></th>
                <th id="sum-invoice-scholarship"></th>
                <th id="sum-invoice-discount"></th>
                <th id="sum-admin-cost"></th>
                <th id="sum-final-bill"></th>
                <th id="sum-total-paid"></th>
                <th id="sum-total-not-paid"></th>
                <th id="sum-payment-status"></th>
            </tr>
        </tfoot>
    </table>
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
        _newStudentInvoiceTable.init();

        $('#new-student-invoice-table').on( 'column-visibility.dt', function ( e, settings, column, state ) {
            if ([4,5,6,7,8,9,10,11,12].includes(column)) {
                const data = $('#new-student-invoice-table').dataTable().api().rows({page:'current'}).data().toArray();
                _newStudentInvoiceTable.renderFooter(data);
            }
            // console.log('Column '+ column +' has changed to '+ (state ? 'visible' : 'hidden'));
        });
    })

    const _newStudentInvoiceTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#new-student-invoice-table').DataTable({
                ajax: {
                    url: _baseURL + '/api/payment/report/new-student-invoice/studyprogram/datatable',
                    data: (d) => {
                        const filters = this.getFilters();

                        if (filters.length > 0) {
                            d.withFilter = filters;
                        }
                    },
                },
                stateSave: false,
                order: [[3, 'asc']],
                columnDefs: [
                    {
                        visible: false,
                        targets: [1,4,5,6,7,8],
                    },
                    {
                        searchable: true,
                        targets: [3],
                    },
                    { searchable: false, targets: '_all' },
                    {
                        orderable: true,
                        targets: [1,3,4,5,6,7,8,9,10,11,12],
                    },
                    { orderable: false, targets: '_all' },
                ],
                columns: [
                    // 0
                    {
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
                                {
                                    text: this.template.buttonLinkCell(
                                        `${row.registration_major_name} (${row.registration_major_type.toUpperCase()} ${row.registration_major_lecture_type_name})`,
                                        {
                                            link: _baseURL + '/payment/report/new-student-invoice/student?' +
                                                $.param({
                                                    school_year_id: row.registration_year_id,
                                                    period_id: row.registration_period_id,
                                                    path_id: row.registration_path_id,
                                                    studyprogram_id: row.registration_major_id,
                                                    lecture_type_id: row.registration_major_lecture_type_id,
                                                })
                                        }
                                    ),
                                    bold: true,
                                    small: false,
                                    nowrap: true
                                },
                                {text: row.registration_faculty_name, bold: false, small: true, nowrap: true},
                            ]);
                        }
                    },
                    // 4
                    {
                        data: 'invoice_component_total_amount',
                        render: (data) => {
                            return this.template.currencyCell(data);
                        }
                    },
                    // 5
                    {
                        data: 'invoice_penalty_total_amount',
                        render: (data) => {
                            return this.template.currencyCell(data);
                        }
                    },
                    // 6
                    {
                        data: 'invoice_scholarship_total_amount',
                        render: (data) => {
                            return this.template.currencyCell(data);
                        }
                    },
                    // 7
                    {
                        data: 'invoice_discount_total_amount',
                        render: (data) => {
                            return this.template.currencyCell(data);
                        }
                    },
                    // 8
                    {
                        data: 'payment_admin_cost',
                        render: (data) => {
                            return this.template.currencyCell(data);
                        }
                    },
                    // 9
                    {
                        data: 'invoice_nominal_total',
                        render: (data) => {
                            return this.template.currencyCell(data, {bold: true, additionalClass: 'text-primary'});
                        }
                    },
                    // 10
                    {
                        data: 'payment_total_paid',
                        render: (data) => {
                            return this.template.currencyCell(data, {bold: true, additionalClass: 'text-success'});
                        }
                    },
                    // 11
                    {
                        data: 'payment_total_unpaid',
                        render: (data) => {
                            return this.template.currencyCell(data, {bold: true, additionalClass: 'text-danger'});
                        }
                    },
                    // 12
                    {
                        data: 'invoice_count',
                        render: (data, _, row) => {
                            return this.template.listCell([
                                {text: 'Total: '+row.invoice_count, bold: true, small: false, nowrap: true},
                                {text: 'Lunas: '+row.payment_status_paid_count, bold: false, small: true, nowrap: true},
                                {text: 'Belum Lunas: '+row.payment_status_not_paid_count, bold: false, small: true, nowrap: true},
                            ]);
                        }
                    },
                ],
                drawCallback: (settings) => {
                    this.renderRefreshSection();
                    const data = this.instance.rows({page:'current'}).data().toArray();
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
                        columns: [1,2,3,4,5,6,7,8,9,10,11,12],
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
                                        '/api/payment/report/new-student-invoice/studyprogram/export?' +
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
                                        '/api/payment/report/new-student-invoice/studyprogram/export?' +
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
                            <a onclick="_newStudentInvoiceTable.showRowDetailModal(event)" class="dropdown-item"><i data-feather="eye"></i>&nbsp; Detail</a>
                            <a onclick="_newStudentInvoiceTable.navigateStudentDetail(event)" class="dropdown-item"><i data-feather="file-text"></i>&nbsp; Detail Per Mahasiswa</a>
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
                sumInvoiceSummaryTotal += parseInt(row.invoice_count);
                sumInvoiceSummaryPaid += parseInt(row.payment_status_paid_count);
                sumInvoiceSummaryNotPaid += parseInt(row.payment_status_not_paid_count);
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

            $('.dataTables_scrollFoot #sum-payment-status').html(
                _datatableTemplates.defaultCell(`T: ${sumInvoiceSummaryTotal}, L: ${sumInvoiceSummaryPaid}, BL: ${sumInvoiceSummaryNotPaid}`)
            );
        },
        getFilters: function() {
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

            return filters;
        },
        renderRefreshSection: function() {
            $.get(_baseURL + '/api/payment/report/new-student-invoice/studyprogram/refresh-info')
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
        navigateStudentDetail: function(e) {
            const row = this.instance.row($(e.currentTarget).parents('tr')).data();

            window.location.href = _baseURL +
                '/payment/report/new-student-invoice/student?' +
                $.param({
                    school_year_id: row.registration_year_id,
                    period_id: row.registration_period_id,
                    path_id: row.registration_path_id,
                    studyprogram_id: row.registration_major_id,
                    lecture_type_id: row.registration_major_lecture_type_id,
                });
        }
    }

    function assignFilter(selector, prefix = null, postfix = null) {
        let value = $(selector).val();

        if (value === '#ALL')
            return null;

        if (value)
            value = `${prefix ?? ''}${value}${postfix ?? ''}`;

        return value;
    }

    async function refreshData() {
        const res = await $.ajax({
            async: true,
            url: `${_baseURL}/api/payment/report/new-student-invoice/studyprogram/refresh`,
            type: 'get',
        });

        if (res.success) {
            _toastr.success(res.message, 'Sukses');
            _newStudentInvoiceTable.reload();
            _newStudentInvoiceTable.renderRefreshSection();
        } else {
            _toastr.error(res.message, 'Gagal');
        }
    }
</script>
@endsection

@push('laravel-component-setup')
<script>
    $(async function() {
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
    });
</script>
@endpush
