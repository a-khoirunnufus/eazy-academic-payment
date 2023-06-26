@push('styles')
    <style>
        #single-bill,
        #multiple-bills,
        #form-upload-evidence-wrapper,
        #reset-payment-section {
            display: none;
        }
        #single-bill.show,
        #multiple-bills.show,
        #form-upload-evidence-wrapper.show,
        #reset-payment-section.show {
            display: block;
        }
    </style>
@endpush

<div id="reset-payment-section" class="show mb-3">
    <button onclick="paymentDetailTab.resetPayment()" class="btn btn-outline-warning">Ganti Metode Pembayaran</button>
</div>

<div id="single-bill">
    <h4 class="mb-1">Detail Pembayaran</h4>
    <table id="table-payment-detail-full" class="table table-striped table-bordered">
        <tbody></tbody>
    </table>

    <div id="form-upload-evidence-wrapper" class="mt-3">
        <h4 class="mb-1">Upload Bukti Bayar</h4>
        <form class="form-upload-evidence" style="width: 400px">
            <input type="hidden" name="prr_id" value="">
            <input type="hidden" name="prrb_id" value="">
            <div class="mb-1">
                <label class="form-label">Nama Pemilik Rekening</label>
                <input name="account_owner_name" type="text" class="form-control">
            </div>
            <div class="mb-1">
                <label class="form-label">Nomor Rekening</label>
                <input name="account_number" type="text" class="form-control">
            </div>
            <div class="mb-1">
                <label class="form-label">File Bukti Bayar</label>
                <input name="file_evidence" type="file" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>
</div>


<div id="multiple-bills">
    <table id="table-payment-detail-installment" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Pembayaran</th>
                <th>Rincian Biaya</th>
                <th>Total Biaya</th>
                <th>Alamat Transfer</th>
                <th>Tenggat Bayar</th>
                <th>Status Pembayaran</th>
                <th>Bukti Pembayaran</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <!-- Upload Payment Evidence Modal -->
    <div class="modal fade" id="uploadPaymentEvidenceModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-white" style="padding: 2rem 3rem 3rem 3rem">
                    <h4 class="modal-title fw-bolder" id="uploadPaymentEvidenceModalLabel">Upload Bukti Pembayaran</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3 pt-0">
                    <form class="form-upload-evidence" style="width: 400px">
                        <input type="hidden" name="prr_id" value="">
                        <input type="hidden" name="prrb_id" value="">
                        <div class="mb-1">
                            <label class="form-label">Nama Pemilik Rekening</label>
                            <input name="account_owner_name" type="text" class="form-control">
                        </div>
                        <div class="mb-1">
                            <label class="form-label">Nomor Rekening</label>
                            <input name="account_number" type="text" class="form-control">
                        </div>
                        <div class="mb-1">
                            <label class="form-label">File Bukti Bayar</label>
                            <input name="file_evidence" type="file" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@prepend('scripts')
<script>

    /**
     * @var prrId
     * @func getRequestCache()
     */

    $(function(){
        $('form.form-upload-evidence').each(function() {
            $(this).submit(async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
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
                        uploadPaymentEvidenceModal.hide();
                        await deleteRequestCache(`${_baseURL}/api/student/payment/detail/${prrId}`);
                        paymentDetailTab.showHandler();
                    } else {
                        _toastr.error(res.message, 'Gagal');
                    }

                } catch (error) {
                    console.error('Error Happen', error);
                }

            });
        });
    });

    const paymentDetailTab = {
        alwaysAllowReset: true,
        showHandler: async function() {
            const payment = await getRequestCache(`${_baseURL}/api/student/payment/detail/${prrId}`);

            // check installment
            if (payment.payment_bill.length == 1) {
                // full 100% payment

                $('#single-bill').addClass('show');
                $('#multiple-bills').removeClass('show');

                const paymentBill = payment.payment_bill[0];

                if (paymentBill.prrb_manual_name) {
                    !paymentDetailTab.alwaysAllowReset && $('#reset-payment-section').removeClass('show');
                    $('#form-upload-evidence-wrapper').removeClass('show');
                }

                if(paymentBill.prrb_manual_status == null || paymentBill.prrb_manual_status == 'rejected') {
                    $('#form-upload-evidence-wrapper').addClass('show');
                }

                $('#table-payment-detail-full tbody').html(`
                    <tr>
                        <th style="width: 300px">Tenggat Pembayaran</th>
                        <td>${moment(paymentBill.prrb_expired_date).format('DD-MM-YYYY')}</td>
                    </tr>
                    <tr>
                        <th style="width: 300px">Biaya Daftar Ulang</th>
                        <td>${Rupiah.format(paymentBill.prrb_amount)}</td>
                    </tr>
                    <tr>
                        <th style="width: 300px">Biaya Admin</th>
                        <td>${Rupiah.format(paymentBill.prrb_admin_cost)}</td>
                    </tr>
                    <tr>
                        <th style="width: 300px">Total Tagihan</th>
                        <td>${Rupiah.format(paymentBill.prrb_amount + paymentBill.prrb_admin_cost)}</td>
                    </tr>
                    <tr>
                        <th style="width: 300px">Status Pembayaran</th>
                        <td>${paymentBill.prrb_status == 'lunas' ?
                                '<span class="badge bg-success">Lunas</span>'
                                : '<span class="badge bg-danger">Belum Lunas</span>'
                            }
                        </td>
                    </tr>
                    <tr>
                        <th style="width: 300px">Bukti Pembayaran</th>
                        <td>${!paymentBill.prrb_manual_name ?
                                '<span class="badge bg-warning">Belum Diupload</span>'
                                : `
                                    <div>
                                        <p>Nama Pemilik Rekening : ${paymentBill.prrb_manual_name}</p>
                                        <p>Nomor Rekening : ${paymentBill.prrb_manual_norek}</p>
                                        <p>
                                            <a href="${_baseURL}/api/download-cloud?path=${paymentBill.prrb_manual_evidence}" class="p-0 btn btn-link btn-sm">
                                                <i data-feather="download"></i>&nbsp;&nbsp;
                                                Download Bukti Pembayaran
                                            </a>
                                        </p>
                                        <p class="mb-0">Status Approval : ${
                                            paymentBill.prrb_manual_status == 'waiting' ?
                                                '<span class="badge bg-warning">Menunggu Approval</span>'
                                                : paymentBill.prrb_manual_status == 'rejected' ?
                                                    '<span class="badge bg-danger">Ditolak</span>'
                                                    : paymentBill.prrb_manual_status == 'accepted' ?
                                                        '<span class="badge bg-success">Disetujui</span>'
                                                        : 'N/A'
                                        }</p>
                                    </div>
                                `
                            }</td>
                    </tr>
                `);
                feather.replace();

                $('#single-bill #form-upload-evidence-wrapper .form-upload-evidence input[name="prr_id"]').val(paymentBill.prr_id);
                $('#single-bill #form-upload-evidence-wrapper .form-upload-evidence input[name="prrb_id"]').val(paymentBill.prrb_id);
            }
            else if (payment.payment_bill.length > 1) {
                // installment option

                $('#single-bill').removeClass('show');
                $('#form-upload-evidence-wrapper').removeClass('show');
                $('#multiple-bills').addClass('show');

                const paymentBills = payment.payment_bill;

                if (paymentBills[0].prrb_manual_name) {
                    !paymentDetailTab.alwaysAllowReset && $('#reset-payment-section').removeClass('show');
                }

                $('#table-payment-detail-installment tbody').html(`
                    ${
                        paymentBills.map(item => {
                            return `
                                <tr>
                                    <td>Cicilan Ke-${item.prrb_order}</td>
                                    <td>
                                        <div>
                                            <p>
                                                ${payment.register ? 'Biaya Daftar Ulang' : 'Biaya Registrasi Semester Baru'}<br>
                                                ${Rupiah.format(item.prrb_amount)}
                                            </p>
                                            <p>
                                                Biaya Admin<br>
                                                ${Rupiah.format(item.prrb_admin_cost)}
                                            </p>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bolder">
                                            ${Rupiah.format(item.prrb_amount + item.prrb_admin_cost)}
                                        </div>
                                    </td>
                                    <td>${payment.payment_method.mpm_name}<br>${item.prrb_invoice_num}</td>
                                    <td>${moment(item.prrb_expired_date).format('DD-MM-YYYY')}</td>
                                    <td>${
                                        item.prrb_status == 'lunas' ?
                                            '<span class="badge bg-success">Lunas</span>'
                                            : item.prrb_status == 'belum lunas' ?
                                                '<span class="badge bg-danger">Belum Lunas</span>'
                                                : 'N/A'
                                    }</td>
                                    <td>
                                        ${!item.prrb_manual_name ? `
                                                <button
                                                    onclick="paymentDetailTab.openUploadEvidenceModal(event)"
                                                    data-eazy-prrId="${item.prr_id}"
                                                    data-eazy-prrbId="${item.prrb_id}"
                                                    class="btn btn-sm btn-outline-primary"
                                                >
                                                    <i data-feather="upload"></i>&nbsp;&nbsp;Upload Bukti
                                                </button>
                                            ` : `
                                                <button
                                                    onclick="paymentDetailTab.openDetailEvidenceModal(event)"
                                                    data-eazy-prrId="${item.prr_id}"
                                                    data-eazy-prrbId="${item.prrb_id}"
                                                    class="btn btn-sm btn-outline-primary"
                                                >
                                                    <i data-feather="eye"></i>&nbsp;&nbsp;Lihat Detail
                                                </button>
                                            `
                                        }
                                        ${item.prrb_manual_status == 'rejected' ? `
                                                <button
                                                    onclick="paymentDetailTab.openUploadEvidenceModal(event)"
                                                    data-eazy-prrId="${item.prr_id}"
                                                    data-eazy-prrbId="${item.prrb_id}"
                                                    class="btn btn-sm btn-outline-primary mt-1"
                                                >
                                                    <i data-feather="upload"></i>&nbsp;&nbsp;Upload Bukti
                                                </button>
                                            ` : ''
                                        }
                                    </td>
                                </tr>
                            `;
                        })
                    }
                `);
                feather.replace();
            }
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
                    url: `${_baseURL}/api/student/payment/reset-payment/${prrId}`,
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

            const payment = await getRequestCache(`${_baseURL}/api/student/payment/detail/${prrIdValue}`);
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

    const uploadPaymentEvidenceModal = new bootstrap.Modal(document.getElementById('uploadPaymentEvidenceModal'));

</script>
@endprepend
