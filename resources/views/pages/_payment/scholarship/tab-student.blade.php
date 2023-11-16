<table id="invoice-component-table" class="table table-striped">
    <thead>
        <tr>
            <th></th>
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

<div class="modal fade" id="copy-data-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Salin Data Penerima Beasiswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="section-border mb-1">
                    <h5 class="fw-bolder mb-1">Data Tersalin</h5>
                    <div class="eazy-table-wrapper">
                        <table id="table-copied-data" class="table table-sm table-striped" style="width: 100%; font-size: .9rem;">
                            <thead>
                                <tr>
                                    <th class="text-nowrap">Mahasiswa</th>
                                    <th class="text-nowrap">Fakultas - Prodi</th>
                                    <th class="text-nowrap">Beasiswa</th>
                                    <th class="text-nowrap">Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="odd">
                                    <td class="sorting_1">
                                        <div>
                                            <span class="text-nowrap">Bella Putri</span><br>
                                            <small class="text-nowrap text-secondary">469063</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="text-nowrap">S1 Farmasi</span><br>
                                            <small class="text-nowrap text-secondary">Fakultas Farmasi</small>
                                        </div>
                                    </td>
                                    <td><span class="">Beasiswa Yayasan</span> <br></td>
                                    <td>Rp&nbsp;2.400.000,00</td>
                                </tr>
                                <tr class="odd">
                                    <td class="sorting_1">
                                        <div>
                                            <span class="text-nowrap">Budi budidi</span><br>
                                            <small class="text-nowrap text-secondary">469063</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="text-nowrap">S1 Farmasi</span><br>
                                            <small class="text-nowrap text-secondary">Fakultas Farmasi</small>
                                        </div>
                                    </td>
                                    <td><span class="">Beasiswa Yayasan</span> <br></td>
                                    <td>Rp&nbsp;2.400.000,00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="section-border">
                    <h5 class="fw-bolder mb-1">Kustomisasi Data</h5>
                    <form>
                        <div class="form-group">
                            <label class="form-label">Periode</label>
                            <select class="form-select">
                                <option selected>Pilih Periode</option>
                                <option value="1">2022/2023 Ganjil</option>
                                <option value="2">2022/2023 Genap</option>
                                <option value="3">2023/2024 Ganjl</option>
                                <option value="3">2023/2024 Genap</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <div>
                                <span class="d-inline-block me-1">
                                    <input type="radio" name="msr_status" value="1" class="form-check-input" checked>&nbsp; Aktif
                                </span>
                                <span class="d-inline-block">
                                    <input type="radio" name="msr_status" value="0" class="form-check-input">&nbsp; Tidak Aktif
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-end">
                <button class="btn btn-secondary">
                    Batal
                </button>
                <button class="btn btn-success">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

@prepend('scripts')
<script>
    var dt = null;
    var dataDt = [];
    $(function() {
        _scholarshipReceiverTable.init();
        for (var i = 7; i <= 15; i++) {
            dt.column(i).visible(false)
        }
    })

    const _scholarshipReceiverTable = {
        ..._datatable,
        init: function(searchFilter = '#ALL') {
            dt = this.instance = $('#invoice-component-table').DataTable({
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
                        render: () => {
                            return '<input class="form-check-input" type="checkbox" />';
                        }
                    },
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
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"invoice-component-actions d-flex align-items-end">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" <"search-filter">lB> >' +
                    '>t' +
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
                                    columns: [7,8,9,10,11,12,13,14,15]
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
                                    columns: [7, 8, 9, 10, 11, 12, 13, 14, 15]
                                }
                            },
                            {
                                text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-copy font-small-4 me-50"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>Copy</span>',
                                className: 'dropdown-item',
                                extend: 'copy',
                                exportOptions: {
                                    columns: [7, 8, 9, 10, 11, 12, 13, 14, 15]
                                }
                            }
                        ]
                    },
                ],
                initComplete: function() {
                    $('.invoice-component-actions').html(`
                        <div style="margin-bottom: 7px">
                            <button onclick="_scholarshipReceiverTableActions.add()" class="btn btn-info">
                                <span style="vertical-align: middle">
                                    <i data-feather="plus"></i>&nbsp;&nbsp;
                                    Tambah Penerima
                                </span>
                            </button>
                            <button class="btn btn-outline-primary ms-1" onclick="$('#copy-data-modal').modal('show')">
                                <i data-feather="copy"></i>&nbsp;&nbsp;Salin Data
                            </button>
                        </div>
                    `)
                    $('.search-filter').html(`
                        <div id="invoice-component-table_filter" class="dataTables_filter">
                            <label>
                                <input type="search" class="form-control" placeholder="Cari Data" aria-controls="invoice-component-table" onkeyup="searchFilter(event, this)">
                            </label>
                        </div>
                    `);
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
                if(row.msr_status_generate === 0){
                    action = `<a onclick="_scholarshipReceiverTableActions.edit(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                    <a onclick="_scholarshipReceiverTableActions.delete(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>`;
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
                    console.log(data);
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

    const _scholarshipReceiverTableActions = {
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
                        _scholarshipReceiverTable.reload()
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
            let data = _scholarshipReceiverTable.getRowData(e);
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
                        _scholarshipReceiverTable.reload()
                    },
                },
            });
            _componentForm.clearData()
            _componentForm.setData(data)
            _scholarshipReceiverTable.selected = data
        },
        delete: function(e) {
            let data = _scholarshipReceiverTable.getRowData(e);
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
                            _scholarshipReceiverTable.reload()
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
        console.log(id);
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
                _scholarshipReceiverTable.init();
            } else {
                dt.clear().destroy();
                _scholarshipReceiverTable.init(text);
            }
            console.log(text)
        }
    }
</script>
@endprepend
