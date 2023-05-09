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
                <select class="form-select" eazy-select2-active id="periode">
                    <option value="#" selected>Semua Periode Masuk</option>
                    @foreach($periode as $waktu)
                    <option value="{{ $waktu->msy_year }}">{{ $waktu->msy_year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow-1">
                <label class="form-label">Jalur Pendaftaran</label>
                <select class="form-select" eazy-select2-active id="jalur">
                    <option value="#" selected>Semua Jalur Pendaftaran</option>
                    @foreach($jalur_pendaftaran as $jalur)
                    <option value="{{ $jalur->path_name }}">{{ $jalur->path_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow-1">
                <label class="form-label">Gelombang</label>
                <select class="form-select" eazy-select2-active id="gelombang">
                    <option value="#" selected>Semua Gelombang</option>
                    @foreach($gelombang as $kloter)
                    <option value="{{ $kloter->period_name }}">{{ $kloter->period_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex align-items-end">
                <button class="btn btn-primary" onclick="filter()">
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
                <th>Jalur</th>
                <th>Gelombang Pendaftaran</th>
                <th>Periode Masuk</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection


@section('js_section')
<script>
    var tables;
    $(function() {
        _registrationFormTable.init()
        tables.columns([4,5,6]).visible(true);
    })

    const _registrationFormTable = {
        ..._datatable,
        init: function() {
            tables = this.instance = $('#registration-form-table').DataTable({
                serverSide: false,
                ajax: {
                    url: _baseURL + '/api/dt/registration-form',
                },
                columns: [{
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
                    {
                        name: 'track',
                        data: 'track',
                        render: (data) => {
                            return data;
                        },
                    },
                    {
                        name: 'wave',
                        data: 'wave',
                        render: (data) => {
                            return data;
                        },
                    },
                    {
                        name: 'period',
                        data: 'period',
                        render: (data) => {
                            return data;
                        }
                    },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom: '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"registration-form-actions d-flex align-items-end">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>t' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                buttons: [{
                    extend: 'excel',
                    text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file font-small-4 me-50"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>Excel</span>',
                    className: "dt-button buttons-collection btn btn-outline-secondary",
                    exportOptions: {
                        columns:[6, 4, 5, 3],
                        format: {
                            body: function(data, row, column, node) {
                                return data;
                            }
                        }
                    }
                }],
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
                            <a onclick="_registrationFormTableActions.edit(${id})" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
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
                    formActionUrl: _baseURL + "/api/dt/registration-form/create",
                    formType: 'add',
                    isTwoColumn: true,
                    fields: {
                        entry_period: {
                            title: 'Periode Masuk',
                            content: {
                                template: `
                                    <select class="form-select" eazy-select2-active name="periode">
                                        <option disabled selected>Pilih Periode Masuk</option>
                                        @foreach($periode as $waktu)
                                            <option value="{{ $waktu->msy_code }}">{{ $waktu->msy_year }}</option>
                                        @endforeach
                                    </select>
                                `,
                            },
                        },
                        registration_path: {
                            title: 'Jalur Pendaftaran',
                            content: {
                                template: `
                                    <select class="form-select" eazy-select2-active name="jalur">
                                        <option disabled selected>Pilih Jalur Pendaftaran</option>
                                        @foreach($jalur_pendaftaran as $jalur)
                                            <option value="{{ $jalur->path_id }}">{{ $jalur->path_name }}</option>
                                        @endforeach
                                    </select>  
                                `,
                            },
                        },
                        wave: {
                            title: 'Gelombang',
                            content: {
                                template: `
                                    <select class="form-select" eazy-select2-active name="gelombang">
                                        <option disabled selected>Pilih Gelombang</option>
                                        @foreach($gelombang as $kloter)
                                            <option value="{{ $kloter->period_id }}">{{ $kloter->period_name }}</option>
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
                        // Swal.fire({
                        //     icon: 'success',
                        //     text: 'Berhasil menambahkan skema',
                        // }).then(() => {
                        //     this.tableRef.reload()
                        // })
                    },
                },
            });
        },
        edit: function(id) {
            $.get(_baseURL + '/api/dt/registration-form/id/' + id, function(result, status) {
                var data = result;
                Modal.show({
                    type: 'form',
                    modalTitle: 'Edit Skema',
                    config: {
                        formId: 'form-edit-rates',
                        formActionUrl: _baseURL + '/api/dt/registration-form/edit/id/' + id,
                        formType: 'edit',
                        isTwoColumn: true,
                        fields: {
                            entry_period: {
                                title: 'Periode Masuk',
                                content: {
                                    template: `<input type="text" name="rate" value="${data.period}" class="form-control" placeholder="Masukkan nominal tarif" readonly />`,
                                },
                            },
                            registration_path: {
                                title: 'Jalur Pendaftaran',
                                content: {
                                    template: `<input type="text" name="rate" value="${data.track}" class="form-control" placeholder="Masukkan nominal tarif" readonly />`,
                                },
                            },
                            wave: {
                                title: 'Gelombang',
                                content: {
                                    template: `<input type="text" name="rate" value="${data.wave}" class="form-control" placeholder="Masukkan nominal tarif" readonly />`,
                                },
                            },
                            rate: {
                                title: 'Nominal Tarif',
                                content: {
                                    template: `<input type="number" name="rate" value="${data.rate}" class="form-control" placeholder="Masukkan nominal tarif" />`,
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
            })

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

    function filter() {
        var periode = $("#periode").val();
        var jalur = $("#jalur").val();
        var gelombang = $("#gelombang").val();
        console.log(gelombang);
        tables.columns().search("").draw();
        if (periode == "#" && jalur == "#" && gelombang == "#") {
            tables.columns().search("").draw();
        }
        if (periode == "#" && jalur == "#" && gelombang != "#") {
            tables.columns(5).search(gelombang).draw();
        }
        if (periode == "#" && jalur != "#" && gelombang == "#") {
            tables.columns(4).search(jalur).draw();
        }
        if (periode == "#" && jalur != "#" && gelombang != "#") {
            tables.columns(5).search(gelombang).draw();
            tables.columns(4).search(jalur).draw();
        }
        if (periode != "#" && jalur == "#" && gelombang == "#") {
            tables.columns(6).search(periode).draw();
        }
        if (periode != "#" && jalur == "#" && gelombang != "#") {
            tables.columns(5).search(gelombang).draw();
            tables.columns(6).search(periode).draw();
        }
        if (periode != "#" && jalur != "#" && gelombang == "#") {
            tables.columns(4).search(jalur).draw();
            tables.columns(6).search(periode).draw();
        }
        if (periode != "#" && jalur != "#" && gelombang != "#") {
            tables.columns(5).search(gelombang).draw();
            tables.columns(4).search(jalur).draw();
            tables.columns(6).search(periode).draw();
        }
        // tables.columns(5).search(gelombang).draw();
    }
</script>
@endsection