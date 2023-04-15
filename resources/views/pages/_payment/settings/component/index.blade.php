@extends('layouts.static_master')


@section('page_title', 'Setting Tagihan, Tarif, dan Pembayaran')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('content')

@include('pages.setting._shortcuts', ['active' => 'invoice-component'])

<div class="card">
    <table id="invoice-component-table" class="table table-striped">
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
    $(function(){
        _invoiceComponentTable.init()
    })

    const _invoiceComponentTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#invoice-component-table').DataTable({
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
                            <button onclick="_invoiceComponentTableActions.add()" class="btn btn-primary">
                                <span style="vertical-align: middle">
                                    <i data-feather="plus" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Tambah Komponen Tagihan
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
                            <a onclick="_invoiceComponentTableActions.edit(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="_invoiceComponentTableActions.delete(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
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
                idField: 'msct_id',
                nameField: 'msct_name',
                val: data.msct_id
            });
            data.msc_is_new_student == 1 ? $('[name=msc_is_new_student]').prop('checked', true) : '';
            data.msc_is_student == 1 ? $('[name=msc_is_student]').prop('checked', true) : '';
            data.msc_is_participant == 1 ? $('[name=msc_is_participant]').prop('checked', true) : '';
        }
    }

    const _invoiceComponentTableActions = {
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
                        _invoiceComponentTable.reload()
                    },
                },
            });
            _options.load({
                optionUrl: _baseURL + '/api/payment/settings/component-type',
                idField: 'msct_id',
                nameField: 'msct_name'
            });
        },
        edit: function(e) {
            let data = _invoiceComponentTable.getRowData(e);
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
                        _invoiceComponentTable.reload()
                    },
                },
            });
            _componentForm.clearData()
            _componentForm.setData(data)
            _invoiceComponentTable.selected = data
        },
        delete: function(e) {
            let data = _invoiceComponentTable.getRowData(e);
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
                            _invoiceComponentTable.reload()
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
        }
    }
</script>
@endsection
