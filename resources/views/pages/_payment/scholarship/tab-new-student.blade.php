<table id="table-scholarship-receiver-new-student" class="table table-striped">
    <thead>
        <tr>
            <th>
                <input id="check-all-receiver" class="form-check-input" type="checkbox" />
            </th>
            <th class="text-center">Aksi</th>
            <th>Mahasiswa</th>
            <th>Fakultas - Prodi</th>
            <th>Beasiswa</th>
            <th>Periode </th>
            <th>Nominal</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<div class="modal fade" id="modal-copy-data-new-student" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Salin Data Penerima Beasiswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="eazy-table-wrapper">
                    <form id="form-copy-data-new-student">
                        <table id="table-copied-data-new-student" class="table table-striped table-sm" style="width: 100%; font-size: .9rem;">
                            <thead>
                                <tr>
                                    <th class="text-nowrap">Mahasiswa<br>Fakultas<br>Program Studi</th>
                                    <th class="text-nowrap">Beasiswa</th>
                                    <th class="text-nowrap">
                                        <span class="d-block" style="margin-bottom: 10px">Periode</span>
                                        <select id="select-all-period" class="form-select form-select-sm w-150">
                                            <option selected>Pilih Periode Batch</option>
                                        </select>
                                    </th>
                                    <th class="text-nowrap">
                                        <span class="d-block" style="margin-bottom: 10px">Nominal</span>
                                        <input type="text" class="form-control form-control-sm input-all-nominal w-150" placeholder="Masukkan Nominal"/>
                                    </th>
                                    <th class="text-nowrap">
                                        <span class="d-block" style="margin-bottom: 10px">Status Aktif</span>
                                        <select id="select-all-status" class="form-select form-select-sm w-150">
                                            <option selected>Pilih Status Batch</option>
                                            <option value="1">Aktif</option>
                                            <option value="0">Tidak Aktif</option>
                                        </select>
                                    </th>
                                    <th class="text-nowrap">Status Salin</th>
                                    <th class="text-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-end">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary" onclick="copyNewStudentReceiverActions.validateData()">Validasi Data</button>
                <button class="btn btn-success" onclick="copyNewStudentReceiverActions.storeBatch()">Simpan</button>
            </div>
        </div>
    </div>
</div>

@prepend('scripts')
<script>

    $(function() {
        _scholarshipReceiverNewStudentTable.init();

        $('#table-scholarship-receiver-new-student #check-all-receiver').on('change', function() {
            if (this.checked) {
                $('#table-scholarship-receiver-new-student input.check-receiver').each(function() {
                    $(this).prop('checked', true);
                });
            } else {
                $('#table-scholarship-receiver-new-student input.check-receiver').each(function() {
                    $(this).prop('checked', false);
                });
            }
        });

        copyNewStudentReceiverActions.setupElements();
    })

    const _scholarshipReceiverNewStudentTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#table-scholarship-receiver-new-student').DataTable({
                ajax: {
                    url: _baseURL + '/api/payment/scholarship-receiver-new/index',
                    data: (d) => {
                        const filters = this.getFilters();
                        if (filters.length > 0) {
                            d.filters = filters;
                        }
                    },
                },
                order: [[2, 'asc']],
                columns: [
                    {
                        orderable: false,
                        searchable: false,
                        render: (data, type, row, meta) => {
                            return `<input data-dt-row="${meta.row}" class="check-receiver form-check-input" type="checkbox" />`;
                        }
                    },
                    {
                        name: 'action',
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: (data, _, row) => {
                            // console.log(row);
                            return this.template.rowAction(row)
                        }
                    },
                    {
                        name: 'reg_id',
                        data: 'reg_id',
                        searchable: false,
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <span class="text-nowrap fw-bold">${row.new_student.participant.par_fullname}</span><br>
                                    <small class="text-nowrap text-secondary">${row.new_student.reg_number}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        searchable: false,
                        orderable: false,
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <span class="text-nowrap fw-bold">${row.new_student.studyprogram.studyprogram_type} ${row.new_student.studyprogram.studyprogram_name}</span><br>
                                    <small class="text-nowrap text-secondary">${row.new_student.studyprogram.faculty.faculty_name}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'ms_id',
                        data: 'ms_id',
                        searchable: false,
                        orderable: false,
                        render: (data, _, row) => {
                            let company = (row.scholarship.ms_from) ? row.scholarship.ms_from : "";
                            return "<span class='fw-bolder'>" + row.scholarship.ms_name + "</span> <br>" + company;
                        }
                    },
                    {
                        name: 'msr_period',
                        data: 'msr_period',
                        searchable: false,
                        orderable: false,
                        render: (data, _, row) => {
                            return row.period.msy_year + _helperNewStudent.semester(row.period.msy_semester)
                        }
                    },
                    {
                        name: 'msr_nominal',
                        data: 'msr_nominal',
                        searchable: false,
                        render: (data, _, row) => {
                            return Rupiah.format(data)
                        }
                    },
                    {
                        name: 'msr_status',
                        data: 'msr_status',
                        searchable: false,
                        orderable: false,
                        render: (data, _, row) => {
                            let status = "Tidak Aktif";
                            let bg = "bg-danger";
                            if (row.msr_status === 1) {
                                status = "Aktif";
                                bg = "bg-success";
                            }
                            return '<div class="badge ' + bg + '">' + status + '</div>'
                        }
                    },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                language: {
                    search: '_INPUT_',
                    searchPlaceholder: "Cari Data",
                    lengthMenu: '_MENU_',
                    paginate: { 'first': 'First', 'last': 'Last', 'next': 'Next', 'previous': 'Prev' },
                    processing: "Loading...",
                    emptyTable: "Tidak ada data",
                    infoEmpty:  "Menampilkan 0",
                    lengthMenu: "_MENU_",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    infoFiltered: "(difilter dari _MAX_ entri)",
                    zeroRecords: "Tidak ditemukan data yang cocok"
                },
                dom: '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"custom-actions-new-student d-flex align-items-end">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end"flB> >' +
                    '><"eazy-table-wrapper"tr>' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                buttons: [
                    {
                        extend: 'collection',
                        text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-external-link font-small-4 me-50"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>Export</span>',
                        className: 'btn btn-outline-secondary dropdown-toggle',
                        buttons: [
                            {
                                text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clipboard font-small-4 me-50"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>Pdf</span>',
                                className: 'dropdown-item',
                                extend: 'pdf',
                                orientation: 'landscape',
                                exportOptions: {
                                    columns: [8,9,10,11,12,13,14,15,16]
                                }
                            },
                            {
                                text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file font-small-4 me-50"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>Excel</span>',
                                className: 'dropdown-item',
                                action: function(e, dt, node, config) {
                                    var formData = new FormData();
                                    formData.append("data", JSON.stringify(dataDt));
                                    formData.append("_token", '{{csrf_token()}}');
                                    // window.open(_baseURL+'/payment/scholarship/exportData?data='+JSON.stringify(dataDt));
                                    var xhr = new XMLHttpRequest();
                                    xhr.onload = function() {
                                        var downloadUrl = URL.createObjectURL(xhr.response);
                                        var a = document.createElement("a");
                                        document.body.appendChild(a);
                                        a.style = "display: none";
                                        a.href = downloadUrl;
                                        a.download = "Laporan Mahasiswa Penerima Beasiswa";
                                        a.click();
                                    }
                                    xhr.open("POST", _baseURL + "/api/payment/scholarship-receiver/exportData");
                                    xhr.responseType = 'blob';
                                    xhr.send(formData);
                                }
                            },
                            {
                                text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text font-small-4 me-50"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>Csv</span>',
                                className: 'dropdown-item',
                                extend: 'csv',
                                exportOptions: {
                                    columns: [8, 9, 10, 11, 12, 13, 14, 15, 16]
                                }
                            },
                            {
                                text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-copy font-small-4 me-50"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>Copy</span>',
                                className: 'dropdown-item',
                                extend: 'copy',
                                exportOptions: {
                                    columns: [8, 9, 10, 11, 12, 13, 14, 15, 16]
                                }
                            }
                        ]
                    },
                ],
                initComplete: function() {
                    $('.custom-actions-new-student').html(`
                        <div style="margin-bottom: 7px">
                            <button onclick="_scholarshipReceiverNewStudentTableActions.add()" class="btn btn-info">
                                <span style="vertical-align: middle">
                                    <i data-feather="plus" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Tambah Penerima
                                </span>
                            </button>
                            <button class="btn btn-outline-primary ms-1" onclick="copyNewStudentReceiverActions.openModalCopyData()">
                                <i data-feather="copy"></i>&nbsp;&nbsp;Salin Data
                            </button>
                        </div>
                    `)
                    feather.replace()
                }
            });
            this.implementSearchDelay()
        },
        template: {
            rowAction: function(row) {
                let action = `
                <span class="d-inline-block" tabindex="0" data-toggle="tooltip"
                    data-placement="right" title="Data yang sudah digenerate tidak bisa diubah kembali" >
                    <button class="dropdown-item" disabled><i data-feather="edit"></i>&nbsp;&nbsp;Edit</button>
                </span>
                <span class="d-inline-block" tabindex="0" data-toggle="tooltip"
                    data-placement="right" title="Data yang sudah digenerate tidak bisa diubah kembali" >
                    <button class="dropdown-item" disabled><i data-feather="trash"></i>&nbsp;&nbsp;Delete</button>
                </span>
                    `;
                if(row.msr_status_generate === 0){
                    action = `<a onclick="_scholarshipReceiverNewStudentTableActions.edit(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                    <a onclick="_scholarshipReceiverNewStudentTableActions.delete(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>`;
                }
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            ${action}
                        </div>
                    </div>
                `
            }
        },
        getFilters: function() {
            let filters = [];

            if (assignFilter('#period-filter')) {
                filters.push({
                    column: 'msr_period',
                    operator: '=',
                    value: assignFilter('#period-filter'),
                });
            }

            if (assignFilter('#scholarship-filter')) {
                filters.push({
                    column: 'ms_id',
                    operator: '=',
                    value: assignFilter('#scholarship-filter'),
                });
            }

            if (assignFilter('#faculty-filter')) {
                filters.push({
                    column: 'newStudent.studyProgram.faculty_id',
                    operator: '=',
                    value: assignFilter('#faculty-filter'),
                });
            }

            if (assignFilter('#studyprogram-filter')) {
                filters.push({
                    column: 'newStudent.studyProgram.studyprogram_id',
                    operator: '=',
                    value: assignFilter('#studyprogram-filter'),
                });
            }

            return filters;
        },
    }

    const _componentFormNewStudent = {
        clearData: function() {
            FormDataJson.clear('#form-edit-scholarship-new')
            $("#form-edit-scholarship-new .select2").trigger('change')
            $(".form-alert").remove()
        },
        setData: function(d) {
            $.get(_baseURL + '/api/payment/scholarship-receiver-new/scholarship', (data) => {
                if (Object.keys(data).length > 0) {
                    data.map(item => {
                        $('#ms_id_new').append(`
                            <option value="` + item.ms_id + `" data-nominal="` + item.ms_nominal + `">` + item.ms_name + `(sisa anggaran: ` + Rupiah.format(item.ms_budget - item.ms_realization) + `)</option>
                        `);
                    });
                    $('#ms_id_new').val(d.ms_id);
                    $('#ms_id_new').trigger('change');
                    selectRefresh();
                }
            });
            $.get(_baseURL + '/api/payment/scholarship-receiver-new/student', (data) => {
                if (Object.keys(data).length > 0) {
                    data.map(item => {
                        $('#student_number_new').append(`
                            <option value="` + item.reg_id + `">` + item.participant.par_fullname + ` - ` + item.reg_number + `</option>
                        `);
                    });
                    $('#student_number_new').val(d.reg_id);
                    $('#student_number_new').trigger('change');
                    selectRefresh();
                }
            });
            $("#ms_id_new").change(function() {
                ms_id = $(this).val();
                $.get(_baseURL + '/api/payment/scholarship-receiver-new/period/' + ms_id, (data) => {
                    // console.log(data);
                    if (Object.keys(data).length > 0) {
                        $("#msr_period_new").empty();
                        data.map(item => {
                            $('#msr_period_new').append(`
                                <option value="` + item.msy_id + `">` + item.msy_year + ` ` + _helperNewStudent.semester(item.msy_semester) + `</option>
                            `);
                        });
                        $('#msr_period_new').val(d.msr_period);
                        $('#msr_period_new').trigger('change');
                        selectRefresh();
                    }
                });
            });
            $("[name=msr_nominal]").val(d.msr_nominal);
            d.msr_status == 1 ? $('#msr_status_1').prop('checked', true) : $('#msr_status_0').prop('checked', true);
        }
    }

    const _helperNewStudent = {
        semester: function(msy_semester) {
            var semester = ' Genap';
            if (msy_semester == 1) {
                semester = ' Ganjil';
            }
            return semester;
        }
    }

    const _scholarshipReceiverNewStudentTableActions = {
        add: function() {
            Modal.show({
                type: 'form',
                modalTitle: 'Tambah Penerima',
                modalSize: 'md',
                config: {
                    formId: 'form-add-scholarship-receiver',
                    formActionUrl: _baseURL + '/api/payment/scholarship-receiver-new/store',
                    formType: 'add',
                    fields: {
                        ms_id: {
                            title: 'Beasiswa',
                            content: {
                                template: `<select name="ms_id" id="ms_id_new" class="form-control select2">
                                        <option value="">Pilih Beasiswa</option>
                                    </select>`,
                            },
                        },
                        reg_id: {
                            title: 'Mahasiswa',
                            content: {
                                template: `<select name="reg_id" id="student_number_new" class="form-control select2">
                                        <option value="">Pilih Mahasiswa</option>
                                    </select>`,
                            },
                        },
                        msr_period: {
                            title: 'Periode',
                            content: {
                                template: `<select name="msr_period" id="msr_period_new" class="form-control select2">
                                        <option value="">Pilih Periode</option>
                                    </select>`,
                            },
                        },
                        msr_nominal: {
                            title: 'Nominal',
                            content: {
                                template: `<input type="number" name="msr_nominal" class="form-control">`,
                            },
                        },
                        msr_status: {
                            title: 'Status',
                            content: {
                                template: `<br><input type="radio" name="msr_status" value="1" class="form-check-input" checked/> Aktif <input type="radio" name="msr_status" value="0" class="form-check-input"/> Tidak Aktif`,
                            },
                        },
                    },
                    formSubmitLabel: 'Tambah Penerima',
                    callback: function(e) {
                        _scholarshipReceiverNewStudentTable.reload()
                    },
                },
            });
            $.get(_baseURL + '/api/payment/scholarship-receiver-new/scholarship', (data) => {
                if (Object.keys(data).length > 0) {
                    data.map(item => {
                        $('#ms_id_new').append(`
                            <option value="` + item.ms_id + `" data-nominal="` + item.ms_nominal + `">` + item.ms_name + `(sisa anggaran: ` + Rupiah.format(item.ms_budget - item.ms_realization) + `)</option>
                        `);
                    });
                    selectRefresh();
                }
            });
            $.get(_baseURL + '/api/payment/scholarship-receiver-new/student', (data) => {
                if (Object.keys(data).length > 0) {
                    data.map(item => {
                        $('#student_number_new').append(`
                            <option value="` + item.reg_id + `">` + item.participant.par_fullname + ` - ` + item.reg_number + `</option>
                        `);
                    });
                    selectRefresh();
                }
            });
            $("#ms_id_new").change(function() {
                nominal = $(this).find(":selected").data("nominal");
                ms_id = $(this).val();
                $('[name="msr_nominal"]').val(nominal);
                $.get(_baseURL + '/api/payment/scholarship-receiver-new/period/' + ms_id, (data) => {
                    if (Object.keys(data).length > 0) {
                        $("#msr_period_new").empty();
                        data.map(item => {
                            $('#msr_period_new').append(`
                            <option value="` + item.msy_id + `">` + item.msy_year + ` ` + _helperNewStudent.semester(item.msy_semester) + `</option>
                        `);
                        });
                        selectRefresh();
                    }
                });
            })
        },
        edit: function(e) {
            let data = _scholarshipReceiverNewStudentTable.getRowData(e);
            Modal.show({
                type: 'form',
                modalTitle: 'Edit Penerima Beasiswa',
                modalSize: 'md',
                config: {
                    formId: 'form-edit-scholarship-new-receiver',
                    formActionUrl: _baseURL + '/api/payment/scholarship-receiver-new/store',
                    formType: 'edit',
                    rowId: data.msr_id,
                    fields: {
                        ms_id: {
                            title: 'Beasiswa',
                            content: {
                                template: `<select name="ms_id" id="ms_id_new" class="form-control select2">
                                        <option value="">Pilih Beasiswa</option>
                                    </select>`,
                            },
                        },
                        reg_id: {
                            title: 'Mahasiswa',
                            content: {
                                template: `<select name="reg_id" id="student_number_new" class="form-control select2">
                                        <option value="">Pilih Mahasiswa</option>
                                    </select>`,
                            },
                        },
                        msr_period: {
                            title: 'Periode',
                            content: {
                                template: `<select name="msr_period" id="msr_period_new" class="form-control select2">
                                        <option value="">Pilih Periode</option>
                                    </select>`,
                            },
                        },
                        msr_nominal: {
                            title: 'Nominal',
                            content: {
                                template: `<input type="number" name="msr_nominal" class="form-control">`,
                            },
                        },
                        md_status: {
                            title: 'Status',
                            content: {
                                template: `<br><input type="radio" name="msr_status" value="1" id="msr_status_1" class="form-check-input" checked/> Aktif <input type="radio" name="msr_status" id="msr_status_0" value="0" class="form-check-input"/> Tidak Aktif`,
                            },
                        },
                    },
                    formSubmitLabel: 'Edit Beasiswa',
                    callback: function() {
                        _scholarshipReceiverNewStudentTable.reload()
                    },
                },
            });
            _componentFormNewStudent.clearData()
            _componentFormNewStudent.setData(data)
            _scholarshipReceiverNewStudentTable.selected = data
        },
        delete: function(e) {
            let data = _scholarshipReceiverNewStudentTable.getRowData(e);
            Swal.fire({
                title: 'Konfirmasi',
                html: 'Apakah anda yakin ingin menghapus <br> <span class="fw-bolder">' + data.new_student.participant.par_fullname + '</span> sebagai penerima beasiswa?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(_baseURL + '/api/payment/scholarship-receiver-new/delete/' + data.msr_id, {
                        _method: 'DELETE'
                    }, function(data) {
                        data = JSON.parse(data)
                        Swal.fire({
                            icon: 'success',
                            text: data.message,
                        }).then(() => {
                            _scholarshipReceiverNewStudentTable.reload()
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

    const copyNewStudentReceiverActions = {
        setupElements: () => {
            $.get({url: `${_baseURL}/api/payment/resource/school-year`})
                .then(schoolYears => {
                    $('#modal-copy-data-new-student #select-all-period').append(
                        schoolYears.map(item => `
                            <option value="${item.msy_id}">${item.msy_year} ${item.msy_semester == 1 ? 'Ganjil' : 'Genap'}</option>
                        `).join('')
                    );
                });

            $('#modal-copy-data-new-student select#select-all-period').on('change', function() {
                const selectedValue = $(this).val();
                $('#form-copy-data-new-student select[name="msr_period[]"]').each(function() {
                    $(this).val(selectedValue);
                });
            });

            $('#modal-copy-data-new-student input.input-all-nominal').on('change', function() {
                const currentValue = $(this).val();
                $('#form-copy-data-new-student input.input_msr_nominal').each(function() {
                    $(this).val(currentValue);
                    document.querySelectorAll('.input_msr_nominal').forEach(elm => {
                        elm.dispatchEvent(new Event('input', { bubbles: true }));
                    });
                });
            });
            _numberCurrencyFormat.load('input-all-nominal');

            $('#modal-copy-data-new-student select#select-all-status').on('change', function() {
                const selectedValue = $(this).val();
                $('#form-copy-data-new-student select[name="msr_status[]"]').each(function() {
                    $(this).val(selectedValue);
                });
            });
        },
        openModalCopyData: async () => {
            const schoolYear = await $.get({
                async: true,
                url: `${_baseURL}/api/payment/resource/school-year`,
            });

            $('#modal-copy-data-new-student #table-copied-data-new-student tbody').empty();

            $('#table-scholarship-receiver-new-student input.check-receiver:checked').each(function() {
                const rowIdx = $(this).data('dt-row');
                const row = _scholarshipReceiverNewStudentTable.instance.row(parseInt(rowIdx)).data();

                const inputNominalId = 'input_msr_nominal_' + Math.floor(Math.random()*500);

                $('#modal-copy-data-new-student #table-copied-data-new-student tbody').append(`
                    <tr>
                        <td>
                            <div>
                                ${_datatableTemplates.titleWithSubtitleCell(row.new_student.participant.par_fullname, row.new_student.reg_number)}
                                <input type="hidden" name="reg_id[]" value="${row.reg_id}" />
                            </div>
                            <div style="margin-top: 5px">
                                ${_datatableTemplates.titleWithSubtitleCell(
                                    row.new_student.studyprogram.studyprogram_type.toUpperCase()+' '+row.new_student.studyprogram.studyprogram_name,
                                    row.new_student.studyprogram.faculty.faculty_name
                                )}
                            </div>
                        </td>
                        <td>
                            <div>
                                <span class="fw-bold text-nowrap">
                                    ${row.scholarship.ms_name}&nbsp;
                                    <a class="btn d-inline-block p-0" onclick="copyNewStudentReceiverActions.showScholarshipDetailModal(${row.scholarship.ms_id})"><i data-feather="info"></i></a>
                                </span>
                                ${
                                    row.scholarship.ms_from
                                        ? `<br><small class="text-secondary  text-nowrap">${row.scholarship.ms_from ?? '-'}</small>`
                                        : ''
                                }
                            </div>
                            <input type="hidden" name="ms_id[]" value="${row.ms_id}" />
                        </td>
                        <td>
                            <select name="msr_period[]" class="form-select form-select-sm w-150" value="${row.msr_period}">
                                ${
                                    schoolYear
                                        .filter(item => {
                                            return item.msy_code >= row.scholarship.period_start.msy_code && item.msy_code <= row.scholarship.period_end.msy_code;
                                        })
                                        .map(item => `
                                            <option value="${item.msy_id}" ${row.msr_period == item.msy_id ? 'selected' : ''}>${item.msy_year} ${item.msy_semester == 1 ? 'Ganjil' : 'Genap'}</option>
                                        `)
                                        .join('')
                                }
                            </select>
                        </td>
                        <td>
                            <input class="form-control form-control-sm w-150 input_msr_nominal ${inputNominalId}" type="text" value="${row.msr_nominal}" />
                        </td>
                        <td>
                            <select name="msr_status[]" class="form-select form-select-sm w-150" value="${row.msr_status}">
                                <option value="1" ${row.msr_status == 1 ? 'selected' : ''}>Aktif</option>
                                <option value="0" ${row.msr_status == 0 ? 'selected' : ''}>Tidak Aktif</option>
                            </select>
                        </td>
                        <td>
                            <div class="badge bg-success text-nowrap" style="font-size: inherit">
                                Data Valid
                            </div>
                            <input type="hidden" name="is_data_valid[]" value="1" />
                        </td>
                        <td>
                            <a class="btn btn-icon btn-sm btn-danger" onclick="copyNewStudentReceiverActions.deleteCopyRow(this)">
                                <i data-feather="trash"></i>
                            </a>
                        </td>
                    </tr>
                `);

                _numberCurrencyFormat.load(inputNominalId, 'msr_nominal', 1);
            });

            $('#modal-copy-data-new-student').modal('show');

            feather.replace();

            copyNewStudentReceiverActions.validateData();
        },
        deleteCopyRow: async (elm) => {
            const confirmed = await _swalConfirmSync({
                    title: 'Konfirmasi',
                    text: 'Apakah anda yakin ingin menghapus?',
                });

            if(!confirmed) return;

            $(elm).parents('tr')[0].remove();
        },
        validateData: () => {
            return new Promise(async (resolve, reject) => {
                const data = FormDataJson.toJson("#form-copy-data-new-student");

                const res = await $.ajax({
                    async: true,
                    url: _baseURL + '/api/payment/scholarship-receiver-new/validate-batch',
                    type: 'post',
                    data: data,
                });

                $(`#table-copied-data-new-student tbody tr td:nth-child(6)`).html(`
                    <div class="badge bg-success text-nowrap" style="font-size: inherit">
                        Data Valid
                    </div>
                    <input type="hidden" name="is_data_valid[]" value="1" />
                `);

                if (Object.keys(res).length > 0) {
                    for (const key in res) {
                        const rowIdx = key.split('_')[1];
                        $(`#table-copied-data-new-student tbody > tr:nth-child(${rowIdx}) td:nth-child(6)`).html(`
                            <div class="badge bg-danger text-nowrap" style="font-size: inherit">
                                Data Tidak Valid
                            </div>
                            <input type="hidden" name="is_data_valid[]" value="0" />
                            <ul class="list-group" style="margin-top: 5px">
                                ${res[key].map(msg => `<li class="list-group-item text-nowrap text-danger fw-bold" style="font-size: .85rem; padding: 0 5px;">${msg}</li>`).join('')}
                            </ul>
                        `);
                    }

                    resolve(false);
                }

                resolve(true);
            })
        },
        storeBatch: async () => {
            const confirmed = await _swalConfirmSync({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin menambahkan data?',
            });
            if (!confirmed) return;

            const isDataValid = await copyNewStudentReceiverActions.validateData();
            if (!isDataValid) {
                _toastr.error('Masih terdapat data yang belum valid, silahkan sesuaikan data.', 'Gagal');
                return;
            }

            try {
                const formData = new FormData($('#form-copy-data-new-student')[0]);

                const res = await $.ajax({
                    async: true,
                    url: _baseURL + '/api/payment/scholarship-receiver-new/store-batch',
                    type: 'post',
                    data: formData,
                    contentType: false,
                    processData: false,
                });

                if (res.success) {
                    _toastr.success(res.message, 'Berhasil');
                } else {
                    _toastr.error(res.message, 'Gagal');
                }
            } catch (error) {
                _toastr.error(error.responseJSON.message, 'Gagal');
            } finally {
                $('#modal-copy-data-new-student').modal('hide');
                _scholarshipReceiverNewStudentTable.reload();
            }

        },
        showScholarshipDetailModal: async (id) => {
            const scholarship = await $.ajax({
                async: true,
                url: _baseURL + '/api/payment/resource/scholarship/' + id,
            });

            let html = `
                <tr>
                    <td class="align-top px-1" style="width: 200px">Nama Beasiswa</td>
                    <td class="align-top px-0" style="width: 5px">:</td>
                    <td class="align-top px-1" style="min-width: 400px; max-width: fit-content">
                        ${scholarship.ms_name}
                    </td>
                </tr>
                <tr>
                    <td class="align-top px-1" style="width: 200px">Rekanan</td>
                    <td class="align-top px-0" style="width: 5px">:</td>
                    <td class="align-top px-1" style="min-width: 400px; max-width: fit-content">
                        ${scholarship.ms_from ?? '-'}
                    </td>
                </tr>
                <tr>
                    <td class="align-top px-1" style="width: 200px">Periode Awal</td>
                    <td class="align-top px-0" style="width: 5px">:</td>
                    <td class="align-top px-1" style="min-width: 400px; max-width: fit-content">
                        ${scholarship.period_start.msy_year}
                        ${
                            scholarship.period_start.msy_semester == 1 ? 'Ganjil'
                                : scholarship.period_start.msy_semester == 2 ? 'Genap'
                                    : 'Antara'
                        }
                    </td>
                </tr>
                <tr>
                    <td class="align-top px-1" style="width: 200px">Periode Akhir</td>
                    <td class="align-top px-0" style="width: 5px">:</td>
                    <td class="align-top px-1" style="min-width: 400px; max-width: fit-content">
                        ${scholarship.period_end.msy_year}
                        ${
                            scholarship.period_end.msy_semester == 1 ? 'Ganjil'
                                : scholarship.period_end.msy_semester == 2 ? 'Genap'
                                    : 'Antara'
                        }
                    </td>
                </tr>
                <tr>
                    <td class="align-top px-1" style="width: 200px">Anggaran Tersedia</td>
                    <td class="align-top px-0" style="width: 5px">:</td>
                    <td class="align-top px-1" style="min-width: 400px; max-width: fit-content">
                        ${Rupiah.format(scholarship.ms_budget - scholarship.ms_realization)}
                    </td>
                </tr>
            `;

            html = $('<table class="table table-bordered dtr-details-custom mb-0" />').append(html);

            $('#modal-scholarship-detail .custom-body').html(html);

            $('#modal-scholarship-detail').modal('show');
        },
    };

</script>
@endprepend
