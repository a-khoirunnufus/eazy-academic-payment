@extends('layouts.static_master')


@section('page_title', 'Setting Tagihan, Tarif, dan Pembayaran')
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

@endsection


@section('js_section')
<script>
    $(function(){
        _discountTable.init();
    })

    const _discountTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#invoice-component-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/payment/discount/index',
                    data: function(d) {
                        d.custom_filters = {
                            'md_period_start_filter': $('select[name="md_period_start_filter"]').val(),
                            'md_period_end_filter': $('select[name="md_period_end_filter"]').val(),
                        };
                    }
                },
                columns: [
                    {
                        name: 'action',
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: (data, _, row) => {
                            console.log(row);
                            return this.template.rowAction(data)
                        }
                    },
                    {name: 'md_name', data: 'md_name'},
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
                            if(row.md_status === 1){
                                status = "Aktif";
                                bg = "bg-success";
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
                            <button onclick="_discountTableActions.add()" class="btn btn-primary">
                                <span style="vertical-align: middle">
                                    <i data-feather="plus" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Tambah Potongan
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
                            <a onclick="_discountTableActions.edit(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="_discountTableActions.delete(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const _componentForm = {
        clearData: function(){
            FormDataJson.clear('#form-edit-discount')
            $("#form-edit-discount .select2").trigger('change')
            $(".form-alert").remove()
        },
        setData: function(data){
            $("[name=md_name]").val(data.md_name);
            $.get(_baseURL + '/api/payment/discount/period', (d) => {
                if (Object.keys(d).length > 0) {
                    d.map(item => {
                        $('#md_period_start').append(`
                            <option value="`+item.msy_id+`">`+item.msy_year+` `+_helper.semester(item.msy_semester)+`</option>
                        `);
                        $('#md_period_end').append(`
                            <option value="`+item.msy_id+`">`+item.msy_year+` `+_helper.semester(item.msy_semester)+`</option>
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
        semester: function(msy_semester){
            var semester = ' Genap';
            if(msy_semester == 1) {
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
                                template:
                                    `<input
                                        type="text"
                                        name="md_name"
                                        class="form-control"
                                    >`,
                            },
                        },
                        md_period_start: {
                            title: 'Periode Awal',
                            content: {
                                template:
                                    `<select name="md_period_start" id="md_period_start" class="form-control select2">
                                        <option value="">Pilih Periode</option>
                                    </select>`,
                            },
                        },
                        md_period_end: {
                            title: 'Periode Akhir',
                            content: {
                                template:
                                    `<select name="md_period_end" id="md_period_end" class="form-control select2">
                                        <option value="">Pilih Periode</option>
                                    </select>`,
                            },
                        },
                        md_nominal: {
                            title: 'Nominal',
                            content: {
                                template:
                                    `<input type="number" name="md_nominal" class="form-control">`,
                            },
                        },
                        md_budget: {
                            title: 'Anggaran',
                            content: {
                                template:
                                    `<input type="number" name="md_budget" class="form-control">`,
                            },
                        },
                        md_status: {
                            title: 'Status',
                            content: {
                                template:
                                    `<br><input type="radio" name="md_status" value="1" class="form-check-input" checked/> Aktif <input type="radio" name="md_status" value="0" class="form-check-input"/> Tidak Aktif`,
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
                            <option value="`+item.msy_id+`">`+item.msy_year+` `+_helper.semester(item.msy_semester)+`</option>
                        `);
                        $('#md_period_end').append(`
                            <option value="`+item.msy_id+`">`+item.msy_year+` `+_helper.semester(item.msy_semester)+`</option>
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
                                template:
                                    `<input
                                        type="text"
                                        name="md_name"
                                        class="form-control"
                                    >`,
                            },
                        },
                        md_period_start: {
                            title: 'Periode Awal',
                            content: {
                                template:
                                    `<select name="md_period_start" id="md_period_start" class="form-control select2">
                                        <option value="">Pilih Periode</option>
                                    </select>`,
                            },
                        },
                        md_period_end: {
                            title: 'Periode Akhir',
                            content: {
                                template:
                                    `<select name="md_period_end" id="md_period_end" class="form-control select2">
                                        <option value="">Pilih Periode</option>
                                    </select>`,
                            },
                        },
                        md_nominal: {
                            title: 'Nominal',
                            content: {
                                template:
                                    `<input type="number" name="md_nominal" class="form-control">`,
                            },
                        },
                        md_budget: {
                            title: 'Anggaran',
                            content: {
                                template:
                                    `<input type="number" name="md_budget" class="form-control">`,
                            },
                        },
                        md_status: {
                            title: 'Status',
                            content: {
                                template:
                                    `<br><input type="radio" name="md_status" value="1" id="md_status_1" class="form-check-input" checked/> Aktif <input type="radio" name="md_status" id="md_status_0" value="0" class="form-check-input"/> Tidak Aktif`,
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
                    }, function(data){
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

</script>
@endsection
