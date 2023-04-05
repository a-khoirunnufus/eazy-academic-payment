@extends('layouts.static_master')


@section('page_title', 'Setting Tagihan, Tarif, dan Pembayaran')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
    <style>
        .academic-rules-filter {
            display: flex;
            gap: 1rem;
        }
    </style>
@endsection

@section('content')

@include('pages.setting._shortcuts', ['active' => 'academic-rules'])

<div class="card">
    <div class="card-body">
        <div class="academic-rules-filter">
            <div style="width: 250px">
                <label class="form-label">Status</label>
                <select class="form-select">
                    <option selected disabled>Pilih status</option>
                    <option value="1">Aktif</option>
                    <option value="2">Tidak Aktif</option>
                </select>
            </div>
            <div class="d-flex align-items-end">
                <button class="btn btn-primary">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="academic-rules-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Periode Masuk</th>
                <th>Aturan Akademik</th>
                <th>Komponen Tagihan</th>
                <th>Cicilan</th>
                <th class="text-center">Minimal Lunas</th>
                <th class="text-center">Status Aturan</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection


@section('js_section')
<script>
    $(function(){
        _academicRulesTable.init()
    })

    const _academicRulesTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#academic-rules-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/dt/academic-rules',
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
                    {
                        name: 'period', 
                        data: 'period',
                        render: (data) => {
                            return `<span class="fw-bold">${data}</span>`;
                        }
                    },
                    {
                        name: 'rule', 
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <span class="fw-bold">${row.invoice_component}</span><br>
                                    <small class="text-secondary">${row.rule_name}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'invoice_component', 
                        data: 'invoice_component',
                        render: (data) => {
                            return `<span class="fw-bold">${data}</span>`;
                        }
                    },
                    {
                        name: 'instalment', 
                        data: 'instalment',
                        render: (data) => {
                            return `<span class="fw-bold">${data}</span>`;
                        }
                    },
                    {
                        name: 'minimum_paid_percent', 
                        data: 'minimum_paid_percent',
                        render: (data) => {
                            return '<div class="text-center text-danger fw-bold">'+data+'%</div>'
                        }
                    },
                    {
                        name: 'is_active', 
                        data: 'is_active',
                        render: (data) => {
                            var html = '<div class="d-flex justify-content-center">'
                            if(data) {
                                html += '<div class="badge bg-success" style="font-size: inherit">Aktif</div>'
                            } else {
                                html += '<div class="badge bg-danger" style="font-size: inherit">Tidak Aktif</div>'
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
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"academic-rules-actions d-flex align-items-end">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>t' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function() {
                    $('.academic-rules-actions').html(`
                        <div style="margin-bottom: 7px">
                            <button onclick="_academicRulesTableActions.add()" class="btn btn-primary me-1">
                                <span style="vertical-align: middle">
                                    <i data-feather="plus" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Tambah Aturan
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
                            <a onclick="_academicRulesTableActions.edit()" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="_academicRulesTableActions.delete()" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const _academicRulesTableActions = {
        tableRef: _academicRulesTable,
        add: function() {
            Modal.show({
                type: 'form',
                modalTitle: 'Tambah Aturan Akademik',
                modalSize: 'lg',
                config: {
                    formId: 'form-add-academic-rules',
                    formActionUrl: '#',
                    formType: 'add',
                    isTwoColumn: true,
                    fields: {
                        entry_period: {
                            title: 'Periode Masuk',
                            content: {
                                template: `
                                    <select class="form-select" eazy-select2-active>
                                        <option disabled selected>Pilih Periode Masuk</option>
                                        @foreach($static_school_years as $school_year)
                                            <option value="{{ $school_year }}">{{ $school_year }}</option>
                                        @endforeach
                                    </select>
                                `,
                            },
                        },
                        rule: {
                            title: 'Aturan Akademik',
                            content: {
                                template: `<input type="text" name="rule" class="form-control" placeholder="Masukkan nama aturan akademik" />`,
                            },
                        },
                        invoice_component: {
                            title: 'Komponen Tagihan',
                            content: {
                                template: `
                                    <select class="form-select" eazy-select2-active>
                                        <option disabled selected>Pilih Komponen Tagihan</option>
                                        @foreach($static_invoice_components as $invoice_component)
                                            <option value="{{ $invoice_component }}">{{ $invoice_component }}</option>
                                        @endforeach
                                    </select>
                                `,
                            },
                        },
                        instalment: {
                            title: 'Cicilan',
                            content: {
                                template: `
                                    <select class="form-select" eazy-select2-active>
                                        <option disabled selected>Pilih Skema Cicilan</option>
                                        @foreach($static_installments as $installment)
                                            <option value="{{ $installment }}">{{ $installment }}</option>
                                        @endforeach
                                    </select>
                                `,
                            },
                        },
                        minimum_paid: {
                            title: 'Minimal Lunas(%)',
                            content: {
                                template: `<input type="number" name="minimum_paid" class="form-control" placeholder="Masukan persentase minimal lunas" />`,
                            },
                        },
                        is_active: {
                            title: 'Status Aturan',
                            content: {
                                template: `
                                    <select class="form-select" name="is_active">
                                        <option disabled selected>Pilih Status Aturan</option>
                                        <option value="1">Aktif</option>
                                        <option value="2">Tidak Aktif</option>
                                    </select>
                                `
                            },
                        },
                    },
                    formSubmitLabel: 'Tambah Aturan',
                    callback: function() {
                        // ex: reload table
                        Swal.fire({
                            icon: 'success',
                            text: 'Berhasil menambahkan aturan',
                        }).then(() => {
                            this.tableRef.reload()
                        })
                    },
                },
            });
        },
        edit: function() {
            const {
                entry_period, 
                rule_name,
                invoice_component,
                installment,
                minimum_paid,
                is_active
            } = {
                entry_period: '2023/2024',
                rule_name: 'Mengambil Cuti',
                invoice_component: 'Cuti',
                installment: 'Full 100% Pembayaran',
                minimum_paid: '10',
                is_active: 'active',
            };

            Modal.show({
                type: 'form',
                modalTitle: 'Edit Aturan Akademik',
                modalSize: 'lg',
                config: {
                    formId: 'form-edit-academic-rules',
                    formActionUrl: '#',
                    formType: 'edit',
                    isTwoColumn: true,
                    fields: {
                        entry_period: {
                            title: 'Periode Masuk',
                            content: {
                                template: `
                                    <select class="form-select" value=":selected" eazy-select2-active>
                                        <option disabled>Pilih Periode Masuk</option>
                                        @foreach($static_school_years as $school_year)
                                            <option value="{{ $school_year }}">{{ $school_year }}</option>
                                        @endforeach
                                    </select>
                                `,
                                selected: entry_period,
                            },
                        },
                        rule_name: {
                            title: 'Aturan Akademik',
                            content: {
                                template: `<input type="text" name="rule_name" value=":value" class="form-control" placeholder="Masukkan nama aturan akademik" />`,
                                value: rule_name,
                            },
                        },
                        invoice_component: {
                            title: 'Komponen Tagihan',
                            content: {
                                template: `
                                    <select class="form-select" value=":selected" eazy-select2-active>
                                        <option disabled>Pilih Komponen Tagihan</option>
                                        @foreach($static_invoice_components as $invoice_component)
                                            <option value="{{ $invoice_component }}">{{ $invoice_component }}</option>
                                        @endforeach
                                    </select>
                                `,
                                selected: invoice_component,
                            },
                        },
                        installment: {
                            title: 'Cicilan',
                            content: {
                                template: `
                                    <select class="form-select" value=":selected" eazy-select2-active>
                                        <option disabled>Pilih Skema Cicilan</option>
                                        @foreach($static_installments as $installment)
                                            <option value="{{ $installment }}">{{ $installment }}</option>
                                        @endforeach
                                    </select>
                                `,
                                selected: installment,
                            },
                        },
                        minimum_paid: {
                            title: 'Minimal Lunas(%)',
                            content: {
                                template: `<input type="number" name="minimum_paid" value=":value" class="form-control" placeholder="Masukan persentase minimal lunas" />`,
                                value: minimum_paid,
                            },
                        },
                        is_active: {
                            title: 'Status Aturan',
                            content: {
                                template: `
                                    <select class="form-select" name="is_active">
                                        <option disabled>Pilih Status Aturan</option>
                                        <option value="active">Aktif</option>
                                        <option value="inactive">Tidak Aktif</option>
                                    </select>
                                `,
                                selected: is_active,
                            },
                        },
                    },
                    formSubmitLabel: 'Edit Aturan',
                    callback: function() {
                        // ex: reload table
                        Swal.fire({
                            icon: 'success',
                            text: 'Berhasil mengupdate aturan',
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
                text: 'Apakah anda yakin ingin menghapus aturan ini?',
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
                        text: 'Berhasil menghapus aturan',
                    })
                }
            })
        },
    }
</script>
@endsection
