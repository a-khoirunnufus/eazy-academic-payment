<table id="table-scholarship-receiver-student" class="table table-striped">
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
            <th>Nim</th>
            <th>Nama</th>
            <th>Fakultas</th>
            <th>Prodi</th>
            <th>Beasiswa</th>
            <th>Perusahaan</th>
            <th>PIC</th>
            <th>Nominal</th>
            <th>Status</th>
            <th>Generate</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<div class="modal fade" id="modal-copy-data-student" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Salin Data Penerima Beasiswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-row justify-content-end mb-1 w-100">
                    <button class="btn btn-primary" onclick="copyStudentReceiverActions.validateData()">Validasi Data</button>
                </div>
                <div class="eazy-table-wrapper">
                    <form id="form-copy-data-student">
                        <table id="table-copied-data-student" class="table table-striped" style="width: 100%; font-size: .9rem;">
                            <thead>
                                <tr>
                                    <th class="text-nowrap">Mahasiswa</th>
                                    <th class="text-nowrap">Fakultas - Prodi</th>
                                    <th class="text-nowrap">Beasiswa</th>
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
                <button class="btn btn-success" onclick="copyStudentReceiverActions.storeBatch()">Simpan</button>
            </div>
        </div>
    </div>
</div>

@prepend('scripts')
<script>
    var dt = null;
    var dataDt = [];

    $(function() {
        _scholarshipReceiverStudentTable.init();
        for (var i = 8; i <= 16; i++) {
            dt.column(i).visible(false)
        }

        $('#table-scholarship-receiver-student #check-all-receiver').on('change', function() {
            if (this.checked) {
                $('#table-scholarship-receiver-student input.check-receiver').each(function() {
                    $(this).prop('checked', true);
                });
            } else {
                $('#table-scholarship-receiver-student input.check-receiver').each(function() {
                    $(this).prop('checked', false);
                });
            }
        });

        copyStudentReceiverActions.setupElements();
    })

    const _scholarshipReceiverStudentTable = {
        ..._datatable,
        init: function(searchFilter = '#ALL') {
            this.instance = $('#table-scholarship-receiver-student').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL + '/api/payment/scholarship-receiver/index',
                    data: function(d) {
                        d.custom_filters = {
                            'md_period_start_filter': $('select[name="md_period_start_filter"]').val(),
                            'md_period_end_filter': $('select[name="md_period_end_filter"]').val(),
                            'schoolarship_filter': $('select[name="schoolarship_filter"]').val(),
                            'faculty_filter': $('select[name="faculty_filter"]').val(),
                            'program_study_filter': $('select[name="program_study_filter"]').val(),
                            'search_filter': searchFilter,
                        };
                    },
                    dataSrc: function(json) {
                        dataDt = json.data;
                        return json.data;
                    }
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
                                    <span class="text-nowrap fw-bold">${row.student.fullname}</span><br>
                                    <small class="text-nowrap text-secondary">${row.student.student_id}</small>
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
                                    <span class="text-nowrap fw-bold">${row.student.study_program.studyprogram_type} ${row.student.study_program.studyprogram_name}</span><br>
                                    <small class="text-nowrap text-secondary">${row.student.study_program.faculty.faculty_name}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'ms_id',
                        data: 'ms_id',
                        searchable: false,
                        render: (data, _, row) => {
                            let company = (row.scholarship.ms_from) ? row.scholarship.ms_from : "";
                            return "<span class='fw-bolder'>" + row.scholarship.ms_name + "</span> <br>" + company;
                        }
                    },
                    {
                        name: 'msr_period',
                        data: 'msr_period',
                        searchable: false,
                        render: (data, _, row) => {
                            return row.period.msy_year + _helper.semester(row.period.msy_semester)
                        }
                    },
                    {
                        name: 'msr_nominal',
                        data: 'msr_nominal',
                        render: (data, _, row) => {
                            return Rupiah.format(data)
                        }
                    },
                    {
                        name: 'msr_status',
                        data: 'msr_status',
                        searchable: false,
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
                    {
                        name: 'student_number',
                        data: 'student_number',
                        searchable: false,
                        render: (data, _, row) => {
                            return row.student.student_id;
                        }
                    },
                    {
                        name: 'student_number',
                        data: 'student_number',
                        searchable: false,
                        render: (data, _, row) => {
                            return row.student.fullname;
                        }
                    },
                    {
                        name: 'student_number',
                        data: 'student_number',
                        searchable: false,
                        render: (data, _, row) => {
                            return row.student.study_program.faculty.faculty_name;
                        }
                    },
                    {
                        name: 'student_number',
                        data: 'student_number',
                        searchable: false,
                        render: (data, _, row) => {
                            return `${row.student.study_program.studyprogram_type} ${row.student.study_program.studyprogram_name}`;
                        }
                    },
                    {
                        name: 'ms_id',
                        data: 'ms_id',
                        searchable: false,
                        render: (data, _, row) => {
                            return row.scholarship.ms_name;
                        }
                    },
                    {
                        name: 'ms_id',
                        data: 'ms_id',
                        searchable: false,
                        render: (data, _, row) => {
                            return row.scholarship.ms_from;
                        }
                    },
                    {
                        name: 'ms_id',
                        data: 'ms_id',
                        searchable: false,
                        render: (data, _, row) => {
                            return row.scholarship.ms_from_name;
                        }
                    },
                    {
                        name: 'msr_nominal',
                        data: 'msr_nominal',
                        render: (data, _, row) => {
                            return data
                        }
                    },
                    {
                        name: 'msr_status',
                        data: 'msr_status',
                        searchable: false,
                        render: (data, _, row) => {
                            let status = "Tidak Aktif";
                            if (row.msr_status === 1) {
                                status = "Aktif";
                            }
                            return status;
                        }
                    },
                    {
                        name: 'msr_status_generate',
                        data: 'msr_status_generate',
                        searchable: false,
                        render: (data, _, row) => {
                            let status = "Belum Digenerate";
                            let bg = "bg-danger";
                            if (row.msr_status_generate === 1) {
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
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"custom-actions d-flex align-items-end">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" <"search-filter">lB> >' +
                    '><"eazy-table-wrapper"t>' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                buttons: [{
                        extend: 'collection',
                        text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-external-link font-small-4 me-50"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>Export</span>',
                        className: 'btn btn-outline-secondary dropdown-toggle',
                        buttons: [{
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
                    $('.custom-actions').html(`
                        <div style="margin-bottom: 7px">
                            <button onclick="_scholarshipReceiverStudentTableActions.add()" class="btn btn-info">
                                <span style="vertical-align: middle">
                                    <i data-feather="plus"></i>&nbsp;&nbsp;
                                    Tambah Penerima
                                </span>
                            </button>
                            <button class="btn btn-outline-primary ms-1" onclick="copyStudentReceiverActions.openModalCopyData()">
                                <i data-feather="copy"></i>&nbsp;&nbsp;Salin Data
                            </button>
                        </div>
                    `)
                    $('.search-filter').html(`
                        <div id="table-scholarship-receiver-student_filter" class="dataTables_filter">
                            <label>
                                <input type="search" class="form-control" placeholder="Cari Data" aria-controls="table-scholarship-receiver-student" onkeyup="searchFilter(event, this)">
                            </label>
                        </div>
                    `);
                    feather.replace()
                }
            });
            dt = this.instance;
            this.implementSearchDelay();
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
                    action = `<a onclick="_scholarshipReceiverStudentTableActions.edit(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                    <a onclick="_scholarshipReceiverStudentTableActions.delete(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>`;
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

    const _componentForm = {
        clearData: function() {
            FormDataJson.clear('#form-edit-scholarship')
            $("#form-edit-scholarship .select2").trigger('change')
            $(".form-alert").remove()
        },
        setData: function(d) {
            $.get(_baseURL + '/api/payment/scholarship-receiver/scholarship', (data) => {
                if (Object.keys(data).length > 0) {
                    data.map(item => {
                        $('#ms_id').append(`
                            <option value="` + item.ms_id + `" data-nominal="` + item.ms_nominal + `">` + item.ms_name + `(sisa anggaran: ` + Rupiah.format(item.ms_budget - item.ms_realization) + `)</option>
                        `);
                    });
                    $('#ms_id').val(d.ms_id);
                    $('#ms_id').trigger('change');
                    selectRefresh();
                }
            });
            $.get(_baseURL + '/api/payment/scholarship-receiver/student', (data) => {
                if (Object.keys(data).length > 0) {
                    data.map(item => {
                        $('#student_number').append(`
                            <option value="` + item.student_number + `">` + item.fullname + ` - ` + item.student_id + `</option>
                        `);
                    });
                    $('#student_number').val(d.student_number);
                    $('#student_number').trigger('change');
                    selectRefresh();
                }
            });
            $("#ms_id").change(function() {
                ms_id = $(this).val();
                $.get(_baseURL + '/api/payment/scholarship-receiver/period/' + ms_id, (data) => {
                    // console.log(data);
                    if (Object.keys(data).length > 0) {
                        $("#msr_period").empty();
                        data.map(item => {
                            $('#msr_period').append(`
                                <option value="` + item.msy_id + `">` + item.msy_year + ` ` + _helper.semester(item.msy_semester) + `</option>
                            `);
                        });
                        $('#msr_period').val(d.msr_period);
                        $('#msr_period').trigger('change');
                        selectRefresh();
                    }
                });
            });
            $("[name=msr_nominal]").val(d.msr_nominal);
            d.msr_status == 1 ? $('#msr_status_1').prop('checked', true) : $('#msr_status_0').prop('checked', true);
        }
    }

    const _helper = {
        semester: function(msy_semester) {
            var semester = ' Genap';
            if (msy_semester == 1) {
                semester = ' Ganjil';
            }
            return semester;
        }
    }

    const _scholarshipReceiverStudentTableActions = {
        add: function() {
            Modal.show({
                type: 'form',
                modalTitle: 'Tambah Penerima',
                modalSize: 'md',
                config: {
                    formId: 'form-add-discount-receiver',
                    formActionUrl: _baseURL + '/api/payment/scholarship-receiver/store',
                    formType: 'add',
                    fields: {
                        ms_id: {
                            title: 'Beasiswa',
                            content: {
                                template: `<select name="ms_id" id="ms_id" class="form-control select2">
                                        <option value="">Pilih Beasiswa</option>
                                    </select>`,
                            },
                        },
                        student_number: {
                            title: 'Mahasiswa',
                            content: {
                                template: `<select name="student_number" id="student_number" class="form-control select2">
                                        <option value="">Pilih Mahasiswa</option>
                                    </select>`,
                            },
                        },
                        msr_period: {
                            title: 'Periode',
                            content: {
                                template: `<select name="msr_period" id="msr_period" class="form-control select2">
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
                        _scholarshipReceiverStudentTable.reload()
                    },
                },
            });
            $.get(_baseURL + '/api/payment/scholarship-receiver/scholarship', (data) => {
                if (Object.keys(data).length > 0) {
                    data.map(item => {
                        $('#ms_id').append(`
                            <option value="` + item.ms_id + `" data-nominal="` + item.ms_nominal + `">` + item.ms_name + `(sisa anggaran: ` + Rupiah.format(item.ms_budget - item.ms_realization) + `)</option>
                        `);
                    });
                    selectRefresh();
                }
            });
            $.get(_baseURL + '/api/payment/scholarship-receiver/student', (data) => {
                if (Object.keys(data).length > 0) {
                    data.map(item => {
                        $('#student_number').append(`
                            <option value="` + item.student_number + `">` + item.fullname + ` - ` + item.student_id + `</option>
                        `);
                    });
                    selectRefresh();
                }
            });
            $("#ms_id").change(function() {
                nominal = $(this).find(":selected").data("nominal");
                ms_id = $(this).val();
                $('[name="msr_nominal"]').val(nominal);
                $.get(_baseURL + '/api/payment/scholarship-receiver/period/' + ms_id, (data) => {
                    if (Object.keys(data).length > 0) {
                        $("#msr_period").empty();
                        data.map(item => {
                            $('#msr_period').append(`
                            <option value="` + item.msy_id + `">` + item.msy_year + ` ` + _helper.semester(item.msy_semester) + `</option>
                        `);
                        });
                        selectRefresh();
                    }
                });
            })
        },
        edit: function(e) {
            let data = _scholarshipReceiverStudentTable.getRowData(e);
            Modal.show({
                type: 'form',
                modalTitle: 'Edit Penerima Beasiswa',
                modalSize: 'md',
                config: {
                    formId: 'form-edit-scholarship-receiver',
                    formActionUrl: _baseURL + '/api/payment/scholarship-receiver/store',
                    formType: 'edit',
                    rowId: data.msr_id,
                    fields: {
                        ms_id: {
                            title: 'Beasiswa',
                            content: {
                                template: `<select name="ms_id" id="ms_id" class="form-control select2">
                                        <option value="">Pilih Beasiswa</option>
                                    </select>`,
                            },
                        },
                        student_number: {
                            title: 'Mahasiswa',
                            content: {
                                template: `<select name="student_number" id="student_number" class="form-control select2">
                                        <option value="">Pilih Mahasiswa</option>
                                    </select>`,
                            },
                        },
                        msr_period: {
                            title: 'Periode',
                            content: {
                                template: `<select name="msr_period" id="msr_period" class="form-control select2">
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
                        _scholarshipReceiverStudentTable.reload()
                    },
                },
            });
            _componentForm.clearData()
            _componentForm.setData(data)
            _scholarshipReceiverStudentTable.selected = data
        },
        delete: function(e) {
            let data = _scholarshipReceiverStudentTable.getRowData(e);
            Swal.fire({
                title: 'Konfirmasi',
                html: 'Apakah anda yakin ingin menghapus <br> <span class="fw-bolder">' + data.student.fullname + '</span> sebagai penerima beasiswa?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(_baseURL + '/api/payment/scholarship-receiver/delete/' + data.msr_id, {
                        _method: 'DELETE'
                    }, function(data) {
                        data = JSON.parse(data)
                        Swal.fire({
                            icon: 'success',
                            text: data.message,
                        }).then(() => {
                            _scholarshipReceiverStudentTable.reload()
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

    function getStudyProgram(elm) {
        $('select[name="program_study_filter"]').html(`
        <option value="#ALL" selected>Semua Program Studi</option>
        `)

        var id = $(elm).val();
        // console.log(id);
        if (id != '#ALL') {
            var xhr = new XMLHttpRequest();
            xhr.onload = function() {
                var data = JSON.parse(this.responseText);
                for (var i = 0; i < data.length; i++) {
                    $('select[name="program_study_filter"]').append(`
                    <option value="${data[i].studyprogram_id}" selected>${data[i].studyprogram_type + " " + data[i].studyprogram_name}</option>
                    `);
                }
            }
            xhr.open('GET', _baseURL + '/api/payment/scholarship-receiver/study-program?id=' + id);
            xhr.send();
        }
    }

    function searchFilter(event, elm) {
        var key = event.key;
        var text = elm.value;
        if (key == 'Enter') {
            elm.value = "";
            if (text == '') {
                dt.clear().destroy();
                _scholarshipReceiverStudentTable.init();
            } else {
                dt.clear().destroy();
                _scholarshipReceiverStudentTable.init(text);
            }
            // console.log(text)
        }
    }

    const copyStudentReceiverActions = {
        setupElements: () => {
            $.get({url: `${_baseURL}/api/payment/resource/school-year`})
                .then(schoolYears => {
                    $('#modal-copy-data-student #select-all-period').append(
                        schoolYears.map(item => `
                            <option value="${item.msy_id}">${item.msy_year} ${item.msy_semester == 1 ? 'Ganjil' : 'Genap'}</option>
                        `).join('')
                    );
                });

            $('#modal-copy-data-student select#select-all-period').on('change', function() {
                const selectedValue = $(this).val();
                $('#form-copy-data-student select[name="msr_period[]"]').each(function() {
                    $(this).val(selectedValue);
                });
            });

            $('#modal-copy-data-student input#input-all-nominal').on('change', function() {
                const currentValue = $(this).val();
                $('#form-copy-data-student input[name="msr_nominal[]"]').each(function() {
                    $(this).val(currentValue);
                });
            });

            $('#modal-copy-data-student select#select-all-status').on('change', function() {
                const selectedValue = $(this).val();
                $('#form-copy-data-student select[name="msr_status[]"]').each(function() {
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

            $('#table-scholarship-receiver-student input.check-receiver:checked').each(function() {
                const rowIdx = $(this).data('dt-row');
                const row = _scholarshipReceiverStudentTable.instance.row(parseInt(rowIdx)).data();

                htmlRows += `
                    <tr>
                        <td>
                            <div>
                                ${_datatableTemplates.titleWithSubtitleCell(row.student.fullname, row.student.student_id)}
                                <input type="hidden" name="student_number[]" value="${row.student_number}" />
                            </div>
                        </td>
                        <td>
                            <div>
                                ${_datatableTemplates.titleWithSubtitleCell(
                                    row.student.study_program.studyprogram_type.toUpperCase()+' '+row.student.study_program.studyprogram_name,
                                    row.student.study_program.faculty.faculty_name
                                )}
                            </div>
                        </td>
                        <td>
                            <div>
                                <span class="fw-bold text-nowrap">
                                    ${row.scholarship.ms_name}&nbsp;
                                    <a class="btn d-inline-block p-0" onclick="copyStudentReceiverActions.showScholarshipDetailModal(${row.scholarship.ms_id})"><i data-feather="info"></i></a>
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
                            <select name="msr_period[]" class="form-select w-200" value="${row.msr_period}">
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
                            <input name="msr_nominal[]" class="form-control w-200" type="number" value="${row.msr_nominal}" />
                        </td>
                        <td>
                            <select name="msr_status[]" class="form-select w-200" value="${row.msr_status}">
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
                            <a class="btn btn-icon btn-sm btn-danger" onclick="copyStudentReceiverActions.deleteCopyRow(this)">
                                <i data-feather="trash"></i>
                            </a>
                        </td>
                    </tr>
                `;
            });

            if (htmlRows == '') {
                htmlRows = '<tr><td colspan="6" class="text-center">Tidak ada data yang dipilih</td></tr>';
            }

            $('#modal-copy-data-student #table-copied-data-student tbody').html(htmlRows);

            $('#modal-copy-data-student').modal('show');

            feather.replace();

            copyStudentReceiverActions.validateData();
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
                const data = FormDataJson.toJson("#form-copy-data-student");

                const res = await $.ajax({
                    async: true,
                    url: _baseURL + '/api/payment/scholarship-receiver/validate-batch',
                    type: 'post',
                    data: data,
                });

                // console.log(res);

                $(`#table-copied-data-student tbody tr td:nth-child(7)`).html(`
                    <div class="badge bg-success text-nowrap" style="font-size: inherit">
                        Data Valid
                    </div>
                    <input type="hidden" name="is_data_valid[]" value="1" />
                `);

                if (Object.keys(res).length > 0) {
                    for (const key in res) {
                        const rowIdx = key.split('_')[1];
                        $(`#table-copied-data-student tbody > tr:nth-child(${rowIdx}) td:nth-child(7)`).html(`
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

            const isDataValid = await copyStudentReceiverActions.validateData();
            if (!isDataValid) {
                _toastr.error('Masih terdapat data yang belum valid, silahkan sesuaikan data.', 'Gagal');
                return;
            }

            try {
                const formData = new FormData($('#form-copy-data-student')[0]);

                const res = await $.ajax({
                    async: true,
                    url: _baseURL + '/api/payment/scholarship-receiver/store-batch',
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
                $('#modal-copy-data-student').modal('hide');
                _scholarshipReceiverStudentTable.reload();
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
