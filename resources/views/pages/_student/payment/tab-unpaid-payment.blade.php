<table id="table-unpaid-payment" class="table table-striped">
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
            <th>Tagihan</th>
            <th>Tenggat Pembayaran</th>
            <th>Nominal Tagihan</th>
            <th>Biaya Admin</th>
            <th>Dibayar Pada</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

@prepend('scripts')
<script>
    /**
     * @var object userMaster
     */
    var unpaidData = [];
    var reportUnpaidTable = $('#report-unpaid').DataTable({
        data: []
    })

    $(function() {
        _unpaidPaymentTable.init();
    });

    const _unpaidPaymentTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#table-unpaid-payment').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL + '/api/student/payment',
                    data: function(d) {
                        d.student_type = userMaster.participant ? 'new_student' : 'student';
                        d.participant_id = userMaster.participant?.par_id;
                        d.student_id = userMaster.student?.student_id;
                        d.status = 'unpaid';
                    },
                    dataSrc: function(json) {
                        unpaidData = [];
                        return json.data;
                    }
                },
                stateSave: false,
                columnDefs: [{
                    targets: [8],
                    visible: 'participant' in userMaster,
                    searchable: 'participant' in userMaster,
                }, ],
                columns: [{
                        name: 'action',
                        data: 'prr_id',
                        orderable: false,
                        render: (data, _, row) => {
                            var xhr = new XMLHttpRequest();
                            xhr.onload = function(){
                                var response = JSON.parse(this.responseText);
                                response.forEach(item => {
                                    var xhrD = new XMLHttpRequest()
                                    xhrD.onload = function(){
                                        var dispensation = JSON.parse(this.responseText);
                                        if(dispensation != null){
                                            item.prrb_due_date = dispensation.mds_deadline
                                        }
                                        item.prrb_due_date = item.prrb_due_date ?? '';
                                        item.prrb_amount = item.prrb_amount ?? 0;
                                        item.prrb_admin_cost = item.prrb_admin_cost ?? 0;
                                        item.prrb_paid_date = item.prrb_paid_date ?? '-';
                                        item.prrb_status = item.prrb_status ?? '';
                                    }
                                    xhrD.open("GET", _baseURL+`/api/student/dispensation/spesific-payment/${item.prr_id}`, false);
                                    xhrD.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                                    xhrD.send();

                                    unpaidData.push(item);
                                })
                            }
                            xhr.open("GET", _baseURL+"/api/student/payment/"+data+"/bill", false);
                            xhr.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}");
                            xhr.send();
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
                        columns: [
                            { 
                                render: (data, _, row) => {
                                    return 'Tagihan ke-'+counter++;
                                } 
                            },
                            { "data": 'prrb_due_date' },
                            { "data": 'prrb_amount' },
                            { "data": 'prrb_admin_cost' },
                            { "data": 'prrb_paid_date' },
                            { "data": 'prrb_status' },
                        ],
                        buttons: ['pdf']
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
                            <a class="dropdown-item" onclick="_unpaidPaymentTableAction.detail(event)"><i data-feather="eye"></i>&nbsp;&nbsp;Detail</a>
                        </div>
                    </div>
                `
            },
        }
    }

    const _unpaidPaymentTableAction = {
        detail: function(e) {
            console.log(e);
            invoiceDetailModal.open(e, _unpaidPaymentTable);
        }
    }

    function printUnpaid(){
        var btn = $('#report-unpaid_wrapper .buttons-pdf');
        btn.click();
    }
</script>
@endprepend