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

@include('pages._payment.settings._shortcuts', ['active' => 'subject-rates'])

<input type="file" name="import" id="myFiles" style="display:none;" onchange="myImport()">
<div class="card">
    <div class="card-body">
        <div class="rates-per-course-filter">
            <div>
                <label class="form-label">Fakultas</label>
                <select class="form-select select2" name="faculty-filter">
                    <option value="#ALL" selected>Semua Fakultas</option>
                    @foreach($faculty as $item)
                    <option value="{{ $item->faculty_id }}">{{ $item->faculty_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Program Studi</label>
                <select class="form-select select2" name="studyprogram-filter">
                    <option value="#ALL" selected>Semua Program Studi</option>
                    @foreach($studyProgram as $item)
                    <option value="{{ $item->studyprogram_id }}">{{ $item->studyprogram_type." ".$item->studyprogram_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex align-items-end">
                <button class="btn btn-primary" onclick="_ratesPerCourseTable.reload()">
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
                <th>Jurusan</th>
                <th>Jumlah SKS</th>
                <th>Tingkat</th>
                <th>Nominal Tarif</th>
                <th class="text-center">Status Mata Kuliah</th>
                <th class="text-center">Paket?</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
{{-- <div class="modal fade" id="mainModal" tabindex="-1" aria-labelledby="frmbox-title" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg justify-content-center">
        <div class="modal-content" style="">
            <div class="modal-header bg-transparent">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-sm-5 mx-50 pb-3">
                <h1 class=" fw-bolder h3 mb-1" id="frmbox-title">Tambah Tarif Matakuliah</h1>
                <form id="coureRateForm" method="post">
                    <div class="mb-2">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <label class="form-label">Program Studi</label>
                                <select class="form-select select2" eazy-select2-active id="programStudy">
                                    <option value="all" selected>Semua Program Studi</option>
                                    @foreach($studyProgram as $item)
                                        <option value="{{ $item->studyprogram_id }}">{{ $item->studyprogram_name }}</option>
@endforeach
</select>
</div>
<div class="col-lg-6 col-md-6">
    <label class="form-label">Mata Kuliah</label>
    <select class="form-select select2" eazy-select2-active name="mcr_course_id" id="courseId">
    </select>
</div>
</div>
</div>
<!-- form -->
<div class="d-flex flex-wrap align-items-center justify-content-between mb-1" style="gap:10px">
    <h4 class="fw-bolder mb-0">Tarif Per Tingkat</h4>
    <button type="button" class="btn btn-primary text-white edit-component waves-effect waves-float waves-light" onclick="_ratesPerCourseTableActions.courseRateInputField()"> <i class="bx bx-plus m-auto"></i> Tambah Tingkat
    </button>
</div>
<div id="courseRateInput">
</div>
<div class="d-flex align-items-center flex-wrap justify-content-between mt-3" style="gap:10px">
    <small style="color:#163485">
        *Pastikan Data Yang Anda Masukkan <strong>Lengkap</strong> dan <strong>Benar</strong>
    </small>
    <button class="btn btn-primary edit-component waves-effect waves-float waves-light" type="button" onclick="_ratesPerCourseTableActions.courseRateStore()">
        Simpan
    </button>
    <button type="reset">Reset</button>
</div>
</form>
</div>
</div>
</div>
</div> --}}

@endsection


@section('js_section')
<script>
    var dataCopy = null;
    var spIdCopy = null;
    var cIdCopy = null;
    $(function() {
        _ratesPerCourseTable.init()
    })

    const _Filter = {
        init: function() {
            _options.load({
                optionUrl: _baseURL + '/api/payment/settings/courserates/studyprogram',
                nameField: 'program_study',
                idData: 'studyprogram_id',
                nameData: 'studyprogram_name'
            });
        }
    }

    const _ratesPerCourseTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#rates-per-course-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL + '/api/payment/settings/courserates/index',
                    data: function(d) {
                        d.custom_filter = {
                            'studyprogram_id': $('select[name="studyprogram-filter"]').val(),
                            'faculty_id': $('select[name="faculty-filter"]').val(),
                        };
                    }
                },
                columns: [{
                        name: 'action',
                        data: 'mcr_id',
                        orderable: false,
                        render: (data, _, row) => {
                            return this.template.rowAction(row)
                        }
                    },
                    {
                        name: 'course.subject_name',
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <span class="fw-bold">${row.course.subject_name}</span><br>
                                    <small class="text-secondary">${row.course.subject_code}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'course.subject_code',
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <span class="fw-bold">${row.course.study_program.studyprogram_name}</span><br>
                                    <small class="text-secondary">${row.course.study_program.studyprogram_type}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'course.credit',
                        data: 'course.credit',
                        render: (data) => {
                            return data + ' SKS';
                        }
                    },
                    {
                        name: 'mcr_tingkat',
                        data: 'mcr_tingkat',
                        render: (data) => {
                            return 'Tingkat ' + data;
                        }
                    },
                    {
                        name: 'mcr_rate',
                        data: 'mcr_rate',
                        render: (data) => {
                            return Rupiah.format(data);
                        }
                    },
                    {
                        name: 'mandatory_status',
                        data: 'mandatory_status',
                        render: (data) => {
                            var html = '<div class="d-flex justify-content-center">'
                            if (data.toUpperCase() == 'WAJIB_PRODI') {
                                html += '<div class="badge bg-success" style="font-size: inherit">Wajib</div>'
                            } else {
                                html += '<div class="badge bg-danger" style="font-size: inherit">Tidak Wajib</div>'
                            }
                            html += '</div>'
                            return html
                        }
                    },
                    {
                        name: 'mcr_is_package',
                        data: 'mcr_is_package',
                        render: (data) => {
                            var html = '<div class="d-flex justify-content-center">'
                            if (data) {
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
                dom: '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
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
                            <button type="button" class="btn btn-success" onclick="importBtn()">Import</button>
                        </div>
                    `)
                    feather.replace()
                }
            })
        },
        template: {
            rowAction: function(row) {
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a onclick="_ratesPerCourseTableActions.edit(${row.course.study_program.studyprogram_id},${row.mcr_course_id})" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="_ratesPerCourseTableActions.copy(${row.course.study_program.studyprogram_id},${row.mcr_course_id})" class="dropdown-item" href="javascript:void(0);"><i data-feather="clipboard"></i>&nbsp;&nbsp;Salin</a>
                            <a onclick="_ratesPerCourseTableActions.delete(this)" class="dropdown-item"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const _ratesPerCourseTableActions = {
        tableRef: _ratesPerCourseTable,
        courseRateInputField: function(id = 0, rate = 0, is_package = null, tingkat = null) {
            $('#courseRateInput').append(`
                <div class="d-flex flex-wrap align-items-center mb-1 courseRateInputField" style="gap:10px"
                    id="comp-order-preview-0">
                    <input type="hidden" name="mcr_id[]" value="${id}">
                    <div class="flex-fill">
                        <label class="form-label">Tingkat</label>
                        <select class="form-select select2" eazy-select2-active name="mcr_tingkat[]" id="tingkat${id}" value="">
                            <option value="1">Tingkat 1</option>
                            <option value="2">Tingkat 2</option>
                            <option value="3">Tingkat 3</option>
                            <option value="4">Tingkat 4</option>
                            <option value="5">Tingkat > 4</option>
                        </select>
                    </div>
                    <div class="flex-fill">
                        <label class="form-label">Tarif</label>
                        <input type="text" class="form-control comp_price" name="mcr_rate[]" value="${rate}"
                            placeholder="Tarif Mata Kuliah">
                    </div>
                    <div class="flex-fill text-center">
                        <label class="form-label">Paket</label>
                        <div class="d-flex justify-content-center">
                            <select class="form-select select2" eazy-select2-active name="mcr_is_package[]" id="paket${id}" value="">
                                <option value="0">Tidak</option>
                                <option value="1">Ya</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex align-content-end">
                        <div class="">
                            <label class="form-label" style="opacity: 0">#</label>
                            <a class="btn btn-danger text-white btn-sm d-flex" style="height: 36px"
                            onclick="_ratesPerCourseTableActions.courseRateDeleteField(this,${id})"> <i class="bx bx-trash m-auto"></i> </a>
                        </div>
                    </div>
                </div>
            `);
            if (tingkat) {
                $("#tingkat" + id + "").val(tingkat);
                $("#tingkat" + id + "").trigger('change');
            }
            if (is_package) {
                $("#paket" + id + "").val(is_package);
                $("#paket" + id + "").trigger('change');
            }
        },
        courseRateDeleteField: function(e, id) {
            if (id === 0) {
                $(e).parents('.courseRateInputField').get(0).remove();
            } else {
                _ratesPerCourseTableActions.delete(e, id);
            }
        },
        // courseRateStore: function(){
        //     const formData = new FormData($('#coureRateForm')[0]);
        //     $.post(_baseURL + '/api/payment/settings/courserates/store',$("#coureRateForm").serialize(), function(data){
        //         data = JSON.parse(data)
        //         Swal.fire({
        //             icon: 'success',
        //             text: data.message,
        //         }).then(() => {
        //             this.tableRef.reload()
        //         })
        //         $("#programStudy").val("").trigger("change");
        //         $("#courseId").val("").trigger("change");
        //         $('#courseRateInput').empty();
        //         $("#mainModal").modal('hide');
        //     }).fail((error) => {
        //         Swal.fire({
        //             icon: 'error',
        //             text: data.text,
        //         });
        //     })
        // },
        add: function() {
            // $("#mainModal").modal('show');
            // $('#mainModal').on('hidden.bs.modal', function (event) {
            //     $("#programStudy").val("").trigger("change");
            //     $("#courseId").val("").trigger("change");
            //     $('#courseRateInput').empty();
            //     $("#mainModal").modal('hide');
            // });

            Modal.show({
                type: 'form',
                modalTitle: 'Tambah Tarif Matakuliah',
                modalSize: 'lg',
                config: {
                    formId: 'form-add-rates-per-course',
                    formActionUrl: _baseURL + '/api/payment/settings/courserates/store',
                    formType: 'add',
                    data: $("#coureRateForm").serialize(),
                    isTwoColumn: false,
                    fields: {
                        selections: {
                            type: 'custom-field',
                            content: {
                                template: `<div class="mb-2">
                                    <button type="button" class="btn btn-success" onclick="_ratesPerCourseTableActions.paste('prodi')">Paste</button>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6">
                                            <label class="form-label">Program Studi</label>
                                            <select class="form-select select2" eazy-select2-active id="programStudy" name="program_study">
                                            </select>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <label class="form-label">Mata Kuliah</label>
                                            <select class="form-select select2" eazy-select2-active name="mcr_course_id" id="courseId">
                                            </select>
                                        </div>
                                    </div>
                                </div>`
                            },
                        },
                        input_fields: {
                            type: 'custom-field',
                            content: {
                                template: `
                                <div class="d-flex flex-wrap align-items-center justify-content-between mb-1" style="gap:10px">
                                    <h4 class="fw-bolder mb-0">Tarif Per Tingkat</h4>
                                    <div>
                                    <button type="button"
                                        class="btn btn-primary text-white edit-component waves-effect waves-float waves-light"
                                        onclick="_ratesPerCourseTableActions.courseRateInputField()"> <i class="bx bx-plus m-auto"></i> Tambah Tingkat
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="_ratesPerCourseTableActions.paste('component')">Paste</button>
                                    </div>
                                    
                                </div>
                                <div id="courseRateInput">
                                </div>
                                `
                            },
                        },
                    },
                    formSubmitLabel: 'Simpan',
                    formSubmitNote: `
                    <small style="color:#163485">
                        *Pastikan Data Yang Anda Masukkan <strong>Lengkap</strong> dan <strong>Benar</strong>
                    </small>`,
                    callback: function() {
                        // ex: reload table
                        _ratesPerCourseTable.reload()
                    },
                },
            });
            $('#programStudy').empty().trigger("change");
            $('#courseRateInput').empty();
            _options.load({
                optionUrl: _baseURL + '/api/payment/settings/courserates/studyprogram',
                nameField: 'program_study',
                idData: 'studyprogram_id',
                nameData: 'studyprogram_name'
            });

            $("#programStudy").change(function() {
                studyProgramId = $(this).val();
                $('#courseId').empty().trigger("change");
                $('#courseRateInput').empty();
                _options.load({
                    optionUrl: _baseURL + '/api/payment/settings/courserates/course/' + studyProgramId,
                    nameField: 'mcr_course_id',
                    idData: 'course_id',
                    nameData: 'subject_name'
                });
            })
            $("#courseId").change(function() {
                _ratesPerCourseTableActions.tarif("#courseId");
            })

        },
        tarif: function(e) {
            courseId = $(e).val();
            if (courseId) {
                $.post(_baseURL + '/api/payment/settings/courserates/getbycourseid/' + courseId, {
                    _method: 'GET'
                }, function(data) {
                    data = JSON.parse(data)
                    $('#courseRateInput').empty();
                    if (Object.keys(data).length > 0) {
                        data.map(item => {
                            _ratesPerCourseTableActions.courseRateInputField(item.mcr_id, item.mcr_rate, item.mcr_is_package, item.mcr_tingkat)
                        })
                    }
                    console.log(data);
                }).fail((error) => {
                    Swal.fire({
                        icon: 'error',
                        text: data.text,
                    });
                })
            }
        },
        edit: function(spId, cId) {

            Modal.show({
                type: 'form',
                modalTitle: 'Tambah Tarif Matakuliah',
                modalSize: 'lg',
                config: {
                    formId: 'form-add-rates-per-course',
                    formActionUrl: _baseURL + '/api/payment/settings/courserates/store',
                    formType: 'add',
                    data: $("#coureRateForm").serialize(),
                    isTwoColumn: false,
                    fields: {
                        selections: {
                            type: 'custom-field',
                            content: {
                                template: `<div class="mb-2">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6">
                                            <label class="form-label">Program Studi</label>
                                            <select class="form-select select2" eazy-select2-active id="programStudy" name="program_study">
                                            </select>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <label class="form-label">Mata Kuliah</label>
                                            <select class="form-select select2" eazy-select2-active name="mcr_course_id" id="courseId">
                                            </select>
                                        </div>
                                        <input type="hidden" name="mcr_course_id" id="courseHiddenId">
                                    </div>
                                </div>`
                            },
                        },
                        input_fields: {
                            type: 'custom-field',
                            content: {
                                template: `
                                <div class="d-flex flex-wrap align-items-center justify-content-between mb-1" style="gap:10px">
                                    <h4 class="fw-bolder mb-0">Tarif Per Tingkat</h4>
                                    <button type="button"
                                        class="btn btn-primary text-white edit-component waves-effect waves-float waves-light"
                                        onclick="_ratesPerCourseTableActions.courseRateInputField()"> <i class="bx bx-plus m-auto"></i> Tambah Tingkat
                                    </button>
                                </div>
                                <div id="courseRateInput">
                                </div>
                                `
                            },
                        },
                    },
                    formSubmitLabel: 'Simpan',
                    formSubmitNote: `
                    <small style="color:#163485">
                        *Pastikan Data Yang Anda Masukkan <strong>Lengkap</strong> dan <strong>Benar</strong>
                    </small>`,
                    callback: function() {
                        // ex: reload table
                        _ratesPerCourseTable.reload()
                    },
                },
            });

            $("#courseHiddenId").val(cId);
            $('#programStudy').empty().trigger("change");
            $('#courseRateInput').empty();
            _options.load({
                optionUrl: _baseURL + '/api/payment/settings/courserates/studyprogram',
                nameField: 'program_study',
                idData: 'studyprogram_id',
                nameData: 'studyprogram_name',
                val: spId
            });

            $("#programStudy").change(function() {
                studyProgramId = $(this).val();
                $('#courseId').empty().trigger("change");
                $('#courseRateInput').empty();
                _options.load({
                    optionUrl: _baseURL + '/api/payment/settings/courserates/course/' + studyProgramId,
                    nameField: 'mcr_course_id',
                    idData: 'course_id',
                    nameData: 'subject_name',
                    val: cId
                });
            })
            $('#programStudy').prop('disabled', true);
            $("#courseId").change(function() {
                courseId = $(this).val();
                if (courseId) {
                    $.post(_baseURL + '/api/payment/settings/courserates/getbycourseid/' + courseId, {
                        _method: 'GET'
                    }, function(data) {
                        data = JSON.parse(data)
                        $('#courseRateInput').empty();
                        if (Object.keys(data).length > 0) {
                            data.map(item => {
                                _ratesPerCourseTableActions.courseRateInputField(item.mcr_id, item.mcr_rate, item.mcr_is_package, item.mcr_tingkat)
                            })
                        }
                        console.log(data);
                    }).fail((error) => {
                        Swal.fire({
                            icon: 'error',
                            text: data.text,
                        });
                    })
                }
            })
            $('#courseId').prop('disabled', true);
        },
        delete: function(e, id = 0) {
            let data = _ratesPerCourseTable.getRowData(e);
            let mcrId = 0;
            if (id == 0) {
                mcrId = data.mcr_id;
            } else {
                mcrId = id;
            }
            console.log(mcrId);
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin menghapus tarif mata kuliah ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(_baseURL + '/api/payment/settings/courserates/delete/' + mcrId, {
                        _method: 'DELETE'
                    }, function(data) {
                        data = JSON.parse(data)
                        Swal.fire({
                            icon: 'success',
                            text: data.message,
                        }).then(() => {
                            _ratesPerCourseTable.reload();
                            if (id != 0) {
                                _ratesPerCourseTableActions.tarif("#courseId");
                            }
                        });
                    }).fail((error) => {
                        Swal.fire({
                            icon: 'error',
                            text: data.message,
                        });
                        _responseHandler.generalFailResponse(error)
                    })
                }
            })
        },
        copy: function(spId, cId) {
            spIdCopy = spId;
            cIdCopy = cId;
        },
        paste: function(type) {
            if (type == "prodi") {
                $('#programStudy').empty().trigger("change");
                $('#courseRateInput').empty();
                _options.load({
                    optionUrl: _baseURL + '/api/payment/settings/courserates/studyprogram',
                    nameField: 'program_study',
                    idData: 'studyprogram_id',
                    nameData: 'studyprogram_name',
                    val: spIdCopy
                });

                $("#programStudy").change(function() {
                    studyProgramId = $(this).val();
                    $('#courseId').empty().trigger("change");
                    $('#courseRateInput').empty();
                    _options.load({
                        optionUrl: _baseURL + '/api/payment/settings/courserates/course/' + studyProgramId,
                        nameField: 'mcr_course_id',
                        idData: 'course_id',
                        nameData: 'subject_name',
                        val: cIdCopy
                    });
                })
            }
            if (type == "component") {
                $.post(_baseURL + '/api/payment/settings/courserates/getbycourseid/' + cIdCopy, {
                    _method: 'GET'
                }, function(data) {
                    data = JSON.parse(data)
                    $('#courseRateInput').empty();
                    if (Object.keys(data).length > 0) {
                        data.map(item => {
                            _ratesPerCourseTableActions.courseRateInputField(item.mcr_id, item.mcr_rate, item.mcr_is_package, item.mcr_tingkat)
                        })
                    }
                    console.log(data);
                }).fail((error) => {
                    Swal.fire({
                        icon: 'error',
                        text: data.text,
                    });
                })
            }
        }
    }

    function myImport() {
        var x = document.getElementById("myFiles");
        var txt = "";
        if ('files' in x) {
            if (x.files.length > 0) {
                console.log(x.files[0]);
                Swal.fire({
                    title: "Anda Yakin?",
                    text: "Ingin mengimport data dari file tersebut",
                    showDenyButton: true,
                    confirmButtonText: 'Import',
                    denyButtonText: "Cancel",
                }).then((result) => {
                    if(result.isConfirmed) {
                        var formData = new FormData();
                        formData.append("file", x.files[0]);
                        formData.append("_token", "{{ csrf_token() }}");
                        var xhr = new XMLHttpRequest();
                        xhr.onload = function(){
                            var response = JSON.parse(this.responseText);
                            console.log(response)
                            if(response.status){
                                Swal.fire(response.message, '', 'success');
                            }
                        }
                        xhr.open("POST", _baseURL+'/api/payment/settings/courserates/import', true);
                        xhr.send(formData);
                    }
                })
            }
        } 
    }

    function importBtn(){
        $('#myFiles').click()
    }
</script>
@endsection