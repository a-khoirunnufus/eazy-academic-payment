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

@prepend('scripts')
<script>

    /**
     * @var object userMaster
     */

    $(function(){
        _unpaidPaymentTable.init();
    });

    const _unpaidPaymentTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#table-unpaid-payment').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/student/payment',
                    data: function(d) {
                        d.student_type = userMaster.participant ? 'new_student' : 'student';
                        d.participant_id = userMaster.participant?.par_id;
                        d.student_id = userMaster.student?.student_id;
                        d.status = 'unpaid';
                    }
                },
                stateSave: false,
                columnDefs: [
                    {
                        targets: [8],
                        visible: 'participant' in userMaster,
                        searchable: 'participant' in userMaster,
                    },
                ],
                columns: [
                    {
                        name: 'action',
                        data: 'prr_id',
                        orderable: false,
                        render: (data, _, row) => {
                            return this.template.rowAction(data)
                        }
                    },
                    {
                        name: 'school_year_invoice',
                        render: (data, _, row) => {
                            return this.template.titleWithSubtitleCell(
                                row.invoice_school_year_year,
                                'Semester '+row.invoice_school_year_semester
                            );
                        }
                    },
                    {
                        name: 'invoice_number',
                        data: 'invoice_number',
                        render: (data) => {
                            return this.template.defaultCell(data, {bold: true});
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
                                this.template.invoiceDetailCell(invoiceDetail, invoiceTotal)
                                : '-';
                        }
                    },
                    {
                        name: 'scholarship',
                        render: (data, _, row) => {
                            const scholarshipDetailJson = row.scholarship_detail;
                            const scholarshipDetail = JSON.parse(unescapeHtml(scholarshipDetailJson));
                            const scholarshipTotal = scholarshipDetail.reduce((acc, curr) => acc + curr.nominal, 0);
                            return scholarshipDetail.length > 0 ?
                                this.template.invoiceDetailCell(scholarshipDetail, scholarshipTotal)
                                : '-';
                        }
                    },
                    {
                        name: 'penalty',
                        render: (data, _, row) => {
                            const penaltyDetailJson = row.penalty_detail;
                            const penaltyDetail = JSON.parse(unescapeHtml(penaltyDetailJson));
                            const penaltyTotal = penaltyDetail.reduce((acc, curr) => acc + curr.nominal, 0);
                            return penaltyDetail.length > 0 ?
                                this.template.invoiceDetailCell(penaltyDetail, penaltyTotal)
                                : '-';
                        }
                    },
                    {
                        name: 'total_amount',
                        data: 'total_amount',
                        render: (data) => {
                            return this.template.currencyCell(data, {bold: true});
                        }
                    },
                    {
                        name: 'notes',
                        data: 'notes',
                        render: (data) => {
                            return this.template.defaultCell(data, {nowrap: false});
                        }
                    },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-center header-actions mx-0 row"' +
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
                            <button class="btn btn-success">
                                <i data-feather="printer"></i>&nbsp;&nbsp;Cetak Pembayaran
                            </button>
                            <button class="btn btn-outline-warning">
                                <i data-feather="plus"></i>&nbsp;&nbsp;Pengajuan Cicilan
                            </button>
                            <button class="btn btn-outline-primary">
                                <i data-feather="calendar"></i>&nbsp;&nbsp;Pengajuan Dispensasi
                            </button>
                        </div>
                    `)
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
            invoiceDetailModal.open(e, _unpaidPaymentTable);
        }
    }

</script>
@endprepend
