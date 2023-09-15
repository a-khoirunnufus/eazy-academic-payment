@extends('tpl.vuexy.master-payment')

@section('page_title', 'Pengajuan Dispensasi')
@section('sidebar-size', 'collapsed')
@section('url_back', route('student.payment.index'))

@section('css_section')
    <style>
        .eazy-table-info {
            display: inline-block;
        }
        .eazy-table-info td {
            padding: 5px 10px 5px 0;
        }
        .eazy-table-info.lg td {
            padding: 10px 10px 10px 0;
        }
        .eazy-table-info td:first-child {
            padding-right: 1rem;
            font-weight: 500;
        }

        .nav-tabs.custom .nav-item {
            /* flex-grow: 1; */
        }
        .nav-tabs.custom .nav-link {
            /* width: -webkit-fill-available !important; */
            height: 50px !important;
        }
        .nav-tabs.custom .nav-link.active {
            background-color: #f2f2f2 !important;
        }

        .eazy-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .eazy-header {
            display: flex;
            flex-direction: row;
            gap: 4rem;
            flex-wrap: wrap;
        }
        .eazy-header .eazy-header__item {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 1rem;
        }
        .eazy-header .eazy-header__item .item__icon {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 65px;
            height: 65px;
            border-radius: 1.5rem;
            background-color: rgba(246, 246, 246, 1) !important;
        }
        .eazy-header .eazy-header__item .item__icon svg {
            width: 35px;
            height: 35px;
        }
        .eazy-header .eazy-header__item .item__text {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .eazy-header .eazy-header__item .item__text .text__subtitle {
            display: block;
        }
        .eazy-header .eazy-header__item .item__text .text__title {
            display: block;
            font-weight: 700;
            font-size: 16px;
        }
    </style>
@endsection

@section('content')

<div id="student-info" class="card mb-3">
    <div class="card-body" style="width: 100%">
        <div id="header-info-student">
            ...
        </div>
    </div>
</div>

<div class="card">
    <table id="dispensation-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Tahun Akademik</th>
                <th>No.HP</th>
                <th>Email</th>
                <th>Alasan</th>
                <th>Bukti</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

@endsection

@section('js_section')
<script>

    const userMaster = JSON.parse(`{!! json_encode($user, true) !!}`);

    $(function(){
        renderHeaderInfo();
        _dispensationTable.init();
    });

    async function renderHeaderInfo() {
        const studentType = userMaster.participant ? 'new_student' : 'student';
        const studentId = studentType == 'new_student' ? userMaster.participant.par_id : userMaster.student.student_id;
        const queryParam = `student_type=${studentType}&${studentType == 'new_student' ? 'par_id=' : 'student_id='}${studentId}`;

        const studentDetail = await $.ajax({
            async: true,
            url: `${_baseURL}/api/student/detail?${queryParam}`,
            type: 'get'
        });

        if (studentType == 'new_student') {
            $('#header-info-student').html(`
                <div class="eazy-header">
                    <div class="eazy-header__item">
                        <div class="item__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </div>
                        <div class="item__text">
                            <small class="text__subtitle">Nama Lengkap</small>
                            <span class="text__title">${studentDetail.par_fullname}</span>
                        </div>
                    </div>
                    <div class="eazy-header__item">
                        <div class="item__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-hash"><line x1="4" y1="9" x2="20" y2="9"></line><line x1="4" y1="15" x2="20" y2="15"></line><line x1="10" y1="3" x2="8" y2="21"></line><line x1="16" y1="3" x2="14" y2="21"></line></svg>
                        </div>
                        <div class="item__text">
                            <small class="text__subtitle">NIK</small>
                            <span class="text__title">${studentDetail.par_nik}</span>
                        </div>
                    </div>
                </div>
            `);
        } else if (studentType == 'student') {
            $('#header-info-student').html(`
                <div class="eazy-header">
                    <div class="eazy-header__item">
                        <div class="item__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </div>
                        <div class="item__text">
                            <small class="text__subtitle">Nama Lengkap dan NIM</small>
                            <span class="text__title">${studentDetail.fullname}</span>
                            <span class="d-block">${studentDetail.student_id}</span>
                        </div>
                    </div>
                    <div class="eazy-header__item">
                        <div class="item__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-book-open"><line x1="4" y1="9" x2="20" y2="9"></line><line x1="4" y1="15" x2="20" y2="15"></line><line x1="10" y1="3" x2="8" y2="21"></line><line x1="16" y1="3" x2="14" y2="21"></line></svg>
                        </div>
                        <div class="item__text">
                            <small class="text__subtitle">Fakultas</small>
                            <span class="text__title">${studentDetail.studyprogram.faculty.faculty_name}</span>
                        </div>
                    </div>
                    <div class="eazy-header__item">
                        <div class="item__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bookmark"><line x1="4" y1="9" x2="20" y2="9"></line><line x1="4" y1="15" x2="20" y2="15"></line><line x1="10" y1="3" x2="8" y2="21"></line><line x1="16" y1="3" x2="14" y2="21"></line></svg>
                        </div>
                        <div class="item__text">
                            <small class="text__subtitle">Program Studi</small>
                            <span class="text__title">${studentDetail.studyprogram.studyprogram_name}</span>
                        </div>
                    </div>
                </div>
            `);
        }
    }

    const _helper = {
        semester: function(msy_semester){
            var semester = ' Genap';
            if(msy_semester == 1) {
                semester = ' Ganjil';
            }
            return semester;
        }
    }

    const _dispensationTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#dispensation-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/student/dispensation/index',
                },
                columns: [
                    {
                        name: 'action',
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: (data, _, row) => {
                            console.log(row);
                            return this.template.rowAction(row)
                        }
                    },
                    {
                        name: 'mds_school_year',
                        data: 'mds_school_year',
                        searchable: false,
                        render: (data, _, row) => {
                            return row.period.msy_year + _helper.semester(row.period.msy_semester)
                        }
                    },
                    {name: 'mds_phone', data: 'mds_phone'},
                    {name: 'mds_email', data: 'mds_email'},
                    {name: 'mds_reason', data: 'mds_reason'},
                    {
                        name: 'mds_proof',
                        data: 'mds_proof',
                        searchable: false,
                        render: (data, _, row) => {
                            let link = '{{ url("file","student-dispensation") }}/'+row.mds_id;
                            return '<a href="'+link+'" target="_blank">'+row.mds_proof_filename+'</a>';
                        }
                    },
                    {
                        name: 'mds_status',
                        data: 'mds_status',
                        searchable: false,
                        render: (data, _, row) => {
                            let status = "Ditolak";
                            let bg = "bg-danger";
                            if(row.mds_status === 1){
                                status = "Disetujui";
                                bg = "bg-success";
                            }else if(row.mds_status === 2){
                                status = "Sedang Diproses";
                                bg = "bg-warning";
                            }
                            return '<div class="badge '+bg+'">'+status+'</div>'
                        }
                    },

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
                            <button onclick="_dispensationTableActions.add()" class="btn btn-info">
                                <span style="vertical-align: middle">
                                    <i data-feather="plus" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Pengajuan Dispensasi
                                </span>
                            </button>
                        </div>
                    `)
                    feather.replace()
                }
            })
            this.implementSearchDelay()
        },
        template: {
            rowAction: function(row) {
                let action = ``;
                if(row.mds_status == 2){
                    action = `<a onclick="_dispensationTableActions.edit(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="_dispensationTableActions.delete(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>`;
                }
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a onclick="_dispensationTableActions.detail(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="eye"></i>&nbsp;&nbsp;Detail</a>
                            ${action}
                        </div>
                    </div>
                `
            }
        }
    }

    const _componentForm = {
        clearData: function(){
            FormDataJson.clear('#form-add-dispensation-submission')
            $("#form-add-dispensation-submission .select2").trigger('change')
            $(".form-alert").remove()
        },
        setData: function(data){
            $("[name=fullname]").val(data.student.fullname)
            $("[name=student_number]").val(data.student.student_number)
            $("[name=student_id]").val(data.student.student_id)
            $("[name=academic_year]").val("{{ $year }}")
            $("[name=mds_school_year]").val("{{ $yearCode }}")
            $("[name=mds_phone]").val(data.mds_phone)
            $("[name=mds_email]").val(data.mds_email)
            $("[name=mds_reason]").val(data.mds_reason)
        }
    }

    const _dispensationTableActions = {
        detail: function(e) {
            const data = _dispensationTable.getRowData(e);
            let decline_reason = ``;
            if(data.mds_status == 0){
                decline_reason = `<tr>
                    <td class="fw-bolder">Alasan Penolakan</td>
                    <td class="fw-bolder">:&nbsp;&nbsp;${data.mds_decline_reason}</td>
                </tr>`
            }
            Modal.show({
                type: 'detail',
                modalTitle: 'Detail Pengajuan Dispensasi',
                modalSize: 'md',
                config: {
                    fields: {
                        header: {
                            type: 'custom-field',
                            title: '',
                            content: {
                                template: `
                                <div>
                                    <table class="eazy-table-info">
                                        <tbody>
                                            ${decline_reason}
                                            <tr>
                                                <td>Tahun Akademik</td>
                                                <td>:&nbsp;&nbsp;${data.period.msy_year + _helper.semester(data.period.msy_semester)}</td>
                                            </tr>
                                            <tr>
                                                <td>Nama</td>
                                                <td>:&nbsp;&nbsp;${data.student.fullname}</td>
                                            </tr>
                                            <tr>
                                                <td>NIM</td>
                                                <td>:&nbsp;&nbsp;${data.student.student_id}</td>
                                            </tr>
                                            <tr>
                                                <td>Fakultas</td>
                                                <td>:&nbsp;&nbsp;${data.student.study_program.faculty.faculty_name}</td>
                                            </tr>
                                            <tr>
                                                <td>Prodi</td>
                                                <td>:&nbsp;&nbsp;${data.student.study_program.studyprogram_type} ${data.student.study_program.studyprogram_name}</td>
                                            </tr>
                                            <tr>
                                                <td>No.HP</td>
                                                <td>:&nbsp;&nbsp;${data.mds_phone}</td>
                                            </tr>
                                            <tr>
                                                <td>Email</td>
                                                <td>:&nbsp;&nbsp;${data.mds_email}</td>
                                            </tr>
                                            <tr>
                                                <td>Alasan</td>
                                                <td>:&nbsp;&nbsp;${data.mds_reason}</td>
                                            </tr>
                                            <tr>
                                                <td>Bukti Pendukung</td>
                                                <td>:&nbsp;&nbsp;<a href="${'{{ url("file","student-dispensation") }}/'+data.mds_id}" target="_blank">${data.mds_proof_filename}</a></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>`
                            },
                        },
                    },
                    callback: function() {
                        feather.replace();
                    }
                },
            });
        },
        add: function() {
            Modal.show({
                type: 'form',
                modalTitle: 'Pengajuan Dispensasi',
                modalSize: 'lg',
                config: {
                    formId: 'form-add-dispensation-submission',
                    formActionUrl: _baseURL + '/api/student/dispensation/store',
                    formType: 'add',
                    isTwoColumn: true,
                    fields: {
                        name: {
                            title: 'Nama',
                            content: {
                                template:
                                    `<input
                                        type="text"
                                        name="fullname"
                                        class="form-control" value="{{ $user->student->fullname }}" disabled="disabled"
                                    >
                                    <input type="hidden" name="student_number" value="{{$user->student->student_number}}">`,
                            },
                        },
                        nim: {
                            title: 'NIM',
                            content: {
                                template:
                                    `<input
                                        type="text"
                                        name="student_id"
                                        class="form-control" value="{{ $user->student->student_id }}" disabled="disabled"
                                    >`,
                            },
                        },
                        academic: {
                            title: 'Tahun Akademik',
                            content: {
                                template:
                                    `<input
                                        type="text"
                                        name="academic_year"
                                        class="form-control" value="{{ $year }}" disabled="disabled"
                                    >
                                    <input type="hidden" name="mds_school_year" value="{{$yearCode}}">`,
                            },
                        },
                        no_telp: {
                            title: 'No Telepon',
                            content: {
                                template:
                                    `<input
                                        type="text"
                                        name="mds_phone"
                                        class="form-control"
                                    >`,
                            },
                        },
                        email: {
                            title: 'Email',
                            content: {
                                template:
                                    `<input
                                        type="text"
                                        name="mds_email"
                                        class="form-control"
                                    >`,
                            },
                        },
                        proof: {
                            title: 'Bukti Pendukung (.jpg/.pdf)',
                            content: {
                                template:
                                    `<input
                                        type="file"
                                        name="mds_proof"
                                        class="form-control"
                                    >
                                    `,
                            },
                        },
                        reason: {
                            title: 'Alasan',
                            content: {
                                template:
                                    `<textarea name="mds_reason" class="form-control"></textarea>
                                    `,
                            },
                        },
                    },
                    formSubmitLabel: 'Ajukan',
                    callback: function(e) {
                        _dispensationTable.reload()
                    },
                },
            });
        },
        edit: function(e) {
            let data = _dispensationTable.getRowData(e);
            Modal.show({
                type: 'form',
                modalTitle: 'Edit Pengajuan Dispensasi',
                modalSize: 'lg',
                config: {
                    formId: 'form-edit-dispensation-submission',
                    formActionUrl: _baseURL + '/api/student/dispensation/store',
                    formType: 'edit',
                    isTwoColumn: true,
                    rowId: data.mds_id,
                    fields: {
                        name: {
                            title: 'Nama',
                            content: {
                                template:
                                    `<input
                                        type="text"
                                        name="fullname"
                                        class="form-control" value="{{ $user->student->fullname }}" disabled="disabled"
                                    >
                                    <input type="hidden" name="student_number" value="{{$user->student->student_number}}">`,
                            },
                        },
                        nim: {
                            title: 'NIM',
                            content: {
                                template:
                                    `<input
                                        type="text"
                                        name="student_id"
                                        class="form-control" value="{{ $user->student->student_id }}" disabled="disabled"
                                    >`,
                            },
                        },
                        academic: {
                            title: 'Tahun Akademik',
                            content: {
                                template:
                                    `<input
                                        type="text"
                                        name="academic_year"
                                        class="form-control" value="{{ $year }}" disabled="disabled"
                                    >
                                    <input type="hidden" name="mds_school_year" value="{{$yearCode}}">`,
                            },
                        },
                        no_telp: {
                            title: 'No Telepon',
                            content: {
                                template:
                                    `<input
                                        type="text"
                                        name="mds_phone"
                                        class="form-control"
                                    >`,
                            },
                        },
                        email: {
                            title: 'Email',
                            content: {
                                template:
                                    `<input
                                        type="text"
                                        name="mds_email"
                                        class="form-control"
                                    >`,
                            },
                        },
                        proof: {
                            title: 'Bukti Pendukung (.jpg/.pdf)',
                            content: {
                                template:
                                    `<input
                                        type="file"
                                        name="mds_proof"
                                        class="form-control"
                                    >
                                    `,
                            },
                        },
                        reason: {
                            title: 'Alasan',
                            content: {
                                template:
                                    `<textarea name="mds_reason" class="form-control">
                                    </textarea>
                                    `,
                            },
                        },
                    },
                    formSubmitLabel: 'Edit Pengajuan',
                    callback: function() {
                        _dispensationTable.reload()
                    },
                },
            });
            _componentForm.clearData()
            _componentForm.setData(data)
            _dispensationTable.selected = data
        },
        delete: function(e) {
            let data = _dispensationTable.getRowData(e);
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin menghapus pengajuan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(_baseURL + '/api/student/dispensation/delete/' + data.mds_id, {
                        _method: 'DELETE'
                    }, function(data){
                        data = JSON.parse(data)
                        Swal.fire({
                            icon: 'success',
                            text: data.message,
                        }).then(() => {
                            _dispensationTable.reload()
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
    }

</script>
@endsection
