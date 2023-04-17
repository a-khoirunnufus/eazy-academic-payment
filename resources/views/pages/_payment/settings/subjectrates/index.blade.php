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
<div class="modal fade" id="frmbox-comp" tabindex="-1" aria-labelledby="frmbox-title" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg justify-content-center">
        <div class="modal-content" style="">
            <div class="modal-header bg-transparent">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-sm-5 mx-50 pb-3">
                <h1 class=" fw-bolder h3 mb-1" id="frmbox-title">Tambah Tarif Matakuliah</h1>

                <div class="mb-2">
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <label class="form-label">Program Studi</label>
                            <select class="form-select" eazy-select2-active>
                                <option value="all" selected>Semua Program Studi</option>
                                @foreach($static_study_programs as $study_program)
                                    <option value="{{ $study_program }}">{{ $study_program }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <label class="form-label">Mata Kuliah</label>
                            <select class="form-select" eazy-select2-active>
                                <option value="all" selected>Semua Mata Kuliah</option>
                                @foreach($static_faculties as $faculty)
                                    <option value="{{ $faculty }}">{{ $faculty }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>

                <!-- form -->
                <form id="frm-comp">
                    <input type="hidden" name="_token" value="nJvUaN69Vo4RqgtRTjkyFs5O3uT7Cfe5O7ZHqdT0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap:10px">
                        <h4 class="fw-bolder mb-0">Tarif Per Semester</h4>
                        <button type="button"
                            class="btn btn-primary text-white btn-sm edit-component waves-effect waves-float waves-light"
                            onclick="addNewComp()"> <i class="bx bx-plus m-auto"></i>
                        </button>
                    </div>

                    <hr>

                    <div id="comp-container-input">
                        <div class="d-flex flex-wrap align-items-center mb-1" style="gap:10px"
                            id="comp-order-preview-0">
                            <input type="hidden" name="cd_id[]" value="107">
                            <div class="flex-fill" style="width:40%">
                                <label class="form-label">Semester</label>
                                <select class="select2-comp form-select select2-hidden-accessible" name="comp_id[]"
                                    data-select2-id="26" tabindex="-1" aria-hidden="true">
                                    <option selected="" disabled="">Pilih Nama Komponen Biaya</option>
                                    <option value="41">SDP2</option>,<option value="42">UP3</option>,<option value="11">
                                        Biaya Daftar Ulang</option>,<option value="43" selected="" data-select2-id="28">
                                        BPP</option>,<option value="40">biaya daftar ulang 2</option>,<option
                                        value="44">Asrama</option>
                                </select><span class="select2 select2-container select2-container--default" dir="ltr"
                                    data-select2-id="27" style="width: auto;"><span class="selection"><span
                                            class="select2-selection select2-selection--single" role="combobox"
                                            aria-haspopup="true" aria-expanded="false" tabindex="0"
                                            aria-disabled="false" aria-labelledby="select2-comp_id-zq-container"><span
                                                class="select2-selection__rendered" id="select2-comp_id-zq-container"
                                                role="textbox" aria-readonly="true" title="BPP">BPP</span><span
                                                class="select2-selection__arrow" role="presentation"><b
                                                    role="presentation"></b></span></span></span><span
                                        class="dropdown-wrapper" aria-hidden="true"></span></span>
                            </div>
                            <div class="flex-fill">
                                <label class="form-label">Tarif</label>
                                <input type="text" class="form-control comp_price" name="comp_price[]" value="1,000"
                                    placeholder="Tulis Harga Komponen Biaya">
                            </div>
                            <div>
                                <label class="form-label" style="opacity: 0">#</label>
                                <button class="btn btn-danger text-white btn-sm d-flex" style="height: 36px"
                                    onclick="del_comp('0')"> <i class="bx bx-trash m-auto"></i> </button>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap align-items-center mb-1" style="gap:10px"
                            id="comp-order-preview-1">
                            <input type="hidden" name="cd_id[]" value="108">
                            <div class="flex-fill" style="width:40%">
                                <label class="form-label">Semester</label>
                                <select class="select2-comp form-select select2-hidden-accessible" name="comp_id[]"
                                    data-select2-id="29" tabindex="-1" aria-hidden="true">
                                    <option selected="" disabled="">Pilih Nama Komponen Biaya</option>
                                    <option value="41">SDP2</option>,<option value="42" selected=""
                                        data-select2-id="31">UP3</option>,<option value="11">Biaya Daftar Ulang</option>
                                    ,<option value="43">BPP</option>,<option value="40">biaya daftar ulang 2</option>,
                                    <option value="44">Asrama</option>
                                </select><span class="select2 select2-container select2-container--default" dir="ltr"
                                    data-select2-id="30" style="width: auto;"><span class="selection"><span
                                            class="select2-selection select2-selection--single" role="combobox"
                                            aria-haspopup="true" aria-expanded="false" tabindex="0"
                                            aria-disabled="false" aria-labelledby="select2-comp_id-3x-container"><span
                                                class="select2-selection__rendered" id="select2-comp_id-3x-container"
                                                role="textbox" aria-readonly="true" title="UP3">UP3</span><span
                                                class="select2-selection__arrow" role="presentation"><b
                                                    role="presentation"></b></span></span></span><span
                                        class="dropdown-wrapper" aria-hidden="true"></span></span>
                            </div>
                            <div class="flex-fill">
                                <label class="form-label">Tarif</label>
                                <input type="text" class="form-control comp_price" name="comp_price[]" value="2,000"
                                    placeholder="Tulis Harga Komponen Biaya">
                            </div>
                            <div>
                                <label class="form-label" style="opacity: 0">#</label>
                                <button class="btn btn-danger text-white btn-sm d-flex" style="height: 36px"
                                    onclick="del_comp('1')"> <i class="bx bx-trash m-auto"></i> </button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="d-flex align-items-center flex-wrap justify-content-between mt-3" style="gap:10px">
                    <small style="color:#163485">
                        *Pastikan Data Yang Anda Masukkan <strong>Lengkap</strong> dan <strong>Benar</strong>
                    </small>
                    <button class="btn btn-primary edit-component waves-effect waves-float waves-light" type="button"
                        onclick="save_comp()">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
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
            $("#frmbox-comp").modal('show');
            // Modal.show({
            //     type: 'form',
            //     modalTitle: 'Tambah Tarif Matakuliah',
            //     modalSize: 'lg',
            //     config: {
            //         formId: 'form-add-rates-per-course',
            //         formActionUrl: '#',
            //         formType: 'add',
            //         isTwoColumn: true,
            //         fields: {
            //             course_name: {
            //                 title: 'Mata Kuliah',
            //                 content: {
            //                     template: `
            //                         <select class="form-select" eazy-select2-active>
            //                             <option disabled selected>Pilih Mata Kuliah</option>
            //                             @foreach($static_courses as $course)
            //                                 <option value="{{ $course['code'] }}">{{ $course['code'].' - '.$course['name'] }}</option>
            //                             @endforeach
            //                         </select>
            //                     `,
            //                 },
            //             },
            //             course_type: {
            //                 title: 'Jenis',
            //                 content: {
            //                     template: `<input type="text" class="form-control" disabled eazy-exclude-field />`
            //                 },
            //             },
            //             sks: {
            //                 title: 'SKS',
            //                 content: {
            //                     template: `<input type="number" class="form-control" disabled eazy-exclude-field />`
            //                 },
            //             },
            //             semester: {
            //                 title: 'Semester',
            //                 content: {
            //                     template: `<input type="number" class="form-control" disabled eazy-exclude-field />`
            //                 },
            //             },
            //             mandatory: {
            //                 title: 'Status Mata Kuliah',
            //                 content: {
            //                     template: `<input type="text" class="form-control" disabled eazy-exclude-field />`
            //                 },
            //             },
            //             is_package: {
            //                 title: 'Paket?',
            //                 content: {
            //                     template: `<input type="text" class="form-control" disabled eazy-exclude-field />`
            //                 },
            //             },
            //             rate: {
            //                 title: 'Nominal Tarif',
            //                 content: {
            //                     template: `<input name="rate" type="number" class="form-control" placeholder="Masukkan nominal tarif" />`
            //                 },
            //             },
            //         },
            //         formSubmitLabel: 'Tambah Tarif',
            //         callback: function() {
            //             // ex: reload table
            //             Swal.fire({
            //                 icon: 'success',
            //                 text: 'Berhasil menambah tarif',
            //             }).then(() => {
            //                 this.tableRef.reload()
            //             })
            //         },
            //     },
            // });
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
