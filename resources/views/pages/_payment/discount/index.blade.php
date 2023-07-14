@extends('layouts.static_master')


@section('page_title', 'Data Potongan')
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

@include('pages._payment.discount._shortcuts', ['active' => 'index'])

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
                <label class="form-label">Status</label>
                <select name="status_filter" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                </select>
            </div>
            <div class="d-flex align-items-end">
                <button onclick="_discountTable.reload()" class="btn btn-primary text-nowrap">
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
                <th>Nama Potongan</th>
                <th>Periode Awal</th>
                <th>Periode Akhir</th>
                <th>Nominal</th>
                <th>Anggaran</th>
                <th>Realisasi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<div class="target-print">
    <table id="printTable" class="table table-bordered">
        <thead>
            <tr>
                <td>NAMA POTONGAN</td>
                <td>PERIODE AWAL</td>
                <td>PERIODE </td>
                <td>PEMBAYARAN</td>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
@endsection


@section('js_section')
<script>
    var dt = null;
    var dataDt = null;
    $(function() {
        _discountTable.init();
    })

    const _discountTable = {
        ..._datatable,
        init: function(searchFilter = '#ALL') {
            dt = this.instance = $('#invoice-component-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL + '/api/payment/discount/index',
                    data: function(d) {
                        d.custom_filters = {
                            'md_period_start_filter': $('select[name="md_period_start_filter"]').val(),
                            'md_period_end_filter': $('select[name="md_period_end_filter"]').val(),
                            'status_filter': $('select[name="status_filter"]').val(),
                            'search_filter': searchFilter,
                        }
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
                        name: 'md_name',
                        data: 'md_name'
                    },
                    {
                        name: 'md_period_start',
                        data: 'md_period_start',
                        searchable: false,
                        render: (data, _, row) => {
                            return row.period_start.msy_year + _helper.semester(row.period_start.msy_semester)
                        }
                    },
                    {
                        name: 'md_period_end',
                        data: 'md_period_end',
                        searchable: false,
                        render: (data, _, row) => {
                            return row.period_end.msy_year + _helper.semester(row.period_end.msy_semester)
                        }
                    },
                    {
                        name: 'md_nominal',
                        data: 'md_nominal',
                        render: (data, _, row) => {
                            return Rupiah.format(data)
                        }
                    },
                    {
                        name: 'md_budget',
                        data: 'md_budget',
                        render: (data, _, row) => {
                            return Rupiah.format(data)
                        }
                    },
                    {
                        name: 'md_realization',
                        data: 'md_realization',
                        render: (data, _, row) => {
                            return Rupiah.format(data)
                        }
                    },
                    {
                        name: 'md_status',
                        data: 'md_status',
                        searchable: false,
                        render: (data, _, row) => {
                            let status = "Tidak Aktif";
                            let bg = "bg-danger";
                            if (row.md_status === 1) {
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
                                exportOptions: {
                                    columns: [1,2,3,4,5,6,7]
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
                                        a.download = "Laporan Program Potongan";
                                        a.click();
                                    }
                                    xhr.open("POST", _baseURL + "/api/payment/discount/exportData");
                                    xhr.responseType = 'blob';
                                    xhr.send(formData);
                                }
                            },
                            {
                                text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text font-small-4 me-50"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>Csv</span>',
                                className: 'dropdown-item',
                                action: function(e, dt, node, config) {
                                    var csv = 'NAMA POTONGAN,PERIODE AWAL,PERIODE AKHIR,NOMINAL,ANGGARAN,REALISASI,STATUS\n';

                                    var csvFileData = [];
                                    for(var i = 0; i < dataDt.length; i++){
                                        var row = dataDt[i];
                                        csvFileData.push([
                                            row.md_name,
                                            row.period_start.msy_year + _helper.semester(row.period_start.msy_semester),
                                            row.period_end.msy_year + _helper.semester(row.period_end.msy_semester),
                                            Rupiah.format(row.md_nominal),
                                            Rupiah.format(row.md_budget),
                                            Rupiah.format(row.md_realization),
                                            row.md_status === 1 ? 'Aktif':'Tidak Aktif'
                                        ])
                                    }

                                    //merge the data with CSV  
                                    csvFileData.forEach(function(row) {
                                        csv += row.join(',');
                                        csv += "\n";
                                    });

                                    var hiddenElement = document.createElement('a');
                                    hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(csv);
                                    hiddenElement.target = '_blank';

                                    //provide the name for the CSV file to be downloaded  
                                    hiddenElement.download = 'Laporan Data Potongan.csv';
                                    hiddenElement.click();
                                }
                            }
                        ]
                    },
                    {
                        text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file font-small-4 me-50"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>Excel</span>',
                        className: 'btn btn-outline-secondary',
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
                                a.download = "Laporan Program Potongan";
                                a.click();
                            }
                            xhr.open("POST", _baseURL + "/api/payment/discount/exportData");
                            xhr.responseType = 'blob';
                            xhr.send(formData);
                        }
                    }
                ],
                initComplete: function() {
                    $('.invoice-component-actions').html(`
                        <div style="margin-bottom: 7px">
                            <button onclick="_discountTableActions.add()" class="btn btn-primary">
                                <span style="vertical-align: middle">
                                    <i data-feather="plus" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Tambah Potongan
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
                            <a onclick="_discountTableActions.edit(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="_discountTableActions.delete(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const _componentForm = {
        clearData: function() {
            FormDataJson.clear('#form-edit-discount')
            $("#form-edit-discount .select2").trigger('change')
            $(".form-alert").remove()
        },
        setData: function(data) {
            $("[name=md_name]").val(data.md_name);
            $.get(_baseURL + '/api/payment/discount/period', (d) => {
                if (Object.keys(d).length > 0) {
                    d.map(item => {
                        $('#md_period_start').append(`
                            <option value="` + item.msy_id + `">` + item.msy_year + ` ` + _helper.semester(item.msy_semester) + `</option>
                        `);
                        $('#md_period_end').append(`
                            <option value="` + item.msy_id + `">` + item.msy_year + ` ` + _helper.semester(item.msy_semester) + `</option>
                        `);
                    });
                    $('#md_period_start').val(data.md_period_start);
                    $('#md_period_start').trigger('change');
                    $('#md_period_end').val(data.md_period_end);
                    $('#md_period_end').trigger('change');
                    selectRefresh();
                }
            });
            $("[name=md_nominal]").val(data.md_nominal);
            $("[name=md_budget]").val(data.md_budget);
            data.md_status == 1 ? $('#md_status_1').prop('checked', true) : $('#md_status_0').prop('checked', true);
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

    const _discountTableActions = {
        add: function() {
            Modal.show({
                type: 'form',
                modalTitle: 'Tambah Potongan',
                modalSize: 'md',
                config: {
                    formId: 'form-add-discount',
                    formActionUrl: _baseURL + '/api/payment/discount/store',
                    formType: 'add',
                    fields: {
                        md_name: {
                            title: 'Nama Potongan',
                            content: {
                                template: `<input
                                        type="text"
                                        name="md_name"
                                        class="form-control"
                                    >`,
                            },
                        },
                        md_period_start: {
                            title: 'Periode Awal',
                            content: {
                                template: `<select name="md_period_start" id="md_period_start" class="form-control select2">
                                        <option value="">Pilih Periode</option>
                                    </select>`,
                            },
                        },
                        md_period_end: {
                            title: 'Periode Akhir',
                            content: {
                                template: `<select name="md_period_end" id="md_period_end" class="form-control select2">
                                        <option value="">Pilih Periode</option>
                                    </select>`,
                            },
                        },
                        md_nominal: {
                            title: 'Nominal',
                            content: {
                                template: `<input type="number" name="md_nominal" class="form-control">`,
                            },
                        },
                        md_budget: {
                            title: 'Anggaran',
                            content: {
                                template: `<input type="number" name="md_budget" class="form-control">`,
                            },
                        },
                        md_status: {
                            title: 'Status',
                            content: {
                                template: `<br><input type="radio" name="md_status" value="1" class="form-check-input" checked/> Aktif <input type="radio" name="md_status" value="0" class="form-check-input"/> Tidak Aktif`,
                            },
                        },
                    },
                    formSubmitLabel: 'Tambah Potongan',
                    callback: function(e) {
                        _discountTable.reload()
                    },
                },
            });
            $.get(_baseURL + '/api/payment/discount/period', (data) => {
                if (Object.keys(data).length > 0) {
                    data.map(item => {
                        $('#md_period_start').append(`
                            <option value="` + item.msy_id + `">` + item.msy_year + ` ` + _helper.semester(item.msy_semester) + `</option>
                        `);
                        $('#md_period_end').append(`
                            <option value="` + item.msy_id + `">` + item.msy_year + ` ` + _helper.semester(item.msy_semester) + `</option>
                        `);

                    });
                    selectRefresh();
                }
            });
        },
        edit: function(e) {
            let data = _discountTable.getRowData(e);
            Modal.show({
                type: 'form',
                modalTitle: 'Edit Potongan',
                modalSize: 'md',
                config: {
                    formId: 'form-edit-discount',
                    formActionUrl: _baseURL + '/api/payment/discount/store',
                    formType: 'edit',
                    rowId: data.md_id,
                    fields: {
                        md_name: {
                            title: 'Nama Potongan',
                            content: {
                                template: `<input
                                        type="text"
                                        name="md_name"
                                        class="form-control"
                                    >`,
                            },
                        },
                        md_period_start: {
                            title: 'Periode Awal',
                            content: {
                                template: `<select name="md_period_start" id="md_period_start" class="form-control select2">
                                        <option value="">Pilih Periode</option>
                                    </select>`,
                            },
                        },
                        md_period_end: {
                            title: 'Periode Akhir',
                            content: {
                                template: `<select name="md_period_end" id="md_period_end" class="form-control select2">
                                        <option value="">Pilih Periode</option>
                                    </select>`,
                            },
                        },
                        md_nominal: {
                            title: 'Nominal',
                            content: {
                                template: `<input type="number" name="md_nominal" class="form-control">`,
                            },
                        },
                        md_budget: {
                            title: 'Anggaran',
                            content: {
                                template: `<input type="number" name="md_budget" class="form-control">`,
                            },
                        },
                        md_status: {
                            title: 'Status',
                            content: {
                                template: `<br><input type="radio" name="md_status" value="1" id="md_status_1" class="form-check-input" checked/> Aktif <input type="radio" name="md_status" id="md_status_0" value="0" class="form-check-input"/> Tidak Aktif`,
                            },
                        },
                    },
                    formSubmitLabel: 'Edit Potongan',
                    callback: function() {
                        _discountTable.reload()
                    },
                },
            });
            _componentForm.clearData()
            _componentForm.setData(data)
            _discountTable.selected = data
        },
        delete: function(e) {
            let data = _discountTable.getRowData(e);
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin menghapus potongan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(_baseURL + '/api/payment/discount/delete/' + data.md_id, {
                        _method: 'DELETE'
                    }, function(data) {
                        data = JSON.parse(data)
                        Swal.fire({
                            icon: 'success',
                            text: data.message,
                        }).then(() => {
                            _discountTable.reload()
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
                _discountTable.init();
            } else {
                dt.clear().destroy();
                _discountTable.init(text);
                console.log('cari')
            }
            console.log(text);
        }
    }
</script>
@endsection