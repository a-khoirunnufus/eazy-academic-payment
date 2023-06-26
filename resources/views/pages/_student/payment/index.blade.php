@extends('layouts.student.layout-master')

@section('page_title', 'Tagihan Mahasiswa')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
    <style>
        .eazy-table-info {
            display: inline-block;
        }
        .eazy-table-info td {
            padding: 5px 10px 5px 0;
        }
        .eazy-table-info.lg td {
            padding: 10px 10px 10px 0;
        }
        .eazy-table-info td:first-child {
            padding-right: 1rem;
            font-weight: 500;
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

        .eazy-header {
            display: flex;
            flex-direction: row;
            gap: 4rem;
            flex-wrap: wrap;
        }
        .eazy-header .eazy-header__item {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 1rem;
        }
        .eazy-header .eazy-header__item .item__icon {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 65px;
            height: 65px;
            border-radius: 1.5rem;
            background-color: rgba(246, 246, 246, 1) !important;
        }
        .eazy-header .eazy-header__item .item__icon svg {
            width: 35px;
            height: 35px;
        }
        .eazy-header .eazy-header__item .item__text {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .eazy-header .eazy-header__item .item__text .text__subtitle {
            display: block;
        }
        .eazy-header .eazy-header__item .item__text .text__title {
            display: block;
            font-weight: 700;
            font-size: 16px;
        }

        .eazy-student-info {
            display: flex;
            flex-direction: row;
            gap: 4rem;
        }
        .eazy-student-info .eazy-student-info__item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .eazy-student-info .eazy-student-info__item .item__subtitle {
            display: block;
        }
        .eazy-student-info .eazy-student-info__item .item__subtitle {
            display: block;
            font-weight: 700;
        }
    </style>
@endsection

@section('content')

<div id="student-info" class="card mb-3">
    <div class="card-body" style="width: 100%">
        <div id="header-info-student">
            ...
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

                <div id="invoice-notes"></div>

                <div id="invoice-data" class="mb-3">
                    <h4 class="fw-bolder mb-1">Data Tagihan</h4>
                    <table class="eazy-table-info">
                        <tbody>
                            <tr>
                                <td>Nomor Invoice</td>
                                <td>:&nbsp;&nbsp;<span class="invoice-number">...</span></td>
                            </tr>
                            <tr>
                                <td>Digenerate Pada</td>
                                <td>:&nbsp;&nbsp;<span class="invoice-created">...</span></td>
                            </tr>
                        </tbody>
                    </table>
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

    const userMaster = JSON.parse(`{!! json_encode($user, true) !!}`);

    $(function(){
        _unpaidPaymentTable.init();
        // _paidPaymentTable.init();

        renderHeaderInfo();
    });

    async function renderHeaderInfo() {
        const studentType = userMaster.participant ? 'new_student' : 'student';
        const studentId = studentType == 'new_student' ? userMaster.participant.par_id : userMaster.student.student_id;
        const queryParam = `student_type=${studentType}&${studentType == 'new_student' ? 'par_id=' : 'student_id='}${studentId}`;

        const studentDetail = await $.ajax({
            async: true,
            url: `${_baseURL}/api/student/detail?${queryParam}`,
            type: 'get'
        });

        if (studentType == 'new_student') {
            $('#header-info-student').html(`
                <div class="eazy-header">
                    <div class="eazy-header__item">
                        <div class="item__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </div>
                        <div class="item__text">
                            <small class="text__subtitle">Nama Lengkap</small>
                            <span class="text__title">${studentDetail.par_fullname}</span>
                        </div>
                    </div>
                    <div class="eazy-header__item">
                        <div class="item__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-hash"><line x1="4" y1="9" x2="20" y2="9"></line><line x1="4" y1="15" x2="20" y2="15"></line><line x1="10" y1="3" x2="8" y2="21"></line><line x1="16" y1="3" x2="14" y2="21"></line></svg>
                        </div>
                        <div class="item__text">
                            <small class="text__subtitle">NIK</small>
                            <span class="text__title">${studentDetail.par_nik}</span>
                        </div>
                    </div>
                </div>
            `);
        } else if (studentType == 'student') {
            $('#header-info-student').html(`
                <div class="eazy-header">
                    <div class="eazy-header__item">
                        <div class="item__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </div>
                        <div class="item__text">
                            <small class="text__subtitle">Nama Lengkap dan NIM</small>
                            <span class="text__title">${studentDetail.fullname}</span>
                            <span class="d-block">${studentDetail.student_id}</span>
                        </div>
                    </div>
                    <div class="eazy-header__item">
                        <div class="item__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-book-open"><line x1="4" y1="9" x2="20" y2="9"></line><line x1="4" y1="15" x2="20" y2="15"></line><line x1="10" y1="3" x2="8" y2="21"></line><line x1="16" y1="3" x2="14" y2="21"></line></svg>
                        </div>
                        <div class="item__text">
                            <small class="text__subtitle">Fakultas</small>
                            <span class="text__title">${studentDetail.studyprogram.faculty.faculty_name}</span>
                        </div>
                    </div>
                    <div class="eazy-header__item">
                        <div class="item__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bookmark"><line x1="4" y1="9" x2="20" y2="9"></line><line x1="4" y1="15" x2="20" y2="15"></line><line x1="10" y1="3" x2="8" y2="21"></line><line x1="16" y1="3" x2="14" y2="21"></line></svg>
                        </div>
                        <div class="item__text">
                            <small class="text__subtitle">Program Studi</small>
                            <span class="text__title">${studentDetail.studyprogram.studyprogram_name}</span>
                        </div>
                    </div>
                </div>
            `);
        }
    }

    const _unpaidPaymentTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#table-unpaid-payment').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/student/payment/unpaid-payment',
                    data: function(d) {
                        d.student_type = userMaster.participant ? 'new_student' : 'student';
                        d.participant_id = userMaster.participant?.par_id;
                        d.student_id = userMaster.student?.student_id;
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

    const unpaidPaymentDetailModal = new bootstrap.Modal(document.getElementById('unpaidPaymentDetailModal'));

    const _unpaidPaymentTableAction = {
        detail: function(e) {
            const data = _unpaidPaymentTable.getRowData(e.currentTarget);

            $('#unpaidPaymentDetailModal #invoice-data .invoice-number').text(data.invoice_number);
            $('#unpaidPaymentDetailModal #invoice-data .invoice-created').text(moment(data.invoice_issued_date).format('DD-MM-YYYY'));

            if (data.invoice_student_type == 'new_student') {
                $('#unpaidPaymentDetailModal #invoice-notes').html(`
                    <h4 class="fw-bolder mb-1">Keterangan Tagihan</h4>
                    <div class="mb-4">${data.notes}</div>
                `);
            }

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
        window.location.href = `${_baseURL}/student/payment/proceed-payment/${prrId}?type=${userMaster.participant ? 'new_student' : 'student'}`;
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
