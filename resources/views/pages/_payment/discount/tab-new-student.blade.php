<table id="table-discount-receiver-new-student" class="table table-striped">
    <thead>
        <tr>
            <th>
                <input id="check-all-receiver" class="form-check-input" type="checkbox" />
            </th>
            <th class="text-center">Aksi</th>
            <th>Mahasiswa</th>
            <th>Fakultas - Prodi</th>
            <th>Potongan</th>
            <th>Periode </th>
            <th>Nominal</th>
            <th>Status</th>
            <th>Nim</th>
            <th>Nama</th>
            <th>Fakultas</th>
            <th>prodi</th>
            <th>Status</th>
            <th>Nominal</th>
            <th>Generate</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<div class="modal fade" id="modal-copy-data-new-student" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Salin Data Penerima Potongan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-row justify-content-end mb-1 w-100">
                    <button class="btn btn-primary" onclick="copyNewStudentReceiverActions.validateData()">Validasi Data</button>
                </div>
                <div class="eazy-table-wrapper">
                    <form id="form-copy-data-new-student">
                        <table id="table-copied-data-new-student" class="table table-striped" style="width: 100%; font-size: .9rem;">
                            <thead>
                                <tr>
                                    <th class="text-nowrap">Mahasiswa</th>
                                    <th class="text-nowrap">Fakultas - Prodi</th>
                                    <th class="text-nowrap">Potongan</th>
                                    <th class="text-nowrap">
                                        <span class="d-block" style="margin-bottom: 10px">Periode</span>
                                        <select id="select-all-period" class="form-select w-200">
                                            <option selected>Pilih Periode Batch</option>
                                        </select>
                                    </th>
                                    <th class="text-nowrap">
                                        <span class="d-block" style="margin-bottom: 10px">Nominal</span>
                                        <input id="input-all-nominal" type="number" class="form-control w-200" placeholder="Masukkan Nominal Batch"/>
                                    </th>
                                    <th class="text-nowrap">
                                        <span class="d-block" style="margin-bottom: 10px">Status Aktif</span>
                                        <select id="select-all-status" class="form-select w-200">
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
                <button class="btn btn-success" onclick="copyNewStudentReceiverActions.storeBatch()">Simpan</button>
            </div>
        </div>
    </div>
</div>

@prepend('scripts')
    <script>
        /**
         * @var dataDt
         */

        $(function() {
            _discountReceiverNewStudentTable.init();

            $('#table-discount-receiver-new-student #check-all-receiver').on('change', function() {
                if (this.checked) {
                    $('#table-discount-receiver-new-student input.check-receiver').each(function() {
                        $(this).prop('checked', true);
                    });
                } else {
                    $('#table-discount-receiver-new-student input.check-receiver').each(function() {
                        $(this).prop('checked', false);
                    });
                }
            });

            copyNewStudentReceiverActions.setupElements();
        })

        const _discountReceiverNewStudentTable = {
            ..._datatable,
            init: function() {
                this.instance = $('#table-discount-receiver-new-student').DataTable({
                    serverSide: true,
                    ajax: {
                        url: _baseURL + '/api/payment/discount-receiver-new/index',
                        data: function(d) {
                            d.filters = [
                                {
                                    column: 'period.msy_code',
                                    operator: '=',
                                    value: $('select[name="period"]').val(),
                                },
                                {
                                    column: 'md_id',
                                    operator: '=',
                                    value: $('select[name="discount_filter"]').val(),
                                },
                                {
                                    column: 'newStudent.studyprogram.faculty_id',
                                    operator: '=',
                                    value: $('select[name="faculty_filter"]').val(),
                                },
                                {
                                    column: 'newStudent.reg_major_pass',
                                    operator: '=',
                                    value: $('select[name="study_program_filter"]').val(),
                                },
                            ];
                        },
                        dataSrc: function(json) {
                            dataDt = json.data;
                            return json.data;
                        }
                    },
                    order: [[2, 'asc']],
                    stateSave: false,
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
                            data: 'mdr_id',
                            orderable: false,
                            searchable: false,
                            render: (data, _, row) => {
                                return this.template.rowAction(row)
                            }
                        },
                        {
                            name: 'student_number',
                            data: 'student_number',
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
                            name: 'student_number',
                            data: 'student_number',
                            searchable: false,
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
                            name: 'md_id',
                            data: 'md_id',
                            searchable: false,
                            render: (data, _, row) => {
                                return row.discount.md_name
                            }
                        },
                        {
                            name: 'mdr_period',
                            data: 'mdr_period',
                            searchable: false,
                            render: (data, _, row) => {
                                return row.period.msy_year + _helperNewStudent.semester(row.period.msy_semester)
                            }
                        },
                        {
                            name: 'mdr_nominal',
                            data: 'mdr_nominal',
                            render: (data, _, row) => {
                                return Rupiah.format(data)
                            }
                        },
                        {
                            name: 'mdr_status',
                            data: 'mdr_status',
                            searchable: false,
                            render: (data, _, row) => {
                                let status = "Tidak Aktif";
                                let bg = "bg-danger";
                                if (row.mdr_status === 1) {
                                    status = "Aktif";
                                    bg = "bg-success";
                                }
                                return '<div class="badge ' + bg + '">' + status + '</div>'
                            }
                        },
                        {
                            name: 'student_number',
                            data: 'new_student.reg_number',
                            visible: false,
                        },
                        {
                            name: 'student_number',
                            data: 'new_student.participant.par_fullname',
                            visible: false,
                        },
                        {
                            name: 'student_number',
                            data: 'new_student.studyprogram.faculty.faculty_name',
                            visible: false,
                        },
                        {
                            name: 'student_number',
                            data: 'student_number',
                            visible: false,
                            render: (data, _, row) => {
                                return row.new_student.studyprogram.studyprogram_type + " " + row.new_student.studyprogram.studyprogram_name
                            }
                        },
                        {
                            name: 'mdr_status',
                            data: 'mdr_status',
                            visible: false,
                            searchable: false,
                            render: (data, _, row) => {
                                let status = "Tidak Aktif";
                                if (row.mdr_status === 1) {
                                    status = "Aktif";
                                }
                                return status
                            }
                        },
                        {
                            name: 'mdr_nominal',
                            data: 'mdr_nominal',
                            visible: false,
                        },
                        {
                            name: 'mdr_status_generate',
                            data: 'mdr_status_generate',
                            searchable: false,
                            render: (data, _, row) => {
                                let status = "Belum Digenerate";
                                let bg = "bg-danger";
                                if (row.mdr_status_generate === 1) {
                                    status = "Sudah Digenerate";
                                    bg = "bg-success";
                                }
                                return '<div class="badge ' + bg + '">' + status + '</div>'
                            }
                        },
                    ],
                    drawCallback: function(settings) {
                        feather.replace();
                    },
                    dom: '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
                        '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"custom-actions-new-student d-flex align-items-end">>' +
                        '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                        '><"eazy-table-wrapper"t>' +
                        '<"d-flex justify-content-between mx-2 row"' +
                        '<"col-sm-12 col-md-6"i>' +
                        '<"col-sm-12 col-md-6"p>' +
                        '>',
                    buttons: [{
                        extend: 'collection',
                        text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-external-link font-small-4 me-50"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>Export</span>',
                        className: 'btn btn-outline-secondary dropdown-toggle',
                        buttons: [
                            {
                                text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clipboard font-small-4 me-50"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>Pdf</span>',
                                className: 'dropdown-item',
                                extend: 'pdf',
                                exportOptions: {
                                    columns: [8, 9, 10, 11, 4, 5, 6, 12]
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
                                        a.download = "Laporan Program Penerima Potongan";
                                        a.click();
                                    }
                                    xhr.open("POST", _baseURL + "/api/payment/discount-receiver/exportData");
                                    xhr.responseType = 'blob';
                                    xhr.send(formData);
                                }
                            },
                            {
                                text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text font-small-4 me-50"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>Csv</span>',
                                className: 'dropdown-item',
                                extend: 'csv',
                                exportOptions: {
                                    columns: [8, 9, 10, 11, 4, 5, 6, 12]
                                }
                            },
                            {
                                text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-copy font-small-4 me-50"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>Copy</span>',
                                className: 'dropdown-item',
                                extend: 'copy',
                                exportOptions: {
                                    columns: [8, 9, 10, 11, 4, 5, 6, 12]
                                }
                            }
                        ]
                    }, ],
                    initComplete: function() {
                        $('.custom-actions-new-student').html(`
                            <div style="margin-bottom: 7px">
                                <button onclick="_discountReceiverNewStudentTableActions.add()" class="btn btn-info">
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
                        // $('.search-filter').html(`
                        //     <div id="table-discount-receiver-new-student_filter" class="dataTables_filter">
                        //         <label>
                        //             <input type="search" class="form-control" placeholder="Cari Data" aria-controls="table-discount-receiver-new-student">
                        //         </label>
                        //     </div>
                        // `);
                        feather.replace()
                    }
                })
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
                    if(row.mdr_status_generate === 0){
                        action = `<a onclick="_discountReceiverNewStudentTableActions.edit(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                        <a onclick="_discountReceiverNewStudentTableActions.delete(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>`;
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
            }
        }

        const _componentFormNewStudent = {
            clearData: function() {
                FormDataJson.clear('#form-edit-discount')
                $("#form-edit-discount .select2").trigger('change')
                $(".form-alert").remove()
            },
            setData: function(d) {
                $.get(_baseURL + '/api/payment/discount-receiver-new/discount', (data) => {
                    if (Object.keys(data).length > 0) {
                        data.map(item => {
                            $('#md_id_new').append(`
                                <option value="` + item.md_id + `" data-nominal="` + item.md_nominal + `">` + item.md_name + `(sisa anggaran: ` + Rupiah.format(item.md_budget - item.md_realization) + `)</option>
                            `);
                        });
                        $('#md_id_new').val(d.md_id);
                        $('#md_id_new').trigger('change');
                        selectRefresh();
                    }
                });
                $.get(_baseURL + '/api/payment/discount-receiver-new/student', (data) => {
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
                $("#md_id_new").change(function() {
                    md_id = $(this).val();
                    $.get(_baseURL + '/api/payment/discount-receiver-new/period/' + md_id, (data) => {
                        console.log(data);
                        if (Object.keys(data).length > 0) {
                            $("#mdr_period_new").empty();
                            data.map(item => {
                                $('#mdr_period_new').append(`
                                    <option value="` + item.msy_id + `">` + item.msy_year + ` ` + _helperNewStudent.semester(item.msy_semester) + `</option>
                                `);
                            });
                            $('#mdr_period_new').val(d.mdr_period);
                            $('#mdr_period_new').trigger('change');
                            selectRefresh();
                        }
                    });
                });
                $("[name=mdr_nominal]").val(d.mdr_nominal);
                d.mdr_status == 1 ? $('#mdr_status_1').prop('checked', true) : $('#mdr_status_0').prop('checked', true);
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

        const _discountReceiverNewStudentTableActions = {
            add: function() {
                Modal.show({
                    type: 'form',
                    modalTitle: 'Tambah Penerima',
                    modalSize: 'md',
                    config: {
                        formId: 'form-add-discount-receiver-new',
                        formActionUrl: _baseURL + '/api/payment/discount-receiver-new/store',
                        formType: 'add',
                        fields: {
                            md_id: {
                                title: 'Potongan',
                                content: {
                                    template: `<select name="md_id" id="md_id_new" class="form-control select2">
                                            <option value="">Pilih Potongan</option>
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
                            mdr_period: {
                                title: 'Periode',
                                content: {
                                    template: `<select name="mdr_period" id="mdr_period_new" class="form-control select2">
                                            <option value="">Pilih Periode</option>
                                        </select>`,
                                },
                            },
                            mdr_nominal: {
                                title: 'Nominal',
                                content: {
                                    template: `<input type="number" name="mdr_nominal" class="form-control">`,
                                },
                            },
                            md_status: {
                                title: 'Status',
                                content: {
                                    template: `<br><input type="radio" name="mdr_status" value="1" class="form-check-input" checked/> Aktif <input type="radio" name="md_status" value="0" class="form-check-input"/> Tidak Aktif`,
                                },
                            },
                        },
                        formSubmitLabel: 'Tambah Penerima',
                        callback: function(e) {
                            _discountReceiverNewStudentTable.reload()
                        },
                    },
                });
                $.get(_baseURL + '/api/payment/discount-receiver-new/discount', (data) => {
                    if (Object.keys(data).length > 0) {
                        data.map(item => {
                            $('#md_id_new').append(`
                                <option value="` + item.md_id + `" data-nominal="` + item.md_nominal + `">` + item.md_name + `(sisa anggaran: ` + Rupiah.format(item.md_budget - item.md_realization) + `)</option>
                            `);
                        });
                        selectRefresh();
                    }
                });
                $.get(_baseURL + '/api/payment/discount-receiver-new/student', (data) => {
                    if (Object.keys(data).length > 0) {
                        data.map(item => {
                            $('#student_number_new').append(`
                                <option value="` + item.reg_id + `">` + item.participant.par_fullname + ` - ` + item.reg_number + `</option>
                            `);
                        });
                        selectRefresh();
                    }
                });
                $("#md_id_new").change(function() {
                    nominal = $(this).find(":selected").data("nominal");
                    md_id = $(this).val();
                    $('[name="mdr_nominal"]').val(nominal);
                    $.get(_baseURL + '/api/payment/discount-receiver-new/period/' + md_id, (data) => {
                        if (Object.keys(data).length > 0) {
                            $("#mdr_period_new").empty();
                            data.map(item => {
                                $('#mdr_period_new').append(`
                                <option value="` + item.msy_id + `">` + item.msy_year + ` ` + _helperNewStudent.semester(item.msy_semester) + `</option>
                            `);
                            });
                            selectRefresh();
                        }
                    });
                })
            },
            edit: function(e) {
                let data = _discountReceiverNewStudentTable.getRowData(e);
                Modal.show({
                    type: 'form',
                    modalTitle: 'Edit Penerima Potongan',
                    modalSize: 'md',
                    config: {
                        formId: 'form-edit-discount-receiver',
                        formActionUrl: _baseURL + '/api/payment/discount-receiver-new/store',
                        formType: 'edit',
                        rowId: data.mdr_id,
                        fields: {
                            md_id: {
                                title: 'Potongan',
                                content: {
                                    template: `<select name="md_id" id="md_id_new" class="form-control select2">
                                            <option value="">Pilih Potongan</option>
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
                            mdr_period: {
                                title: 'Periode',
                                content: {
                                    template: `<select name="mdr_period" id="mdr_period_new" class="form-control select2">
                                            <option value="">Pilih Periode</option>
                                        </select>`,
                                },
                            },
                            mdr_nominal: {
                                title: 'Nominal',
                                content: {
                                    template: `<input type="number" name="mdr_nominal" class="form-control">`,
                                },
                            },
                            md_status: {
                                title: 'Status',
                                content: {
                                    template: `<br><input type="radio" name="mdr_status" value="1" id="mdr_status_1" class="form-check-input" checked/> Aktif <input type="radio" name="mdr_status" id="mdr_status_0" value="0" class="form-check-input"/> Tidak Aktif`,
                                },
                            },
                        },
                        formSubmitLabel: 'Edit Potongan',
                        callback: function() {
                            _discountReceiverNewStudentTable.reload()
                        },
                    },
                });
                _componentFormNewStudent.clearData()
                _componentFormNewStudent.setData(data)
                _discountReceiverNewStudentTable.selected = data
            },
            delete: function(e) {
                let data = _discountReceiverNewStudentTable.getRowData(e);
                Swal.fire({
                    title: 'Konfirmasi',
                    html: 'Apakah anda yakin ingin menghapus <br> <span class="fw-bolder">' + data.new_student.participant.par_fullname + '</span> sebagai penerima potongan?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ea5455',
                    cancelButtonColor: '#82868b',
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post(_baseURL + '/api/payment/discount-receiver-new/delete/' + data.mdr_id, {
                            _method: 'DELETE'
                        }, function(data) {
                            data = JSON.parse(data)
                            Swal.fire({
                                icon: 'success',
                                text: data.message,
                            }).then(() => {
                                _discountReceiverNewStudentTable.reload()
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
                    $('#form-copy-data-new-student select[name="mdr_period[]"]').each(function() {
                        $(this).val(selectedValue);
                    });
                });

                $('#modal-copy-data-new-student input#input-all-nominal').on('change', function() {
                    const currentValue = $(this).val();
                    $('#form-copy-data-new-student input[name="mdr_nominal[]"]').each(function() {
                        $(this).val(currentValue);
                    });
                });

                $('#modal-copy-data-new-student select#select-all-status').on('change', function() {
                    const selectedValue = $(this).val();
                    $('#form-copy-data-new-student select[name="mdr_status[]"]').each(function() {
                        $(this).val(selectedValue);
                    });
                });
            },
            openModalCopyData: async () => {
                const schoolYear = await $.get({
                    async: true,
                    url: `${_baseURL}/api/payment/resource/school-year`,
                });

                let htmlRows = '';

                $('#table-discount-receiver-new-student input.check-receiver:checked').each(function() {
                    const rowIdx = $(this).data('dt-row');
                    const row = _discountReceiverNewStudentTable.instance.row(parseInt(rowIdx)).data();

                    htmlRows += `
                        <tr>
                            <td>
                                <div>
                                    ${_datatableTemplates.titleWithSubtitleCell(row.new_student.participant.par_fullname, row.new_student.reg_number)}
                                    <input type="hidden" name="reg_id[]" value="${row.reg_id}" />
                                </div>
                            </td>
                            <td>
                                <div>
                                    ${_datatableTemplates.titleWithSubtitleCell(
                                        row.new_student.studyprogram.studyprogram_type.toUpperCase()+' '+row.new_student.studyprogram.studyprogram_name,
                                        row.new_student.studyprogram.faculty.faculty_name
                                    )}
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="fw-bold text-nowrap">
                                        ${row.discount.md_name}&nbsp;
                                        <a class="btn d-inline-block p-0" onclick="copyNewStudentReceiverActions.showDiscountDetailModal(${row.discount.md_id})"><i data-feather="info"></i></a>
                                    </span>
                                    ${
                                        row.discount.md_from
                                            ? `<br><small class="text-secondary  text-nowrap">${row.discount.md_from ?? '-'}</small>`
                                            : ''
                                    }
                                </div>
                                <input type="hidden" name="md_id[]" value="${row.md_id}" />
                            </td>
                            <td>
                                <select name="mdr_period[]" class="form-select w-200" value="${row.mdr_period}">
                                    ${
                                        schoolYear
                                            .filter(item => {
                                                return item.msy_code >= row.discount.period_start.msy_code && item.msy_code <= row.discount.period_end.msy_code;
                                            })
                                            .map(item => `
                                                <option value="${item.msy_id}" ${row.mdr_period == item.msy_id ? 'selected' : ''}>${item.msy_year} ${item.msy_semester == 1 ? 'Ganjil' : 'Genap'}</option>
                                            `)
                                            .join('')
                                    }
                                </select>
                            </td>
                            <td>
                                <input name="mdr_nominal[]" class="form-control w-200" type="number" value="${row.mdr_nominal}" />
                            </td>
                            <td>
                                <select name="mdr_status[]" class="form-select w-200" value="${row.mdr_status}">
                                    <option value="1" ${row.mdr_status == 1 ? 'selected' : ''}>Aktif</option>
                                    <option value="0" ${row.mdr_status == 0 ? 'selected' : ''}>Tidak Aktif</option>
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
                    `;
                });

                if (htmlRows == '') {
                    htmlRows = '<tr><td colspan="6" class="text-center">Tidak ada data yang dipilih</td></tr>';
                }

                $('#modal-copy-data-new-student #table-copied-data-new-student tbody').html(htmlRows);

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
                        url: _baseURL + '/api/payment/discount-receiver-new/validate-batch',
                        type: 'post',
                        data: data,
                    });

                    // console.log(res);

                    $(`#table-copied-data-new-student tbody tr td:nth-child(7)`).html(`
                        <div class="badge bg-success text-nowrap" style="font-size: inherit">
                            Data Valid
                        </div>
                        <input type="hidden" name="is_data_valid[]" value="1" />
                    `);

                    if (Object.keys(res).length > 0) {
                        for (const key in res) {
                            const rowIdx = key.split('_')[1];
                            $(`#table-copied-data-new-student tbody > tr:nth-child(${rowIdx}) td:nth-child(7)`).html(`
                                <div class="badge bg-danger text-nowrap" style="font-size: inherit">
                                    Data Tidak Valid
                                </div>
                                <input type="hidden" name="is_data_valid[]" value="0" />
                                <ul class="list-group mt-1">
                                    ${res[key].map(msg => `<li class="list-group-item text-nowrap text-danger fw-bold" style="font-size: .85rem;">${msg}</li>`).join('')}
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
                        url: _baseURL + '/api/payment/discount-receiver-new/store-batch',
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
                    _discountReceiverNewStudentTable.reload();
                }

            },
            showDiscountDetailModal: async (id) => {
                const discount = await $.ajax({
                    async: true,
                    url: _baseURL + '/api/payment/resource/discount/' + id,
                });

                let html = `
                    <tr>
                        <td class="align-top px-1" style="width: 200px">Nama Potongan</td>
                        <td class="align-top px-0" style="width: 5px">:</td>
                        <td class="align-top px-1" style="min-width: 400px; max-width: fit-content">
                            ${discount.md_name}
                        </td>
                    </tr>
                    <tr>
                        <td class="align-top px-1" style="width: 200px">Periode Awal</td>
                        <td class="align-top px-0" style="width: 5px">:</td>
                        <td class="align-top px-1" style="min-width: 400px; max-width: fit-content">
                            ${discount.period_start.msy_year}
                            ${
                                discount.period_start.msy_semester == 1 ? 'Ganjil'
                                    : discount.period_start.msy_semester == 2 ? 'Genap'
                                        : 'Antara'
                            }
                        </td>
                    </tr>
                    <tr>
                        <td class="align-top px-1" style="width: 200px">Periode Akhir</td>
                        <td class="align-top px-0" style="width: 5px">:</td>
                        <td class="align-top px-1" style="min-width: 400px; max-width: fit-content">
                            ${discount.period_end.msy_year}
                            ${
                                discount.period_end.msy_semester == 1 ? 'Ganjil'
                                    : discount.period_end.msy_semester == 2 ? 'Genap'
                                        : 'Antara'
                            }
                        </td>
                    </tr>
                    <tr>
                        <td class="align-top px-1" style="width: 200px">Anggaran Tersedia</td>
                        <td class="align-top px-0" style="width: 5px">:</td>
                        <td class="align-top px-1" style="min-width: 400px; max-width: fit-content">
                            ${Rupiah.format(discount.md_budget - discount.md_realization)}
                        </td>
                    </tr>
                `;

                html = $('<table class="table table-bordered dtr-details-custom mb-0" />').append(html);

                $('#modal-discount-detail .custom-body').html(html);

                $('#modal-discount-detail').modal('show');
            },
        };
    </script>
@endprepend
