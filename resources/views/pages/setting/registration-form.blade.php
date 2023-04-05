@extends('layouts.static_master')


@section('page_title', 'Setting Tagihan, Tarif, dan Pembayaran')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
    <style>
        .registration-form-filter {
            display: flex;
            gap: 1rem;
        }
    </style>
@endsection

@section('content')

@include('pages.setting._shortcuts', ['active' => 'registration-form'])

<div class="card">
    <div class="card-body">
        <div class="registration-form-filter">
            <div class="flex-grow-1">
                <label class="form-label">Periode Masuk</label>
                <select class="form-select" eazy-select2-active>
                    <option value="all" selected>Semua Periode Masuk</option>
                    @foreach($static_school_years as $school_year)
                        <option value="{{ $school_year }}">{{ $school_year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow-1">
                <label class="form-label">Jalur Pendaftaran</label>
                <select class="form-select" eazy-select2-active>
                    <option value="all" selected>Semua Jalur Pendaftaran</option>
                    @foreach($static_registration_paths as $registration_path)
                        <option value="{{ $registration_path }}">{{ $registration_path }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow-1">
                <label class="form-label">Gelombang</label>
                <select class="form-select" eazy-select2-active>
                    <option value="all" selected>Semua Gelombang</option>
                    @foreach($static_registration_periods as $registration_period)
                        <option value="{{ $registration_period }}">{{ $registration_period }}</option>
                    @endforeach
                </select>
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
                <th>Periode Masuk</th>
                <th>Jalur / Gelombang Pendaftaran</th>
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
                    {
                        name: 'period', 
                        data: 'period',
                        render: (data) => {
                            return `<span class="fw-bold">${data}</span>`;
                        }
                    },
                    {
                        name: 'track_n_wave', 
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <span class="fw-bold">${row.track}</span><br>
                                    <small class="text-secondary">${row.wave}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'rate', 
                        data: 'rate',
                        render: (data) => {
                            return Rupiah.format(data);
                        }
                    },
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
                    formType: 'add',
                    fields: {
                        entry_period: {
                            title: 'Periode Masuk',
                            content: {
                                template: `
                                    <select class="form-select" eazy-select2-active>
                                        <option disabled selected>Pilih Periode Masuk</option>
                                        @foreach($static_school_years as $school_year)
                                            <option value="{{ $school_year }}">{{ $school_year }}</option>
                                        @endforeach
                                    </select>
                                `,
                            },
                        },
                        registration_path: {
                            title: 'Jalur Pendaftaran',
                            content: {
                                template: `
                                    <select class="form-select" eazy-select2-active>
                                        <option disabled selected>Pilih Jalur Pendaftaran</option>
                                        @foreach($static_registration_paths as $registration_path)
                                            <option value="{{ $registration_path }}">{{ $registration_path }}</option>
                                        @endforeach
                                    </select>  
                                `,
                            },
                        },
                        wave: {
                            title: 'Gelombang',
                            content: {
                                template: `
                                    <select class="form-select" eazy-select2-active>
                                        <option disabled selected>Pilih Gelombang</option>
                                        @foreach($static_registration_periods as $registration_period)
                                            <option value="{{ $registration_period }}">{{ $registration_period }}</option>
                                        @endforeach
                                    </select>    
                                `,
                            },
                        },
                        rate: {
                            title: 'Nominal Tarif',
                            content: {
                                template: `<input type="number" name="rate" class="form-control" placeholder="Masukkan nominal tarif" />`,
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
                    formType: 'edit',
                    fields: {
                        entry_period: {
                            title: 'Periode Masuk',
                            content: {
                                template: `
                                    <input type="text" name="entry_period" class="form-control" value="2023/2024" disabled />
                                `,
                            },
                        },
                        registration_path: {
                            title: 'Jalur Pendaftaran',
                            content: {
                                template: `
                                    <input type="text" name="registration_path" class="form-control" value="Jalur Mandiri" disabled />
                                `,
                            },
                        },
                        wave: {
                            title: 'Gelombang Pendaftaran',
                            content: {
                                template: `
                                    <input type="text" name="wave" class="form-control" value="Periode Juni" disabled />
                                `,
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
