@extends('layouts.static_master')


@section('page_title', 'Generate')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
    <style>
        .other-invoice-filter {
            display: flex;
            gap: 2rem;
        }
    </style>
@endsection

@section('content')

@include('pages.generate._shortcuts', ['active' => 'other-invoice'])

<div class="card">
    <div class="card-body">
        <div class="d-flex flex-row" style="gap: 2rem">
            <div class="other-invoice-filter" style="flex-grow: 1">
                <div class="flex-grow-1">
                    <label class="form-label">Periode Tagihan</label>
                    <select class="form-select">
                        <option value="0">Semua</option>
                        <option value="1" selected>2022 Gasal</option>
                    </select>
                </div>
                <div class="flex-grow-1">
                    <label class="form-label">Komponen Tagihan</label>
                    <select class="form-select">
                        <option value="0">Semua</option>
                        <option value="1" selected>Biaya Wisuda</option>
                    </select>
                </div>
                <div class="flex-grow-1">
                    <label class="form-label">Fakultas</label>
                    <select class="form-select">
                        <option value="0" selected>Semua</option>
                        <option value="1">Fakultas Informatika</option>
                    </select>
                </div>
                <div class="flex-grow-1">
                    <label class="form-label">Sistem Kuliah</label>
                    <select class="form-select">
                        <option value="0" selected>Semua</option>
                    </select>
                </div>
            </div>
            <div class="d-flex align-items-end">
                <button class="btn btn-primary d-inline-block">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="other-invoice-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Fakultas/Program Studi</th>
                <th>Komponen Tagihan</th>
                <th>Total Tagihan</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection


@section('js_section')
<script>
    $(function(){
        _otherInvoiceTable.init()
    })

    const _otherInvoiceTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#other-invoice-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/dt/other-invoice',
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
                    {name: 'invoice_component', data: 'invoice_component'},
                    {
                        name: 'invoice_total', 
                        data: 'invoice_total',
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
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"other-invoice-actions d-flex align-items-end">>' +
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
                            <a class="dropdown-item" href="${_baseURL+'/generate/other-invoice-detail'}"><i data-feather="external-link"></i>&nbsp;&nbsp;Detail pada Unit ini</a>
                            <a onclick="_otherInvoiceTableActions.generate()" class="dropdown-item" href="javascript:void(0);"><i data-feather="mail"></i>&nbsp;&nbsp;Generate pada Unit ini</a>
                            <a onclick="_otherInvoiceTableActions.delete()" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete pada Unit ini</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const _otherInvoiceTableActions = {
        tableRef: _otherInvoiceTable,
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
