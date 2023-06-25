@push('styles')
    <style>
        #single-bill,
        #multiple-bills {
            display: none;
        }
        #single-bill.show,
        #multiple-bills.show {
            display: block;
        }
    </style>
@endpush

<div class="mb-3">
    <button onclick="paymentDetailTab.resetPayment()" class="btn btn-outline-warning">Ganti Metode Pembayaran</button>
</div>

<div id="single-bill">
    <h4 class="mb-1">Detail Pembayaran</h4>
    <table id="table-payment-detail-full" class="table table-striped table-bordered">
        <tbody></tbody>
    </table>

    <div class="mt-3">
        <h4 class="mb-1">Upload Bukti Bayar</h4>
        <form style="width: 400px">
            <div class="mb-1">
                <label class="form-label">Nama Pemilik Rekening</label>
                <input type="text" class="form-control">
            </div>
            <div class="mb-1">
                <label class="form-label">Nomor Rekening</label>
                <input type="text" class="form-control">
            </div>
            <div class="mb-1">
                <label class="form-label">File Bukti Bayar</label>
                <input type="file" class="form-control">
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

    <!-- Detail Payment Evidence Modal -->
    <div class="modal fade" id="detailPaymentEvidenceModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-white" style="padding: 2rem 3rem 3rem 3rem">
                    <h4 class="modal-title fw-bolder" id="detailPaymentEvidenceModalLabel">Detail Bukti Pembayaran</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3 pt-0">
                    <table class="eazy-table-info lg">
                        <tr>
                            <td>Nama Pemilik Rekening</td>
                            <td>: Ahmad Khoirunnufus</td>
                        </tr>
                        <tr>
                            <td>Nomor Rekening</td>
                            <td>: 1234567890</td>
                        </tr>
                        <tr>
                            <td>File Bukti Pembayaran</td>
                            <td>
                                : <button class="btn btn-outline-secondary btn-sm"><i data-feather="file"></i>&nbsp;&nbsp;bukti_pembayaran.pdf</button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Payment Evidence Modal -->
    <div class="modal fade" id="uploadPaymentEvidenceModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-white" style="padding: 2rem 3rem 3rem 3rem">
                    <h4 class="modal-title fw-bolder" id="uploadPaymentEvidenceModalLabel">Upload Bukti Pembayaran</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3 pt-0">
                    <form style="width: 400px">
                        <div class="mb-1">
                            <label class="form-label">Nama Pemilik Rekening</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="mb-1">
                            <label class="form-label">Nomor Rekening</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="mb-1">
                            <label class="form-label">File Bukti Bayar</label>
                            <input type="file" class="form-control">
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

    const paymentDetailTab = {
        showHandler: async function() {
            const payment = await getRequestCache(`${_baseURL}/api/student/payment/detail/${prrId}`);

            // check installment
            if (payment.payment_bill.length == 1) {
                // full 100% payment

                $('#single-bill').addClass('show');
                $('#multiple-bills').removeClass('show');

                const paymentBill = payment.payment_bill[0];
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
                        <td>${paymentBill.prrb_status == 'lunas' ? 'Lunas' : 'Belum Lunas'}</td>
                    </tr>
                    <tr>
                        <th style="width: 300px">Bukti Pembayaran</th>
                        <td>ONGOING</td>
                    </tr>
                `);

            }
            else if (payment.payment_bill.length > 1) {
                // installment option

                $('#single-bill').removeClass('show');
                $('#multiple-bills').addClass('show');

                const paymentBills = payment.payment_bill;
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
                                    <td>${item.prrb_status == 'lunas' ? 'Lunas' : 'Belum Lunas'}</td>
                                    <td>
                                        ${item.prrb_status == 'belum lunas' ?
                                            `<button data-bs-toggle="modal" data-bs-target="#uploadPaymentEvidenceModal" class="btn btn-sm btn-outline-primary"><i data-feather="upload"></i>&nbsp;&nbsp;Upload Bukti</button>`
                                            : `<button data-bs-toggle="modal" data-bs-target="#detailPaymentEvidenceModal" class="btn btn-sm btn-outline-primary"><i data-feather="eye"></i>&nbsp;&nbsp;Lihat Detail</button>`
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
    };

</script>
@endprepend
