@extends('layouts.static_master')


@section('page_title', 'Detail Tagihan Per Mahasiswa')
@section('sidebar-size', 'collapsed')
@section('url_back', url('generate/other-invoice'))

@section('css_section')
    <style>
        .other-invoice-detail-filter {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            grid-gap: 1rem;
        }
        .table-on-cell tr td {
            padding: 10px 0px !important;

        }
        .table-on-cell tr td:not(:last-child) {
            padding-right: 10px !important;
        }
        .eazy-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }
    </style>
@endsection

@section('content')

@include('pages.generate._shortcuts', ['active' => null])

<div class="card">
    <div class="card-body">
        <div class="d-flex flex-column" style="gap: 2rem">
            <div class="other-invoice-detail-filter" style="flex-grow: 1">
                <div>
                    <label class="form-label">Periode Tagihan</label>
                    <select class="form-select">
                        <option value="0">Semua</option>
                        <option value="1" selected>2022 Ganjil</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Fakultas</label>
                    <select class="form-select">
                        <option value="0">Semua</option>
                        <option value="1" selected>Fakultas Informatika</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Program Studi</label>
                    <select class="form-select">
                        <option value="0">Semua</option>
                        <option value="1" selected>S1 Informatika</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Sistem Kuliah</label>
                    <select class="form-select">
                        <option value="0">Semua</option>
                        <option value="1" selected>Reguler</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Angkatan</label>
                    <select class="form-select">
                        <option value="0">Semua</option>
                        <option value="1" selected>2018</option>
                    </select>
                </div>
            </div>
            <div>
                <button class="btn btn-primary">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="other-invoice-detail-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>No</th>
                <th>NIM</th>
                <th>Nama Mahasiswa</th>
                <th>Rincian Tagihan</th>
                <th>Total Tagihan</th>
                <th>Status Mahasiswa</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection


@section('js_section')
<script>
    $(function(){
        _otherInvoiceDetailTable.init()
    })

    const _otherInvoiceDetailTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#other-invoice-detail-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/dt/other-invoice-detail',
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
                    {name: 'no', data: 'id'},
                    {name: 'student_id', data: 'student_id'},
                    {name: 'student_name', data: 'student_name'},
                    {
                        name: 'invoice_detail', 
                        data: 'invoice_detail',
                        render: (data) => {
                            let html = '<div class="d-flex flex-column" style="gap: .5rem">';
                            for (const itemName in data) {
                                html += `<span class="d-inline-block" style="white-space: nowrap">
                                    <span class="fw-bold">${itemName}</span><br>${Rupiah.format(data[itemName])}
                                </span>`
                            }
                            html += '</div>';
                            return html
                        }    
                    },
                    {
                        name: 'invoice_total', 
                        data: 'invoice_total',
                        render: (data) => {
                            return Rupiah.format(data)
                        }
                    },
                    {name: 'student_status', data: 'student_status'}
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"other-invoice-detail-actions d-flex align-items-end">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>' +
                    '<"eazy-table-wrapper" t>' +
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
                            <a onclick="_otherInvoiceDetailTableAction.generate()" class="dropdown-item" href="javascript:void(0);"><i data-feather="mail"></i>&nbsp;&nbsp;Generate Tagihan</a>
                            <a onclick="_otherInvoiceDetailTableAction.delete()" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete Tagihan</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const _otherInvoiceDetailTableAction = {
        tableRef: _otherInvoiceDetailTable,
        generate: function() {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin generate tagihan mahasiswa ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#356CFF',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Generate',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    // ex: do ajax request
                    Swal.fire({
                        icon: 'success',
                        text: 'Berhasil generate tagihan',
                    })
                }
            })
        },
        delete: function() {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin menghapus tagihan mahasiswa ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    // ex: do ajax request
                    Swal.fire({
                        icon: 'success',
                        text: 'Berhasil menghapus tagihan',
                    })
                }
            })
        },
    }
</script>
@endsection
