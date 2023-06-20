@extends('layouts.student.layout-master')

@section('page_title', 'Pembayaran Mahasiswa')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
    <style>
        .eazy-table-info {
            display: inline-block;
        }
        .eazy-table-info td {
            padding: 10px 0;
        }
        .eazy-table-info td:first-child {
            padding-right: 1rem;
        }

        .nav-tabs.custom .nav-item {
            /* flex-grow: 1; */
        }
        .nav-tabs.custom .nav-link {
            /* width: -webkit-fill-available !important; */
            height: 50px !important;
        }
        .nav-tabs.custom .nav-link.active {
            background-color: #f2f2f2 !important;
        }

        .eazy-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        #payment-method-not-selected,
        #payment-method-selected {
            display: none;
        }
        #payment-method-not-selected.show,
        #payment-method-selected.show {
            display: block;
        }
    </style>
@endsection

@section('content')

<div id="student-info" class="card mb-3">
    <div class="card-body" style="width: 100%">
        <div class="d-flex flex-row" style="gap: 4rem; flex-wrap: wrap">
            <div class="d-flex flex-row align-items-center" style="gap: 1rem">
                <div class="round d-flex justify-content-center align-items-center bg-light" style="width: 65px; height: 65px">
                    <i style="width: 35px; height: 35px" data-feather="user"></i>
                </div>
                <div class="d-flex flex-column" style="gap: 5px">
                    <small class="d-block">Nama dan No Partisipan</small>
                    <span class="fw-bolder" style="font-size: 16px">{{ $user->fullname }}</span>
                    <span class="text-secondary d-block">{{ $user->participant_number }}</span>
                </div>
            </div>
            <div class="d-flex flex-row align-items-center" style="gap: 1rem">
                <div class="round d-flex justify-content-center align-items-center bg-light" style="width: 65px; height: 65px">
                    <i style="width: 35px; height: 35px" data-feather="book-open"></i>
                </div>
                <div class="d-flex flex-column" style="gap: 5px">
                    <small class="d-block">Fakultas</small>
                    <span class="fw-bolder" style="font-size: 16px">N/A</span>
                </div>
            </div>
            <div class="d-flex flex-row align-items-center" style="gap: 1rem">
                <div class="round d-flex justify-content-center align-items-center bg-light" style="width: 65px; height: 65px">
                    <i style="width: 35px; height: 35px" data-feather="bookmark"></i>
                </div>
                <div class="d-flex flex-column" style="gap: 5px">
                    <small class="d-block">Program Studi</small>
                    <span class="fw-bolder" style="font-size: 16px">N/A</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="nav-tabs-shadow nav-align-top">
        <ul class="nav nav-tabs custom border-bottom" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-invoice_n_va" aria-controls="navs-invoice_n_va" aria-selected="true">Tagihan Belum Lunas</button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-payment" aria-controls="navs-payment" aria-selected="false">Tagihan Lunas</button>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="navs-invoice_n_va" role="tabpanel">
                <table id="table-unpaid-payment" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Aksi</th>
                            <th>Tahun Akademik Tagihan</th>
                            <th>Kode Tagihan</th>
                            <th>Bulan</th>
                            <th>Total / Rincian Tagihan</th>
                            <th>Total / Rincian Potongan</th>
                            <th>Total / Rincian Beasiswa</th>
                            <th>Total / Rincian Denda</th>
                            <th>Jumlah Total</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="navs-payment" role="tabpanel">
                <table id="payment-table" class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">Aksi</th>
                            <th>Periode Masuk</th>
                            <th>Kode Tagihan</th>
                            <th>Bulan</th>
                            <th>Cicilan</th>
                            <th>Metode Pembayaran</th>
                            <th>Nominal</th>
                            <th>Total Bayar</th>
                            <th>Status Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Payment Detail Modal -->
<div class="modal fade" id="unpaidPaymentDetailModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-white" style="padding: 2rem 3rem 3rem 3rem">
                <h4 class="modal-title fw-bolder" id="unpaidPaymentDetailModalLabel">Tagihan Mahasiswa</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 pt-0">
                <div id="invoice-header" class="d-flex flex-row justify-content-between align-items-start mb-3">
                    <div>
                        <img src="{{ url('images/logo-eazy-small.png') }}" style="height: 40px" alt="eazy logo">
                    </div>
                    <div>
                        <span class="invoice-issue-date d-block fw-bold text-end" style="font-size: 12px">...</span>
                        <span class="text-end" style="font-size: 10px">No Tagihan: <span class="invoice-number fw-bold">...</span> | Telkom University</span>
                    </div>
                </div>

                <div id="student-data" class="mb-4">
                    <h4 class="fw-bolder mb-1">Data Mahasiwa</h4>
                    <div class="d-flex flex-row justify-content-between mb-4" style="gap: 2rem">
                        <div class="d-flex flex-column" style="gap: 5px">
                            <small class="d-block">Nama</small>
                            <span class="fw-bolder">{{ $user->fullname }}</span>
                            <span class="text-secondary d-block">No Partisipan: {{ $user->participant_number }}</span>
                        </div>
                        <div class="d-flex flex-column" style="gap: 5px">
                            <small class="d-block">Fakultas</small>
                            <span class="fw-bolder">N/A</span>
                        </div>
                        <div class="d-flex flex-column" style="gap: 5px">
                            <small class="d-block">Program Studi</small>
                            <span class="fw-bolder">N/A</span>
                        </div>
                    </div>
                </div>

                <div id="invoice-detail" class="mb-4">
                    <h4 class="fw-bolder mb-1">Detail Tagihan</h4>
                    <table id="table-invoice-detail" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Komponen Tagihan</th>
                                <th>Biaya Bayar</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot></tfoot>
                    </table>
                </div>

                <div class="d-flex justify-content-start" style="gap: 1rem">
                    <a type="button" id="btn-proceed-payment" data-eazy-prr-id="" onclick="proceedPayment(event)" class="btn btn-success d-inline-block">
                        Halaman Pembayaran&nbsp;&nbsp;<i data-feather="arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js_section')
<script>
    $(function(){
        _unpaidPaymentTable.init();
        // _paidPaymentTable.init();
    });

    const userMaster = JSON.parse(`{!! json_encode($user) !!}`);

    const _unpaidPaymentTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#table-unpaid-payment').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/student/payment/unpaid-payment',
                    data: function(d) {
                        d.participant_id = userMaster.participant_id;
                    }
                },
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
                        name: 'month',
                        data: 'month',
                        render: (data) => {
                            return this.template.defaultCell(data ?? '-');
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

    const unpaidPaymentDetailModal = new bootstrap.Modal(document.getElementById('unpaidPaymentDetailModal'));

    const _unpaidPaymentTableAction = {
        detail: function(e) {
            const data = _unpaidPaymentTable.getRowData(e.currentTarget);

            $('#unpaidPaymentDetailModal #invoice-header .invoice-issue-date').text(moment(data.invoice_issued_date).format('DD MMMM YYYY, HH:mm'));
            $('#unpaidPaymentDetailModal #invoice-header .invoice-number').text(data.invoice_number);

            const invoiceDetail = JSON.parse(unescapeHtml(data.invoice_detail));
            const invoiceTotal = invoiceDetail.reduce((acc, curr) => acc + curr.nominal, 0);
            const discountDetail = JSON.parse(unescapeHtml(data.discount_detail));
            const discountTotal = discountDetail.reduce((acc, curr) => acc + curr.nominal, 0);
            const scholarshipDetail = JSON.parse(unescapeHtml(data.scholarship_detail));
            const scholarshipTotal = scholarshipDetail.reduce((acc, curr) => acc + curr.nominal, 0);
            const penaltyDetail = JSON.parse(unescapeHtml(data.penalty_detail));
            const penaltyTotal = penaltyDetail.reduce((acc, curr) => acc + curr.nominal, 0);
            const totalAmount = (invoiceTotal + penaltyTotal) - (discountTotal + scholarshipTotal);
            $('#unpaidPaymentDetailModal #invoice-detail #table-invoice-detail tbody').html(`
                ${invoiceDetail.map(item => {
                    return `
                        <tr>
                            <td>${item.name}</td>
                            <td>${Rupiah.format(item.nominal)}</td>
                        </tr>
                    `;
                }).join('')}
                ${discountDetail.map(item => {
                    return `
                        <tr>
                            <td>${item.name}</td>
                            <td>${Rupiah.format(item.nominal)}</td>
                        </tr>
                    `;
                }).join('')}
                ${scholarshipDetail.map(item => {
                    return `
                        <tr>
                            <td>${item.name}</td>
                            <td>${Rupiah.format(item.nominal)}</td>
                        </tr>
                    `;
                }).join('')}
                ${penaltyDetail.map(item => {
                    return `
                        <tr>
                            <td>${item.name}</td>
                            <td>${Rupiah.format(item.nominal)}</td>
                        </tr>
                    `;
                }).join('')}
            `);
            $('#unpaidPaymentDetailModal #invoice-detail #table-invoice-detail tfoot').html(`
                <tr>
                    <th>Total Tagihan</th>
                    <th>${Rupiah.format(totalAmount)}</th>
                </tr>
            `);

            $('#unpaidPaymentDetailModal #btn-proceed-payment').attr('data-eazy-prr-id', data.prr_id);

            unpaidPaymentDetailModal.show();
        }
    }

    function proceedPayment(e) {
        const prrId = $(e.currentTarget).attr('data-eazy-prr-id');
        window.location.href = _baseURL+'/student/payment/proceed-payment/'+prrId;
    }

    const _paidPaymentTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#payment-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/student/paid-payment',
                },
                columns: [
                    {
                        name: 'action',
                        data: 'id',
                        orderable: false,
                        render: (data, _, row) => {
                            return this.template.rowAction(data)
                        }
                    },
                    {
                        name: 'entry_period',
                        render: (data, _, row) => {
                            return this.template.titleWithSubtitleCell(row.period, row.semester);
                        }
                    },
                    {
                        name: 'invoice_code',
                        data: 'invoice_code',
                        render: (data) => {
                            return this.template.buttonLinkCell(data, {onclickFunc: 'paymentDetailModal.show()'});
                        }
                    },
                    {
                        name: 'month',
                        data: 'month',
                        render: (data) => {
                            return this.template.defaultCell(data);
                        }
                    },
                    {
                        name: 'nth_installment',
                        data: 'nth_installment',
                        render: (data) => {
                            return this.template.defaultCell(data, {prefix: 'Cicilan Ke-'});
                        }
                    },
                    {
                        name: 'payment',
                        render: (data, _, row) => {
                            return this.template.listDetailCell(row.payment_method_detail, row.payment_method_name);
                        }
                    },
                    {
                        name: 'invoice_total',
                        data: 'invoice_total',
                        render: (data) => {
                            return this.template.currencyCell(data, {bold: true});
                        }
                    },
                    {
                        name: 'payment_total',
                        data: 'payment_total',
                        render: (data) => {
                            return this.template.currencyCell(data, {bold: true, additionalClass: 'text-danger'});
                        }
                    },
                    {
                        name: 'is_paid_off',
                        data: 'is_paid_off',
                        render: (data) => {
                            return this.template.badgeCell(
                                data ? 'Lunas' : 'Tidak Lunas',
                                data ? 'success' : 'danger',
                            );
                        }
                    },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-center header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"payment-actions">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>t' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function() {
                    $('.payment-actions').html(`
                        <h5 class="mb-0"></h5>
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
                            <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#paymentDetailModal"><i data-feather="eye"></i>&nbsp;&nbsp;Detail</a>
                        </div>
                    </div>
                `
            },
        }
    }


</script>
@endsection
