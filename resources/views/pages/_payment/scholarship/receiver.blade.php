@extends('layouts.static_master')


@section('page_title', 'Mahasiswa Penerima Beasiswa')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
<style>
    .eazy-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }
</style>
@endsection

@section('content')

@include('pages._payment.scholarship._shortcuts', ['active' => 'receiver'])

<div class="card">
    <div class="card-body">
        <div class="datatable-filter one-row">
            <div>
                <label class="form-label">Periode Awal</label>
                <select name="md_period_start_filter" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Periode</option>
                    @foreach ($period as $item)
                    <option value="{{$item->msy_id}}">{{$item->msy_year}} {{ ($item->msy_semester == 1)? 'Ganjil' : 'Genap' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Periode Akhir</label>
                <select name="md_period_end_filter" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Periode</option>
                    @foreach ($period as $item)
                    <option value="{{$item->msy_id}}">{{$item->msy_year}} {{ ($item->msy_semester == 1)? 'Ganjil' : 'Genap' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Nama Beasiswa</label>
                <select name="schoolarship_filter" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Beasiswa</option>
                    @foreach ($schoolarship as $item)
                    <option value="{{$item->ms_id}}">{{$item->ms_name}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Fakultas</label>
                <select name="faculty_filter" class="form-select" eazy-select2-active onchange="getStudyProgram(this)">
                    <option value="#ALL" selected>Semua Fakultas</option>
                    @foreach ($faculty as $item)
                    <option value="{{$item->faculty_id}}">{{$item->faculty_name}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Studi Program</label>
                <select name="program_study_filter" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Program Studi</option>
                </select>
            </div>
            <div class="d-flex align-items-end">
                <button onclick="_scholarshipReceiverTable.reload()" class="btn btn-primary text-nowrap">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="invoice-component-table" class="table table-striped">
        <thead>
            <tr>
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
                <th>Status</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

@endsection


@section('js_section')
<script>
    var dt = null;
    var dataDt = [];
    $(function() {
        _scholarshipReceiverTable.init();
        for (var i = 7; i <= 14; i++) {
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
                columns: [{
                        name: 'action',
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: (data, _, row) => {
                            console.log(row);
                            return this.template.rowAction(data)
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
                                    columns: [7,8,9,10,11,12,13,14]
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
                                    columns: [7, 8, 9, 10, 11, 12, 13, 14]
                                }
                            }
                        ]
                    },
                ],
                initComplete: function() {
                    $('.invoice-component-actions').html(`
                        <div style="margin-bottom: 7px">
                            <button onclick="_scholarshipReceiverTableActions.add()" class="btn btn-primary">
                                <span style="vertical-align: middle">
                                    <i data-feather="plus" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Tambah Penerima
                                </span>
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
            rowAction: function(id) {
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a onclick="_scholarshipReceiverTableActions.edit(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="_scholarshipReceiverTableActions.delete(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
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
@endsection