@extends('layouts.static_master')


@section('page_title', 'Generate')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
    <style>
        .new-student-invoice-filter {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            grid-gap: 1rem;
        }
    </style>
@endsection

@section('content')

@include('pages.generate._shortcuts', ['active' => 'new-student-invoice'])

<div class="card">
    <div class="card-body">
        <div class="d-flex flex-column" style="gap: 2rem">
            <div class="new-student-invoice-filter" style="flex-grow: 1">
                <div>
                    <label class="form-label">Periode Masuk</label>
                    <select class="form-select">
                        <option value="0" selected>Semua</option>
                        <option value="1">Semester Genap 2016/2017</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Periode Tagihan</label>
                    <select class="form-select">
                        <option value="0">Semua</option>
                        <option value="1" selected>2022 Gasal</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Jalur Pendaftaran</label>
                    <select class="form-select">
                        <option value="0" selected>Semua</option>
                        <option value="1">Umum</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Gelombang</label>
                    <select class="form-select">
                        <option value="0" selected>Semua</option>
                        <option value="1">Gelombang 1</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Fakultas</label>
                    <select class="form-select">
                        <option value="0" selected>Semua</option>
                        <option value="1">Fakultas Informatika</option>
                    </select>
                </div>
                
                <div>
                    <label class="form-label">Sistem Kuliah</label>
                    <select class="form-select">
                        <option value="0" selected>Semua</option>
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
    <table id="new-student-invoice-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center" rowspan="2">Aksi</th>
                <th rowspan="2">Fakultas/Program Studi</th>
                <th rowspan="1" colspan="3" class="text-center">Total</th>
                <th rowspan="2">Jumlah Total</th>
            </tr>
            <tr>
                <th colspan="1">Tagihan</th>
                <th colspan="1">Denda</th>
                <th colspan="1">Potongan</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection


@section('js_section')
<script>
    $(function(){
        _newStudentInvoiceTable.init()
    })

    const _newStudentInvoiceTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#new-student-invoice-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/dt/new-student-invoice',
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
                    {name: 'unit_name', data: 'unit_name'},
                    {
                        name: 'invoice', 
                        data: 'invoice',
                        render: (data) => {
                            return Rupiah.format(data)
                        }
                    },
                    {
                        name: 'penalty', 
                        data: 'penalty',
                        render: (data) => {
                            return Rupiah.format(data)
                        }
                    },
                    {
                        name: 'discount', 
                        data: 'discount',
                        render: (data) => {
                            return Rupiah.format(data)
                        }
                    },
                    {
                        name: 'total', 
                        data: 'total',
                        render: (data) => {
                            return Rupiah.format(data)
                        }
                    },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"new-student-invoice-actions d-flex align-items-end">>' +
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
                            <a class="dropdown-item" href="${_baseURL+'/generate/student-invoice-detail'}"><i data-feather="external-link"></i>&nbsp;&nbsp;Detail pada Unit ini</a>
                            <a onclick="_newStudentInvoiceTableActions.generate()" class="dropdown-item" href="javascript:void(0);"><i data-feather="mail"></i>&nbsp;&nbsp;Generate pada Unit ini</a>
                            <a onclick="_newStudentInvoiceTableActions.delete()" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete pada Unit ini</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const _newStudentInvoiceTableActions = {
        tableRef: _newStudentInvoiceTable,
        generate: function() {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin generate tagihan pada unit ini?',
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
                text: 'Apakah anda yakin ingin menghapus tagihan pada unit ini?',
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
