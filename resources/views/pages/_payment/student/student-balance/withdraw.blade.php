@extends('tpl.vuexy.master-payment')

@section('page_title', 'Riwayat Penarikan Saldo')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
@endsection

@section('content')

@include('pages._payment.student.student-balance._shortcuts', ['active' => 'withdraw'])

<div class="card">
    <table id="table-withdraw-history" class="table responsive table-striped nowrap" width="100%">
        <thead>
            <tr>
                <th>Jumlah Penarikan</th>
                <th>Diproses Oleh</th>
                <th>Waktu</th>
                <th>File Terkait</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

@endsection

@section('js_section')
    <script>

        const studentMaster = JSON.parse(`{!! json_encode($student, true) !!}`);

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
                            d.withFilter = [
                                {
                                    column: 'student_id',
                                    operator: '=',
                                    value: studentMaster.student_id,
                                },
                            ];
                        }
                    },
                    stateSave: false,
                    order: [[2, 'desc']],
                    columns: [
                        // 0
                        {
                            data: 'amount',
                            searchable: false,
                            render: (data) => {
                                return this.template.currencyCell(data);
                            }
                        },
                        // 1
                        {
                            data: 'issuer_name',
                            searchable: false,
                            orderable: false,
                        },
                        // 2
                        {
                            data: 'issued_time',
                            searchable: false,
                            render: (data) => {
                                return this.template.dateTimeCell(data);
                            }
                        },
                        // 3
                        {
                            data: 'related_files',
                            searchable: false,
                            orderable: false,
                            render: (data) => {
                                const files = JSON.parse(unescapeHtml(data));

                                if (!files || files?.length == 0) return '-';

                                return `<div class="d-flex flex-column" style="gap: .5rem">
                                    ${files.map(item => (`
                                        <a href="${_baseURL}/api/download-cloud?path=${item}" target="_blank" class="btn btn-link p-0 d-block text-start">
                                            <i data-feather="file"></i> &nbsp;Download
                                        </a>
                                    `)).join('')}
                                </div>`
                            }
                        },
                        // 4
                        {
                            title: 'Jumlah Penarikan',
                            data: 'amount',
                            visible: false,
                        },
                    ],
                    responsive: false,
                    scrollX: true,
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
                        btnTypes: ['print', 'csv', 'excel', 'pdf', 'copy'],
                        exportColumns: [4, 1, 2],
                    }),
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

    </script>
@endsection
