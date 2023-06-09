@extends('layouts.static_master')

@section('page_title', 'Pembayaran Mahasiswa')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
    <style>
        .table-info {
            display: inline-block;
        }
        .table-info td {
            padding: 10px 0;
        }
        .table-info td:first-child {
            padding-right: 1rem;
        }

        .nav-tabs.custom .nav-item {
            flex-grow: 1;
        }
        .nav-tabs.custom .nav-link {
            width: -webkit-fill-available !important;
            height: 50px !important;
        }
        .nav-tabs.custom .nav-link.active {
            background-color: #f2f2f2 !important;
        }

        .eazy-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }
    </style>
@endsection

@section('content')

<div id="student-info" class="card mb-3">
    <div class="card-body" style="width: 100%">
        <div class="d-flex flex-row mb-3" style="gap: 2rem; flex-wrap: wrap">
            <div class="d-flex flex-row align-items-center flex-grow-1" style="gap: 1rem">
                <div class="round d-flex justify-content-center align-items-center bg-light" style="width: 65px; height: 65px">
                    <i style="width: 35px; height: 35px" data-feather="user"></i>
                </div>
                <div class="d-flex flex-column" style="gap: 5px">
                    <small class="d-block">Nama</small>
                    <span class="fw-bolder" style="font-size: 16px">Armansyah Adhikara</span>
                    <span class="text-secondary d-block">NIM : 1231023929</span>
                </div>
            </div>
            <div class="d-flex flex-row align-items-center flex-grow-1" style="gap: 1rem">
                <div class="round d-flex justify-content-center align-items-center bg-light" style="width: 65px; height: 65px">
                    <i style="width: 35px; height: 35px" data-feather="book-open"></i>
                </div>
                <div class="d-flex flex-column" style="gap: 5px">
                    <small class="d-block">Informasi Studi</small>
                    <span class="fw-bolder" style="font-size: 16px">Fakultas Informatika</span>
                    <span class="text-secondary d-block">Tahun Kurikulum 2013</span>
                </div>
            </div>
            <div class="d-flex flex-row align-items-center flex-grow-1" style="gap: 1rem">
                <div class="round d-flex justify-content-center align-items-center bg-light" style="width: 65px; height: 65px">
                    <i style="width: 35px; height: 35px" data-feather="bookmark"></i>
                </div>
                <div class="d-flex flex-column" style="gap: 5px">
                    <small class="d-block">Informasi Studi</small>
                    <span class="fw-bolder" style="font-size: 16px">S1 Informatika</span>
                    <span class="text-secondary d-block">Angkatan 2023</span>
                </div>
            </div>
            <div class="d-flex flex-row align-items-center flex-grow-1" style="gap: 1rem">
                <div class="round d-flex justify-content-center align-items-center bg-light" style="width: 65px; height: 65px">
                    <i style="width: 35px; height: 35px" data-feather="award"></i>
                </div>
                <div class="d-flex flex-column" style="gap: 5px">
                    <small class="d-block">Informasi Studi</small>
                    <span class="fw-bolder" style="font-size: 16px">IPK : 3.44</span>
                    <span class="text-secondary d-block">SKS Total : 138</span>
                </div>
            </div>
            <div class="d-flex flex-row align-items-center flex-grow-1" style="gap: 1rem">
                <div class="round d-flex justify-content-center align-items-center bg-light" style="width: 65px; height: 65px">
                    <i style="width: 35px; height: 35px" data-feather="bookmark"></i>
                </div>
                <div class="d-flex flex-column" style="gap: 5px">
                    <small class="d-block">Pembimbing</small>
                    <span class="fw-bolder" style="font-size: 16px">Dr. Achmad Maulana M.Kom</span>
                    <span class="text-secondary d-block">NIP : 131241214</span>
                </div>
            </div>
        </div>

        <div class="d-flex flex-row" style="gap: 1rem">
            <button class="btn btn-primary">
                <i data-feather="activity"></i>&nbsp;&nbsp;Generate VA
            </button>
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
    </div>
</div>

<div class="card">
    <div class="nav-tabs-shadow nav-align-top">
        <ul class="nav nav-tabs custom border-bottom" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-invoice_n_va" aria-controls="navs-invoice_n_va" aria-selected="true">Tagihan dan Virtual Account</button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-payment" aria-controls="navs-payment" aria-selected="false">Pembayaran</button>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="navs-invoice_n_va" role="tabpanel">
                <table id="invoice-table" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Periode Masuk</th>
                            <th>Kode Tagihan</th>
                            <th>Bulan</th>
                            <th>Cicilan</th>
                            <th>Total / Rincian Tagihan</th>
                            <th>Total / Rincian Potongan</th>
                            <th>Total / Rincian Beasiswa</th>
                            <th>Nominal</th>
                            <th>Total Bayar</th>
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
<div class="modal fade" id="paymentDetailModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-white" style="padding: 2rem 3rem 3rem 3rem">
                <h4 class="modal-title fw-bolder" id="paymentDetailModalLabel">Detail Pembayaran Tagihan</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 pt-0">
                <div id="invoice-header" class="d-flex flex-row justify-content-between align-items-start mb-3">
                    <div>
                        <img src="{{ url('images/logo-eazy-small.png') }}" style="height: 40px" alt="eazy logo">
                    </div>
                    <div>
                        <span class="d-block fw-bold text-end" style="font-size: 12px">19/01/2023, 22:00</span>
                        <span class="text-end" style="font-size: 10px">Invoice Pembayaran: <span class="fw-bold">INV/20192/2010210</span> | Telkom University</span>
                    </div>
                </div>

                <div id="student-data" class="mb-4">
                    <h4 class="fw-bolder mb-1">Data Mahasiwa</h4>
                    <div class="d-flex flex-row justify-content-between mb-4" style="gap: 2rem">
                        <div class="d-flex flex-column" style="gap: 5px">
                            <small class="d-block">Nama</small>
                            <span class="fw-bolder">Armansyah Adhikara</span>
                            <span class="text-secondary d-block">NIM : 1231023929 | TAK : 70</span>
                        </div>
                        <div class="d-flex flex-column" style="gap: 5px">
                            <small class="d-block">Informasi Studi</small>
                            <span class="fw-bolder">Fakultas Informatika</span>
                            <span class="text-secondary d-block">Tahun Kurikulum 2013</span>
                        </div>
                        <div class="d-flex flex-column" style="gap: 5px">
                            <small class="d-block">Informasi Studi</small>
                            <span class="fw-bolder">S1 Informatika</span>
                            <span class="text-secondary d-block">Angkatan 2023</span>
                        </div>
                        <div class="d-flex flex-column" style="gap: 5px">
                            <small class="d-block">Informasi Studi</small>
                            <span class="fw-bolder text-success">LULUS</span>
                            <span class="text-secondary d-block">IPK : 3.44</span>
                        </div>
                    </div>
                </div>

                <div id="payment-data" class="mb-4">
                    <h4 class="fw-bolder mb-1">Data Pembayaran</h4>
                    <table class="table-info">
                        <tr>
                            <td>Nomor Invoice</td>
                            <td>
                                <span class="fw-bold">:&nbsp;&nbsp;INV/20192/2010210<span>
                            </td>
                        </tr>
                        <tr>
                            <td>Tenggat Pembayaran</td>
                            <td>
                                <span class="fw-bold">:&nbsp;&nbsp;20-03-2023 / 00:00 WIB</span>    
                            </td>
                        </tr>
                        <tr>
                            <td>Status Pembayaran</td>
                            <td>
                                :&nbsp;&nbsp;<span class="badge bg-warning">Menunggu Pembayaran</span>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="invoice-detail" class="mb-3">
                    <h4 class="fw-bolder mb-2">Rincian Tagihan</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Komponen Tagihan</th>
                                <th>Biaya Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>BPP</td>
                                <td>Rp 500,000,00</td>
                            </tr>
                            <tr>
                                <td>Praktikum</td>
                                <td>Rp 200,000,00</td>
                            </tr>
                            <tr>
                                <td>SKS</td>
                                <td>Rp 20,000,00</td>
                            </tr>
                            <tr>
                                <td>Seragam</td>
                                <td>Rp 100,000,00</td>
                            </tr>
                            <tr>
                                <td>Denda</td>
                                <td>Rp 0,00</td>
                            </tr>
                            <tr>
                                <td>Beasiswa</td>
                                <td>Rp 0,00</td>
                            </tr>
                            <tr>
                                <td>Potongan</td>
                                <td>- Rp 200,000,00</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total Tagihan</th>
                                <th>Rp 700,000,00</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="d-flex justify-content-start" style="gap: 1rem">
                    <a type="button" href="{{ url('_student/proceed-payment') }}" class="btn btn-success d-inline-block">
                        <i data-feather="check-circle"></i>&nbsp;&nbsp;Bayar
                    </a>
                    <button type="button" class="btn btn-danger d-inline-block" data-bs-dismiss="modal">
                    <i data-feather="x"></i>&nbsp;&nbsp;Batal
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
        _invoiceTable.init();
        _paymentTable.init();
    })

    const _invoiceTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#invoice-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/_student/api/dt/invoice',
                },
                columns: [
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
                            return this.template.defaultCell(data, {bold: true});
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
                        name: 'invoice', 
                        render: (data, _, row) => {
                            return this.template.invoiceDetailCell(row.invoice_detail, row.invoice_total);
                        }    
                    },
                    {
                        name: 'discount', 
                        render: (data, _, row) => {
                            return this.template.invoiceDetailCell(row.discount_detail, row.discount_total);
                        }    
                    },
                    {
                        name: 'scholarship', 
                        render: (data, _, row) => {
                            return this.template.invoiceDetailCell(row.scholarship_detail, row.scholarship_total);
                        }    
                    },
                    {
                        name: 'all_invoice_total', 
                        data: 'all_invoice_total',
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
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-center header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"invoice-actions">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>' +
                    '<"eazy-table-wrapper" t>' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function() {
                    $('.invoice-actions').html(`
                        <h5 class="mb-0">Tagihan Lunas</h5>
                    `)
                    feather.replace()
                }
            })
        },
        template: { ..._datatableTemplates }
    }

    const _paymentTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#payment-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/_student/api/dt/payment',
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
                        <h5 class="mb-0">Daftar Transaksi dan Pembayaran</h5>
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
                            <a class="dropdown-item" href="${_baseURL+'/_student/proceed-payment'}"><i data-feather="credit-card"></i>&nbsp;&nbsp;Lanjutkan Pembayaran</a>
                        </div>
                    </div>
                `
            },
        }
    }
    
    const paymentDetailModal = new bootstrap.Modal(document.getElementById('paymentDetailModal'));
</script>
@endsection
