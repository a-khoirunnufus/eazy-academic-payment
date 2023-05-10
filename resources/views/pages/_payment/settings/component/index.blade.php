@extends('layouts.static_master')


@section('page_title', 'Setting Tagihan, Tarif, dan Pembayaran')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('content')

@include('pages._payment.settings._shortcuts', ['active' => 'component'])

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

<!-- Import Invoice Component Modal -->
<div class="modal fade" id="importInvoiceComponentModal" tabindex="-1" data-bs-backdrop="static" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header bg-white" style="padding: 2rem 3rem 3rem 3rem">
                <h4 class="modal-title fw-bolder" id="importInvoiceComponentModalLabel">Import Komponen Tagihan</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 pt-0">
                <form id="form-import-invoice-component">
                    <div class="form-group mb-2">
                        <label class="form-label-md">Template Komponen Tagihan</label>
                        <a onclick="importInvoiceComponentForm.downloadTemplate()" class="btn btn-primary">
                            <i data-feather="layout" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                            Download Template
                        </a>
                    </div>
                    <div class="form-group">
                        <label class="form-label-md">File Import Komponen Tagihan</label>
                        <input type="file" name="excel_file" class="form-control" />
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button onclick="_invoiceComponentTableActions.import()" class="btn btn-success me-1">Import Komponen</button>
                <button data-bs-dismiss="modal" class="btn btn-outline-secondary">Batal</a>
            </div>
        </div>
    </div>
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
                        <div class="ms-1" style="margin-bottom: 7px">
                            <button onclick="_invoiceComponentTableActions.openImport()" class="btn btn-primary">
                                <span style="vertical-align: middle">
                                    <i data-feather="file-text" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Import Komponen Tagihan
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
                nameField: 'msct_id',
                idData: 'msct_id',
                nameData: 'msct_name',
                val: data.msct_id
            });
            data.msc_is_new_student == 1 ? $('[name=msc_is_new_student]').prop('checked', true) : '';
            data.msc_is_student == 1 ? $('[name=msc_is_student]').prop('checked', true) : '';
            data.msc_is_participant == 1 ? $('[name=msc_is_participant]').prop('checked', true) : '';
        }
    }

    const importInvoiceComponentModal = new bootstrap.Modal(document.getElementById('importInvoiceComponentModal'));

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
                nameField: 'msct_id',
                idData: 'msct_id',
                nameData: 'msct_name'
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
        },
        openImport: function() {
            importInvoiceComponentForm.clearForm();
            importInvoiceComponentModal.show();
        },
        import: function() {
            let formData = new FormData(document.getElementById('form-import-invoice-component'));

            $.ajax({
                url: _baseURL+'/api/payment/settings/component/import',
                type: 'POST',
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    if(data.success){
                        importInvoiceComponentModal.hide();
                        _toastr.success(data.message, 'Success');
                        _invoiceComponentTable.reload();
                        if (data.error_url) {
                            window.location.href = data.error_url;
                        }
                    } else {
                        _toastr.error(data.message, 'Failed');
                    }
                },
                error: function(jqXHR) {
                    _responseHandler.formFailResponse(jqXHR);
                }
            });
        }
    }

    const importInvoiceComponentForm = {
        downloadTemplate: () => {
            window.location.href = _baseURL+'/api/download?storage=local&type=excel-template&filename=import-invoice-component-template.xlsx';
        },
        clearForm: () => {
            $('form#form-import-invoice-component input[name="excel_file"]').val('');
        },
    }
</script>
@endsection
