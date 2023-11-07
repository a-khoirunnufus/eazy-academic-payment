@extends('tpl.vuexy.master-payment')

@section('page_title', 'Laporan Pembayaran Tagihan Pendaftar')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
<style>
    table.dataTable thead th {
        white-space: nowrap
    }
    table.dtr-details-custom td {
        padding: 10px 1.4rem;
    }
    .dtr-bs-modal .modal-dialog {
        max-width: max-content;
    }
</style>
@endsection

@section('content')

<div class="card">
    <div class="card-body">
        <div class="datatable-filter multiple-row">
            <x-select-option
                title="Tahun Akademik"
                select-id="school-year-filter"
                resource-url="/api/payment/resource/school-year"
                value="msy_id"
                :default-value="$current_year->msy_id"
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
                title="Jenis Perkuliahan"
                select-id="lecture-type-filter"
                resource-url="/api/payment/resource/lecture-type"
                value="mlt_id"
                label-template=":mlt_name"
                :label-template-items="['mlt_name']"
            />
            <div>
                <label class="form-label">Nominal Tagihan Gross</label>
                <select id="nominal-gross-filter" class="form-select"></select>
            </div>
            <div>
                <label class="form-label">Nominal Tagihan Nett</label>
                <select id="nominal-nett-filter" class="form-select"></select>
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
                <button onclick="_registrantInvoiceTable.reload()" class="btn btn-info text-nowrap">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="registrant-invoice-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Nomor Tagihan</th>
                <th>Pendaftar</th>
                <th>Komponen Tagihan</th>
                <th>Nominal Tagihan (Gross)</th>
                <th>Nominal Tagihan (Nett)</th>
                <th>Status Tagihan</th>
                <th>Tahun Akademik</th>
                <th>Periode / Jalur</th>
                <th>Pilihan 1</th>
                <th>Pilihan 2</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

@endsection

@section('js_section')
<script>
    $(function() {
        _registrantInvoiceTable.init();
    })

    const _registrantInvoiceTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#registrant-invoice-table').DataTable({
                ajax: {
                    url: _baseURL + '/api/payment/report/registrant-invoice/datatable',
                    data: function(d) {
                        d.school_year = assignFilter('#school-year-filter');
                        let filters = [];

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
                                column: 'registration_majors',
                                operator: 'ilike',
                                value: assignFilter('#faculty-filter', '%"faculty_id":"', '"%'),
                            });
                        }

                        if (assignFilter('#studyprogram-filter')) {
                            filters.push({
                                column: 'registration_majors',
                                operator: 'ilike',
                                value: assignFilter('#studyprogram-filter', '%"major_id":"', '"%'),
                            });
                        }

                        if (assignFilter('#lecture-type-filter')) {
                            filters.push({
                                column: 'registration_majors',
                                operator: 'ilike',
                                value: assignFilter('#lecture-type-filter', '%"major_lecture_type_id":"', '"%'),
                            });
                        }

                        if (assignFilter('#status-filter')) {
                            filters.push({
                                column: 'payment_status',
                                operator: '=',
                                value: assignFilter('#status-filter'),
                            });
                        }

                        const nominalGrossFilter = assignFilter('#nominal-gross-filter');
                        if (nominalGrossFilter) {
                            const addFilters = nominalGrossFilter.split('&')
                                .map(item => item.split('.'))
                                .map(item => ({
                                    column: 'invoice_nominal_gross',
                                    operator: item[0],
                                    value: item[1]
                                }));

                            filters = [...filters, ...addFilters];
                        }

                        const nominalNettFilter = assignFilter('#nominal-nett-filter');
                        if (nominalNettFilter) {
                            const addFilters = nominalNettFilter.split('&')
                                .map(item => item.split('.'))
                                .map(item => ({
                                    column: 'invoice_nominal_nett',
                                    operator: item[0],
                                    value: item[1]
                                }));

                            filters = [...filters, ...addFilters];
                        }

                        if (filters.length > 0) {
                            d.withFilter = filters;
                        }
                    },
                },
                stateSave: false,
                order: [],
                columns: [
                    {
                        data: 'invoice_id',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: (data, _, row) => {
                            return this.template.rowAction();
                        }
                    },
                    {
                        data: 'invoice_id',
                        render: (data) => {
                            return this.template.defaultCell(data, {bold: true});
                        }
                    },
                    {
                        data: 'registrant_fullname',
                        render: (data, _, row) => {
                            return this.template.listCell([
                                {text: row.registrant_fullname, bold: true, small: false, nowrap: true},
                                {text: row.registrant_number, bold: false, small: true, nowrap: true},
                            ]);
                        }
                    },
                    {
                        data: 'invoice_items',
                        searchable: false,
                        orderable: false,
                        render: (data, _, row) => {
                            if (!data) return '';
                            let jsonData = JSON.parse(unescapeHtml(data));
                            return this.template.listCell(
                                jsonData.map(item => ({
                                    text: `<span class="fw-bold">${item.component}</span> : ${Rupiah.format(item.amount)}`,
                                    bold: false,
                                    small: false,
                                    nowrap: true,
                                }))
                            );
                        }
                    },
                    {
                        data: 'invoice_nominal_gross',
                        searchable: false,
                        render: (data) => {
                            return this.template.currencyCell(data);
                        }
                    },
                    {
                        data: 'invoice_nominal_nett',
                        searchable: false,
                        render: (data) => {
                            return this.template.currencyCell(data);
                        }
                    },
                    {
                        data: 'payment_status',
                        searchable: false,
                        render: (data) => {
                            let bsColor = 'secondary';
                            if (data == 'lunas') bsColor = 'success';
                            if (data == 'belum lunas') bsColor = 'danger';
                            if (data == 'kredit') bsColor = 'warning';
                            return this.template.badgeCell(data, bsColor, {centered: false});
                        }
                    },
                    {
                        data: 'registration_year_name',
                        orderable: false,
                        render: (data) => {
                            return this.template.defaultCell(data);
                        }
                    },
                    {
                        data: 'registration_period_name',
                        orderable: false,
                        render: (data, _, row) => {
                            return this.template.listCell([
                                {text: row.registration_period_name, bold: true, small: false, nowrap: true},
                                {text: row.registration_path_name, bold: false, small: true, nowrap: true},
                            ]);
                        }
                    },
                    {
                        data: 'registration_majors',
                        searchable: false,
                        orderable: false,
                        render: (data, _, row) => {
                            if (!data) return '';
                            let jsonData = JSON.parse(unescapeHtml(data));
                            jsonData = jsonData[0];
                            // console.log(jsonData)
                            return this.template.listCell([
                                {text: `${jsonData.major_name} (${jsonData.major_type.toUpperCase()} ${jsonData.major_lecture_type_name})`, bold: true, small: false, nowrap: true},
                                {text: jsonData.faculty_name, bold: false, small: true, nowrap: true},
                            ]);
                        }
                    },
                    {
                        data: 'registration_majors',
                        searchable: false,
                        orderable: false,
                        render: (data, _, row) => {
                            if (!data) return '';
                            let jsonData = JSON.parse(unescapeHtml(data));
                            jsonData = jsonData[1];
                            if (!jsonData) return '';
                            // console.log(jsonData)
                            return this.template.listCell([
                                {text: `${jsonData.major_name} (${jsonData.major_type.toUpperCase()} ${jsonData.major_lecture_type_name})`, bold: true, small: false, nowrap: true},
                                {text: jsonData.faculty_name, bold: false, small: true, nowrap: true},
                            ]);
                        }
                    },
                ],
                responsive: {
                    details: {
                        display: DataTable.Responsive.display.modal({
                            header: function (row) {
                                return 'Detail Tagihan Pendaftar';
                            }
                        }),
                        renderer: function ( api, rowIdx, columns ) {
                            var data = $.map( columns, function ( col, i ) {
                                if (i == 0) return '';
                                return (
                                    '<tr data-dt-row="'+col.rowIndex+'" data-dt-column="'+col.columnIndex+'">'+
                                        '<td class="align-top">'+col.title+':'+'</td> '+
                                        '<td class="align-top">'+col.data+'</td>'+
                                    '</tr>'
                                );
                            } ).join('');

                            return data ?
                                $('<table class="table table-bordered dtr-details-custom mb-0" />').append( data ) :
                                false;
                        },
                        type: 'none',
                    }
                },
                drawCallback: function(settings) {
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
                // buttons: _datatableBtnExportTemplate({
                //     btnTypes: ['excel', 'csv'],
                //     exportColumns: [13,14,15,16,17,18,19,20,21,22,23,24,25]
                // }),
                initComplete: function() {}
            });
            this.implementSearchDelay();
        },
        template: {
            ..._datatableTemplates,
            rowAction: function() {
                return `
                    <button type="button" class="btn btn-light btn-sm btn-icon round">
                        <i data-feather="info"></i>
                    </button>
                `
            },
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
</script>
@endsection

@push('laravel-component-setup')
    <script>
        $(function() {
            setupFilters.studyprogram();
            setupFilters.nominalGross();
            setupFilters.nominalNett();
        });

        const setupFilters = {
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
            },
            nominalGross: async function() {
                const formatted = [
                    {id: '>.0&<=.100000', text: 'Rp1,00 sampai Rp100.000,00'},
                    {id: '>.100000&<=.500000', text: 'Rp100.001,00 sampai Rp500.000,00'},
                    {id: '>.500000&<=.1000000', text: 'Rp500.001,00 sampai Rp1.000.000,00'},
                    {id: '>.1000000', text: 'Lebih dari Rp1.000.001,00'},
                ];

                $('#nominal-gross-filter').select2({
                    data: [
                        {id: '#ALL', text: "Semua Nominal"},
                        ...formatted,
                    ],
                    minimumResultsForSearch: 6,
                });
            },
            nominalNett: async function() {
                const formatted = [
                    {id: '>.0&<=.100000', text: 'Rp1,00 sampai Rp100.000,00'},
                    {id: '>.100000&<=.500000', text: 'Rp100.001,00 sampai Rp500.000,00'},
                    {id: '>.500000&<=.1000000', text: 'Rp500.001,00 sampai Rp1.000.000,00'},
                    {id: '>.1000000', text: 'Lebih dari Rp1.000.001,00'},
                ];

                $('#nominal-nett-filter').select2({
                    data: [
                        {id: '#ALL', text: "Semua Nominal"},
                        ...formatted,
                    ],
                    minimumResultsForSearch: 6,
                });
            },

        }
    </script>
@endpush
