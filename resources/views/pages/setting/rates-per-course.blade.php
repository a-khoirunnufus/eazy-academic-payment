@extends('layouts.static_master')


@section('page_title', 'Setting Tarif Per Matakuliah')
@section('sidebar-size', 'collapsed')
@section('url_back', url('setting/rates'))

@section('css_section')
    <style>
        .rates-per-course-filter {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            grid-gap: 1rem;
        }
    </style>
@endsection

@section('content')

@include('pages.setting._shortcuts', ['active' => 'rates-per-course'])

<div class="card">
    <div class="card-body">
        <div class="rates-per-course-filter">
            <div>
                <label class="form-label">Periode Masuk</label>
                <select class="form-select" eazy-select2-active>
                    <option value="all" selected>Semua Periode Masuk</option>
                    @foreach($static_school_years as $school_year)
                        <option value="{{ $school_year }}">{{ $school_year }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Gelombang</label>
                <select class="form-select" eazy-select2-active>
                    <option value="all" selected>Semua Gelombang</option>
                    @foreach($static_registration_periods as $registration_period)
                        <option value="{{ $registration_period }}">{{ $registration_period }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Jalur Pendaftaran</label>
                <select class="form-select" eazy-select2-active>
                    <option value="all" selected>Semua Jalur Pendaftaran</option>
                    @foreach($static_registration_paths as $registration_path)
                        <option value="{{ $registration_path }}">{{ $registration_path }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Sistem Kuliah</label>
                <select class="form-select" eazy-select2-active>
                    <option value="all" selected>Semua Sistem Kuliah</option>
                    @foreach($static_study_systems as $study_system)
                        <option value="{{ $study_system }}">{{ $study_system }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Fakultas</label>
                <select class="form-select" eazy-select2-active>
                    <option value="all" selected>Semua Fakultas</option>
                    @foreach($static_faculties as $faculty)
                        <option value="{{ $faculty }}">{{ $faculty }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Program Studi</label>
                <select class="form-select" eazy-select2-active>
                    <option value="all" selected>Semua Program Studi</option>
                    @foreach($static_study_programs as $study_program)
                        <option value="{{ $study_program }}">{{ $study_program }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Komponen Tagihan</label>
                <select class="form-select" eazy-select2-active>
                    <option value="all" selected>Semua Komponen Tagihan</option>
                    @foreach($static_invoice_components as $invoice_component)
                        <option value="{{ $invoice_component }}">{{ $invoice_component }}</option>
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
    <table id="rates-per-course-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Nama / Kode Matakuliah</th>
                <th>Jenis</th>
                <th>Jumlah SKS</th>
                <th>Semester</th>
                <th>Nominal Tarif</th>
                <th class="text-center">Status Mata Kuliah</th>
                <th class="text-center">Paket?</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection


@section('js_section')
<script>
    $(function(){
        _ratesPerCourseTable.init()
    })

    const _ratesPerCourseTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#rates-per-course-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/dt/rates-per-course',
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
                        name: 'course_name_n_code',
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <span class="fw-bold">${row.course_name}</span><br>
                                    <small class="text-secondary">${row.course_code}</small>
                                </div>
                            `;
                        }
                    },
                    {name: 'course_type', data: 'course_type'},
                    {
                        name: 'sks', 
                        data: 'sks',
                        render: (data) => {
                            return data+' SKS';
                        }
                    },
                    {
                        name: 'semester', 
                        data: 'semester',
                        render: (data) => {
                            return 'Semester '+data;
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
                        name: 'mandatory', 
                        data: 'mandatory',
                        render: (data) => {
                            var html = '<div class="d-flex justify-content-center">'
                            if(data.toUpperCase() == 'W') {
                                html += '<div class="badge bg-success" style="font-size: inherit">Wajib</div>'
                            } else {
                                html += '<div class="badge bg-danger" style="font-size: inherit">Tidak Wajib</div>'
                            }
                            html += '</div>'
                            return html
                        }
                    },
                    {
                        name: 'is_package', 
                        data: 'is_package',
                        render: (data) => {
                            var html = '<div class="d-flex justify-content-center">'
                            if(data) {
                                html += '<div class="eazy-badge blue"><i data-feather="check"></i></div>'
                            } else {
                                html += '<div class="eazy-badge red"><i data-feather="x"></i></div>'
                            }
                            html += '</div>'
                            return html
                        }
                    },
                    
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"rate-per-course-actions d-flex align-items-end">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>t' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function() {
                    $('.rate-per-course-actions').html(`
                        <div style="margin-bottom: 7px">
                            <button onclick="_ratesPerCourseTableActions.add()" class="btn btn-primary me-1">
                                <span style="vertical-align: middle">
                                    <i data-feather="plus" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Tambah Tarif Matakuliah
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
                            <a onclick="_ratesPerCourseTableActions.edit()" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="_ratesPerCourseTableActions.delete()" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const _ratesPerCourseTableActions = {
        tableRef: _ratesPerCourseTable,
        add: function() {
            Modal.show({
                type: 'form',
                modalTitle: 'Tambah Tarif Matakuliah',
                modalSize: 'lg',
                config: {
                    formId: 'form-add-rates-per-course',
                    formActionUrl: '#',
                    formType: 'add',
                    isTwoColumn: true,
                    fields: {
                        course_name: {
                            title: 'Mata Kuliah',
                            content: {
                                template: `
                                    <select class="form-select" eazy-select2-active>
                                        <option disabled selected>Pilih Mata Kuliah</option>
                                        @foreach($static_courses as $course)
                                            <option value="{{ $course['code'] }}">{{ $course['code'].' - '.$course['name'] }}</option>
                                        @endforeach
                                    </select>
                                `,
                            },
                        },
                        course_type: {
                            title: 'Jenis',
                            content: {
                                template: `<input type="text" class="form-control" disabled eazy-exclude-field />`
                            },
                        },
                        sks: {
                            title: 'SKS',
                            content: {
                                template: `<input type="number" class="form-control" disabled eazy-exclude-field />`
                            },
                        },
                        semester: {
                            title: 'Semester',
                            content: {
                                template: `<input type="number" class="form-control" disabled eazy-exclude-field />`
                            },
                        },
                        mandatory: {
                            title: 'Status Mata Kuliah',
                            content: {
                                template: `<input type="text" class="form-control" disabled eazy-exclude-field />`
                            },
                        },
                        is_package: {
                            title: 'Paket?',
                            content: {
                                template: `<input type="text" class="form-control" disabled eazy-exclude-field />`
                            },
                        },
                        rate: {
                            title: 'Nominal Tarif',
                            content: {
                                template: `<input name="rate" type="number" class="form-control" placeholder="Masukkan nominal tarif" />`
                            },
                        },
                    },
                    formSubmitLabel: 'Tambah Tarif',
                    callback: function() {
                        // ex: reload table
                        Swal.fire({
                            icon: 'success',
                            text: 'Berhasil menambah tarif',
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
                modalTitle: 'Edit Tarif Matakuliah',
                modalSize: 'lg',
                config: {
                    formId: 'form-edit-rates-per-course',
                    formActionUrl: '#',
                    formType: 'edit',
                    isTwoColumn: true,
                    fields: {
                        course_code: {
                            title: 'Kode',
                            content: {
                                template: `<input type="text" class="form-control" value="CSH1A2" disabled eazy-exclude-field />`
                            },
                        },
                        course_name: {
                            title: 'Nama Matakuliah',
                            content: {
                                template: `<input type="text" class="form-control" value="Pembentukan Karakter" disabled eazy-exclude-field />`
                            },
                        },
                        course_type: {
                            title: 'Jenis',
                            content: {
                                template: `<input type="text" class="form-control" value="Kuliah" disabled eazy-exclude-field />`
                            },
                        },
                        sks: {
                            title: 'SKS',
                            content: {
                                template: `<input type="number" class="form-control" value="2" disabled eazy-exclude-field />`
                            },
                        },
                        semester: {
                            title: 'Semester',
                            content: {
                                template: `<input type="number" class="form-control" value="1" disabled eazy-exclude-field />`
                            },
                        },
                        mandatory: {
                            title: 'Status Mata Kuliah',
                            content: {
                                template: `<input type="text" class="form-control" value="Wajib" disabled eazy-exclude-field />`
                            },
                        },
                        is_package: {
                            title: 'Paket?',
                            content: {
                                template: `<input type="text" class="form-control" value="Ya" disabled eazy-exclude-field />`
                            },
                        },
                        rate: {
                            title: 'Nominal Tarif',
                            content: {
                                template: `<input name="rate" type="number" class="form-control" value="1000000" />`
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
                text: 'Apakah anda yakin ingin menghapus tarif matakuliah ini?',
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
                        text: 'Berhasil menghapus tarif matakuliah',
                    })
                }
            })
        },
    }
</script>
@endsection
