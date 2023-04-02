@extends('layouts.static_master')


@section('page_title', 'Setting')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
    <style>
        .rates-filter {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            grid-gap: 1rem;
        }
    </style>
@endsection

@section('content')

@include('pages.setting._shortcuts', ['active' => 'rates'])

<div class="card">
    <div class="card-body">
        <div class="d-flex flex-column" style="gap: 2rem">
            <div class="rates-filter" style="flex-grow: 1">
                <div>
                    <label class="form-label">Periode Masuk</label>
                    <select class="form-select">
                        <option selected>Open this select menu</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Gelombang</label>
                    <select class="form-select">
                        <option selected>Open this select menu</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Jalur Pendaftaran</label>
                    <select class="form-select">
                        <option selected>Open this select menu</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Sistem Kuliah</label>
                    <select class="form-select">
                        <option selected>Open this select menu</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Program Studi</label>
                    <select class="form-select">
                        <option selected>Open this select menu</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Program Studi</label>
                    <select class="form-select">
                        <option selected>Open this select menu</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Komponen Tagihan</label>
                    <select class="form-select">
                        <option selected>Open this select menu</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
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
    <table id="rates-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>No</th>
                <th>Periode Masuk</th>
                <th>Gelombang</th>
                <th>Jalur Pendaftaran</th>
                <th>Sistem Kuliah</th>
                <th>Program Studi</th>
                <th>Komponen Tagihan</th>
                <th>Nominal Tarif</th>
                <th>Cicilan</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection


@section('js_section')
<script>
    $(function(){
        _ratesTable.init()
    })

    const _ratesTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#rates-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/dt/rates',
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
                    {name: 'entry_period', data: 'entry_period'},
                    {name: 'wave', data: 'wave'},
                    {name: 'registration_path', data: 'registration_path'},
                    {name: 'study_system', data: 'study_system'},
                    {name: 'study_program', data: 'study_program'},
                    {name: 'invoice_component', data: 'invoice_component'},
                    {name: 'rate', data: 'rate'},
                    {name: 'instalment', data: 'instalment'},
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"invoice-component-actions d-flex align-items-end">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>t' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function() {
                    $('.invoice-component-actions').html(`
                        <div style="margin-bottom: 7px">
                            <button onclick="_ratesTableActions.add()" class="btn btn-primary me-1">
                                <span style="vertical-align: middle">
                                    <i data-feather="plus" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Tambah Tarif
                                </span>
                            </button>
                            <button onclick="_ratesTableActions.copy()" class="btn btn-warning">
                                <span style="vertical-align: middle">
                                    <i data-feather="copy" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Salin
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
                            <a href="${_baseURL+'/setting/rates-per-course'}" class="dropdown-item" href="javascript:void(0);"><i data-feather="settings"></i>&nbsp;&nbsp;Setting Per-Matakuliah</a>
                            <a onclick="_ratesTableActions.edit()" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="_ratesTableActions.delete()" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const _ratesTableActions = {
        tableRef: _ratesTable,
        add: function() {
            Modal.show({
                type: 'form',
                modalTitle: 'Tambah Tarif',
                config: {
                    formId: 'form-add-rates',
                    formActionUrl: '#',
                    fields: {
                        entry_period: {
                            title: 'Periode Masuk',
                            content: {
                                template: 
                                    `<select class="form-select" name="entry_period">
                                        <option selected>Open this select menu</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>`,
                            },
                        },
                        wave: {
                            title: 'Gelombang',
                            content: {
                                template: 
                                    `<select class="form-select" name="wave">
                                        <option selected>Open this select menu</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>`,
                            },
                        },
                        registration_path: {
                            title: 'Jalur Masuk',
                            content: {
                                template: 
                                    `<select class="form-select" name="registration_path">
                                        <option selected>Open this select menu</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>`,
                            },
                        },
                        study_system: {
                            title: 'Sistem Kuliah',
                            content: {
                                template: 
                                    `<select class="form-select" name="study_system">
                                        <option selected>Open this select menu</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>`,
                            },
                        },
                        study_program: {
                            title: 'Program Studi',
                            content: {
                                template: 
                                    `<select class="form-select" name="study_program">
                                        <option selected>Open this select menu</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>`,
                            },
                        },
                        invoice_component: {
                            title: 'Komponen Tagihan',
                            content: {
                                template: 
                                    `<select class="form-select" name="invoice_component">
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
                        instalment: {
                            title: 'Cicilan',
                            content: {
                                template: 
                                    `<select class="form-select" name="instalment">
                                        <option selected>Open this select menu</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>`,
                            },
                        },
                    },
                    formSubmitLabel: 'Tambah Tarif',
                    callback: function() {
                        // ex: reload table
                        Swal.fire({
                            icon: 'success',
                            text: 'Berhasil menambahkan tarif',
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
                modalTitle: 'Edit Tarif',
                config: {
                    formId: 'form-edit-rates',
                    formActionUrl: '#',
                    fields: {
                        entry_period: {
                            title: 'Periode Masuk',
                            content: {
                                template: 
                                    `<select class="form-select" name="entry_period">
                                        <option selected>Open this select menu</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>`,
                            },
                        },
                        wave: {
                            title: 'Gelombang',
                            content: {
                                template: 
                                    `<select class="form-select" name="wave">
                                        <option selected>Open this select menu</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>`,
                            },
                        },
                        registration_path: {
                            title: 'Jalur Masuk',
                            content: {
                                template: 
                                    `<select class="form-select" name="registration_path">
                                        <option selected>Open this select menu</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>`,
                            },
                        },
                        study_system: {
                            title: 'Sistem Kuliah',
                            content: {
                                template: 
                                    `<select class="form-select" name="study_system">
                                        <option selected>Open this select menu</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>`,
                            },
                        },
                        study_program: {
                            title: 'Program Studi',
                            content: {
                                template: 
                                    `<select class="form-select" name="study_program">
                                        <option selected>Open this select menu</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>`,
                            },
                        },
                        invoice_component: {
                            title: 'Komponen Tagihan',
                            content: {
                                template: 
                                    `<select class="form-select" name="invoice_component">
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
                        instalment: {
                            title: 'Cicilan',
                            content: {
                                template: 
                                    `<select class="form-select" name="instalment">
                                        <option selected>Open this select menu</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>`,
                            },
                        },
                    },
                    formSubmitLabel: 'Edit Tarif',
                    callback: function() {
                        // ex: reload table
                        Swal.fire({
                            icon: 'success',
                            text: 'Berhasil mengupdate tarif',
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
                text: 'Apakah anda yakin ingin menghapus tarif ini?',
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
                        text: 'Berhasil menghapus tarif',
                    })
                }
            })
        },
        copy: function() {}
    }
</script>
@endsection
