@push('styles')
    <style>
        #reset-payment-section {
            display: none;
        }
        #reset-payment-section.show {
            display: flex;
            justify-content: end;
        }

        .payment-method-list {
            display: flex;
            flex-direction: column;
            padding-left: 0;
            margin-bottom: 0;
            border-radius: 0.357rem;
        }
        .payment-method-list > .payment-method-list__item {
            position: relative;
            display: block;
            padding: 0.75rem 1.25rem;
            color: #6e6b7b;
            background-color: #fff;
            border: 1px solid rgba(34, 41, 47, 0.125);
        }
        .payment-method-list > .payment-method-list__item.active {
            border: 1px solid #7367f0 !important;
            box-shadow: 0 4px 24px 0 rgb(34 41 47 / 10%) !important;
        }
        .line {
            text-decoration: line-through;
        }
    </style>
@endpush

<div id="reset-payment-section" class="show mb-2">
    <button onclick="payBillTab.resetPayment()" class="btn btn-outline-warning">Ganti Opsi Pembayaran</button>
</div>

<div>
    <table id="table-pay-bill" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Jumlah Tagihan</th>
                <th>Tenggat Pembayaran</th>
                <th>Status Pembayaran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Payment Method Modal -->
<div class="modal fade" id="paymentMethodModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-white" style="padding: 2rem 3rem 3rem 3rem">
                <h4 class="modal-title fw-bolder" id="paymentMethodModalLabel">Pembayaran</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 pt-0">
                <div>
                    <h4 class="mb-1">Metode Pembayaran</h4>
                    <form id="form-payment-method">
                        <input type="hidden" name="prrb_id" value="" />
                        <div class="accordion border" id="accordionPaymentMethod">
                        </div>
                    </form>
                </div>

                <div class="mt-3">
                    <h4 class="mb-1">Ringkasan Pembayaran</h4>
                    <div id="payment-summary">
                        ...
                    </div>
                </div>
            </div>
            <div class="modal-footer px-3 py-2">
                <div class="d-flex flex-row justify-content-between w-100 align-items-center">
                    <div>
                        <p class="mb-0">Metode Pembayaran</p>
                        <h5 id="footer-payment-method">...</h5>
                    </div>
                    <div>
                        <p class="mb-0">Total Tagihan</p>
                        <h5 id="footer-bill-total-amount">...</h5>
                    </div>
                    <button onclick="payBillModal.selectPaymentMethod()" class="d-block btn btn-success" style="width: 200px">Bayar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Instruction Modal -->
<div class="modal fade" id="paymentInstructionModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-white" style="padding: 2rem 3rem 3rem 3rem">
                <h4 class="modal-title fw-bolder" id="paymentInstructionModalLabel">Pembayaran</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 pt-0">
                <div id="va-number-exp-warning">
                    ...
                </div>

                <table id="table-pay-data" class="table table-bordered">
                    <tbody></tbody>
                </table>

                <div id="payment-evidence-section">
                </div>

                <div class="mt-3">
                    <div class="d-flex flex-row align-items-center justify-content-between mb-1">
                        <h5 class="mb-0">Riwayat Bukti Pembayaran</h5>
                        <button id="btn-open-upload-evidence-modal" class="btn btn-success">Unggah Bukti Pembayaran</button>
                    </div>
                    <table id="table-approval-history" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Total Bayar</th>
                                <th>Waktu Bayar</th>
                                <th>Status Approval</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <h5 class="mb-1">Riwayat Transaksi</h5>
                    <table id="table-transaction-history" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Total Bayar</th>
                                <th>Waktu Bayar</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <h5 class="mb-1">Petunjuk Pembayaran</h5>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptatem quis sapiente repellendus dignissimos quam aliquam reprehenderit dolorum? Libero, totam magnam iusto reprehenderit consequuntur ullam ipsam modi hic! Eaque, officia. Ratione!</p>
                    <ul>
                        <li><a href="https://simulator.sandbox.midtrans.com/bca/va/index" target="_blank">BCA Virtual Account Payment Simulator</a></li>
                        <li><a href="https://simulator.sandbox.midtrans.com/bni/va/index" target="_blank">BNI Virtual Account Payment Simulator</a></li>
                        <li><a href="https://simulator.sandbox.midtrans.com/mandiri/bill/index" target="_blank">Mandiri Bill Payment Simulator</a></li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer px-3 py-2"></div>
        </div>
    </div>
</div>

<!-- Payment Detail Modal -->
<div class="modal fade" id="paymentDetailModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-white" style="padding: 2rem 3rem 3rem 3rem">
                <h4 class="modal-title fw-bolder" id="paymentDetailModalLabel">Detail Pembayaran</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 pt-0">
                ...
            </div>
        </div>
    </div>
</div>


@prepend('scripts')
<script>

    /**
     * @var integer prrId
     * @var object FormDataJson
     * @func getRequestCache()
     */

    // enabling multiple modal open
    $(document).on('show.bs.modal', '.modal', function() {
        const zIndex = 1040 + 10 * $('.modal:visible').length;
        $(this).css('z-index', zIndex);
        setTimeout(() => $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack'));
    });

    $(function(){
        $('#form-payment-method').change(payBillModal.paymentMethodChangeHandler);
        feather.replace();
    });

    const payBillTab = {
        alwaysAllowReset: false,
        showHandler: async function() {
            const payment = await getRequestCache(`${_baseURL}/api/student/payment/${prrId}`);
            const studentType = payment.register ? 'new_student' : 'student';

            let hasPaid = false;
            for (bill of payment.payment_bill) {
                if (bill.prrb_status == 'lunas') {
                    hasPaid = true;
                    break;
                }
            }
            if (hasPaid) {
                !payBillTab.alwaysAllowReset && $('#reset-payment-section').removeClass('show');
            }

            $('#table-pay-bill tbody').html(`
                ${payment.payment_bill.map(item => {
                    var row = null;
                    var xhr = new XMLHttpRequest()
                    xhr.onload = function(){
                        var response = JSON.parse(this.responseText);
                        if(response == null){
                            console.log('not found');
                        }
                        console.log(response);
                        row = `
                            <tr>
                                <td>${item.prrb_order}</td>
                                <td>
                                    Cicilan Ke-${item.prrb_order} Pembayaran ${studentType == 'new_student' ? 'Daftar Ulang' : 'Registrasi Semester Baru'}
                                    Program Studi ${ studentType == 'new_student' ? `
                                            ${payment.register.studyprogram.studyprogram_type.toUpperCase()}
                                            ${payment.register.studyprogram.studyprogram_name}
                                            ${payment.register.lecture_type?.mlt_name ?? 'N/A'}
                                        ` : `
                                            ${payment.student.studyprogram.studyprogram_type.toUpperCase()}
                                            ${payment.student.studyprogram.studyprogram_name}
                                            ${payment.student.lecture_type?.mlt_name ?? 'N/A'}
                                        `
                                    }
                                    Tahun Ajaran ${payment.year.msy_year}
                                    Semester ${payment.year.msy_semester}
                                </td>
                                <td>${Rupiah.format(item.prrb_amount)}</td>
                                <td>${ response == null ? moment(item.prrb_due_date).format('DD-MM-YYYY') : '<p class="line">'+moment(item.prrb_due_date).format('DD-MM-YYYY')+'</p><br><p>'+moment(response.mds_deadline).format('DD-MM-YYYY')}</td>
                                <td>
                                    ${item.prrb_status == 'lunas' ?
                                        '<span class="badge bg-success">Lunas</span>'
                                        : item.prrb_status == 'belum lunas' ?
                                            '<span class="badge bg-danger">Belum Lunas</span>'
                                            : 'N/A'
                                    }
                                </td>
                                <td>
                                    ${
                                        item.prrb_status == 'belum lunas' ? `
                                            <button class="btn btn-sm btn-success" onclick="payBillModal.open(${item.prrb_id})">
                                                Bayar
                                            </button>
                                        ` : `
                                            <button class="btn btn-sm btn-info text-nowrap" onclick="paymentDetailModal.open(${item.prrb_id})">
                                                Detail Pembayaran
                                            </button>
                                        `
                                    }
                                </td>
                            </tr>
                        `;
                    }
                    xhr.open("GET", _baseURL+"/api/student/dispensation/spesific-payment/{{ $prr_id }}", false);
                    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                    xhr.send();
                    return row;
                    // return `
                    //     <tr>
                    //         <td>${item.prrb_order}</td>
                    //         <td>
                    //             Cicilan Ke-${item.prrb_order} Pembayaran ${studentType == 'new_student' ? 'Daftar Ulang' : 'Registrasi Semester Baru'}
                    //             Program Studi ${ studentType == 'new_student' ? `
                    //                     ${payment.register.studyprogram.studyprogram_type.toUpperCase()}
                    //                     ${payment.register.studyprogram.studyprogram_name}
                    //                     ${payment.register.lecture_type?.mlt_name ?? 'N/A'}
                    //                 ` : `
                    //                     ${payment.student.studyprogram.studyprogram_type.toUpperCase()}
                    //                     ${payment.student.studyprogram.studyprogram_name}
                    //                     ${payment.student.lecture_type?.mlt_name ?? 'N/A'}
                    //                 `
                    //             }
                    //             Tahun Ajaran ${payment.year.msy_year}
                    //             Semester ${payment.year.msy_semester}
                    //         </td>
                    //         <td>${Rupiah.format(item.prrb_amount)}</td>
                    //         <td>${moment(item.prrb_due_date).format('DD-MM-YYYY')}</td>
                    //         <td>
                    //             ${item.prrb_status == 'lunas' ?
                    //                 '<span class="badge bg-success">Lunas</span>'
                    //                 : item.prrb_status == 'belum lunas' ?
                    //                     '<span class="badge bg-danger">Belum Lunas</span>'
                    //                     : 'N/A'
                    //             }
                    //         </td>
                    //         <td>
                    //             ${
                    //                 item.prrb_status == 'belum lunas' ? `
                    //                     <button class="btn btn-sm btn-success" onclick="payBillModal.open(${item.prrb_id})">
                    //                         Bayar
                    //                     </button>
                    //                 ` : `
                    //                     <button class="btn btn-sm btn-info text-nowrap" onclick="paymentDetailModal.open(${item.prrb_id})">
                    //                         Detail Pembayaran
                    //                     </button>
                    //                 `
                    //             }
                    //         </td>
                    //     </tr>
                    // `;
                })}
            `);
        },
        resetPayment: async function() {
            const confirmed = await _swalConfirmSync({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin mengganti metode pembayaran?',
            });

            if(!confirmed) return;

            try {
                const res = await $.ajax({
                    async: true,
                    url: `${_baseURL}/api/student/payment/${prrId}/reset-payment`,
                    type: 'post',
                });

                if (res.success) {
                    _toastr.success(res.message, 'Berhasil');
                    tabManager.updateDisableState();
                } else {
                    _toastr.error(res.message, 'Gagal');
                }
            } catch (error) {
                _toastr.error('Gagal mengganti metode pembayaran!', 'Gagal');
            }
        },
        openUploadEvidenceModal: function(e) {
            const target = $(e.currentTarget);
            const prrIdValue = target.attr('data-eazy-prrId');
            const prrbIdValue = target.attr('data-eazy-prrbId');
            $('#multiple-bills #uploadPaymentEvidenceModal .form-upload-evidence input[name="prr_id"]').val(prrIdValue);
            $('#multiple-bills #uploadPaymentEvidenceModal .form-upload-evidence input[name="prrb_id"]').val(prrbIdValue);
            uploadPaymentEvidenceModal.show();
        },
        openDetailEvidenceModal: async function(e) {
            const target = $(e.currentTarget);
            const prrIdValue = target.attr('data-eazy-prrId');
            const prrbIdValue = target.attr('data-eazy-prrbId');

            const payment = await getRequestCache(`${_baseURL}/api/student/payment/${prrIdValue}`);
            let paymentBill = {};

            for (const bill of payment.payment_bill) {
                if (bill.prrb_id == parseInt(prrbIdValue)) {
                    paymentBill = bill;
                    break;
                }
            }

            Modal.show({
                type: 'detail',
                modalTitle: 'Detail Bukti Pembayaran',
                modalSize: 'md',
                config: {
                    fields: {
                        prrb_manual_name: {
                            title: 'Nama Pemilik Rekening',
                            content: {
                                template: ':name',
                                name: paymentBill.prrb_manual_name
                            },
                        },
                        prrb_manual_norek: {
                            title: 'Nomor Rekening',
                            content: {
                                template: ':number',
                                number: paymentBill.prrb_manual_norek
                            },
                        },
                        prrb_paid_date: {
                            title: 'Dibayar Pada Tanggal',
                            content: {
                                template: ':text',
                                text: moment(paymentBill.prrb_paid_date).format('DD-MM-YYYY')
                            },
                        },
                        prrb_manual_evidence: {
                            title: 'File Bukti Pembayaran',
                            content: {
                                template: `
                                    <a href="${_baseURL}/api/download-cloud?path=:path" class="p-0 btn btn-link btn-sm">
                                        <i data-feather="download"></i>&nbsp;&nbsp;
                                        Download Bukti Pembayaran
                                    </a>
                                `,
                                path: paymentBill.prrb_manual_evidence
                            }
                        },
                        prrb_manual_status: {
                            title: 'Status Approval',
                            content: {
                                template: `
                                    ${
                                        paymentBill.prrb_manual_status == 'waiting' ?
                                            '<span class="badge bg-warning">Menunggu Approval</span>'
                                            : paymentBill.prrb_manual_status == 'rejected' ?
                                                '<span class="badge bg-danger">Ditolak</span>'
                                                : paymentBill.prrb_manual_status == 'accepted' ?
                                                    '<span class="badge bg-success">Disetujui</span>'
                                                    : 'N/A'
                                    }
                                `,
                            },
                        },
                        prrb_manual_note: {
                            title: 'Catatan',
                            content: {
                                template: ':text',
                                text: paymentBill.prrb_manual_note ?? '-'
                            },
                        },
                    },
                    callback: function() {
                        feather.replace();
                    }
                },
            });
        },
    };

    const payBillModal = {
        open: async function(prrbId) {
            const bill = await $.ajax({
                async: true,
                url: `${_baseURL}/api/student/payment/${prrId}/bill/${prrbId}`,
                type: 'get',
            });

            if (bill.prrb_payment_method != null) {
                payBillModal.openPaymentInstructionModal(bill);
            } else {
                payBillModal.openPaymentMethodModal(prrbId);
            }
        },
        openPaymentMethodModal: async function(prrbId) {
            // clear value
            $('#paymentMethodModal #payment-summary').html('...');
            $('#paymentMethodModal #footer-payment-method').text('...');
            $('#paymentMethodModal #footer-bill-total-amount').text('...');
            FormDataJson.clear('#form-payment-method');
            $('#paymentMethodModal #form-payment-method .payment-method-list__item').each(function() {
                $(this).removeClass('active');
            });

            let paymentMethods = await $.ajax({
                async: true,
                url: `${_baseURL}/api/student/payment-method`,
                type: 'get',
            });
            paymentMethods = paymentMethods.filter(item => item.payment_methods.length > 0);

            $('#paymentMethodModal #accordionPaymentMethod').html(`
                ${
                    paymentMethods.map(type => {
                        return `
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_${type.code}">
                                        ${type.name}
                                    </button>
                                </h2>
                                <div id="collapse_${type.code}" class="accordion-collapse collapse">
                                    <div class="accordion-body">
                                        <ul class="payment-method-list">
                                            ${
                                                type.payment_methods.map(method => {
                                                    return `
                                                        <li class="payment-method-list__item">
                                                            <input class="form-check-input me-1" type="radio" name="payment_method" value="${method.mpm_key}" />
                                                            <span>${method.mpm_name}</span>
                                                        </li>
                                                    `;
                                                }).join('')
                                            }
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('')
                }
            `);

            $('#paymentMethodModal #form-payment-method input[name="prrb_id"]').val(prrbId);
            paymentMethodModal.show();
        },
        openPaymentInstructionModal: async function(bill) {
            const paymentMethod = await $.ajax({
                async: true,
                url: `${_baseURL}/api/student/payment-method/${bill.prrb_payment_method}`,
                type: 'get',
            });

            const paymentApprovals = await $.ajax({
                async: true,
                url: `${_baseURL}/api/student/payment/${prrId}/bill/${bill.prrb_id}/approval`,
                type: 'get',
            });

            const paymentTransactions = await $.ajax({
                async: true,
                url: `${_baseURL}/api/student/payment/${prrId}/bill/${bill.prrb_id}/transaction`,
                type: 'get',
            });

            if (paymentMethod.mpm_type != 'bank_transfer_manual') {
                $('#paymentInstructionModal #va-number-exp-warning').html(`
                    <div class="alert p-1 alert-warning d-flex flex-row align-items-start">
                        <div class="me-1">
                            <i data-feather="alert-triangle"></i>
                        </div>
                        <div>
                            ${paymentMethod.mpm_type == 'bank_transfer_va' ? 'Nomor Virtual Account' : 'Kode Bill'} akan kadaluwarsa pada ${moment(bill.prrb_midtrans_transaction_exp).format('DD-MM-YYYY HH:mm')}. Segera selesaikan pembayaran Anda.
                        </div>
                    </div>
                `);
            } else {
                $('#paymentInstructionModal #va-number-exp-warning').html('');
            }

            $('#paymentInstructionModal #table-pay-data tbody').html(`
                <tr>
                    <td style="width: 50%">Metode Pembayaran</td>
                    <td>${paymentMethod.mpm_name}</td>
                </tr>
                ${
                    paymentMethod.mpm_type == 'bank_transfer_va' ? `
                        <tr>
                            <td style="width: 50%">Nomor Virtual Account</td>
                            <td>${bill.prrb_va_number}</td>
                        </tr>
                    ` : paymentMethod.mpm_type == 'bank_transfer_manual' ? `
                            <tr>
                                <td style="width: 50%">Nomor Rekening</td>
                                <td>${bill.prrb_account_number}</td>
                            </tr>
                        ` : paymentMethod.mpm_type == 'bank_transfer_bill_payment' ? `
                                <tr>
                                    <td style="width: 50%">Biller Code</td>
                                    <td>${bill.prrb_mandiri_biller_code}</td>
                                </tr>
                                <tr>
                                    <td style="width: 50%">Bill Key</td>
                                    <td>${bill.prrb_mandiri_bill_key}</td>
                                </tr>
                            ` : ''
                }
                <tr>
                    <td style="width: 50%">Jumlah Total Tagihan</td>
                    <td>${Rupiah.format(bill.prrb_amount + bill.prrb_admin_cost)}</td>
                </tr>
            `);

            // if (paymentMethod.mpm_type == 'bank_transfer_manual') {
            //     if (!bill.prrb_manual_name) {
            //         $('#paymentInstructionModal #payment-evidence-section').html(`
            //             <h5 class="mt-3 mb-1">Upload Bukti Pembayaran</h5>
            //             <form onsubmit="payBillModal.uploadPaymentEvidence(event)" style="width: 400px">
            //                 <input type="hidden" name="prr_id" value="${prrId}">
            //                 <input type="hidden" name="prrb_id" value="${bill.prrb_id}">
            //                 <div class="mb-1">
            //                     <label class="form-label">Nama Pemilik Rekening</label>
            //                     <input name="account_owner_name" type="text" class="form-control">
            //                 </div>
            //                 <div class="mb-1">
            //                     <label class="form-label">Nomor Rekening</label>
            //                     <input name="account_number" type="text" class="form-control">
            //                 </div>
            //                 <div class="mb-1">
            //                     <label class="form-label">File Bukti Bayar</label>
            //                     <input name="file_evidence" type="file" class="form-control">
            //                 </div>
            //                 <button type="submit" class="btn btn-primary">Upload Bukti Pembayaran</button>
            //             </form>
            //         `);
            //     } else {
            //         $('#paymentInstructionModal #payment-evidence-section').html(`
            //             <h5 class="mt-3 mb-1">Bukti Pembayaran</h5>
            //             <table class="table table-bordered">
            //                 <tbody>
            //                     <tr>
            //                         <td style="width: 50%">Nama Pengirim</td>
            //                         <td>${bill.prrb_manual_name}</td>
            //                         </tr>
            //                     <tr>
            //                         <td style="width: 50%">Nomor Rekening Pengirim</td>
            //                         <td>${bill.prrb_manual_norek}</td>
            //                     </tr>
            //                     <tr>
            //                         <td style="width: 50%">File Bukti Pembayaran</td>
            //                         <td>
            //                             <a href="${_baseURL}/api/download-cloud?path=${bill.prrb_manual_evidence}" class="p-0 btn btn-link btn-sm">
            //                                 <i data-feather="download"></i>&nbsp;&nbsp;
            //                                 Download Bukti Pembayaran
            //                             </a>
            //                         </td>
            //                     </tr>
            //                     <tr>
            //                         <td style="width: 50%">Status Approval</td>
            //                         <td>
            //                             ${
            //                                 bill.prrb_manual_status == 'waiting' ?
            //                                     '<span class="badge bg-warning">Menunggu Approval</span>'
            //                                     : bill.prrb_manual_status == 'rejected' ?
            //                                         '<span class="badge bg-danger">Ditolak</span>'
            //                                         : bill.prrb_manual_status == 'accepted' ?
            //                                             '<span class="badge bg-success">Disetujui</span>'
            //                                             : 'N/A'
            //                             }
            //                         </td>
            //                     </tr>
            //                     <tr>
            //                         <td style="width: 50%">Catatan Approval</td>
            //                         <td>${bill.prrb_manual_note ?? ''}</td>
            //                     </tr>
            //                 </tbody>
            //             </table>
            //         `);

            //         if (bill.prrb_manual_status == 'rejected') {
            //             $('#paymentInstructionModal #payment-evidence-section').append(`
            //                 <h5 class="mt-3 mb-1">Upload Kembali Bukti Pembayaran</h5>
            //                 <form onsubmit="payBillModal.uploadPaymentEvidence(event)" style="width: 400px">
            //                     <input type="hidden" name="prr_id" value="${prrId}">
            //                     <input type="hidden" name="prrb_id" value="${bill.prrb_id}">
            //                     <div class="mb-1">
            //                         <label class="form-label">Nama Pemilik Rekening</label>
            //                         <input name="account_owner_name" type="text" class="form-control">
            //                     </div>
            //                     <div class="mb-1">
            //                         <label class="form-label">Nomor Rekening</label>
            //                         <input name="account_number" type="text" class="form-control">
            //                     </div>
            //                     <div class="mb-1">
            //                         <label class="form-label">File Bukti Bayar</label>
            //                         <input name="file_evidence" type="file" class="form-control">
            //                     </div>
            //                     <button type="submit" class="btn btn-primary">Upload Bukti Pembayaran</button>
            //                 </form>
            //             `);
            //         }
            //     }
            // }

            $('#paymentInstructionModal #btn-open-upload-evidence-modal').attr('onclick', `payBillModal.openUploadEvidenceModal(${bill.prrb_id})`);

            $('#paymentInstructionModal #table-approval-history tbody').html(`
                ${
                    paymentApprovals.map((item, index) => {
                        return `
                            <tr>
                                <td>${index+1}</td>
                                <td>${Rupiah.format(item.pma_amount)}</td>
                                <td>${item.pma_payment_time}</td>
                                <td>${item.pma_approval_status}</td>
                                <td>
                                    <button class="btn btn-info btn-sm btn-icon rounded" onclick="payBillModal.openPaymentApprovalDetailModal(${item.pma_id})">
                                        <i data-feather="eye"></i>
                                    </button>
                                </td>
                            </tr>
                        `
                    }).join('')
                }
            `);

            $('#paymentInstructionModal #table-transaction-history tbody').html(`
                ${
                    paymentTransactions.map((item, index) => {
                        return `
                            <tr>
                                <td>${index+1}</td>
                                <td>${Rupiah.format(item.prrt_amount)}</td>
                                <td>${item.prrt_time}</td>
                                <td>
                                    Metode Pembayaran: ${item.prrt_payment_method}<br>
                                    Nomor Virtual Account: ${item.prrt_va_number}<br>
                                    Nomor Rekening Tujuan: ${item.prrt_account_number}<br>
                                    Kode Billing: ${item.prrt_mandiri_bill_key}<br>
                                    Kode Biller: ${item.prrt_mandiri_biller_code}
                                </td>
                            </tr>
                        `
                    }).join('')
                }
            `);

            if (!bill.prrb_manual_name) {
                $('#paymentInstructionModal .modal-footer').html(`
                    <div class="d-flex justify-content-end">
                        <button onclick="payBillModal.resetPaymentMethod(${bill.prrb_id})" class="btn btn-outline-warning">Ganti Metode Pembayaran</button>
                    </div>
                `);
            } else {
                $('#paymentInstructionModal .modal-footer').html('');
            }

            feather.replace();
            paymentInstructionModal.show();
        },
        openUploadEvidenceModal: function(prrbId) {
            Modal.show({
                type: 'form',
                modalTitle: 'Unggah Bukti Pembayaran',
                modalSize: 'lg',
                config: {
                    formId: 'form-upload-evidence',
                    formActionUrl: `${_baseURL}/api/student/payment/${prrId}/bill/${prrbId}/evidence`,
                    formType: 'add',
                    isTwoColumn: true,
                    fields: {
                        student_type: {
                            isHidden: true,
                            content: {
                                template: '<input type="hidden" name="student_type" value=":value" />',
                                value: userMaster.student ? 'student' : 'new_student'
                            },
                        },
                        sender_account_name: {
                            title: 'Nama Rekening Anda',
                            content: {
                                template: '<input type="text" name="sender_account_name" class="form-control" />',
                            },
                        },
                        sender_account_number: {
                            title: 'Nomor Rekening Anda',
                            content: {
                                template: '<input type="text" name="sender_account_number" class="form-control" />',
                            },
                        },
                        sender_bank: {
                            title: 'Bank Rekening Anda',
                            content: {
                                template: '<input type="text" name="sender_bank" class="form-control" />',
                            },
                        },
                        amount: {
                            title: 'Nominal yang dibayar',
                            content: {
                                template: '<input type="number" name="amount" class="form-control" />',
                            },
                        },
                        receiver_account_number: {
                            title: 'Nomor Rekening Tujuan',
                            content: {
                                template: '<input type="text" name="receiver_account_number" class="form-control" />',
                            },
                        },
                        receiver_account_name: {
                            title: 'Nama Rekening Tujuan',
                            content: {
                                template: '<input type="text" name="receiver_account_name" class="form-control" />',
                            },
                        },
                        receiver_bank: {
                            title: 'Bank Tujuan',
                            content: {
                                template: '<input type="text" name="receiver_bank" class="form-control" />',
                            },
                        },
                        payment_date: {
                            title: 'Waktu Pembayaran',
                            content: {
                                template: '<input type="datetime-local" name="payment_time" class="form-control" />',
                            },
                        },
                        evidence: {
                            title: 'Bukti Pembayaran',
                            content: {
                                template: '<input type="file" name="evidence" class="form-control" />',
                            },
                        },
                    },
                    formSubmitLabel: 'Unggah',
                    callback: function(e) {
                        // close payment instruction modal
                        paymentInstructionModal.hide();

                        // reopen payment instruction modal
                        payBillModal.open(prrbId);
                    },
                },
            });
        },
        openPaymentApprovalDetailModal: async function(pmaId) {
            const approval = await $.ajax({
                async: true,
                url: `${_baseURL}/api/student/payment/${prrId}/bill/${bill.prrb_id}/approval/${pmaId}`,
                type: 'get',
            });

            Modal.show({
                type: 'detail',
                modalTitle: 'Detail Approval Pembayaran',
                modalSize: 'lg',
                config: {
                    isTwoColumn: true,
                    fields: {
                        student_name: {
                            title: 'Nama Mahasiswa',
                            content: {
                                template: `:value`,
                                value: approval.pma_student_name,
                            },
                        },
                        student_id: {
                            title: 'NIM',
                            content: {
                                template: `:value`,
                                value: approval.pma_student_id ?? '-',
                            },
                        },
                        student_studyprogram: {
                            title: 'Program Studi',
                            content: {
                                template: `:value`,
                                value: approval.pma_student_studyprogram,
                            },
                        },
                        sender_account_name: {
                            title: 'Nama Rekening Pengirim',
                            content: {
                                template: `:value`,
                                value: approval.pma_sender_account_name,
                            },
                        },
                        sender_account_number: {
                            title: 'Nomor Rekening Pengirim',
                            content: {
                                template: `:value`,
                                value: approval.pma_sender_account_number,
                            },
                        },
                        sender_bank: {
                            title: 'Bank Pengirim',
                            content: {
                                template: `:value`,
                                value: approval.pma_sender_bank,
                            },
                        },
                        amount: {
                            title: 'Nominal Pembayaran',
                            content: {
                                template: `:value`,
                                value: Rupiah.format(approval.pma_amount),
                            },
                        },
                        receiver_account_number: {
                            title: 'Nomor Rekening Tujuan',
                            content: {
                                template: `:value`,
                                value: approval.pma_receiver_account_number,
                            },
                        },
                        receiver_account_name: {
                            title: 'Nama Rekening Tujuan',
                            content: {
                                template: `:value`,
                                value: approval.pma_receiver_account_name,
                            },
                        },
                        receiver_bank: {
                            title: 'Bank Tujuan',
                            content: {
                                template: `:value`,
                                value: approval.pma_receiver_bank,
                            },
                        },
                        payment_time: {
                            title: 'Waktu Pembayaran',
                            content: {
                                template: `:value`,
                                value: moment(approval.pma_payment_time).format('DD/MM/YYYY HH:mm:ss'),
                            },
                        },
                        evidence: {
                            title: 'Bukti Pembayaran',
                            content: {
                                template: '<a href=":link" target="_blank">Download</a>',
                                link: _baseURL+'/api/download-cloud?path='+approval.pma_evidence,
                            },
                        },
                        approval_status: {
                            title: 'Status Approval',
                            content: {
                                template: `:value`,
                                value: approval.pma_approval_status == 'waiting' ? 'Menunggu Pembayaran'
                                    : approval.pma_approval_status == 'accepted' ? 'Diterima'
                                    : approval.pma_approval_status == 'rejected' ? 'Ditolak' : '-',
                            },
                        },
                        notes: {
                            title: 'Catatan Approval',
                            content: {
                                template: `:value`,
                                value: approval.pma_notes ?? '-',
                            },
                        },
                    },
                    callback: function() {
                        feather.replace();
                    }
                },
            });

        },
        paymentMethodChangeHandler: async function(e) {
            $('.payment-method-list__item').each(function() {
                $(this).removeClass('active');
            });

            const prrbId = parseInt($('#form-payment-method input[name="prrb_id"]').val());

            const paymentMethodKey = e.target.value;
            $(`.payment-method-list__item > input[value="${paymentMethodKey}"]`).parent().addClass('active');

            const bill = await $.ajax({
                async: true,
                url: `${_baseURL}/api/student/payment/${prrId}/bill/${prrbId}`,
                type: 'get',
            });
            const billAmount = bill.prrb_amount;

            const paymentMethod = await $.ajax({
                async: true,
                url: `${_baseURL}/api/student/payment-method/${paymentMethodKey}`,
                type: 'get',
            });
            const adminFee = paymentMethod.mpm_fee;

            // set payment summary section
            $('#paymentMethodModal #payment-summary').html(`
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>Tagihan Daftar Ulang</td>
                            <td>${Rupiah.format(billAmount)}</td>
                        </tr>
                        <tr>
                            <td>Biaya Admin</td>
                            <td>${Rupiah.format(adminFee)}</td>
                        </tr>
                    </tbody>
                </table>
            `);

            // set footer section
            $('#paymentMethodModal #footer-payment-method').text(paymentMethod.mpm_name);
            $('#paymentMethodModal #footer-bill-total-amount').text(Rupiah.format(billAmount + adminFee));
        },
        selectPaymentMethod: async function() {
            const confirmed = await _swalConfirmSync({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin memilih metode pembayaran ini?',
            });

            if(!confirmed) return;

            const {
                prrb_id: prrbId,
                payment_method: paymentMethodKey
            } = FormDataJson.toJson('#paymentMethodModal #form-payment-method');

            try {
                const res = await $.ajax({
                    async: true,
                    url: `${_baseURL}/api/student/payment/${prrId}/bill/${prrbId}/select-method`,
                    type: 'post',
                    data: {
                        payment_method: paymentMethodKey,
                    },
                });

                if (res.success) {
                    _toastr.success(res.message, 'Sukses');
                    // hide payment method modal
                    paymentMethodModal.hide();
                    // open payment instruction modal
                    payBillModal.open(prrbId);
                }

            } catch (error) {
                const res = error.responseJSON;
                _toastr.error(res.message, 'Gagal');
            }
        },
        resetPaymentMethod: async function(prrbId) {
            const confirmed = await _swalConfirmSync({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin mengganti metode pembayaran?',
            });

            if(!confirmed) return;

            try {
                const res = await $.ajax({
                    async: true,
                    url: `${_baseURL}/api/student/payment/${prrId}/bill/${prrbId}/reset-method`,
                    type: 'post',
                });

                if (res.success) {
                    _toastr.success(res.message, 'Sukses');
                    // hide payment method modal
                    paymentInstructionModal.hide();
                    // open payment method modal
                    payBillModal.open(prrbId);
                }

            } catch (error) {
                const res = error.responseJSON;
                _toastr.error(res.message, 'Gagal');
            }
        },
        uploadPaymentEvidence: async function(e) {
            e.preventDefault();

            const target = e.currentTarget;
            const formData = new FormData(target);
            const prrIdValue = formData.get('prr_id'); formData.delete('prr_id');
            const prrbIdValue = formData.get('prrb_id'); formData.delete('prrb_id');

            // for (const value of formData.values()) { console.log(value) }; return;

            try {
                const res = await $.ajax({
                    async: true,
                    type: "POST",
                    url: `${_baseURL}/api/student/payment/${prrIdValue}/bill/${prrbIdValue}/evidence`,
                    data: formData,
                    processData: false,
                    contentType: false,
                    cache: false,
                });

                if (res.success) {
                    _toastr.success(res.message, 'Sukses');
                    paymentInstructionModal.hide();
                    await deleteRequestCache(`${_baseURL}/api/student/payment/${prrId}`);
                    payBillModal.open(prrbIdValue);
                } else {
                    _toastr.error(res.message, 'Gagal');
                }

            } catch (error) {
                console.error('Error Happen', error);
            }
        },
    }

    const paymentDetailModal = {
        bsModal: new bootstrap.Modal(document.getElementById('paymentDetailModal')),
        open: async function(prrbId) {
            const bill = await $.ajax({
                async: true,
                url: `${_baseURL}/api/student/payment/${prrId}/bill/${prrbId}`,
                type: 'get',
            });

            const paymentMethod = await $.ajax({
                async: true,
                url: `${_baseURL}/api/student/payment-method/${bill.prrb_payment_method}`,
                type: 'get',
            });

            $('#paymentDetailModal .modal-body').html(`
                <table class="table table-bordered mb-3">
                    <tbody>
                        <tr>
                            <td style="width: 50%">Metode Pembayaran</td>
                            <td>${paymentMethod.mpm_name}</td>
                        </tr>
                        ${
                            paymentMethod.mpm_type == 'bank_transfer_va' ? `
                                <tr>
                                    <td>Nomor Virtual Account</td>
                                    <td>${bill.prrb_va_number}</td>
                                </tr>
                            ` : paymentMethod.mpm_type == 'bank_transfer_manual' ? `
                                    <tr>
                                        <td>Nomor Rekening</td>
                                        <td>${bill.prrb_account_number}</td>
                                    </tr>
                                ` : paymentMethod.mpm_type == 'bank_transfer_bill_payment' ? `
                                        <tr>
                                            <td>Biller Code</td>
                                            <td>${bill.prrb_mandiri_biller_code}</td>
                                        </tr>
                                        <tr>
                                            <td>Bill Key</td>
                                            <td>${bill.prrb_mandiri_bill_key}</td>
                                        </tr>
                                    ` : ''
                        }
                        <tr>
                            <td style="width: 50%">Dibayar Pada</td>
                            <td>${moment(bill.prrb_paid_date).format('DD-MM-YYYY HH:mm')}</td>
                        </tr>
                    </tbody>
                </table>

                ${
                    paymentMethod.mpm_type == 'bank_transfer_manual' ? `
                        <div class="mb-3">
                            <h5 class="mb-1">Bukti Pembayaran</h5>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td style="width: 50%">Nama Pengirim</td>
                                        <td>${bill.prrb_manual_name}</td>
                                        </tr>
                                    <tr>
                                        <td style="width: 50%">Nomor Rekening Pengirim</td>
                                        <td>${bill.prrb_manual_norek}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 50%">File Bukti Pembayaran</td>
                                        <td>
                                            <a href="${_baseURL}/api/download-cloud?path=${bill.prrb_manual_evidence}" class="p-0 btn btn-link btn-sm">
                                                <i data-feather="download"></i>&nbsp;&nbsp;
                                                Download Bukti Pembayaran
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 50%">Status Approval</td>
                                        <td>
                                            ${
                                                bill.prrb_manual_status == 'waiting' ?
                                                    '<span class="badge bg-warning">Menunggu Approval</span>'
                                                    : bill.prrb_manual_status == 'rejected' ?
                                                        '<span class="badge bg-danger">Ditolak</span>'
                                                        : bill.prrb_manual_status == 'accepted' ?
                                                            '<span class="badge bg-success">Disetujui</span>'
                                                            : 'N/A'
                                            }
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 50%">Catatan Approval</td>
                                        <td>${bill.prrb_manual_note ?? ''}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    ` : ''
                }

                <div>
                    <h5 class="mb-1">Rincian Pembayaran</h5>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td style="width: 50%">
                                    Cicilan ke-${bill.prrb_order}
                                </td>
                                <td>${Rupiah.format(bill.prrb_amount)}</td>
                            </tr>
                            <tr>
                                <td style="width: 50%">Biaya Admin</td>
                                <td>${Rupiah.format(bill.prrb_admin_cost)}</td>
                            </tr>
                            <tr>
                                <th style="width: 50%">Total Bayar</th>
                                <th>${Rupiah.format(bill.prrb_amount + bill.prrb_admin_cost)}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            `);

            feather.replace();
            paymentDetailModal.bsModal.show();
        },
    }

    const paymentMethodModal = new bootstrap.Modal(document.getElementById('paymentMethodModal'));
    const paymentInstructionModal = new bootstrap.Modal(document.getElementById('paymentInstructionModal'));

</script>
@endprepend
