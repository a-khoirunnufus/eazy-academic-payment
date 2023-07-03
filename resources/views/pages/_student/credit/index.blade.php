@extends('layouts.student.layout-master')

@section('page_title', 'Pengajuan Cicilan Pembayaran')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

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
    <table id="credit-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Kode Komponen</th>
                <th>Komponen Tagihan</th>
                <th class="text-center">Mahasiswa Lama</th>
                <th class="text-center">Mahasiswa Baru</th>
                <th class="text-center">Pendaftar</th>
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
        _creditTable.init();
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

    const _creditTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#credit-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/payment/settings/component/index',
                },
                columns: [
                    {
                        name: 'action',
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: (data, _, row) => {
                            return this.template.rowAction(data)
                        }
                    },
                    {name: 'msc_name', data: 'msc_name'},
                    {name: 'msc_description', data: 'msc_description'},
                    {
                        name: 'msc_is_student',
                        data: 'msc_is_student',
                        render: (data, _, row) => {
                            var html = '<div class="d-flex justify-content-center">'
                            if(data == 1) {
                                html += '<div class="eazy-badge blue"><i data-feather="check"></i></div>'
                            } else {
                                html += '<div class="eazy-badge red"><i data-feather="x"></i></div>'
                            }
                            html += '</div>'
                            return html
                        }
                    },
                    {
                        name: 'msc_is_new_student',
                        data: 'msc_is_new_student',
                        render: (data, _, row) => {
                            var html = '<div class="d-flex justify-content-center">'
                            if(data == 1) {
                                html += '<div class="eazy-badge blue"><i data-feather="check"></i></div>'
                            } else {
                                html += '<div class="eazy-badge red"><i data-feather="x"></i></div>'
                            }
                            html += '</div>'
                            return html
                        }
                    },
                    {
                        name: 'msc_is_participant',
                        data: 'msc_is_participant',
                        render: (data, _, row) => {
                            var html = '<div class="d-flex justify-content-center">'
                            if(data == 1) {
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
                            <button onclick="_creditTableActions.add()" class="btn btn-primary">
                                <span style="vertical-align: middle">
                                    <i data-feather="plus" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Pengajuan Cicilan
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
            rowAction: function(id) {
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a onclick="_creditTableActions.edit(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="_creditTableActions.delete(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const _componentForm = {
        clearData: function(){
            FormDataJson.clear('#form-add-invoice-component')
            $("#form-add-invoice-component .select2").trigger('change')
            $(".form-alert").remove()
        },
        setData: function(data){
            $("[name=msc_name]").val(data.msc_name)
            $("[name=msc_description]").val(data.msc_description)
            _options.load({
                optionUrl: _baseURL + '/api/payment/settings/component-type',
                nameField: 'msct_id',
                idData: 'msct_id',
                nameData: 'msct_name',
                val: data.msct_id
            });
            data.msc_is_new_student == 1 ? $('[name=msc_is_new_student]').prop('checked', true) : '';
            data.msc_is_student == 1 ? $('[name=msc_is_student]').prop('checked', true) : '';
            data.msc_is_participant == 1 ? $('[name=msc_is_participant]').prop('checked', true) : '';
        }
    }

    const _creditTableActions = {
        add: function() {
            Modal.show({
                type: 'form',
                modalTitle: 'Tambah Komponen Tagihan',
                modalSize: 'md',
                config: {
                    formId: 'form-add-invoice-component',
                    formActionUrl: _baseURL + '/api/payment/settings/component/store',
                    formType: 'add',
                    fields: {
                        invoice_component_code: {
                            title: 'Kode Komponen Tagihan',
                            content: {
                                template:
                                    `<input
                                        type="text"
                                        name="msc_name"
                                        class="form-control"
                                    >`,
                            },
                        },
                        invoice_component_name: {
                            title: 'Nama Komponen Tagihan',
                            content: {
                                template:
                                    `<input
                                        type="text"
                                        name="msc_description"
                                        class="form-control"
                                    >`,
                            },
                        },
                        component_type: {
                            title: 'Jenis Komponen Tagihan',
                            content: {
                                template:
                                    `<select name="msct_id" class="form-control select2">
                                        <option value="">Pilih Jenis Komponen</option>
                                    </select>`,
                            },
                        },
                        subjects: {
                            title: 'Tersedia Bagi',
                            type: 'checkbox',
                            content: {
                                template: `
                                    <table class="table table-bordered">
                                        <tr class="bg-light">
                                            <th class="text-center">Mahasiswa Lama</th>
                                            <th class="text-center">Mahasiswa Baru</th>
                                            <th class="text-center">Pendaftar</th>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><input type="checkbox" name="msc_is_student" class="form-check-input" /></td>
                                            <td class="text-center"><input type="checkbox" name="msc_is_new_student" class="form-check-input" /></td>
                                            <td class="text-center"><input type="checkbox" name="msc_is_participant" class="form-check-input" /></td>
                                        </tr>
                                    </table>
                                `
                            },
                        },
                    },
                    formSubmitLabel: 'Tambah Komponen',
                    callback: function(e) {
                        _creditTable.reload()
                    },
                },
            });
            _options.load({
                optionUrl: _baseURL + '/api/payment/settings/component-type',
                nameField: 'msct_id',
                idData: 'msct_id',
                nameData: 'msct_name'
            });
        },
        edit: function(e) {
            let data = _creditTable.getRowData(e);
            Modal.show({
                type: 'form',
                modalTitle: 'Edit Komponen Tagihan',
                modalSize: 'md',
                config: {
                    formId: 'form-edit-transaction-group',
                    formActionUrl: _baseURL + '/api/payment/settings/component/store',
                    formType: 'edit',
                    rowId: data.msc_id,
                    fields: {
                        invoice_component_code: {
                            title: 'Kode Komponen Tagihan',
                            content: {
                                template:
                                    `<input
                                        type="text"
                                        name="msc_name"
                                        class="form-control"
                                        value=""
                                    >`,
                            },
                        },
                        invoice_component_name: {
                            title: 'Nama Komponen Tagihan',
                            content: {
                                template:
                                    `<input
                                        type="text"
                                        name="msc_description"
                                        class="form-control"
                                        value=""
                                    >`,
                            },
                        },
                        component_type: {
                            title: 'Jenis Komponen Tagihan',
                            content: {
                                template:
                                    `<select name="msct_id" id="msct_id" class="form-control select2">
                                        <option value="">Pilih Jenis Komponen</option>
                                    </select>`,
                            },
                        },
                        subjects: {
                            title: null,
                            type: 'checkbox',
                            content: {
                                template: `
                                    <table class="table table-bordered">
                                        <tr class="bg-light">
                                            <th class="text-center">Mahasiswa Lama</th>
                                            <th class="text-center">Mahasiswa Baru</th>
                                            <th class="text-center">Pendaftar</th>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><input type="checkbox" name="msc_is_student" class="form-check-input" /></td>
                                            <td class="text-center"><input type="checkbox" name="msc_is_new_student" class="form-check-input" /></td>
                                            <td class="text-center"><input type="checkbox" name="msc_is_participant" class="form-check-input" /></td>
                                        </tr>
                                    </table>
                                `
                            },
                        },
                    },
                    formSubmitLabel: 'Edit Komponen',
                    callback: function() {
                        _creditTable.reload()
                    },
                },
            });
            _componentForm.clearData()
            _componentForm.setData(data)
            _creditTable.selected = data
        },
        delete: function(e) {
            let data = _creditTable.getRowData(e);
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin menghapus komponen tagihan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(_baseURL + '/api/payment/settings/component/delete/' + data.msc_id, {
                        _method: 'DELETE'
                    }, function(data){
                        data = JSON.parse(data)
                        Swal.fire({
                            icon: 'success',
                            text: data.text,
                        }).then(() => {
                            _creditTable.reload()
                        });
                    }).fail((error) => {
                        Swal.fire({
                            icon: 'error',
                            text: data.text,
                        });
                        _responseHandler.generalFailResponse(error)
                    })
                }
            })
        },
    }

</script>
@endsection
