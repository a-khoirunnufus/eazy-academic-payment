<div>
    <h4 class="mb-1">Detail Pembayaran</h4>
    <table class="table table-striped table-bordered">
        <tbody>
            <tr>
                <th style="width: 300px">Tenggat Pembayaran</th>
                <td>Selasa, 31 Januari 2023</td>
            </tr>
            <tr>
                <th style="width: 300px">Biaya Daftar Ulang</th>
                <td>Rp 15.000.000,00</td>
            </tr>
            <tr>
                <th style="width: 300px">Biaya Admin</th>
                <td>Rp 4.000,00</td>
            </tr>
            <tr>
                <th style="width: 300px">Total Tagihan</th>
                <td>Rp 15.004.000,00</td>
            </tr>
            <tr>
                <th style="width: 300px">Status Pembayaran</th>
                <td>Belum Lunas</td>
            </tr>
        </tbody>
    </table>
</div>

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

<div class="mt-3">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Pembayaran</th>
                <th>Rincian Biaya</th>
                <th>Alamat Transfer</th>
                <th>Tenggat Bayar</th>
                <th>Bukti Pembayaran</th>
                <th>Status Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Cicilan Ke-1</td>
                <td>
                    <div>
                        <p>
                            Biaya Daftar Ulang<br>
                            Rp 5.000.000,00
                        </p>
                        <p>
                            Biaya Admin<br>
                            Rp 4.000,00
                        </p>
                        <p class="fw-bolder">
                            Total Tagihan<br>
                            Rp 5.004.000,00
                        </p>
                    </div>
                </td>
                <td>BNI<br>1234567890</td>
                <td>Rabu, 11 Januari 2023</td>
                <td>
                    <button data-bs-toggle="modal" data-bs-target="#detailPaymentEvidenceModal" class="btn btn-sm btn-outline-primary"><i data-feather="eye"></i>&nbsp;&nbsp;Lihat Detail</button>
                </td>
                <td>Lunas</td>
            </tr>
            <tr>
                <td>Cicilan Ke-2</td>
                <td>
                    <div>
                        <p>
                            Biaya Daftar Ulang<br>
                            Rp 5.000.000,00
                        </p>
                        <p>
                            Biaya Admin<br>
                            Rp 4.000,00
                        </p>
                        <p class="fw-bolder">
                            Total Tagihan<br>
                            Rp 5.004.000,00
                        </p>
                    </div>
                </td>
                <td>BNI<br>1234567890</td>
                <td>Rabu, 11 Januari 2023</td>
                <td>
                    <p>Belum Diupload</p>
                    <button data-bs-toggle="modal" data-bs-target="#uploadPaymentEvidenceModal" class="btn btn-sm btn-outline-primary"><i data-feather="upload"></i>&nbsp;&nbsp;Upload</button>
                </td>
                <td>Belum Lunas</td>
            </tr>
            <tr>
                <td>Cicilan Ke-3</td>
                <td>
                    <div>
                        <p>
                            Biaya Daftar Ulang<br>
                            Rp 5.000.000,00
                        </p>
                        <p>
                            Biaya Admin<br>
                            Rp 4.000,00
                        </p>
                        <p class="fw-bolder">
                            Total Tagihan<br>
                            Rp 5.004.000,00
                        </p>
                    </div>
                </td>
                <td>BNI<br>1234567890</td>
                <td>Rabu, 11 Januari 2023</td>
                <td>
                    <p>Belum Diupload</p>
                    <button data-bs-toggle="modal" data-bs-target="#uploadPaymentEvidenceModal" class="btn btn-sm btn-outline-primary"><i data-feather="upload"></i>&nbsp;&nbsp;Upload</button>
                </td>
                <td>Belum Lunas</td>
            </tr>
        </tbody>
    </table>
</div>

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
