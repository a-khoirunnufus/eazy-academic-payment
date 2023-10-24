<table id="table-unpaid-invoice" class="table table-striped">
    <thead>
        <tr>
            <th>Aksi</th>
            <th>Kode Tagihan</th>
            <th>Tahun Akademik Tagihan</th>
            <th>Rincian / Total Tagihan</th>
            <th>Rincian / Total Denda</th>
            <th>Rincian / Total Potongan</th>
            <th>Rincian / Total Beasiswa</th>
            <th>Total Tagihan Akhir</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

@prepend('scripts')
<script>

    /**
     * @var object studentMaster
     */

    $(function() {
        _unpaidInvoiceTable.init();
    });

    const _unpaidInvoiceTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#table-unpaid-invoice').DataTable({
                ajax: {
                    url: _baseURL + '/api/payment/student-invoice',
                    data: function(d) {
                        d.student_number = studentMaster.student_number;
                        d.status = 'unpaid';
                        d.withData = [
                            'year',
                        ];
                        d.withAppend = [
                            'computed_component_list',
                            'computed_component_total_amount',
                            'computed_penalty_list',
                            'computed_penalty_total_amount',
                            'computed_scholarship_list',
                            'computed_scholarship_total_amount',
                            'computed_discount_list',
                            'computed_discount_total_amount',
                            'computed_final_bill',
                            'computed_payment_status',
                        ];
                    },
                },
                ordering: false,
                searching: false,
                stateSave: false,
                columns: [
                    // 0
                    {
                        data: 'prr_id',
                        orderable: false,
                        render: (data, _, row) => {
                            return this.template.rowAction(data)
                        }
                    },
                    // 1
                    {
                        data: 'prr_id',
                        render: (data) => {
                            return this.template.defaultCell(data, {
                                bold: true
                            });
                        }
                    },
                    // 2
                    {
                        render: (data, _, row) => {
                            return this.template.titleWithSubtitleCell(
                                row.year.msy_year,
                                row.year.msy_semester == 1 ? 'Ganjil'
                                    : row.year.msy_semester == 2 ? 'Genap'
                                        : 'Antara'
                            );
                        }
                    },
                    // 3
                    {
                        render: (data, _, row) => {
                            return this.template.listCell([
                                ...row.computed_component_list.map(item => ({
                                    text: `${item.prrd_component} : ${Rupiah.format(item.prrd_amount)}`,
                                    bold: false,
                                    small: true,
                                    nowrap: true,
                                })),
                                {
                                    text: `Total : ${Rupiah.format(row.computed_component_total_amount)}`,
                                    bold: true,
                                    small: false,
                                    nowrap: false,
                                }
                            ]);
                        }
                    },
                    // 4
                    {
                        render: (data, _, row) => {
                            return this.template.listCell([
                                ...row.computed_penalty_list.map(item => ({
                                    text: `${item.prrd_component} : ${Rupiah.format(item.prrd_amount)}`,
                                    bold: false,
                                    small: true,
                                    nowrap: true,
                                })),
                                {
                                    text: `Total : ${Rupiah.format(row.computed_penalty_total_amount)}`,
                                    bold: true,
                                    small: false,
                                    nowrap: false,
                                }
                            ]);
                        }
                    },
                    // 5
                    {
                        render: (data, _, row) => {
                            return this.template.listCell([
                                ...row.computed_scholarship_list.map(item => ({
                                    text: `${item.prrd_component} : ${Rupiah.format(item.prrd_amount)}`,
                                    bold: false,
                                    small: true,
                                    nowrap: true,
                                })),
                                {
                                    text: `Total : ${Rupiah.format(row.computed_scholarship_total_amount)}`,
                                    bold: true,
                                    small: false,
                                    nowrap: false,
                                }
                            ]);
                        }
                    },
                    // 6
                    {
                        render: (data, _, row) => {
                            return this.template.listCell([
                                ...row.computed_discount_list.map(item => ({
                                    text: `${item.prrd_component} : ${Rupiah.format(item.prrd_amount)}`,
                                    bold: false,
                                    small: true,
                                    nowrap: true,
                                })),
                                {
                                    text: `Total : ${Rupiah.format(row.computed_discount_total_amount)}`,
                                    bold: true,
                                    small: false,
                                    nowrap: false,
                                }
                            ]);
                        }
                    },
                    // 7
                    {
                        data: 'computed_final_bill',
                        render: (data) => {
                            return this.template.currencyCell(data, {bold: true});
                        }
                    },
                    // invisible column
                    // 8
                    {
                        title: 'Kode Tagihan',
                        data: 'prr_id',
                        visible: false
                    },
                    // 9
                    {
                        title: 'Tahun Akademik Tagihan',
                        data: 'year.msy_year',
                        visible: false ,
                        return: (data, _, row) => {
                            return (
                                row.year.msy_year + ' '
                                + row.year.msy_semester == 1 ? 'Ganjil'
                                    : row.year.msy_semester == 2 ? 'Genap'
                                        : 'Antara'
                            );
                        }
                    },
                    // 10
                    {
                        title: 'Jumlah Tagihan',
                        data: 'computed_component_total_amount',
                        visible: false
                    },
                    // 11
                    {
                        title: 'Jumlah Denda',
                        data: 'computed_penalty_total_amount',
                        visible: false
                    },
                    // 12
                    {
                        title: 'Jumlah Beasiswa',
                        data: 'computed_scholarship_total_amount',
                        visible: false
                    },
                    // 13
                    {
                        title: 'Jumlah Potongan',
                        data: 'computed_discount_total_amount',
                        visible: false
                    },
                    // 14
                    {
                        title: 'Total Tagihan Akhir',
                        data: 'computed_final_bill',
                        visible: false
                    },
                    // 15
                    {
                        title: 'Status Pembayaran',
                        data: 'computed_payment_status',
                        visible: false
                    },
                ],
                buttons: _datatableBtnExportTemplate({
                    btnTypes: ['print', 'csv', 'excel', 'pdf', 'copy'],
                    exportColumns: [8,9,10,11,12,13,14,15]
                }),
                language: {
                    search: '_INPUT_',
                    searchPlaceholder: "Cari Data",
                    lengthMenu: '_MENU_',
                    paginate: { 'first': 'First', 'last': 'Last', 'next': ' ', 'previous': ' ' },
                    processing: "Loading...",
                    emptyTable: "Tidak ada data",
                    infoEmpty:  "Menampilkan 0",
                    lengthMenu: "_MENU_",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    infoFiltered: "(difilter dari _MAX_ entri)",
                    zeroRecords: "Tidak ditemukan data yang cocok"
                },
                dom: '<"d-flex justify-content-between align-items-center header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"invoice-actions">>' +
                    '>' +
                    'tr' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                drawCallback: function(settings) {
                    feather.replace();
                },
                initComplete: function() {
                    $('.invoice-actions').html(`
                        <div class="d-flex flex-row px-1 justify-content-end" style="gap: 1rem">
                            <a href="{{ route('payment.student-credit.index') }}" class="btn btn-outline-warning">
                                <i data-feather="plus"></i>&nbsp;&nbsp;Pengajuan Cicilan
                            </a>
                            <a href="{{ route('payment.student-dispensation.index') }}" class="btn btn-outline-primary">
                                <i data-feather="calendar"></i>&nbsp;&nbsp;Pengajuan Dispensasi
                            </a>
                        </div>
                    `);
                    feather.replace();
                }
            })
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
                            <a class="dropdown-item" onclick="_unpaidInvoiceTableAction.detail(event)"><i data-feather="eye"></i>&nbsp;&nbsp;Detail</a>
                        </div>
                    </div>
                `
            },
        }
    }

    const _unpaidInvoiceTableAction = {
        detail: function(e) {
            invoiceDetailModal.open(e, _unpaidInvoiceTable);
        }
    }
</script>
@endprepend
