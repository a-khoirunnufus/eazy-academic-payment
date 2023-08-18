@extends('layouts.static_master')


@section('page_title', 'Data Beasiswa')
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

@include('pages._payment.scholarship._shortcuts', ['active' => 'index'])

<div class="card">
    <div class="card-body">
        <div class="datatable-filter one-row">
            <div>
                <label class="form-label">Periode Awal</label>
                <select name="ms_period_start_filter" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Periode</option>
                    @foreach ($period as $item)
                    <option value="{{$item->msy_id}}">{{$item->msy_year}} {{ ($item->msy_semester == 1)? 'Ganjil' : 'Genap' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Periode Akhir</label>
                <select name="ms_period_end_filter" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Periode</option>
                    @foreach ($period as $item)
                    <option value="{{$item->msy_id}}">{{$item->msy_year}} {{ ($item->msy_semester == 1)? 'Ganjil' : 'Genap' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Jenis Beasiswa</label>
                <select name="type_filter" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Jenis Beasiswa</option>
                    <option value="1">Internal</option>
                    <option value="2">External</option>
                </select>
            </div>
            <div>
                <label class="form-label">Status Beasiswa</label>
                <select name="status_filter" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Status Beasiswa</option>
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                </select>
            </div>
            <div class="d-flex align-items-end">
                <button onclick="_scholarshipTable.reload()" class="btn btn-primary text-nowrap">
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
                <th>Nama Beasiswa</th>
                <th>Jenis</th>
                <th>Rekanan</th>
                <th>Periode Awal</th>
                <th>Periode Akhir</th>
                <th>Nominal</th>
                <th>Anggaran</th>
                <th>Realisasi</th>
                <th>Status</th>
                <th>Instansi/Perusahaan</th>
                <th>PIC</th>
                <th>Kontak</th>
                <th>Status</th>
                <th>Nominal</th>
                <th>Anggaran</th>
                <th>Realisasi</th>
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
        _scholarshipTable.init();
        for(var i = 10; i <= 16; i++){
            dt.column(i).visible(false)
        }
    })

    const _scholarshipTable = {
        ..._datatable,
        init: function(searchFilter = '#ALL') {
            dt = this.instance = $('#invoice-component-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL + '/api/payment/scholarship/index',
                    data: function(d) {
                        d.custom_filters = {
                            'ms_period_start_filter': $('select[name="ms_period_start_filter"]').val(),
                            'ms_period_end_filter': $('select[name="ms_period_end_filter"]').val(),
                            'type_filter': $('select[name="type_filter"]').val(),
                            'status_filter': $('select[name="status_filter"]').val(),
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
                        name: 'ms_name',
                        data: 'ms_name'
                    },
                    {
                        name: 'ms_type',
                        data: 'ms_type',
                        searchable: false,
                        render: (data, _, row) => {
                            let type = "";
                            if (row.ms_type === 1) {
                                type = "Internal";
                            } else if (row.ms_type === 2) {
                                type = "External";
                            }
                            return type;
                        }
                    },
                    {
                        name: 'ms_from',
                        data: 'ms_from',
                        searchable: false,
                        render: (data, _, row) => {
                            let name = (row.ms_from_name) ? row.ms_from_name : "";
                            return "<span class='fw-bolder'>" + row.ms_from + "</span> <br>" + name;
                        }
                    },
                    {
                        name: 'ms_period_start',
                        data: 'ms_period_start',
                        searchable: false,
                        render: (data, _, row) => {
                            return row.period_start.msy_year + _helper.semester(row.period_start.msy_semester)
                        }
                    },
                    {
                        name: 'ms_period_end',
                        data: 'ms_period_end',
                        searchable: false,
                        render: (data, _, row) => {
                            return row.period_end.msy_year + _helper.semester(row.period_end.msy_semester)
                        }
                    },
                    {
                        name: 'ms_nominal',
                        data: 'ms_nominal',
                        render: (data, _, row) => {
                            return Rupiah.format(data)
                        }
                    },
                    {
                        name: 'ms_budget',
                        data: 'ms_budget',
                        render: (data, _, row) => {
                            return Rupiah.format(data)
                        }
                    },
                    {
                        name: 'ms_realization',
                        data: 'ms_realization',
                        render: (data, _, row) => {
                            return Rupiah.format(data)
                        }
                    },
                    {
                        name: 'ms_status',
                        data: 'ms_status',
                        searchable: false,
                        render: (data, _, row) => {
                            let status = "Tidak Aktif";
                            let bg = "bg-danger";
                            if (row.ms_status === 1) {
                                status = "Aktif";
                                bg = "bg-success";
                            }
                            return '<div class="badge ' + bg + '">' + status + '</div>'
                        }
                    },
                    {
                        // name: 'ms_from',
                        data: 'ms_from',
                        render: (data, _, row) => {
                            return row.ms_from
                        }
                    },
                    {
                        name: 'ms_from',
                        data: 'ms_from',
                        render: (data, _, row) => {
                            let name = (row.ms_from_name) ? row.ms_from_name : "";
                            return name;
                        }
                    },
                    {
                        name: 'ms_from',
                        data: 'ms_from_phone',
                    },
                    {
                        name: 'ms_status',
                        data: 'ms_status',
                        render: (data, _, row) => {
                            let status = "Tidak Aktif";
                            if (row.ms_status === 1) {
                                status = "Aktif";
                            }
                            return status;
                        }
                    },
                    {
                        name: 'ms_nominal',
                        data: 'ms_nominal',
                    },
                    {
                        name: 'ms_budget',
                        data: 'ms_budget',
                    },
                    {
                        name: 'ms_realization',
                        data: 'ms_realization',
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
                                    columns: [1,2,10,11,12,4,5,6,7,8,13]
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
                                        a.download = "Laporan Program Beasiswa";
                                        a.click();
                                    }
                                    xhr.open("POST", _baseURL + "/api/payment/scholarship/exportData");
                                    xhr.responseType = 'blob';
                                    xhr.send(formData);
                                }
                            },
                            {
                                text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text font-small-4 me-50"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>Csv</span>',
                                className: 'dropdown-item',
                                extend: 'csv',
                                exportOptions: {
                                    columns: [1,2,10,11,12,4,5,14,15,16,13]
                                }
                            },
                            {
                                text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-copy font-small-4 me-50"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>Copy</span>',
                                className: 'dropdown-item',
                                extend: 'copy',
                                exportOptions: {
                                    columns: [1,2,10,11,12,4,5,14,15,16,13]
                                }
                            }
                        ]
                    },
                ],
                initComplete: function() {
                    $('.invoice-component-actions').html(`
                        <div style="margin-bottom: 7px">
                            <button onclick="_scholarshipTableActions.add()" class="btn btn-primary">
                                <span style="vertical-align: middle">
                                    <i data-feather="plus" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Tambah Beasiswa
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
                            <a onclick="_scholarshipTableActions.edit(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="_scholarshipTableActions.delete(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
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
        setData: function(data) {
            $("[name=ms_name]").val(data.ms_name);
            $('#ms_type').val(data.ms_type);
            $('#ms_type').trigger('change');
            $("[name=ms_from]").val(data.ms_from);
            $("[name=ms_from_name]").val(data.ms_from_name);
            $("[name=ms_from_phone]").val(data.ms_from_phone);
            $("[name=ms_from_email]").val(data.ms_from_email);
            $.get(_baseURL + '/api/payment/scholarship/period', (d) => {
                if (Object.keys(d).length > 0) {
                    d.map(item => {
                        $('#ms_period_start').append(`
                            <option value="` + item.msy_id + `">` + item.msy_year + ` ` + _helper.semester(item.msy_semester) + `</option>
                        `);
                        $('#ms_period_end').append(`
                            <option value="` + item.msy_id + `">` + item.msy_year + ` ` + _helper.semester(item.msy_semester) + `</option>
                        `);
                    });
                    $('#ms_period_start').val(data.ms_period_start);
                    $('#ms_period_start').trigger('change');
                    $('#ms_period_end').val(data.ms_period_end);
                    $('#ms_period_end').trigger('change');
                    selectRefresh();
                }
            });
            $("[name=ms_nominal]").val(data.ms_nominal);
            $("[name=ms_budget]").val(data.ms_budget);
            data.ms_status == 1 ? $('#ms_status_1').prop('checked', true) : $('#ms_status_0').prop('checked', true);
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

    const _scholarshipTableActions = {
        add: function() {
            Modal.show({
                type: 'form',
                modalTitle: 'Tambah Beasiswa',
                modalSize: 'lg',
                config: {
                    formId: 'form-add-scholarship',
                    formActionUrl: _baseURL + '/api/payment/scholarship/store',
                    formType: 'add',
                    isTwoColumn: true,
                    fields: {
                        ms_name: {
                            title: 'Nama Beasiswa <span class="text-danger">*</span>',
                            content: {
                                template: `<input
                                        type="text"
                                        name="ms_name"
                                        class="form-control"
                                    >`,
                            },
                        },
                        ms_type: {
                            title: 'Tipe Beasiswa <span class="text-danger">*</span>',
                            content: {
                                template: `<select name="ms_type" id="ms_type" class="form-control select2">
                                        <option value="1">Internal</option>
                                        <option value="2">External</option>
                                    </select>`,
                            },
                        },
                        ms_from: {
                            title: 'Rekanan',
                            content: {
                                template: `<input
                                        type="text"
                                        name="ms_from"
                                        class="form-control"
                                    >`,
                            },
                        },
                        ms_from_name: {
                            title: 'Pemilik Rekanan',
                            content: {
                                template: `<input
                                        type="text"
                                        name="ms_from_name"
                                        class="form-control"
                                    >`,
                            },
                        },
                        ms_from_email: {
                            title: 'Email Rekanan',
                            content: {
                                template: `<input
                                        type="text"
                                        name="ms_from_email"
                                        class="form-control"
                                    >`,
                            },
                        },
                        ms_from_phone: {
                            title: 'No Telp. Rekanan',
                            content: {
                                template: `<input
                                        type="text"
                                        name="ms_from_phone"
                                        class="form-control"
                                    >`,
                            },
                        },
                        md_period_start: {
                            title: 'Periode Awal <span class="text-danger">*</span>',
                            content: {
                                template: `<select name="ms_period_start" id="ms_period_start" class="form-control select2">
                                        <option value="">Pilih Periode</option>
                                    </select>`,
                            },
                        },
                        md_period_end: {
                            title: 'Periode Akhir <span class="text-danger">*</span>',
                            content: {
                                template: `<select name="ms_period_end" id="ms_period_end" class="form-control select2">
                                        <option value="">Pilih Periode</option>
                                    </select>`,
                            },
                        },
                        md_nominal: {
                            title: 'Nominal <span class="text-danger">*</span>',
                            content: {
                                template: `<input type="number" name="ms_nominal" class="form-control">`,
                            },
                        },
                        md_budget: {
                            title: 'Anggaran <span class="text-danger">*</span>',
                            content: {
                                template: `<input type="number" name="ms_budget" class="form-control">`,
                            },
                        },
                        md_status: {
                            title: 'Status <span class="text-danger">*</span>',
                            content: {
                                template: `<br><input type="radio" name="ms_status" value="1" class="form-check-input" checked/> Aktif <input type="radio" name="ms_status" value="0" class="form-check-input"/> Tidak Aktif`,
                            },
                        },
                    },
                    formSubmitLabel: 'Tambah Beasiswa',
                    formSubmitNote: `
                    <small class="text-danger">
                        *Kolom <strong>wajib</strong> diisi
                    </small>`,
                    callback: function(e) {
                        _scholarshipTable.reload()
                    },
                },
            });
            $.get(_baseURL + '/api/payment/scholarship/period', (data) => {
                if (Object.keys(data).length > 0) {
                    data.map(item => {
                        $('#ms_period_start').append(`
                            <option value="` + item.msy_id + `">` + item.msy_year + ` ` + _helper.semester(item.msy_semester) + `</option>
                        `);
                        $('#ms_period_end').append(`
                            <option value="` + item.msy_id + `">` + item.msy_year + ` ` + _helper.semester(item.msy_semester) + `</option>
                        `);
                    });
                    selectRefresh();
                }
            });
        },
        edit: function(e) {
            let data = _scholarshipTable.getRowData(e);
            Modal.show({
                type: 'form',
                modalTitle: 'Edit Beasiswa',
                modalSize: 'lg',
                config: {
                    formId: 'form-edit-scholarship',
                    formActionUrl: _baseURL + '/api/payment/scholarship/store',
                    formType: 'edit',
                    isTwoColumn: true,
                    rowId: data.ms_id,
                    fields: {
                        ms_name: {
                            title: 'Nama Beasiswa <span class="text-danger">*</span>',
                            content: {
                                template: `<input
                                        type="text"
                                        name="ms_name"
                                        class="form-control"
                                    >`,
                            },
                        },
                        ms_type: {
                            title: 'Tipe Beasiswa <span class="text-danger">*</span>',
                            content: {
                                template: `<select name="ms_type" id="ms_type" class="form-control select2">
                                        <option value="1">Internal</option>
                                        <option value="2">External</option>
                                    </select>`,
                            },
                        },
                        ms_from: {
                            title: 'Rekanan',
                            content: {
                                template: `<input
                                        type="text"
                                        name="ms_from"
                                        class="form-control"
                                    >`,
                            },
                        },
                        ms_from_name: {
                            title: 'Pemilik Rekanan',
                            content: {
                                template: `<input
                                        type="text"
                                        name="ms_from_name"
                                        class="form-control"
                                    >`,
                            },
                        },
                        ms_from_email: {
                            title: 'Email Rekanan',
                            content: {
                                template: `<input
                                        type="text"
                                        name="ms_from_email"
                                        class="form-control"
                                    >`,
                            },
                        },
                        ms_from_phone: {
                            title: 'No Telp. Rekanan',
                            content: {
                                template: `<input
                                        type="text"
                                        name="ms_from_phone"
                                        class="form-control"
                                    >`,
                            },
                        },
                        md_period_start: {
                            title: 'Periode Awal <span class="text-danger">*</span>',
                            content: {
                                template: `<select name="ms_period_start" id="ms_period_start" class="form-control select2">
                                        <option value="">Pilih Periode</option>
                                    </select>`,
                            },
                        },
                        md_period_end: {
                            title: 'Periode Akhir <span class="text-danger">*</span>',
                            content: {
                                template: `<select name="ms_period_end" id="ms_period_end" class="form-control select2">
                                        <option value="">Pilih Periode</option>
                                    </select>`,
                            },
                        },
                        md_nominal: {
                            title: 'Nominal <span class="text-danger">*</span>',
                            content: {
                                template: `<input type="number" name="ms_nominal" class="form-control">`,
                            },
                        },
                        md_budget: {
                            title: 'Anggaran <span class="text-danger">*</span>',
                            content: {
                                template: `<input type="number" name="ms_budget" class="form-control">`,
                            },
                        },
                        md_status: {
                            title: 'Status',
                            content: {
                                template: `<br><input type="radio" name="ms_status" value="1" id="ms_status_1" class="form-check-input" checked/> Aktif <input type="radio" name="ms_status" id="ms_status_0" value="0" class="form-check-input"/> Tidak Aktif`,
                            },
                        },
                    },
                    formSubmitLabel: 'Edit Beasiswa',
                    formSubmitNote: `
                    <small class="text-danger">
                        *Kolom <strong>wajib</strong> diisi
                    </small>`,
                    callback: function() {
                        _scholarshipTable.reload()
                    },
                },
            });
            _componentForm.clearData()
            _componentForm.setData(data)
            _scholarshipTable.selected = data
        },
        delete: function(e) {
            let data = _scholarshipTable.getRowData(e);
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin menghapus beasiswa ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(_baseURL + '/api/payment/scholarship/delete/' + data.ms_id, {
                        _method: 'DELETE'
                    }, function(data) {
                        data = JSON.parse(data)
                        Swal.fire({
                            icon: 'success',
                            text: data.message,
                        }).then(() => {
                            _scholarshipTable.reload()
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

    function searchFilter(event, elm) {
        var key = event.key;
        var text = elm.value;
        if (key == 'Enter') {
            elm.value = "";
            if (text == '') {
                dt.clear().destroy();
                _scholarshipTable.init();
            } else {
                dt.clear().destroy();
                _scholarshipTable.init(text);
            }
            console.log(text)
        }
    }
</script>
@endsection
