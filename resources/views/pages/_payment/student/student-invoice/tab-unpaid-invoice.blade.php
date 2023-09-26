<table id="table-unpaid-invoice" class="table table-striped">
    <thead>
        <tr>
            <th>Aksi</th>
            <th>Tahun Akademik Tagihan</th>
            <th>Kode Tagihan</th>
            <th>Total / Rincian Tagihan</th>
            <th>Total / Rincian Potongan</th>
            <th>Total / Rincian Beasiswa</th>
            <th>Total / Rincian Denda</th>
            <th>Jumlah Total</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<table id="report-unpaid">
    <thead>
        <tr>
            <th>Kode Tagihan</th>
            <th>Komponen Tagihan</th>
            <th>Nominal Tagihan</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

@prepend('scripts')
<script>

    /**
     * @var object studentMaster
     */

    var unpaidData = [];
    var reportUnpaidTable = $('#report-unpaid').DataTable({
        data: []
    })

    $(function() {
        _unpaidInvoiceTable.init();
    });

    const _unpaidInvoiceTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#table-unpaid-invoice').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL + '/api/payment/student-invoice',
                    data: function(d) {
                        d.student_type = 'student';
                        d.participant_id = null;
                        d.student_number = studentMaster.student_number;
                        d.status = 'unpaid';
                    },
                    dataSrc: function(json) {
                        unpaidData = [];
                        return json.data;
                    }
                },
                stateSave: false,
                // columnDefs: [{
                //     targets: [8],
                //     visible: 'participant' in userMaster,
                //     searchable: 'participant' in userMaster,
                // }, ],
                columns: [{
                        name: 'action',
                        data: 'prr_id',
                        orderable: false,
                        render: (data, _, row) => {
                            // var xhrD = new XMLHttpRequest()
                            // xhrD.onload = function() {
                            //     var payment = JSON.parse(this.responseText);
                            //     payment.payment_detail.forEach(element => {
                            //         unpaidData.push(element);
                            //     });
                            // }
                            // xhrD.open("GET", _baseURL + `/api/payment/student-invoice/${data}`, false);
                            // xhrD.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                            // xhrD.send();
                            return this.template.rowAction(data)
                        }
                    },
                    {
                        name: 'school_year_invoice',
                        render: (data, _, row) => {
                            return this.template.titleWithSubtitleCell(
                                row.invoice_school_year_year,
                                'Semester ' + row.invoice_school_year_semester
                            );
                        }
                    },
                    {
                        name: 'invoice_number',
                        data: 'invoice_number',
                        render: (data) => {
                            return this.template.defaultCell(data, {
                                bold: true
                            });
                        }
                    },
                    {
                        name: 'invoice',
                        render: (data, _, row) => {
                            const invoiceDetailJson = row.invoice_detail;
                            const invoiceDetail = JSON.parse(unescapeHtml(invoiceDetailJson));
                            const invoiceTotal = invoiceDetail.reduce((acc, curr) => acc + curr.nominal, 0);
                            return this.template.invoiceDetailCell(invoiceDetail, invoiceTotal);
                        }
                    },
                    {
                        name: 'discount',
                        render: (data, _, row) => {
                            const discountDetailJson = row.discount_detail;
                            const discountDetail = JSON.parse(unescapeHtml(discountDetailJson));
                            const discountTotal = discountDetail.reduce((acc, curr) => acc + curr.nominal, 0);
                            return discountDetail.length > 0 ?
                                this.template.invoiceDetailCell(invoiceDetail, invoiceTotal) :
                                '-';
                        }
                    },
                    {
                        name: 'scholarship',
                        render: (data, _, row) => {
                            const scholarshipDetailJson = row.scholarship_detail;
                            const scholarshipDetail = JSON.parse(unescapeHtml(scholarshipDetailJson));
                            const scholarshipTotal = scholarshipDetail.reduce((acc, curr) => acc + curr.nominal, 0);
                            return scholarshipDetail.length > 0 ?
                                this.template.invoiceDetailCell(scholarshipDetail, scholarshipTotal) :
                                '-';
                        }
                    },
                    {
                        name: 'penalty',
                        render: (data, _, row) => {
                            const penaltyDetailJson = row.penalty_detail;
                            const penaltyDetail = JSON.parse(unescapeHtml(penaltyDetailJson));
                            const penaltyTotal = penaltyDetail.reduce((acc, curr) => acc + curr.nominal, 0);
                            return penaltyDetail.length > 0 ?
                                this.template.invoiceDetailCell(penaltyDetail, penaltyTotal) :
                                '-';
                        }
                    },
                    {
                        name: 'total_amount',
                        data: 'total_amount',
                        render: (data) => {
                            return this.template.currencyCell(data, {
                                bold: true
                            });
                        }
                    },
                    {
                        name: 'notes',
                        data: 'notes',
                        render: (data) => {
                            return this.template.defaultCell(data, {
                                nowrap: false
                            });
                        }
                    },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                buttons: [{
                    extend: 'collection',
                    text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-external-link font-small-4 me-50"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>Export</span>',
                    className: 'btn btn-outline-secondary dropdown-toggle',
                    buttons: [
                        {
                            text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file font-small-4 me-50"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>Excel</span>',
                            className: 'dropdown-item',
                            action: function(e, dt, node, config){
                                exportUnpaid('excel');
                            }
                        },
                        {
                            text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clipboard font-small-4 me-50"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>Pdf</span>',
                            className: 'dropdown-item',
                            action: function(e, dt, node, config){
                                exportUnpaid('pdf');
                            }
                        },
                        {
                            text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text font-small-4 me-50"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>Csv</span>',
                            className: 'dropdown-item',
                            action: function(e, dt, node, config){
                                exportUnpaid('csv');
                            }
                        },
                        {
                            text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-copy font-small-4 me-50"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>Copy</span>',
                            className: 'dropdown-item',
                            action: function(e, dt, node, config){
                                exportUnpaid('copy');
                            }
                        },
                    ]
                }, ],
                dom: '<"d-flex justify-content-between align-items-center header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"invoice-actions">>' +
                    '>' +
                    '<"eazy-table-wrapper" t>' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function() {
                    $('.invoice-actions').html(`
                        <div class="d-flex flex-row px-1 justify-content-end" style="gap: 1rem">
                            <button class="btn btn-success" onclick="printUnpaid()">
                                <i data-feather="printer"></i>&nbsp;&nbsp;Cetak Pembayaran
                            </button>
                            <a href="{{ route('student.credit.index') }}" class="btn btn-outline-warning">
                                <i data-feather="plus"></i>&nbsp;&nbsp;Pengajuan Cicilan
                            </a>
                            <a href="{{ route('student.dispensation.index') }}" class="btn btn-outline-primary">
                                <i data-feather="calendar"></i>&nbsp;&nbsp;Pengajuan Dispensasi
                            </a>
                        </div>
                    `)
                    console.log(unpaidData);
                    reportUnpaidTable.clear().destroy();
                    var counter = 0;
                    reportUnpaidTable = $('#report-unpaid').DataTable({
                        data: unpaidData,
                        serverSide: false,
                        paging: false,
                        columns: [{
                                render: (data, _, row) => {
                                    return 'INV/' + row.prr_id;
                                }
                            },
                            {
                                data: 'prrd_component'
                            },
                            {
                                data: 'prrd_amount',
                                render: (data, _, row) => {
                                    return row.is_plus == 1 ? data : data * -1;
                                }
                            },
                        ],
                        buttons: ['pdf', 'excel', 'csv', 'copy']
                    })

                    feather.replace()
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
            console.log(e);
            invoiceDetailModal.open(e, _unpaidInvoiceTable);
        }
    }

    function printUnpaid() {
        var btn = $('#report-unpaid_wrapper .buttons-pdf');
        btn.click();
    }

    function exportUnpaid(type) {
        var btn = $('#report-unpaid_wrapper .buttons-'+type);
        btn.click();
    }
</script>
@endprepend
