@extends('layouts.student_master')


@section('page_title', 'Pembayaran')
@section('sidebar-size', 'collapsed')
@section('url_back', url('student/student-payment'))

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
    </style>
@endsection

@section('content')

<div class="d-flex" style="gap: 1rem">
    <div class="card w-50">
        <div class="card-body">
            <h5 class="mb-2">Pilih Metode Pembayaran</h5>
            <nav>
                <div class="nav nav-tabs border-bottom" role="tablist">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#nav-e-money" type="button" role="tab">E-Money</button>
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nav-bank-transfer" type="button" role="tab">Transfer Bank</button>
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nav-input-pin" type="button" role="tab">Input Pin</button>
                </div>
            </nav>
            <div class="tab-content pt-1">
                <div class="tab-pane fade show active" id="nav-e-money" role="tabpanel" aria-labelledby="nav-home-tab">
                    <p>E Wallet</p>
                    <div class="d-flex" style="gap: 1rem">
                        <div class="p-1 border border-primary rounded" style="cursor: pointer">
                            <span class="h1 fw-bolder text-primary">G</span> <span class="text-primary">Gopay</span>
                        </div>
                        <div class="p-1 border rounded" style="cursor: pointer">
                            <span class="h1 fw-bolder text-warning">S</span> <span class="text-warning">Shopee</span>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-bank-transfer" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <p>Bank</p>
                    <div class="d-flex" style="gap: 1rem">
                        <div class="p-1 border border-primary rounded" style="cursor: pointer">
                            <span class="h1 fw-bolder text-primary">B</span> <span class="text-primary">BCA</span>
                        </div>
                        <div class="p-1 border rounded" style="cursor: pointer">
                            <span class="h1 fw-bolder text-primary">M</span> <span class="text-primary">Mandiri</span>
                        </div>
                        <div class="p-1 border rounded" style="cursor: pointer">
                            <span class="h1 fw-bolder text-primary">B</span> <span class="text-primary">BNI</span>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-input-pin" role="tabpanel" aria-labelledby="nav-contact-tab">
                    <div>
                        <label class="form-label-md">PIN</label>
                        <div class="d-flex">
                            <input type="number" class="form-control me-1" style="width: 250px" placeholder="Masukkan PIN">
                            <button class="btn btn-primary">Input</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card w-50">
        <div class="card-body">
            <h5 class="mb-2">Detail Pembayaran</h5>
            <div class="mb-2">
                <p class="text-secondary">Nomor E-Wallet</p>
                <h4 class="text-primary">Gopay</h4>
            </div>
            <div class="d-flex mb-3" style="gap: 2rem">
                <div class="d-inline-block">
                    <p class="text-secondary">Biaya Pendaftaran</p>
                    <h4 class="text-primary">Rp 150,000,00</h4>
                </div>
                <div class="d-inline-block">
                    <p class="text-secondary">Biaya Admin</p>
                    <h4 class="text-primary">Rp 2,500,00</h4>
                </div>
            </div>
            <div class="d-flex align-items-end">
                <div class="d-inline-block flex-grow-1">
                    <p class="text-secondary">Total yang harus dibayar</p>
                    <h4 class="text-success">Rp 137,500,00</h4>
                </div>
                <button type="button" class="btn btn-link d-inline-block" style="height: fit-content">Tata cara pembayaran</button>
                <button class="btn btn-success btn-icon d-inline-block" style="height: fit-content">
                    <i data-feather="printer"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection


@section('js_section')
<script>
    
</script>
@endsection
