@extends('tpl.vuexy.master-payment')


@section('page_title', 'Generate Tagihan')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
    <style>
        .old-student-invoice-filter {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            grid-gap: 1rem;
        }
    </style>
@endsection

@section('content')

@include('pages.generate._shortcuts', ['active' => 'old-student-invoice'])

<div class="card">
    <div class="card-body">
        <div class="old-student-invoice-filter">
            <div>
                <label class="form-label">Periode Masuk</label>
                <select class="form-select" eazy-select2-active>
                    <option value="all" selected>Semua Periode Masuk</option>
                    @foreach($static_school_years as $school_year)
                        <option value="{{ $school_year }}">{{ $school_year }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Periode Tagihan</label>
                <select class="form-select" eazy-select2-active>
                    <option value="all" selected>Semua Periode Tagihan</option>
                    @foreach($static_school_years as $school_year)
                        @foreach($static_semesters as $semester)
                            <option value="{{ $semester.'_'.$school_year }}">{{ $semester.' '.$school_year }}</option>
                        @endforeach
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
                <label class="form-label">Gelombang</label>
                <select class="form-select" eazy-select2-active>
                    <option value="all" selected>Semua Gelombang</option>
                    @foreach($static_registration_periods as $registration_period)
                        <option value="{{ $registration_period }}">{{ $registration_period }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Fakultas</label>
                <select class="form-select" eazy-select2-active>
                    <option value="all" selected>Semua Fakultas</option>
                    @foreach($static_faculties as $faculty)
                        <option value="{{ $faculty }}">{{ $faculty }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Sistem Kuliah</label>
                <select class="form-select" eazy-select2-active>
                    <option value="all" selected>Semua Sistem Kuliah</option>
                    @foreach($static_study_systems as $study_system)
                        <option value="{{ $study_system }}">{{ $study_system }}</option>
                    @endforeach
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
    <table id="old-student-invoice-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center" rowspan="2">Aksi</th>
                <th rowspan="2">Periode Masuk</th>
                <th rowspan="2">Program Studi / Fakultas</th>
                <th rowspan="1" colspan="3" class="text-center">Jenis Tagihan</th>
                <th rowspan="2">Jumlah Total</th>
            </tr>
            <tr>
                <th colspan="1">Tagihan</th>
                <th colspan="1">Denda</th>
                <th colspan="1">Potongan</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection


@section('js_section')
<script>
    $(function(){
        _oldStudentInvoiceTable.init()
    })

    const _oldStudentInvoiceTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#old-student-invoice-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/dt/old-student-invoice',
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
                        name: 'unit',
                        render: (data, _, row) => {
                            return `
                                <div class="${ row.is_child ? 'ps-2' : '' }">
                                    <a type="button" href="${_baseURL+'/generate/student-invoice-detail'}" class="btn btn-link">${row.unit_name}</a>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'invoice',
                        data: 'invoice',
                        render: (data) => {
                            return Rupiah.format(data)
                        }
                    },
                    {
                        name: 'penalty',
                        data: 'penalty',
                        render: (data) => {
                            return Rupiah.format(data)
                        }
                    },
                    {
                        name: 'discount',
                        data: 'discount',
                        render: (data) => {
                            return Rupiah.format(data)
                        }
                    },
                    {
                        name: 'total',
                        data: 'total',
                        render: (data) => {
                            return Rupiah.format(data)
                        }
                    },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"old-student-invoice-actions d-flex align-items-end">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>t' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function() {
                    $('.old-student-invoice-actions').html(`
                        <div style="margin-bottom: 7px">
                            <h5>Daftar Tagihan</h5>
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
                            <a class="dropdown-item" href="${_baseURL+'/generate/student-invoice-detail'}"><i data-feather="external-link"></i>&nbsp;&nbsp;Detail pada Unit ini</a>
                            <a onclick="_oldStudentInvoiceTableActions.generate()" class="dropdown-item" href="javascript:void(0);"><i data-feather="mail"></i>&nbsp;&nbsp;Generate pada Unit ini</a>
                            <a onclick="_oldStudentInvoiceTableActions.delete()" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete pada Unit ini</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const _oldStudentInvoiceTableActions = {
        tableRef: _oldStudentInvoiceTable,
        generate: function() {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin generate tagihan pada unit ini?',
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
        },
        delete: function() {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin menghapus tagihan pada unit ini?',
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
    }
</script>
@endsection
