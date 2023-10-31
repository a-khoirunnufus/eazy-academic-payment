@extends('tpl.vuexy.master-payment')

@section('page_title', 'List Saldo Mahasiswa')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
@endsection

@section('content')

@include('pages._payment.students-balance._shortcuts', ['active' => 'index'])

<div class="card">
    <div class="card-body">
        <div class="datatable-filter multiple-row">
            <div>
                <label class="form-label">Tahun Masuk</label>
                <select id="entry-year-filter" class="form-select"></select>
            </div>
            <x-select-option
                title="Periode Masuk"
                select-id="entry-period-filter"
                resource-url="/api/payment/resource/registration-period"
                value="period_id"
                label-template=":period_name"
                :label-template-items="['period_name']"
            />
            <x-select-option
                title="Jalur Masuk"
                select-id="entry-path-filter"
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
            <div>
                <label class="form-label">Jumlah Saldo</label>
                <select id="balance-amount-filter" class="form-select"></select>
            </div>
            <div class="d-flex align-items-end">
                <button onclick="_listStudentTable.reload()" class="btn btn-info text-nowrap">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="table-list-student" class="table table-striped align-top">
        <thead>
            <tr>
                <th>Nama / NIM</th>
                <th>Detail Masuk</th>
                <th>Fakultas / Program Studi</th>
                <th>Saldo</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

@endsection


@section('js_section')
    <script>
        $(function() {
            _listStudentTable.init();
        })

        const _listStudentTable = {
            ..._datatable,
            init: function() {
                this.instance = $('#table-list-student').DataTable({
                    ajax: {
                        url: _baseURL + '/api/payment/students-balance',
                        data: function(d) {
                            let filters = [];

                            if (assignFilter('#entry-year-filter', null, '%')) {
                                filters.push({
                                    column: 'msy_year',
                                    operator: 'ilike',
                                    value: assignFilter('#entry-year-filter', null, '%'),
                                });
                            }

                            if (assignFilter('#entry-period-filter')) {
                                filters.push({
                                    column: 'period_id',
                                    operator: '=',
                                    value: assignFilter('#entry-period-filter'),
                                });
                            }

                            if (assignFilter('#entry-path-filter')) {
                                filters.push({
                                    column: 'path_id',
                                    operator: '=',
                                    value: assignFilter('#entry-path-filter'),
                                });
                            }

                            if (assignFilter('#faculty-filter')) {
                                filters.push({
                                    column: 'faculty_id',
                                    operator: '=',
                                    value: assignFilter('#faculty-filter'),
                                });
                            }

                            if (assignFilter('#studyprogram-filter')) {
                                filters.push({
                                    column: 'studyprogram_id',
                                    operator: '=',
                                    value: assignFilter('#studyprogram-filter'),
                                });
                            }

                            if (assignFilter('#lecture-type-filter')) {
                                filters.push({
                                    column: 'mlt_id',
                                    operator: '=',
                                    value: assignFilter('#lecture-type-filter'),
                                });
                            }

                            const balanceAmountFilter = assignFilter('#balance-amount-filter');
                            if (balanceAmountFilter) {
                                const addFilters = balanceAmountFilter.split('&')
                                    .map(item => item.split('.'))
                                    .map(item => ({
                                        column: 'current_balance',
                                        operator: item[0],
                                        value: item[1]
                                    }));

                                filters = [...filters, ...addFilters];
                            }

                            if (filters.length > 0) {
                                d.filters = filters;
                            }
                        },
                    },
                    stateSave: false,
                    order: [],
                    columns: [
                        // 0 fullname
                        {
                            data: 'fullname',
                            render: (data, _, row) => {
                                return this.template.listCell([
                                    {text: row.fullname, bold: true, small: false, nowrap: true},
                                    {text: row.student_id, bold: false, small: true, nowrap: true},
                                ]);
                            }
                        },
                        // 1 msy_year
                        {
                            data: 'msy_year',
                            searchable: false,
                            orderable: false,
                            render: (data, _, row) => {
                                return this.template.listCell([
                                    {text: `Tahun Masuk : ${row.msy_year?.toString().substring(0, 4) ?? '-'}`, bold: true, small: false, nowrap: true},
                                    {text: `Periode Masuk : ${row.period_name ?? '-'}`, bold: false, small: true, nowrap: true},
                                    {text: `Jalur Masuk : ${row.path_name ?? '-'}`, bold: false, small: true, nowrap: true},
                                ]);
                            }
                        },
                        // 2 faculty_name
                        {
                            data: 'faculty_name',
                            searchable: false,
                            orderable: false,
                            render: (data, _, row) => {
                                return this.template.listCell([
                                    {text: row.faculty_name, bold: true, small: false, nowrap: true},
                                    {text: `${row.studyprogram_name} (${row.studyprogram_type.toUpperCase()}${row.mlt_name ? ' '+row.mlt_name :  ''})`, bold: false, small: true, nowrap: true},
                                ]);
                            }
                        },
                        // 3 current balance
                        {
                            data: 'current_balance',
                            searchable:false,
                            render: (data) => {
                                return this.template.currencyCell(data);
                            }
                        },
                        // invisible columns
                        // 4
                        {
                            title: 'NIM',
                            data: 'student_id',
                            visible: false,
                        },
                        // 5
                        {
                            title: 'Nama',
                            data: 'fullname',
                            visible: false,
                        },
                        // 6
                        {
                            title: 'Tahun Masuk',
                            data: 'msy_year',
                            visible: false,
                        },
                        // 7
                        {
                            title: 'Periode Masuk',
                            data: 'period_name',
                            visible: false,
                        },
                        // 8
                        {
                            title: 'Jalur Masuk',
                            data: 'path_name',
                            visible: false,
                        },
                        // 9
                        {
                            title: 'Fakultas',
                            data: 'faculty_name',
                            visible: false,
                        },
                        // 10
                        {
                            title: 'Program Studi',
                            data: 'studyprogram_name',
                            visible: false,
                        },
                        // 11
                        {
                            title: 'Jenjang',
                            data: 'studyprogram_type',
                            visible: false,
                        },
                        // 12
                        {
                            title: 'Jenis Perkuliahan',
                            data: 'mlt_name',
                            visible: false,
                        },
                        // 11
                        {
                            title: 'Saldo',
                            data: 'current_balance',
                            visible: false,
                        },
                    ],
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
                    buttons: _datatableBtnExportTemplate({
                        btnTypes: ['excel', 'csv'],
                        exportColumns: [4,5,6,7,8,9,10,11]
                    }),
                    drawCallback: (settings) => {
                        feather.replace();
                    },
                    initComplete: () => {
                        $('.custom-actions').html(`
                            <div>
                                <button onclick="refreshData()" class="btn btn-primary">
                                    <i data-feather="refresh-cw"></i> &nbsp; Refresh Data
                                </button>
                            </div>
                        `);
                        feather.replace();
                    }
                });

                this.implementSearchDelay();
            },
            template: _datatableTemplates,
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
                url: `${_baseURL}/api/payment/students-balance/refresh`,
                type: 'get',
            });

            if (res.success) {
                _toastr.success(res.message, 'Sukses');
                _listStudentTable.reload();
            } else {
                _toastr.error(res.message, 'Gagal');
            }
        }
    </script>
@endsection

@push('laravel-component-setup')
    <script>
        $(function() {
            setupFilters.entryYear();
            setupFilters.studyprogram();
            setupFilters.balanceAmount();
        });

        const setupFilters = {
            entryYear: async function() {
                const schoolYears = await getRequestCache(`${_baseURL}/api/payment/resource/school-year?semester=1`);
                const entryYearArr = schoolYears.map(item => item.msy_year.substring(0, 4));
                const formatted = entryYearArr.map(item => ({id: item, text: item}));

                $('#entry-year-filter').select2({
                    data: [
                        {id: '#ALL', text: "Semua Tahun Masuk"},
                        ...formatted,
                    ],
                    minimumResultsForSearch: 6,
                });
            },
            studyprogram: async function() {
                const data = await getRequestCache(`${_baseURL}/api/payment/resource/studyprogram`);
                const formatted = data.map(item => ({
                    id: item.studyprogram_id,
                    text: item.studyprogram_type.toUpperCase() + ' ' + item.studyprogram_name,
                }));

                $('#studyprogram-filter').select2({
                    data: [
                        {id: '#ALL', text: "Semua Program Studi"},
                        ...formatted,
                    ],
                    minimumResultsForSearch: 6,
                });

                $('#faculty-filter').change(async function() {
                    const facultyId = this.value;
                    const studyprograms = await getRequestCache(`${_baseURL}/api/payment/resource/studyprogram?faculty=${facultyId != '#ALL' ? facultyId : ''}`);

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
            balanceAmount: async function() {
                const formatted = [
                    {id: '=.0', text: 'Saldo kosong'},
                    {id: '>.0&<=.10000000', text: 'Rp1,00 sampai Rp10.000.000,00'},
                    {id: '>.10000000&<=.100000000', text: 'Rp10.000.000,00 sampai Rp100.000.000,00'},
                    {id: '>.100000000', text: 'Lebih dari Rp100.000.000,00'},
                ];

                $('#balance-amount-filter').select2({
                    data: [
                        {id: '#ALL', text: "Semua Jumlah Saldo"},
                        ...formatted,
                    ],
                    minimumResultsForSearch: 6,
                });
            },
        }
    </script>
@endpush
