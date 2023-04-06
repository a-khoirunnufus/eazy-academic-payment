@extends('_student.layout-master')

@section('page_title', 'Proses Pembayaran')
@section('sidebar-size', 'collapsed')
@section('url_back', url('_student/payment'))

@section('css_section')
    <style>
        .table-info {
            display: inline-block;
        }
        .table-info td {
            padding: 10px;
        }
        .table-info td:first-child {
            padding-right: 1rem;
            font-weight: 500;
        }

        .nav-tabs.custom .nav-item {
            flex-grow: 1;
        }
        .nav-tabs.custom .nav-link {
            /* width: -webkit-fill-available !important; */
            flex-grow: 1;
            height: 50px !important;
        }
        .nav-tabs.custom .nav-link.active {
            background-color: #f2f2f2 !important;
        }
    </style>
@endsection

@section('content')

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Pilih Metode Pembayaran</h5>
    </div>
    <div class="card-body">
        <div class="d-flex flex-row">
            <div id="select-payment-method" class="w-50 pe-3 border-end">
                <nav>
                    <div class="nav nav-tabs custom border-bottom" role="tablist">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nav-e-money" type="button" role="tab">E-Money</button>
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#nav-virtual-account" type="button" role="tab">Virtual Account</button>
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nav-input-pin" type="button" role="tab">Input Pin</button>
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nav-manual-payment" type="button" role="tab">Pembayaran Manual</button>
                    </div>
                </nav>
                <div class="tab-content pt-1">
                    <div class="tab-pane fade" id="nav-e-money" role="tabpanel" aria-labelledby="nav-home-tab">
                        <div class="mb-3">
                            <p>E Wallet</p>
                            <div class="d-flex" style="gap: 1rem">
                                <div class="d-flex align-items-center justify-content-center border rounded" style="min-width: 90px; padding: 0 1rem; height: 70px; cursor: pointer">
                                    <span class="h2 d-block fw-bolder text-primary mb-0">Gopay</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-center border rounded" style="min-width: 90px; padding: 0 1rem; height: 70px; cursor: pointer">
                                    <span class="h2 d-block fw-bolder text-warning mb-0">Shopee</span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label-md">Kode Voucher</label>
                            <div class="d-flex">
                                <input type="number" class="form-control me-1" style="width: 250px" placeholder="Masukkan Kode Voucher">
                                <button class="btn btn-primary">Gunakan Kode</button>
                            </div>
                            <div style="margin-top: 7px">
                              <small class="text-primary">
                                  * Kode voucher tidak berlaku selamanya
                              </small>
                            </div>
                        </div>
                        <button class="btn btn-primary"><i data-feather="activity"></i>&nbsp;&nbsp;Bayar</button>
                    </div>
                    <div class="tab-pane fade show active" id="nav-virtual-account" role="tabpanel" aria-labelledby="nav-profile-tab">
                        <div class="mb-3">
                            <p>Bank</p>
                            <div class="d-flex flex-wrap" style="gap: 1rem">
                                <div class="d-flex align-items-center justify-content-center border border-primary shadow rounded" style="min-width: 90px; padding: 0 1rem; height: 70px; cursor: pointer">
                                    <span class="h2 d-block fw-bolder text-primary mb-0">BNI</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-center border rounded" style="min-width: 90px; padding: 0 1rem; height: 70px; cursor: pointer">
                                    <span class="h2 d-block fw-bolder text-primary mb-0">BRIVA</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-center border rounded" style="min-width: 90px; padding: 0 1rem; height: 70px; cursor: pointer">
                                    <span class="h2 d-block fw-bolder text-primary mb-0">BCA</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-center border rounded" style="min-width: 90px; padding: 0 1rem; height: 70px; cursor: pointer">
                                    <span class="h2 d-block fw-bolder text-primary mb-0">mandiri</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-center border rounded" style="min-width: 90px; padding: 0 1rem; height: 70px; cursor: pointer">
                                    <span class="h2 d-block fw-bolder text-primary mb-0">PRIMA</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-center border rounded" style="min-width: 90px; padding: 0 1rem; height: 70px; cursor: pointer">
                                    <span class="h2 d-block fw-bolder text-primary mb-0">ATM</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-center border rounded" style="min-width: 90px; padding: 0 1rem; height: 70px; cursor: pointer">
                                    <span class="h2 d-block fw-bolder text-primary mb-0">ALTO</span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label-md">Kode Voucher</label>
                            <div class="d-flex">
                                <input type="number" class="form-control me-1" style="width: 250px" placeholder="Masukkan Kode Voucher">
                                <button class="btn btn-primary">Gunakan Kode</button>
                            </div>
                            <div style="margin-top: 7px">
                              <small class="text-primary">
                                  * Kode voucher tidak berlaku selamanya
                              </small>
                            </div>
                        </div>
                        <button class="btn btn-primary"><i data-feather="activity"></i>&nbsp;&nbsp;Generate VA</button>
                    </div>
                    <div class="tab-pane fade" id="nav-input-pin" role="tabpanel" aria-labelledby="nav-contact-tab">
                        <div class="mb-3">
                            <label class="form-label-md">PIN</label>
                            <div class="d-flex">
                                <input type="number" class="form-control me-1" style="width: 250px" placeholder="Masukkan PIN">
                                <button class="btn btn-primary">Input</button>
                            </div>
                        </div>
                        <button class="btn btn-primary"><i data-feather="activity"></i>&nbsp;&nbsp;Bayar</button>
                    </div>
                    <div class="tab-pane fade" id="nav-manual-payment" role="tabpanel" aria-labelledby="nav-contact-tab">
                        <div class="mb-3">
                            ...
                        </div>
                        <button class="btn btn-primary"><i data-feather="activity"></i>&nbsp;&nbsp;Bayar</button>
                    </div>
                </div>
            </div>

            <div id="payment-detail" class="w-50 ps-3">
                <h5 class="mb-2">Detail Pembayaran</h5>
                <div class="mb-2">
                    <p class="text-secondary">Nomor Virtual Akun Bank</p>
                    <h4 class="text-primary">BNI - 09412332121233</h4>
                </div>
                <div class="d-flex mb-3" style="gap: 2rem">
                    <div class="d-inline-block">
                        <p class="text-secondary">Biaya Pendaftaran</p>
                        <h4 class="text-primary">Rp 150,000,00</h4>
                    </div>
                    <div class="d-inline-block">
                        <p class="text-secondary">Potongan Biaya</p>
                        <h4 class="text-danger">Rp 25,500,00</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <div class="d-inline-block flex-grow-1">
                        <p class="text-secondary">Total yang harus dibayar</p>
                        <h4 class="text-success">Rp 124,500,00</h4>
                        <small class="text-success">Sudah termasuk potongan</small>
                    </div>
                    <button type="button" class="btn btn-outline-success d-inline-block text-success me-1" style="height: fit-content">Tata cara pembayaran&nbsp;&nbsp;<i data-feather="book"></i></button>
                    <button class="btn btn-success btn-icon d-inline-block" style="height: fit-content">
                        <i data-feather="printer"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('js_section')
<script>
    
</script>
@endsection
