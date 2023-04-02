@extends('layouts.static_master')


@section('page_title', 'Generate')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
    <style>
        .registrant-invoice-filter {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            grid-gap: 1rem;
        }
    </style>
@endsection

@section('content')

@include('pages.generate._shortcuts', ['active' => 'registrant-invoice'])

<div class="card">
    <div class="card-body">
        <div class="d-flex flex-column" style="gap: 2rem">
            <div class="registrant-invoice-filter" style="flex-grow: 1">
                <div>
                    <label class="form-label">Periode Pendaftaran</label>
                    <select class="form-select">
                        <option value="0">Semua</option>
                        <option value="1" selected>Semester Genap 2016/2017</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Jenjang</label>
                    <select class="form-select">
                        <option value="0">Semua</option>
                        <option value="1" selected>S1</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Gelombang</label>
                    <select class="form-select">
                        <option value="0">Semua</option>
                        <option value="1" selected>Gelombang 1</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Jalur Pendaftaran</label>
                    <select class="form-select">
                        <option value="0">Semua</option>
                        <option value="1" selected>Umum</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Jenis Tagihan</label>
                    <select class="form-select">
                        <option value="0" selected>Semua</option>
                        <option value="1">Formulir Pendaftaran</option>
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
    @php
        $n_invoice_type = 3;
        $invoice_types = ['Jenis Tagihan 1', 'Jenis Tagihan 2', 'Jenis Tagihan 3'];
    @endphp
    <table id="registrant-invoice-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center" rowspan="2">Aksi</th>
                <th rowspan="2">No</th>
                <th rowspan="2">Program Studi</th>
                <th rowspan="1" colspan="{{ $n_invoice_type }}" class="text-center">Jenis Tagihan</th>
            </tr>
            <tr>
                @foreach($invoice_types as $type)
                    <th>{{ $type }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection


@section('js_section')
<script>
    $(function(){
        _registrantInvoiceTable.init()
    })

    const invoiceTypes = JSON.parse('{!! json_encode($invoice_types) !!}')

    const _registrantInvoiceTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#registrant-invoice-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/dt/registrant-invoice',
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
                    {name: 'study_program', data: 'study_program'},
                    ...invoiceTypes.map((item, i) => {
                        return {
                            name: `invoice_type_${i+1}`,
                            data: `invoice_type_${i+1}_nominal`,
                            render: (data) => {
                                return Rupiah.format(data);
                            }
                        }
                    })
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"registrant-invoice-actions d-flex align-items-end">>' +
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
                            <a onclick="_registrantInvoiceTableActions.edit()" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="_registrantInvoiceTableActions.delete()" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
                            <a onclick="_registrantInvoiceTableActions.generate()" class="dropdown-item" href="javascript:void(0);"><i data-feather="mail"></i>&nbsp;&nbsp;Generate</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const _registrantInvoiceTableActions = {
        tableRef: _registrantInvoiceTable,
        edit: function() {
            Modal.show({
                type: 'form',
                modalTitle: 'Edit Tagihan',
                config: {
                    formId: 'form-edit-registrant-invoice',
                    formActionUrl: '#',
                    fields: {
                        study_program: {
                            title: 'Program Studi',
                            content: {
                                template: '<input type="text" name="study_program" class="form-control" value="Sastra Jepang" disabled eazy-field-exclude />'
                            }
                        },
                        ...Object.assign(...invoiceTypes.map((item, i) => {
                            return {
                                [`invoice_type_${i}_nominal`]: {
                                    title: item,
                                    content: {
                                        template: `<input type="number" name="invoice_type_${i}_nominal" class="form-control" value="15000000" />`
                                    }
                                }
                            }
                        }))
                    },
                    formSubmitLabel: 'Edit Tagihan',
                    callback: function() {
                        // ex: reload table
                        Swal.fire({
                            icon: 'success',
                            text: 'Berhasil mengupdate tagihan',
                        }).then(() => {
                            this.tableRef.reload()
                        })
                    },
                },
            });
        },
        delete: function() {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin menghapus tagihan ini?',
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
        generate: function() {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin generate tagihan ini?',
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
        }
    }
</script>
@endsection
