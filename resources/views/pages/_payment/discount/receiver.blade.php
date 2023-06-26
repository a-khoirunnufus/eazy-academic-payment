@extends('layouts.static_master')


@section('page_title', 'Mahasiswa Penerima Potongan')
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

@include('pages._payment.discount._shortcuts', ['active' => 'receiver'])

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
                <button onclick="_discountReceiverTable.reload()" class="btn btn-primary text-nowrap">
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
                <th>Potongan</th>
                <th>Periode </th>
                <th>Nominal</th>
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
        _discountReceiverTable.init();
    })

    const _discountReceiverTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#invoice-component-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/payment/discount-receiver/index',
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
                    {
                        name: 'student_number',
                        data: 'student_number',
                        searchable: false,
                        render: (data, _, row) => {
                            let html = "";
                            if(row.student.student_type_id === 1) {
                                html += '<div class="badge bg-success" style="font-size: inherit">Aktif</div>'
                            } else {
                                html += '<div class="badge bg-danger" style="font-size: inherit">Tidak Aktif</div>'
                            }
                            return `
                                <div>
                                    <span class="text-nowrap fw-bold">${row.student.fullname} ${html}</span><br>
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
                            return row.period.msy_year + _helper.semester(row.period.msy_semester)
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
                            if(row.mdr_status === 1){
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
                            <button onclick="_discountReceiverTableActions.add()" class="btn btn-primary">
                                <span style="vertical-align: middle">
                                    <i data-feather="plus" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Tambah Penerima
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
                            <a onclick="_discountReceiverTableActions.edit(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="_discountReceiverTableActions.delete(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
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
        setData: function(d){
            $.get(_baseURL + '/api/payment/discount-receiver/discount', (data) => {
                if (Object.keys(data).length > 0) {
                    data.map(item => {
                        $('#md_id').append(`
                            <option value="`+item.md_id+`" data-nominal="`+item.md_nominal+`">`+item.md_name+`</option>
                        `);
                    });
                    $('#md_id').val(d.md_id);
                    $('#md_id').trigger('change');
                    selectRefresh();
                }
            });
            $.get(_baseURL + '/api/payment/discount-receiver/student', (data) => {
                if (Object.keys(data).length > 0) {
                    data.map(item => {
                        $('#student_number').append(`
                            <option value="`+item.student_number+`">`+item.fullname+` - `+item.student_id+`</option>
                        `);
                    });
                    $('#student_number').val(d.student_number);
                    $('#student_number').trigger('change');
                    selectRefresh();
                }
            });
            $("#md_id").change(function() {
                md_id = $(this).val();
                $.get(_baseURL + '/api/payment/discount-receiver/period/'+md_id, (data) => {
                    console.log(data);
                    if (Object.keys(data).length > 0) {
                        $("#mdr_period").empty();
                        data.map(item => {
                            $('#mdr_period').append(`
                                <option value="`+item.msy_id+`">`+item.msy_year+` `+_helper.semester(item.msy_semester)+`</option>
                            `);
                        });
                        $('#mdr_period').val(d.mdr_period);
                        $('#mdr_period').trigger('change');
                        selectRefresh();
                    }
                });
            });
            $("[name=mdr_nominal]").val(d.mdr_nominal);
            d.mdr_status == 1 ? $('#mdr_status_1').prop('checked', true) : $('#mdr_status_0').prop('checked', true);
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

    const _discountReceiverTableActions = {
        add: function() {
            Modal.show({
                type: 'form',
                modalTitle: 'Tambah Penerima',
                modalSize: 'md',
                config: {
                    formId: 'form-add-discount-receiver',
                    formActionUrl: _baseURL + '/api/payment/discount-receiver/store',
                    formType: 'add',
                    fields: {
                        md_id: {
                            title: 'Potongan',
                            content: {
                                template:
                                    `<select name="md_id" id="md_id" class="form-control select2">
                                        <option value="">Pilih Potongan</option>
                                    </select>`,
                            },
                        },
                        student_number: {
                            title: 'Mahasiswa',
                            content: {
                                template:
                                    `<select name="student_number" id="student_number" class="form-control select2">
                                        <option value="">Pilih Mahasiswa</option>
                                    </select>`,
                            },
                        },
                        mdr_period: {
                            title: 'Periode',
                            content: {
                                template:
                                    `<select name="mdr_period" id="mdr_period" class="form-control select2">
                                        <option value="">Pilih Periode</option>
                                    </select>`,
                            },
                        },
                        mdr_nominal: {
                            title: 'Nominal',
                            content: {
                                template:
                                    `<input type="number" name="mdr_nominal" class="form-control">`,
                            },
                        },
                        md_status: {
                            title: 'Status',
                            content: {
                                template:
                                    `<br><input type="radio" name="mdr_status" value="1" class="form-check-input" checked/> Aktif <input type="radio" name="md_status" value="0" class="form-check-input"/> Tidak Aktif`,
                            },
                        },
                    },
                    formSubmitLabel: 'Tambah Penerima',
                    callback: function(e) {
                        _discountReceiverTable.reload()
                    },
                },
            });
            $.get(_baseURL + '/api/payment/discount-receiver/discount', (data) => {
                if (Object.keys(data).length > 0) {
                    data.map(item => {
                        $('#md_id').append(`
                            <option value="`+item.md_id+`" data-nominal="`+item.md_nominal+`">`+item.md_name+`</option>
                        `);
                    });
                    selectRefresh();
                }
            });
            $.get(_baseURL + '/api/payment/discount-receiver/student', (data) => {
                if (Object.keys(data).length > 0) {
                    data.map(item => {
                        $('#student_number').append(`
                            <option value="`+item.student_number+`">`+item.fullname+` - `+item.student_id+`</option>
                        `);
                    });
                    selectRefresh();
                }
            });
            $("#md_id").change(function() {
                nominal = $(this).find(":selected").data("nominal");
                md_id = $(this).val();
                $('[name="mdr_nominal"]').val(nominal);
                $.get(_baseURL + '/api/payment/discount-receiver/period/'+md_id, (data) => {
                if (Object.keys(data).length > 0) {
                    $("#mdr_period").empty();
                    data.map(item => {
                        $('#mdr_period').append(`
                            <option value="`+item.msy_id+`">`+item.msy_year+` `+_helper.semester(item.msy_semester)+`</option>
                        `);
                    });
                    selectRefresh();
                }
            });
            })
        },
        edit: function(e) {
            let data = _discountReceiverTable.getRowData(e);
            Modal.show({
                type: 'form',
                modalTitle: 'Edit Penerima Potongan',
                modalSize: 'md',
                config: {
                    formId: 'form-edit-discount-receiver',
                    formActionUrl: _baseURL + '/api/payment/discount-receiver/store',
                    formType: 'edit',
                    rowId: data.mdr_id,
                    fields: {
                        md_id: {
                            title: 'Potongan',
                            content: {
                                template:
                                    `<select name="md_id" id="md_id" class="form-control select2">
                                        <option value="">Pilih Potongan</option>
                                    </select>`,
                            },
                        },
                        student_number: {
                            title: 'Mahasiswa',
                            content: {
                                template:
                                    `<select name="student_number" id="student_number" class="form-control select2">
                                        <option value="">Pilih Mahasiswa</option>
                                    </select>`,
                            },
                        },
                        mdr_period: {
                            title: 'Periode',
                            content: {
                                template:
                                    `<select name="mdr_period" id="mdr_period" class="form-control select2">
                                        <option value="">Pilih Periode</option>
                                    </select>`,
                            },
                        },
                        mdr_nominal: {
                            title: 'Nominal',
                            content: {
                                template:
                                    `<input type="number" name="mdr_nominal" class="form-control">`,
                            },
                        },
                        md_status: {
                            title: 'Status',
                            content: {
                                template:
                                    `<br><input type="radio" name="mdr_status" value="1" id="mdr_status_1" class="form-check-input" checked/> Aktif <input type="radio" name="mdr_status" id="mdr_status_0" value="0" class="form-check-input"/> Tidak Aktif`,
                            },
                        },
                    },
                    formSubmitLabel: 'Edit Potongan',
                    callback: function() {
                        _discountReceiverTable.reload()
                    },
                },
            });
            _componentForm.clearData()
            _componentForm.setData(data)
            _discountReceiverTable.selected = data
        },
        delete: function(e) {
            let data = _discountReceiverTable.getRowData(e);
            Swal.fire({
                title: 'Konfirmasi',
                html: 'Apakah anda yakin ingin menghapus <br> <span class="fw-bolder">'+data.student.fullname+'</span> sebagai penerima potongan?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(_baseURL + '/api/payment/discount-receiver/delete/' + data.mdr_id, {
                        _method: 'DELETE'
                    }, function(data){
                        data = JSON.parse(data)
                        Swal.fire({
                            icon: 'success',
                            text: data.message,
                        }).then(() => {
                            _discountReceiverTable.reload()
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
