<table id="table-paid-invoice" class="table table-striped">
    <thead>
        <tr>
            <th>Aksi</th>
            <th>Tahun Akademik Tagihan</th>
            <th>Kode Tagihan</th>
            <th>Rincian / Total Tagihan</th>
            <th>Rincian / Total Potongan</th>
            <th>Rincian / Total Beasiswa</th>
            <th>Rincian / Total Denda</th>
            <th>Jumlah Total</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<table id="report-paid">
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

     var paidData = [];
     var reportPaidTable = $('#report-paid').DataTable({
        data: [],
        buttons: ['pdf', 'excel', 'csv', 'copy']
    })

     $(function(){
        _paidInvoiceTable.init();
    });

    const _paidInvoiceTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#table-paid-invoice').DataTable({
                serverSide: true,
                processing: true,
                ajax: {
                    url: _baseURL+'/api/payment/student-invoice',
                    data: function(d) {
                        d.student_number = studentMaster.student_number;
                        d.status = 'paid';
                        d.withData = [
                            'year',
                        ];
                        d.withAppend = [
                            'computed_component_list',
                            'computed_component_total_amount',
                            'computed_discount_list',
                            'computed_discount_total_amount',
                            'computed_scholarship_list',
                            'computed_scholarship_total_amount',
                            'computed_final_bill',
                            'computed_payment_status',
                        ];
                    },
                    dataSrc: function(json) {
                        paidData = [];
                        return json.data;
                    }
                },
                ordering: false,
                searching: false,
                stateSave: false,
                // columnDefs: [
                //     {
                //         targets: [8],
                //         visible: 'participant' in userMaster,
                //         searchable: 'participant' in userMaster,
                //     },
                // ],
                columns: [
                    {
                        name: 'action',
                        data: 'prr_id',
                        orderable: false,
                        render: (data, _, row) => {
                            // var xhrD = new XMLHttpRequest()
                            // xhrD.onload = function() {
                            //     var payment = JSON.parse(this.responseText);
                            //     payment.payment_detail.forEach(element => {
                            //         paidData.push(element);
                            //     });
                            // }
                            // xhrD.open("GET", _baseURL + `/api/payment/student-invoice/${data}`, false);
                            // xhrD.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                            // xhrD.send();
                            return this.template.rowAction(data)
                        }
                    },
                    {
                        render: (data, _, row) => {
                            return this.template.titleWithSubtitleCell(
                                row.year.msy_year,
                                'Semester ' + row.year.msy_semester
                            );
                        }
                    },
                    {
                        data: 'prr_id',
                        render: (data) => {
                            return this.template.defaultCell(data, {
                                bold: true
                            });
                        }
                    },
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
                    {
                        render: (data, _, row) => {
                            return this.template.currencyCell(0, {bold: true});
                        }
                    },
                    {
                        data: 'computed_final_bill',
                        render: (data) => {
                            return this.template.currencyCell(data, {bold: true});
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
                                exportPaid('excel');
                            }
                        },
                        {
                            text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clipboard font-small-4 me-50"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>Pdf</span>',
                            className: 'dropdown-item',
                            action: function(e, dt, node, config){
                                exportPaid('pdf');
                            }
                        },
                        {
                            text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text font-small-4 me-50"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>Csv</span>',
                            className: 'dropdown-item',
                            action: function(e, dt, node, config){
                                exportPaid('csv');
                            }
                        },
                        {
                            text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-copy font-small-4 me-50"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>Copy</span>',
                            className: 'dropdown-item',
                            action: function(e, dt, node, config){
                                exportPaid('copy');
                            }
                        },
                    ]
                }, ],
                dom:
                    '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"invoice-component-actions d-flex align-items-end">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>t' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function() {
                    // feather.replace();
                    reportPaidTable.clear().destroy();
                    var counter = 0;
                    reportPaidTable = $('#report-paid').DataTable({
                        data: paidData,
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
                            <a class="dropdown-item" onclick="_paidInvoiceTableAction.detail(event)"><i data-feather="eye"></i>&nbsp;&nbsp;Detail</a>
                        </div>
                    </div>
                `
            },
        }
    }

    const _paidInvoiceTableAction = {
        detail: function(e) {
            invoiceDetailModal.open(e, _paidInvoiceTable);
        }
    }

    function exportPaid(type){
        var btn = $('#report-paid_wrapper .buttons-'+type);
        btn.click();
    }
</script>
@endprepend
