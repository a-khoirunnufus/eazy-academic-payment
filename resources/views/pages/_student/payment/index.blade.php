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
                @include('pages._student.payment.tab-unpaid-payment')
            </div>
            <div class="tab-pane fade" id="navs-payment" role="tabpanel">
                @include('pages._student.payment.tab-paid-payment')
            </div>
        </div>
    </div>
</div>

<!-- Invoice Detail Modal -->
<div class="modal fade" id="invoiceDetailModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-white" style="padding: 2rem 3rem 3rem 3rem">
                <h4 class="modal-title fw-bolder" id="invoiceDetailModalLabel">Tagihan Mahasiswa</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 pt-0">
            </div>
        </div>
    </div>
</div>

@endsection

@section('js_section')
<script>

    const userMaster = JSON.parse(`{!! json_encode($user, true) !!}`);

    $(function(){
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

    const invoiceDetailModal = {
        bsModal: new bootstrap.Modal(document.getElementById('invoiceDetailModal')),
        open: async function(e, datatableObj) {
            invoiceDetailModal._resetContent();

            const data = datatableObj.getRowData(e.currentTarget);

            if (data.invoice_student_type == 'new_student') {
                invoiceDetailModal._renderInvoiceNotes(data.notes);
            }

            invoiceDetailModal._renderInvoiceData(data.invoice_number, data.invoice_issued_date, data.payment_status);

            invoiceDetailModal._renderInvoiceDetail(data);

            const bills = await $.ajax({
                async: true,
                url: `${_baseURL}/api/student/payment/${data.prr_id}/bill`,
                type: 'get',
            });
            if (bills.length > 0) {
                invoiceDetailModal._renderInvoiceBill(bills);
            }

            if (data.payment_status == 'belum lunas') {
                invoiceDetailModal._renderProceedPayment(data.prr_id);
            }

            feather.replace();

            invoiceDetailModal.bsModal.show();
        },
        _resetContent: function() {
            $('#invoiceDetailModal .modal-body').html('');
        },
        _renderInvoiceNotes: function(notes) {
            $('#invoiceDetailModal .modal-body').append(`
                <div class="mb-4">
                    <h4 class="fw-bolder mb-1">Keterangan Tagihan</h4>
                    <div>${notes}</div>
                </div>
            `);
        },
        _renderInvoiceData: function(invoice_number, invoice_issued_date, payment_status) {
            $('#invoiceDetailModal .modal-body').append(`
                <div>
                    <h4 class="fw-bolder mb-1">Data Tagihan</h4>
                    <table class="eazy-table-info">
                        <tbody>
                            <tr>
                                <td>Nomor Invoice</td>
                                <td>:&nbsp;&nbsp;${invoice_number}</td>
                            </tr>
                            <tr>
                                <td>Digenerate Pada</td>
                                <td>:&nbsp;&nbsp;${moment(invoice_issued_date).format('DD-MM-YYYY')}</td>
                            </tr>
                            <tr>
                                <td>Status Tagihan</td>
                                <td>:&nbsp;&nbsp;${
                                    payment_status == 'belum lunas' ?
                                        '<span class="badge bg-danger" style="font-size: 1rem">Belum Lunas</span>'
                                        : payment_status == 'lunas' ?
                                            '<span class="badge bg-success" style="font-size: 1rem">Lunas</span>'
                                            : '<span class="badge bg-secondary" style="font-size: 1rem">N/A</span>'
                                }</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            `);
        },
        _renderInvoiceDetail: function(data) {
            const invoiceDetail = JSON.parse(unescapeHtml(data.invoice_detail));
            const invoiceTotal = invoiceDetail.reduce((acc, curr) => acc + curr.nominal, 0);
            const discountDetail = JSON.parse(unescapeHtml(data.discount_detail));
            const discountTotal = discountDetail.reduce((acc, curr) => acc + curr.nominal, 0);
            const scholarshipDetail = JSON.parse(unescapeHtml(data.scholarship_detail));
            const scholarshipTotal = scholarshipDetail.reduce((acc, curr) => acc + curr.nominal, 0);
            const penaltyDetail = JSON.parse(unescapeHtml(data.penalty_detail));
            const penaltyTotal = penaltyDetail.reduce((acc, curr) => acc + curr.nominal, 0);
            const totalAmount = (invoiceTotal + penaltyTotal) - (discountTotal + scholarshipTotal);

            $('#invoiceDetailModal .modal-body').append(`
                <div class="mt-3">
                    <h4 class="fw-bolder mb-1">Detail Tagihan</h4>
                    <table id="table-invoice-detail" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Komponen Tagihan</th>
                                <th>Biaya Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${
                                invoiceDetail.map(item => {
                                    return `
                                        <tr>
                                            <td>${item.name}</td>
                                            <td>${Rupiah.format(item.nominal)}</td>
                                        </tr>
                                    `;
                                }).join('')
                                +
                                discountDetail.map(item => {
                                    return `
                                        <tr>
                                            <td>${item.name}</td>
                                            <td>${Rupiah.format(item.nominal)}</td>
                                        </tr>
                                    `;
                                }).join('')
                                +
                                scholarshipDetail.map(item => {
                                    return `
                                        <tr>
                                            <td>${item.name}</td>
                                            <td>${Rupiah.format(item.nominal)}</td>
                                        </tr>
                                    `;
                                }).join('')
                                +
                                penaltyDetail.map(item => {
                                    return `
                                        <tr>
                                            <td>${item.name}</td>
                                            <td>${Rupiah.format(item.nominal)}</td>
                                        </tr>
                                    `;
                                }).join('')
                            }
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total Tagihan</th>
                                <th>${Rupiah.format(totalAmount)}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            `);
        },
        _renderInvoiceBill: function(bills) {
            $('#invoiceDetailModal .modal-body').append(`
                <div class="mt-3">
                    <h4 class="fw-bolder mb-1">Cicilan</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tenggat Pembayaran</th>
                                <th>Nominal Tagihan</th>
                                <th>Biaya Admin</th>
                                <th>Dibayar Pada</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${
                                bills.map(bill => {
                                    return `
                                        <tr>
                                            <td>Cicilan ke-${bill.prrb_order}</td>
                                            <td>${moment(bill.prrb_expired_date).format('DD-MM-YYYY')}</td>
                                            <td>${Rupiah.format(bill.prrb_amount)}</td>
                                            <td>${Rupiah.format(bill.prrb_admin_cost)}</td>
                                            <td>${bill.prrb_paid_date != null ? moment(bill.prrb_paid_date).format('DD-MM-YYYY') : '-'}</td>
                                            <td>${
                                                bill.prrb_status == 'belum lunas' ?
                                                    '<span class="badge bg-danger" style="font-size: 1rem">Belum Lunas</span>'
                                                    : bill.prrb_status == 'lunas' ?
                                                        '<span class="badge bg-success" style="font-size: 1rem">Lunas</span>'
                                                        : '<span class="badge bg-secondary" style="font-size: 1rem">N/A</span>'
                                            }</td>
                                        </tr>
                                    `;
                                }).join('')
                            }
                        </tbody>
                    </table>
                </div>
            `);
        },
        _renderProceedPayment: function(prrId) {
            $('#invoiceDetailModal .modal-body').append(`
                <div id="proceed-payment" class="mt-4">
                    <div class="d-flex justify-content-start" style="gap: 1rem">
                        <a type="button" id="btn-proceed-payment" data-eazy-prr-id="${prrId}" onclick="proceedPayment(event)" class="btn btn-success d-inline-block">
                            Halaman Pembayaran&nbsp;&nbsp;<i data-feather="arrow-right"></i>
                        </a>
                    </div>
                </div>
            `);
        },
    }

    function proceedPayment(e) {
        const prrId = $(e.currentTarget).attr('data-eazy-prr-id');
        const email = userMaster.user_email;
        const type = userMaster.participant ? 'new_student' : 'student';

        window.location.href = `${_baseURL}/student/payment/proceed-payment/${prrId}?email=${email}&type=${type}`;
    }

</script>
@endsection
