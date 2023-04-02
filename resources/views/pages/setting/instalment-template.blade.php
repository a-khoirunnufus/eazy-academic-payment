@extends('layouts.static_master')


@section('page_title', 'Setting')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('content')

@include('pages.setting._shortcuts', ['active' => 'instalment-template'])

<div class="card">
    <table id="instalment-template-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Nama Skema</th>
                <th>X Kali Pembayaran</th>
                <th>Status Validitas</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection


@section('js_section')
<script>
    $(function(){
        _instalmentTemplateTable.init()
    })

    const _instalmentTemplateTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#instalment-template-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/dt/instalment-template',
                },
                columns: [
                    {
                        name: 'action',
                        data: 'id',
                        orderable: false,
                        render: (data, _, row) => {
                            return this.template.rowAction(data)
                        }
                    },
                    {name: 'schema_name', data: 'schema_name'},
                    {
                        name: 'n_payment', 
                        data: 'n_payment',
                        render: (data) => {
                            return `${data} Pembayaran`                        }
                    },
                    {name: 'validity', data: 'validity'},
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
                            <button onclick="_instalmentTemplateTableActions.add()" class="btn btn-primary">
                                <span style="vertical-align: middle">
                                    <i data-feather="plus" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Tambah Template Cicilan
                                </span>
                            </button>
                        </div>
                    `)
                    feather.replace()
                }
            })
        },
        template: {
            rowAction: function(id) {
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a onclick="_instalmentTemplateTableActions.edit()" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="_instalmentTemplateTableActions.delete()" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const _instalmentTemplateTableActions = {
        tableRef: _instalmentTemplateTable,
        add: function() {
            Modal.show({
                type: 'form',
                modalTitle: 'Tambah Template Cicilan',
                config: {
                    formId: 'form-add-instalment-template',
                    formActionUrl: '#',
                    fields: {
                        schema_name: {
                            title: 'Nama Skema',
                            content: {
                                template: 
                                    `<input 
                                        type="text"
                                        name="schema_name" 
                                        class="form-control"
                                    >`,
                            },
                        },
                        n_payment: {
                            title: 'X Kali Pembayaran',
                            content: {
                                template: 
                                    `<input 
                                        type="number" 
                                        name="n_payment" 
                                        class="form-control"
                                    >`,
                            },
                        },
                        validity: {
                            title: 'Status Validitas',
                            content: {
                                template: `
                                    <div class="d-flex flex-row" style="gap: 1rem">
                                        <div class="form-check">
                                            <input name="validity" class="form-check-input" type="radio" value="" />
                                            <label class="form-check-label">
                                                Valid
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input name="validity" class="form-check-input" type="radio" value="" checked />
                                            <label class="form-check-label">
                                                Invalid
                                            </label>
                                        </div>
                                    </div>
                                `
                            },
                        },
                    },
                    formSubmitLabel: 'Tambah Template',
                    callback: function() {
                        // ex: reload table
                        Swal.fire({
                            icon: 'success',
                            text: 'Berhasil menambahkan template cicilan',
                        }).then(() => {
                            this.tableRef.reload()
                        })
                    },
                },
            });
        },
        edit: function() {
            Modal.show({
                type: 'form',
                modalTitle: 'Edit Komponen Tagihan',
                config: {
                    formId: 'form-edit-transaction-group',
                    formActionUrl: '#',
                    fields: {
                        schema_name: {
                            title: 'Nama Skema',
                            content: {
                                template: 
                                    `<input 
                                        type="text"
                                        name="schema_name" 
                                        class="form-control"
                                        value="Full 100%"
                                    >`,
                            },
                        },
                        n_payment: {
                            title: 'X Kali Pembayaran',
                            content: {
                                template: 
                                    `<input 
                                        type="number" 
                                        name="n_payment" 
                                        class="form-control"
                                        value="1"
                                    >`,
                            },
                        },
                        validity: {
                            title: 'Status Validitas',
                            content: {
                                template: `
                                    <div class="d-flex flex-row" style="gap: 1rem">
                                        <div class="form-check">
                                            <input name="validity" class="form-check-input" type="radio" value="" checked />
                                            <label class="form-check-label">
                                                Valid
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input name="validity" class="form-check-input" type="radio" value="" />
                                            <label class="form-check-label">
                                                Invalid
                                            </label>
                                        </div>
                                    </div>
                                `
                            },
                        },
                    },
                    formSubmitLabel: 'Edit Template',
                    callback: function() {
                        // ex: reload table
                        Swal.fire({
                            icon: 'success',
                            text: 'Berhasil mengupdate template cicilan',
                        }).then(() => {
                            this.tableRef.reload()
                        })
                    },
                },
            });
        },
        delete: function() {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin menghapus template cician ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    // ex: do ajax request
                    Swal.fire({
                        icon: 'success',
                        text: 'Berhasil menghapus template cician',
                    })
                }
            })
            // Modal.show({
            //     type: 'confirmation',
            //     modalTitle: 'Konfirmasi Menghapus Template',
            //     config: {
            //         callback: function() {},
            //     }
            // });
        }
    }
</script>
@endsection
