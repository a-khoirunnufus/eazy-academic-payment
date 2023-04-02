@extends('layouts.static_master')


@section('page_title', 'Setting')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
    <style>
        .registration-form-filter {
            display: flex;
        }
    </style>
@endsection

@section('content')

@include('pages.setting._shortcuts', ['active' => 'registration-form'])

<div class="card">
    <div class="card-body">
        <div class="d-flex flex-row" style="gap: 2rem">
            <div class="registration-form-filter">
                <div>
                    <label class="form-label">Periode Masuk</label>
                    <select class="form-select">
                        <option disabled>Pilih periode masuk</option>
                        <option value="1" selected>2017/2018 Periode Genap</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>
            </div>
            <div class="d-flex align-items-end">
                <button class="btn btn-primary">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="registration-form-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Jenis Tagihan</th>
                <th>Jalur</th>
                <th>Nominal Tarif</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection


@section('js_section')
<script>
    $(function(){
        _registrationFormTable.init()
    })

    const _registrationFormTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#registration-form-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/dt/registration-form',
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
                    {name: 'invoice_type', data: 'invoice_type'},
                    {name: 'track', data: 'track'},
                    {name: 'rate', data: 'rate'},
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"registration-form-actions d-flex align-items-end">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>t' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function() {
                    $('.registration-form-actions').html(`
                        <div style="margin-bottom: 7px">
                            <button onclick="_registrationFormTableActions.add()" class="btn btn-primary me-1">
                                <span style="vertical-align: middle">
                                    <i data-feather="plus" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Tambah Skema
                                </span>
                            </button>
                        </div>
                    `)
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
                            <a onclick="_registrationFormTableActions.edit()" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="_registrationFormTableActions.delete()" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const _registrationFormTableActions = {
        tableRef: _registrationFormTable,
        add: function() {
            Modal.show({
                type: 'form',
                modalTitle: 'Tambah Skema',
                config: {
                    formId: 'form-add-registraton-form',
                    formActionUrl: '#',
                    fields: {
                        invoice_type: {
                            title: 'Jenis Tagihan',
                            content: {
                                template: 
                                    `<select class="form-select" name="invoice_type">
                                        <option selected>Open this select menu</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>`,
                            },
                        },
                        track: {
                            title: 'Jalur',
                            content: {
                                template: 
                                    `<select class="form-select" name="track">
                                        <option selected>Open this select menu</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>`,
                            },
                        },
                        rate: {
                            title: 'Nominal Tarif',
                            content: {
                                template: `<input type="number" name="rate" class="form-control" />`,
                            },
                        },
                    },
                    formSubmitLabel: 'Tambah Skema',
                    callback: function() {
                        // ex: reload table
                        Swal.fire({
                            icon: 'success',
                            text: 'Berhasil menambahkan skema',
                        }).then(() => {
                            this.tableRef.reload()
                        })
                    },
                },
            });
        },
        edit: function() {
            Modal.show({
                type: 'form',
                modalTitle: 'Edit Skema',
                config: {
                    formId: 'form-edit-rates',
                    formActionUrl: '#',
                    fields: {
                        invoice_type: {
                            title: 'Jenis Tagihan',
                            content: {
                                template: 
                                    `<select class="form-select" name="invoice_type">
                                        <option selected>Open this select menu</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>`,
                            },
                        },
                        track: {
                            title: 'Jalur',
                            content: {
                                template: 
                                    `<select class="form-select" name="track">
                                        <option selected>Open this select menu</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>`,
                            },
                        },
                        rate: {
                            title: 'Nominal Tarif',
                            content: {
                                template: `<input type="number" name="rate" class="form-control" value="150000" />`,
                            },
                        },
                    },
                    formSubmitLabel: 'Edit Skema',
                    callback: function() {
                        // ex: reload table
                        Swal.fire({
                            icon: 'success',
                            text: 'Berhasil mengupdate skema',
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
                text: 'Apakah anda yakin ingin menghapus skema ini?',
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
                        text: 'Berhasil menghapus skema',
                    })
                }
            })
        },
    }
</script>
@endsection
