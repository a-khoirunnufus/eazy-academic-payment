@extends('tpl.vuexy.master-payment')

@section('css_section')
    <style>
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
    </style>
@endsection

@section('page_title', 'Laporan Pembayaran Tagihan Mahasiswa Lama')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('content')

@include('pages._payment.report.old-student-invoice._shortcuts', ['active' => 'per-study-program'])

<div class="card">
    <div class="card-body">
        <div class="datatable-filter one-row">
            <x-select-option
                title="Tahun Akademik"
                select-id="school-year-filter"
                resource-url="/api/payment/resource/school-year"
                value="msy_code"
                :default-value="$current_year->msy_code"
                :default-label="$current_year->msy_year.' '.($current_year->msy_semester == 1 ? 'Ganjil' : ($current_year->msy_semester == 2 ? 'Genap' : 'Antara'))"
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
            <div class="d-flex align-items-end">
                <button onclick="_oldStudentInvoiceTable.filter()" class="btn btn-info text-nowrap">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="old-student-invoice-table" class="table table-striped align-top">
        <thead>
            <tr>
                <th rowspan="2">Tahun Akademik</th>
                <th rowspan="2">Program Studi<br>Fakultas</th>
                <th rowspan="2">Jumlah Tagihan</th>
                <th colspan="4" class="text-center">Rincian</th>
                <th rowspan="2">Biaya Admin</th>
                <th rowspan="2">
                    Total Final Tagihan<br>
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
                <th colspan="2">Total Keseluruhan</th>
                <th id="sum-invoice-summary"></th>
                <th id="sum-invoice-component"></th>
                <th id="sum-invoice-penalty"></th>
                <th id="sum-invoice-scholarship"></th>
                <th id="sum-invoice-discount"></th>
                <th id="sum-admin-cost"></th>
                <th id="sum-final-bill"></th>
                <th id="sum-total-paid"></th>
                <th id="sum-total-not-paid"></th>
            </tr>
        </tfoot>
    </table>
</div>
@endsection

@section('js_section')
    <script>
        $(function() {
            _oldStudentInvoiceTable.init();
        })

        const _oldStudentInvoiceTable = {
            ..._datatable,
            init: function() {
                this.instance = $('#old-student-invoice-table').DataTable({
                    ajax: {
                        url: _baseURL + '/api/report/old-student-invoice/studyprogram',
                        data: function(d) {
                            d.school_year = assignFilter('#school-year-filter');
                            d.filters = [
                                {
                                    column: 'faculty_id',
                                    operator: '=',
                                    value: assignFilter('#faculty-filter'),
                                },
                                {
                                    column: 'studyprogram_id',
                                    operator: '=',
                                    value: assignFilter('#studyprogram-filter'),
                                },
                            ];
                        },
                    },
                    stateSave: false,
                    order: [],
                    columns: [
                        // 0
                        {
                            data: 'school_year.msy_code',
                            render: (data, _, row) => {
                                return this.template.titleWithSubtitleCell(
                                    row.school_year.msy_year,
                                    row.school_year.msy_semester == 1 ? 'Ganjil'
                                        : row.school_year.msy_semester == 2 ? 'Genap'
                                            : 'Antara'
                                );
                            }
                        },
                        // 1
                        {
                            data: 'studyprogram_name',
                            render: (data, _, row) => {
                                return this.template.titleWithSubtitleCell(
                                    this.template.buttonLinkCell(
                                        `${row.studyprogram_name} (${row.studyprogram_type.toUpperCase()})`,
                                        {link: _baseURL + '/payment/report/old-student-invoice/student?school_year=' + assignFilter('#school-year-filter') + '&studyprogram=' + row.studyprogram_id},
                                        {additionalClass: 'd-inline-block'}
                                    ),
                                    row.faculty.faculty_name
                                );
                            }
                        },
                        // 2
                        {
                            data: 'invoice_summary.total',
                            render: (data, _, row) => {
                                return this.template.listCell([
                                    {text: 'Lunas: '+row.invoice_summary.paid_off, bold: true, small: false, nowrap: true},
                                    {text: 'Belum Lunas: '+row.invoice_summary.not_paid_off, bold: true, small: false, nowrap: true},
                                    {text: 'Total: '+row.invoice_summary.total, bold: true, small: false, nowrap: true},
                                ]);
                            }
                        },
                        // 3
                        {
                            data: 'invoice_component',
                            render: (data) => {
                                return this.template.currencyCell(data);
                            }
                        },
                        // 4
                        {
                            data: 'invoice_penalty',
                            render: (data) => {
                                return this.template.currencyCell(data);
                            }
                        },
                        // 5
                        {
                            data: 'invoice_scholarship',
                            render: (data) => {
                                return this.template.currencyCell(data);
                            }
                        },
                        // 6
                        {
                            data: 'invoice_discount',
                            render: (data) => {
                                return this.template.currencyCell(data);
                            }
                        },
                        // 7
                        {
                            data: 'admin_cost',
                            render: (data) => {
                                return this.template.currencyCell(data);
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
                        {
                            name: 'exp_school_year',
                            title: 'Tahun Akademik',
                            visible: false,
                            render: (data, _, row) => {
                                return `${row.school_year.msy_year} ${
                                    row.school_year.msy_semester == 1 ? 'Ganjil'
                                        : row.school_year.msy_semester == 2 ? 'Genap'
                                            : 'Antara'
                                }`;
                            }
                        },
                        {
                            name: 'exp_faculty',
                            title: 'Fakultas',
                            data: 'faculty.faculty_name',
                            visible: false,
                        },
                        {
                            name: 'exp_studyprogram',
                            title: 'Program Studi',
                            visible: false,
                            render: (data, _, row) => {
                                return `${row.studyprogram_type.toUpperCase()} ${row.studyprogram_name}`;
                            }
                        },
                        {
                            name: 'exp_count_invoice_paid_off',
                            title: 'Jumlah Tagihan Lunas',
                            data: 'invoice_summary.paid_off',
                            visible: false,
                        },
                        {
                            name: 'exp_count_invoice_not_paid_off',
                            title: 'Jumlah Tagihan Belum Lunas',
                            data: 'invoice_summary.not_paid_off',
                            visible: false,
                        },
                        {
                            name: 'exp_count_invoice_total',
                            title: 'Jumlah Tagihan Total',
                            data: 'invoice_summary.total',
                            visible: false,
                        },
                        {
                            name: 'exp_invoice_component_nominal',
                            title: 'Total Nominal Tagihan',
                            data: 'invoice_component',
                            visible: false,
                        },
                        {
                            name: 'exp_invoice_penalty_nominal',
                            title: 'Total Nominal Denda',
                            data: 'invoice_penalty',
                            visible: false,
                        },
                        {
                            name: 'exp_invoice_scholarship_nominal',
                            title: 'Total Nominal Beasiswa',
                            data: 'invoice_scholarship',
                            visible: false,
                        },
                        {
                            name: 'exp_invoice_discount_nominal',
                            title: 'Total Nominal Potongan',
                            data: 'invoice_discount',
                            visible: false,
                        },
                        {
                            name: 'exp_admin_cost_nominal',
                            title: 'Total Biaya Admin',
                            data: 'admin_cost',
                            visible: false,
                        },
                        {
                            name: 'exp_invoice_final_bill_nominal',
                            title: 'Total Nominal Final Tagihan',
                            data: 'final_bill',
                            visible: false,
                        },
                        {
                            name: 'exp_total_paid_nominal',
                            title: 'Nominal Terbayar',
                            data: 'total_paid',
                            visible: false,
                        },
                        {
                            name: 'exp_total_not_paid_nominal',
                            title: 'Nominal Belum Dibayar',
                            data: 'total_not_paid',
                            visible: false,
                        }
                    ],
                    drawCallback: (settings) => {
                        const data = $('#old-student-invoice-table').dataTable().api().rows({page:'current'}).data().toArray();
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
                        exportColumns: [11,12,13,14,15,16,17,18,19,20,21,22,23,24]
                    }),
                    initComplete: () => {}
                });

                this.implementSearchDelay();
            },
            template: _datatableTemplates,
            renderFooter: function(data) {
                let sumInvoiceSummaryTotal = 0;
                let sumInvoiceSummaryPaid = 0;
                let sumInvoiceSummaryNotPaid = 0;
                let sumInvoiceComponent = 0;
                let sumInvoicePenalty = 0;
                let sumInvoiceScholarship = 0;
                let sumInvoiceDiscount = 0;
                let sumAdminCost = 0;
                let sumFinalBill = 0;
                let sumTotalPaid = 0;
                let sumTotalNotPaid = 0;

                data.forEach((studyprogram) => {
                    sumInvoiceSummaryTotal += studyprogram.invoice_summary.total;
                    sumInvoiceSummaryPaid += studyprogram.invoice_summary.paid_off;
                    sumInvoiceSummaryNotPaid += studyprogram.invoice_summary.not_paid_off;
                    sumInvoiceComponent += studyprogram.invoice_component;
                    sumInvoicePenalty += studyprogram.invoice_penalty;
                    sumInvoiceScholarship += studyprogram.invoice_scholarship;
                    sumInvoiceDiscount += studyprogram.invoice_discount;
                    sumAdminCost += studyprogram.admin_cost;
                    sumFinalBill += studyprogram.final_bill;
                    sumTotalPaid += studyprogram.total_paid;
                    sumTotalNotPaid += studyprogram.total_not_paid;
                });

                $('.dataTables_scrollFoot #sum-invoice-summary').html(
                    _datatableTemplates.defaultCell(`L: ${sumInvoiceSummaryPaid}, BL: ${sumInvoiceSummaryNotPaid}, T: ${sumInvoiceSummaryTotal}`)
                );

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
            },
            filter: function() {
                this.reload();
            },
            search: function(e) {
                if (e.key == 'Enter')
                    this.reload();
            },
        }

        function assignFilter(selector) {
            const value = $(selector).val();
            if (value === '#ALL') return null;
            return value;
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
