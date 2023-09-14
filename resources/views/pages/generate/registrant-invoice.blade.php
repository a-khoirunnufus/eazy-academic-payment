@extends('tpl.vuexy.master-payment')


@section('page_title', 'Generate Tagihan')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
    <style>
        .registrant-invoice-filter {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            grid-gap: 1rem;
        }
    </style>
@endsection

@section('content')

@include('pages.generate._shortcuts', ['active' => 'registrant-invoice'])

<div class="card">
    <div class="card-body">
        <div class="registrant-invoice-filter" style="flex-grow: 1">
            <div>
                <label class="form-label">Periode Pendaftaran</label>
                <select class="form-select" eazy-select2-active>
                    <option value="all" selected>Semua Periode Pendaftaran</option>
                    @foreach($static_school_years as $school_year)
                        @foreach($static_semesters as $semester)
                            <option value="{{ $semester.'_'.$school_year }}">{{ $semester.' '.$school_year }}</option>
                        @endforeach
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Jenjang Studi</label>
                <select class="form-select" eazy-select2-active>
                    <option value="all" selected>Semua Jenjang Studi</option>
                    @foreach($static_study_levels as $study_level)
                        <option value="{{ $study_level }}">{{ $study_level }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Gelombang</label>
                <select class="form-select" eazy-select2-active>
                    <option value="all" selected>Semua Gelombang</option>
                    @foreach($static_registration_periods as $registration_period)
                        <option value="{{ $registration_period }}">{{ $registration_period }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Jalur Pendaftaran</label>
                <select class="form-select" eazy-select2-active>
                    <option value="all" selected>Semua Jalur Pendaftaran</option>
                    @foreach($static_registration_paths as $registration_path)
                        <option value="{{ $registration_path }}">{{ $registration_path }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Jenis Tagihan</label>
                <select class="form-select" eazy-select2-active>
                    <option value="all" selected>Semua Jenis Tagihan</option>
                    @foreach($static_invoice_types as $invoice_type)
                        <option value="{{ $invoice_type }}">{{ $invoice_type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex align-items-end">
                <button class="btn btn-info">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="registrant-invoice-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center" rowspan="2">Aksi</th>
                <th rowspan="2">Periode Pendaftaran</th>
                <th rowspan="2">Program Studi / Fakultas</th>
                <th rowspan="1" colspan="3" class="text-center">Jenis Tagihan</th>
            </tr>
            <tr>
                <th>Tagihan</th>
                <th>Denda</th>
                <th>Potongan</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection


@section('js_section')
<script>
    $(function(){
        _registrantInvoiceTable.init()
    })

    const invoiceTypes = JSON.parse('{!! json_encode($static_invoice_types) !!}')

    const _registrantInvoiceTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#registrant-invoice-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/dt/registrant-invoice',
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
                        name: 'period_n_semester',
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <span class="fw-bold">${row.period}</span><br>
                                    <small class="text-secondary">${row.semester}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'study_program_n_faculty',
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <span class="fw-bold">${row.study_program}</span><br>
                                    <small class="text-secondary">${row.faculty}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'invoice_nominal',
                        data: 'invoice_nominal',
                        render: (data) => {
                            return Rupiah.format(data);
                        }
                    },
                    {
                        name: 'penalty_nominal',
                        data: 'penalty_nominal',
                        render: (data) => {
                            return Rupiah.format(data);
                        }
                    },
                    {
                        name: 'discount_nominal',
                        data: 'discount_nominal',
                        render: (data) => {
                            return Rupiah.format(data);
                        }
                    }
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"registrant-invoice-actions d-flex align-items-end">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>t' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function() {
                    $('.registrant-invoice-actions').html(`
                        <div style="margin-bottom: 7px">
                            <button onclick="_registrantInvoiceTableActions.add()" class="btn btn-info me-1">
                                <span style="vertical-align: middle">
                                    <i data-feather="plus" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Tambah Pengaturan Tagihan
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
                            <a onclick="_registrantInvoiceTableActions.edit()" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="_registrantInvoiceTableActions.delete()" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
                            <a onclick="_registrantInvoiceTableActions.generate()" class="dropdown-item" href="javascript:void(0);"><i data-feather="mail"></i>&nbsp;&nbsp;Generate</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const _registrantInvoiceTableActions = {
        tableRef: _registrantInvoiceTable,
        add: function() {
            Modal.show({
                type: 'form',
                modalTitle: 'Tambah Pengaturan Tagihan',
                modalSize: 'lg',
                config: {
                    formId: 'form-add-registrant-invoice',
                    formActionUrl: '#',
                    formType: 'add',
                    isTwoColumn: true,
                    fields: {
                        period: {
                            title: 'Periode Pendaftaran',
                            content: {
                                template: `
                                    <select class="form-select" eazy-select2-active>
                                        <option disabled selected>Pilih Periode Pendaftaran</option>
                                        @foreach($static_school_years as $school_year)
                                            @foreach($static_semesters as $semester)
                                                <option value="{{ $semester.'_'.$school_year }}">{{ $semester.' '.$school_year }}</option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                `,
                            },
                        },
                        semester: {
                            title: 'Semester',
                            content: {
                                template: `
                                    <select class="form-select" eazy-select2-active>
                                        <option disabled selected>Pilih Semester</option>
                                        @foreach($static_semesters as $semester)
                                            <option value="{{ $semester }}">{{ $semester }}</option>
                                        @endforeach
                                    </select>
                                `,
                            },
                        },
                        faculty: {
                            title: 'Fakultas',
                            content: {
                                template: `
                                    <select class="form-select" eazy-select2-active>
                                        <option disabled selected>Pilih Fakultas</option>
                                        @foreach($static_faculties as $faculty)
                                            <option value="{{ $faculty }}">{{ $faculty }}</option>
                                        @endforeach
                                    </select>
                                `,
                            },
                        },
                        study_program: {
                            title: 'Program Studi',
                            content: {
                                template: `
                                    <select class="form-select" eazy-select2-active>
                                        <option disabled selected>Pilih Program Studi</option>
                                        @foreach($static_study_programs as $study_program)
                                            <option value="{{ $study_program }}">{{ $study_program }}</option>
                                        @endforeach
                                    </select>
                                `,
                            },
                        },
                        invoice_nominal: {
                            title: 'Nominal Tagihan',
                            content: {
                                template: `
                                    <input type="number" name="invoice_nominal" class="form-control" placeholder="Masukkan nominal tagihan" />
                                `,
                            },
                        },
                        penalty_nominal: {
                            title: 'Nominal Tagihan',
                            content: {
                                template: `
                                    <input type="number" name="penalty_nominal" class="form-control" placeholder="Masukkan nominal denda" />
                                `,
                            },
                        },
                        discount_nominal: {
                            title: 'Nominal Potongan',
                            content: {
                                template: `
                                    <input type="number" name="discount_nominal" class="form-control" placeholder="Masukkan nominal potongan" />
                                `,
                            },
                        },
                    },
                    formSubmitLabel: 'Tambah Tagihan',
                    callback: function() {
                        // ex: reload table
                        Swal.fire({
                            icon: 'success',
                            text: 'Berhasil menambah tagihan',
                        }).then(() => {
                            this.tableRef.reload()
                        })
                    },
                },
            });
        },
        edit: function() {
            const {
                registration_period,
                semester,
                faculty,
                study_program,
                invoice_nominal,
                penalty_nominal,
                discount_nominal,
            } = {
                registration_period: '2022/2023',
                semester: 'Semester Genap',
                faculty: 'Fakultas Informatika',
                study_program: 'S1 Informatika',
                invoice_nominal: '10000000',
                penalty_nominal: '21000',
                discount_nominal: '11000',
            };

            Modal.show({
                type: 'form',
                modalTitle: 'Edit Pengaturan Tagihan',
                modalSize: 'lg',
                config: {
                    formId: 'form-edit-registrant-invoice',
                    formActionUrl: '#',
                    formType: 'edit',
                    isTwoColumn: true,
                    fields: {
                        period: {
                            title: 'Periode Pendaftaran',
                            content: {
                                template: `
                                    <select class="form-select" value=":selected" disabled eazy-select2-active>
                                        <option disabled>Pilih Periode Pendaftaran</option>
                                        @foreach($static_school_years as $school_year)
                                            @foreach($static_semesters as $semester)
                                                <option value="{{ $semester.'_'.$school_year }}">{{ $semester.' '.$school_year }}</option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                `,
                                selected: registration_period,
                            },
                        },
                        semester: {
                            title: 'Semester',
                            content: {
                                template: `
                                    <select class="form-select" value=":semester" disabled eazy-select2-active>
                                        <option disabled>Pilih Semester</option>
                                        @foreach($static_semesters as $semester)
                                            <option value="{{ $semester }}">{{ $semester }}</option>
                                        @endforeach
                                    </select>
                                `,
                                selected: semester,
                            },
                        },
                        faculty: {
                            title: 'Fakultas',
                            content: {
                                template: `
                                    <select class="form-select" value=":selected" disabled eazy-select2-active>
                                        <option disabled>Pilih Fakultas</option>
                                        @foreach($static_faculties as $faculty)
                                            <option value="{{ $faculty }}">{{ $faculty }}</option>
                                        @endforeach
                                    </select>
                                `,
                                selected: faculty,
                            },
                        },
                        study_program: {
                            title: 'Program Studi',
                            content: {
                                template: `
                                    <select class="form-select" value=":selected" disabled eazy-select2-active>
                                        <option disabled>Pilih Program Studi</option>
                                        @foreach($static_study_programs as $study_program)
                                            <option value="{{ $study_program }}">{{ $study_program }}</option>
                                        @endforeach
                                    </select>
                                `,
                                selected: study_program,
                            },
                        },
                        invoice_nominal: {
                            title: 'Nominal Tagihan',
                            content: {
                                template: `
                                    <input type="number" name="invoice_nominal" value=":value" class="form-control" placeholder="Masukkan nominal tagihan" />
                                `,
                                value: invoice_nominal,
                            },
                        },
                        penalty_nominal: {
                            title: 'Nominal Tagihan',
                            content: {
                                template: `
                                    <input type="number" name="penalty_nominal" value=":value" class="form-control" placeholder="Masukkan nominal denda" />
                                `,
                                value: penalty_nominal,
                            },
                        },
                        discount_nominal: {
                            title: 'Nominal Potongan',
                            content: {
                                template: `
                                    <input type="number" name="discount_nominal" value=":value" class="form-control" placeholder="Masukkan nominal potongan" />
                                `,
                                value: discount_nominal,
                            },
                        },
                    },
                    formSubmitLabel: 'Edit Tagihan',
                    callback: function() {
                        // ex: reload table
                        Swal.fire({
                            icon: 'success',
                            text: 'Berhasil mengupdate tagihan',
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
                text: 'Apakah anda yakin ingin menghapus tagihan ini?',
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
                        text: 'Berhasil menghapus tagihan',
                    })
                }
            })
        },
        generate: function() {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin generate tagihan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#356CFF',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Generate',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    // ex: do ajax request
                    Swal.fire({
                        icon: 'success',
                        text: 'Berhasil generate tagihan',
                    })
                }
            })
        }
    }
</script>
@endsection
