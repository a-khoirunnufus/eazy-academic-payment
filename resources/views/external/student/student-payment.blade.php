@extends('layouts.student_master')


@section('page_title', 'Pembayaran Mahasiswa')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

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

<div id="student-info" class="card">
    <div class="card-body" style="width: 100%; overflow-x: auto">
        <div class="d-flex flex-row" style="gap: 4rem; width: max-content; overflow-x: auto">
            <div class="d-flex flex-row align-items-center flex-grow-1" style="gap: 1rem">
                <div class="round d-flex justify-content-center align-items-center bg-light" style="width: 65px; height: 65px">
                    <i style="width: 35px; height: 35px" data-feather="user"></i>
                </div>
                <div class="d-flex flex-column" style="gap: 5px">
                    <small class="d-block">Nama</small>
                    <span class="fw-bolder" style="font-size: 16px">Armansyah Adhikara</span>
                    <span class="text-secondary d-block">NIM : 1231023929 | TAK : 70</span>
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
    </div>
</div>

<div class="card">
    <table id="student-invoice-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>No</th>
                <th>Kode Tagihan</th>
                <th>Periode</th>
                <th>Bulan</th>
                <th>Cicilan Ke-</th>
                <th>Nominal Tagihan</th>
                <th>Nominal Pembayaran</th>
                <th>Status</th>
                <th>Metode Pembayaran</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
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
                <div id="invoice-header" class="p-1 border rounded mb-1">
                    <table class="table-info">
                        <tr>
                            <td>No Invoice</td>
                            <td>INV/20192/2010210</td>
                        </tr>
                        <tr>
                            <td>Tenggat Pembayaran</td>
                            <td>29 April 2023</td>
                        </tr>
                        <tr>
                            <td>Status Pembayaran</td>
                            <td>
                                <span class="badge bg-warning">Menunggu Pembayaran</span>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="student-data" class="p-1 border rounded mb-1">
                    <h5>Data Mahasiwa</h5>
                    <table class="table-info me-3">
                        <tr>
                            <td>NIM</td>
                            <td>123123123</td>
                        </tr>
                        <tr>
                            <td>Nama Mahasiswa</td>
                            <td>Fadhil Af Gani</td>
                        </tr>
                        <tr>
                            <td>Status Mahasiswa</td>
                            <td>Lulus</td>
                        </tr>
                        <tr>
                            <td>Fakultas</td>
                            <td>Ilmu Kesehatan</td>
                        </tr>
                        <tr>
                            <td>Program Studi</td>
                            <td>S1 Ilmu Keperawatan</td>
                        </tr>
                        <tr>
                            <td>Angkatan</td>
                            <td>2014</td>
                        </tr>
                        <tr>
                            <td>Tahun Kurikulum</td>
                            <td>2013</td>
                        </tr>
                    </table>
                </div>

                <table class="table table-bordered mb-3">
                    <thead>
                        <tr>
                            <th>Rincian Tagihan</th>
                            <th>Biaya</th>
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

                <div class="d-flex justify-content-end">
                    <a type="button" href="{{ url('student/proceed-payment') }}" class="btn btn-success btn-lg d-inline-block">Bayar</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('js_section')
<script>
    $(function(){
        _studentInvoiceTable.init()
    })

    const _studentInvoiceTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#student-invoice-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/dt/student/student-invoice',
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
                    {name: 'number', data: 'id'},
                    {name: 'invoice_code', data: 'invoice_code'},
                    {name: 'period', data: 'period'},
                    {name: 'month', data: 'month'},
                    {name: 'n_installment', data: 'n_installment'},
                    {name: 'invoice_nominal', data: 'invoice_nominal'},
                    {name: 'payment_nominal', data: 'payment_nominal'},
                    {name: 'status', data: 'status'},
                    {
                        name: 'payment_method',
                        data: 'payment_method',
                        render: (data) => {
                            let html = '<div class="d-flex flex-column" style="gap: .5rem">';
                            for (const itemName in data) {
                                html += `<span class="d-inline-block" style="white-space: nowrap">
                                    <span class="fw-bold">${itemName}</span><br>${data[itemName]}
                                </span>`
                            }
                            html += '</div>';
                            return html
                        }
                    },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"student-invoice-actions d-flex align-items-end">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>t' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function() {
                    feather.replace()
                }
            })
        },
        template: {
            rowAction: function(id) {
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#paymentDetailModal"><i data-feather="search"></i>&nbsp;&nbsp;Detail</a>
                            <a class="dropdown-item" href="/student/proceed-payment"><i data-feather="credit-card"></i>&nbsp;&nbsp;Lanjutkan Pembayaran</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const _studentInvoiceTableActions = {
        tableRef: _studentInvoiceTable,
        detail: function() {},
    }
</script>
@endsection
