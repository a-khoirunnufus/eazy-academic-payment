@extends('tpl.vuexy.master-payment')

@section('page_title', 'Penarikan Saldo Mahasiswa')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
@endsection

@section('content')

@include('pages._payment.students-balance._shortcuts', ['active' => 'withdraw'])

<div class="card">
    <div class="card-body">
        <div class="datatable-filter one-row">
            <div>
                <label class="form-label">Jumlah Penarikan</label>
                <select id="balance-amount-filter" class="form-select"></select>
            </div>
            <div>
                <label class="form-label">Diproses Oleh</label>
                <select id="issuer-filter" class="form-select"></select>
            </div>
            <div class="d-flex align-items-end">
                <button onclick="_withdrawHistoryTable.reload()" class="btn btn-info text-nowrap">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="my-2">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-withdraw">
        <i data-feather="plus"></i> &nbsp; Tambah Penarikan Saldo
    </button>
</div>

<div class="card">
    <table id="table-withdraw-history" class="table table-striped">
        <thead>
            <tr>
                <th>Nama / NIM</th>
                <th>Jumlah Penarikan</th>
                <th>Diproses Oleh</th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<div class="modal fade" id="modal-withdraw" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Penarikan Saldo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-add-withdraw" onsubmit="#">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Mahasiswa <span class="text-danger">*</span></label>
                        <select id="select-students-balance-list" class="form-select">
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Total Saldo Mahasiswa</label>
                        <input type="text" class="form-control" name="name" disabled />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jumlah Penarikan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">File Terkait</label>
                        <input type="file" class="form-control" name="name" />
                    </div>
                    <div class="mt-2">
                        <small>
                            <span class="text-danger">*</span> Wajib diisi
                        </small>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: flex-start;">
                    <button type="submit" class="btn btn-info m-0">
                        Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js_section')
    <script>
        $(function() {
            _withdrawHistoryTable.init();
        })

        const _withdrawHistoryTable = {
            ..._datatable,
            init: function() {
                this.instance = $('#table-withdraw-history').DataTable({
                    ajax: {
                        url: _baseURL + '/api/payment/students-balance/withdraw-datatable',
                        data: function(d) {
                            d.withData = ['student', 'issuer'];

                            let filters = [];

                            const balanceAmountFilter = assignFilter('#balance-amount-filter');
                            if (balanceAmountFilter) {
                                const addFilters = balanceAmountFilter.split('&')
                                    .map(item => item.split('.'))
                                    .map(item => ({
                                        column: 'sbw_amount',
                                        operator: item[0],
                                        value: item[1]
                                    }));

                                filters = [...filters, ...addFilters];
                            }

                            if (filters.length > 0) {
                                d.filters = filters;
                            }
                        }
                    },
                    stateSave: false,
                    order: [],
                    columns: [
                        // 0
                        {
                            data: 'sbw_id',
                            render: (data) => {
                                return this.template.rowAction(data);
                            }
                        },
                        // 1 fullname
                        {
                            data: 'student.fullname',
                            render: (data, _, row) => {
                                return this.template.listCell([
                                    {text: row.student.fullname, bold: true, small: false, nowrap: true},
                                    {text: row.student.student_id, bold: false, small: true, nowrap: true},
                                ]);
                            }
                        },
                        // 2
                        {
                            data: 'sbw_amount',
                            render: (data) => {
                                return this.template.currencyCell(data);
                            }
                        },
                        // 3
                        {
                            data: 'issuer.user_fullname'
                        },
                        // 4
                        {
                            data: 'sbw_issue_time',
                            render: (data) => {
                                return this.template.dateTimeCell(data);
                            }
                        }
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
                    // buttons: _datatableBtnExportTemplate({
                    //     btnTypes: ['excel', 'csv'],
                    //     exportColumns: [4,5,6,7,8,9,10,11]
                    // }),
                    drawCallback: (settings) => {
                        feather.replace();
                    },
                    initComplete: () => {
                        $('.custom-actions').html(`
                            <h5 class="mb-0">Riwayat Penarikan Saldo</h5>
                        `);
                        // feather.replace();
                    }
                });

                this.implementSearchDelay();
            },
            template: {
                ..._datatableTemplates,
                rowAction: function(id) {
                    return `
                        <div class="dropdown d-flex justify-content-center">
                            <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a onclick="#" class="dropdown-item"><i data-feather="eye"></i>&nbsp;&nbsp;Detail</a>
                            </div>
                        </div>
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

        const _withdrawActions = {
            add: () => {
            }
        }
    </script>
@endsection

@push('laravel-component-setup')
    <script>
        $(function() {
            setupFilters.balanceAmount();
            setupFilters.studentsBalanceList();
        });

        const setupFilters = {
            balanceAmount: async function() {
                const formatted = [
                    {id: '>.0&<=.10000000', text: 'Rp1,00 sampai Rp10.000.000,00'},
                    {id: '>.10000000&<=.100000000', text: 'Rp10.000.000,00 sampai Rp100.000.000,00'},
                    {id: '>.100000000', text: 'Lebih dari Rp100.000.000,00'},
                ];

                $('#balance-amount-filter').select2({
                    data: [
                        {id: '#ALL', text: "Semua Jumlah Penarikan"},
                        ...formatted,
                    ],
                    minimumResultsForSearch: 6,
                });
            },
            studentsBalanceList: async function() {
                const data = await getRequestCache(`${_baseURL}/api/payment/students-balance`);
                const formatted = data.map(item => ({
                    id: item.student_id,
                    text: `${item.student_id} - ${item.fullname}`,
                }));

                $('#select-students-balance-list').select2({
                    data: formatted,
                    minimumResultsForSearch: 6,
                    dropdownParent: $("#modal-withdraw")
                });
            },
        }
    </script>
@endpush
