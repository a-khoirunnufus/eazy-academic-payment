@extends('layouts.static_master')


@section('page_title', 'Setting')
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
                    url: _baseURL+'/api/dt/invoice-component',
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
                    {name: 'code', data: 'code'},
                    {name: 'name', data: 'name'},
                    {
                        name: 'old_student', 
                        data: 'old_student',
                        render: (data, _, row) => {
                            var html = '<div class="d-flex justify-content-center">'
                            if(data) {
                                html += '<input class="form-check-input" type="checkbox" disabled checked>';
                            } else {
                                html += '<input class="form-check-input" type="checkbox" disabled>';
                            }
                            html += '</div>'
                            return html
                        }
                    },
                    {
                        name: 'new_student', 
                        data: 'new_student',
                        render: (data, _, row) => {
                            var html = '<div class="d-flex justify-content-center">'
                            if(data) {
                                html += '<input class="form-check-input" type="checkbox" disabled checked>';
                            } else {
                                html += '<input class="form-check-input" type="checkbox" disabled>';
                            }
                            html += '</div>'
                            return html
                        }
                    },
                    {
                        name: 'registrant', 
                        data: 'registrant',
                        render: (data, _, row) => {
                            var html = '<div class="d-flex justify-content-center">'
                            if(data) {
                                html += '<input class="form-check-input" type="checkbox" disabled checked>';
                            } else {
                                html += '<input class="form-check-input" type="checkbox" disabled>';
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
        },
        template: {
            rowAction: function(id) {
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a onclick="_invoiceComponentTableActions.edit()" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="_invoiceComponentTableActions.delete()" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const _invoiceComponentTableActions = {
        tableRef: _invoiceComponentTable,
        add: function() {
            Modal.show({
                type: 'form',
                modalTitle: 'Tambah Komponen Tagihan',
                config: {
                    formId: 'form-add-invoice-component',
                    formActionUrl: '#',
                    fields: {
                        invoice_component_code: {
                            title: 'Kode Komponen Tagihan',
                            content: {
                                template: 
                                    `<input 
                                        type="text" 
                                        name="invoice_component_code" 
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
                                        name="invoice_component_name" 
                                        class="form-control"
                                    >`,
                            },
                        },
                        old_student: {
                            title: 'Terapkan Kepada Mahasiswa Lama',
                            content: {
                                template: `
                                    <div class="d-flex flex-row" style="gap: 1rem">
                                        <div class="form-check">
                                            <input name="old_student" class="form-check-input" type="radio" value="" />
                                            <label class="form-check-label">
                                                Terapkan
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input name="old_student" class="form-check-input" type="radio" value="" checked />
                                            <label class="form-check-label">
                                                Jangan Terapkan
                                            </label>
                                        </div>
                                    </div>
                                `
                            },
                        },
                        new_student: {
                            title: 'Terapkan Kepada Mahasiswa Baru',
                            content: {
                                template: `
                                    <div class="d-flex flex-row" style="gap: 1rem">
                                        <div class="form-check">
                                            <input name="new_student" class="form-check-input" type="radio" value="" />
                                            <label class="form-check-label">
                                                Terapkan
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input name="new_student" class="form-check-input" type="radio" value="" checked />
                                            <label class="form-check-label">
                                                Jangan Terapkan
                                            </label>
                                        </div>
                                    </div>
                                `
                            },
                        },
                        registrant: {
                            title: 'Terapkan Kepada Pendaftar',
                            content: {
                                template: `
                                    <div class="d-flex flex-row" style="gap: 1rem">
                                        <div class="form-check">
                                            <input name="registrant" class="form-check-input" type="radio" value="" />
                                            <label class="form-check-label">
                                                Terapkan
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input name="registrant" class="form-check-input" type="radio" value="" checked />
                                            <label class="form-check-label">
                                                Jangan Terapkan
                                            </label>
                                        </div>
                                    </div>
                                `
                            },
                        },
                    },
                    formSubmitLabel: 'Tambah Komponen',
                    callback: function() {
                        // ex: reload table
                        Swal.fire({
                            icon: 'success',
                            text: 'Berhasil menambahkan komponen tagihan',
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
                        invoice_component_code: {
                            title: 'Kode Komponen Tagihan',
                            content: {
                                template: 
                                    `<input 
                                        type="text" 
                                        name="invoice_component_code" 
                                        class="form-control"
                                        value="BPP"
                                    >`,
                            },
                        },
                        invoice_component_name: {
                            title: 'Nama Komponen Tagihan',
                            content: {
                                template: 
                                    `<input 
                                        type="text" 
                                        name="invoice_component_name" 
                                        class="form-control"
                                        value="Biaya Perkuliahan"
                                    >`,
                            },
                        },
                        old_student: {
                            title: 'Terapkan Kepada Mahasiswa Lama',
                            content: {
                                template: `
                                    <div class="d-flex flex-row" style="gap: 1rem">
                                        <div class="form-check">
                                            <input name="old_student" class="form-check-input" type="radio" value="" checked />
                                            <label class="form-check-label">
                                                Terapkan
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input name="old_student" class="form-check-input" type="radio" value="" />
                                            <label class="form-check-label">
                                                Jangan Terapkan
                                            </label>
                                        </div>
                                    </div>
                                `
                            },
                        },
                        new_student: {
                            title: 'Terapkan Kepada Mahasiswa Baru',
                            content: {
                                template: `
                                    <div class="d-flex flex-row" style="gap: 1rem">
                                        <div class="form-check">
                                            <input name="new_student" class="form-check-input" type="radio" value="" checked />
                                            <label class="form-check-label">
                                                Terapkan
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input name="new_student" class="form-check-input" type="radio" value="" />
                                            <label class="form-check-label">
                                                Jangan Terapkan
                                            </label>
                                        </div>
                                    </div>
                                `
                            },
                        },
                        registrant: {
                            title: 'Terapkan Kepada Pendaftar',
                            content: {
                                template: `
                                    <div class="d-flex flex-row" style="gap: 1rem">
                                        <div class="form-check">
                                            <input name="registrant" class="form-check-input" type="radio" value="" />
                                            <label class="form-check-label">
                                                Terapkan
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input name="registrant" class="form-check-input" type="radio" value="" checked />
                                            <label class="form-check-label">
                                                Jangan Terapkan
                                            </label>
                                        </div>
                                    </div>
                                `
                            },
                        },
                    },
                    formSubmitLabel: 'Edit Komponen',
                    callback: function() {
                        // ex: reload table
                        Swal.fire({
                            icon: 'success',
                            text: 'Berhasil mengupdate komponen tagihan',
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
                text: 'Apakah anda yakin ingin menghapus komponen tagihan ini?',
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
                        text: 'Berhasil menghapus komponen tagihan',
                    })
                }
            })
            // Modal.show({
            //     type: 'confirmation',
            //     modalTitle: 'Konfirmasi Menghapus Komponen',
            //     config: {
            //         callback: function() {},
            //     }
            // });
        }
    }
</script>
@endsection
