@extends('tpl.vuexy.master-payment')


@section('page_title', 'Generate Tagihan')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
    <style>
        .other-invoice-filter {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            grid-gap: 1rem;
        }
    </style>
@endsection

@section('content')

@include('pages.generate._shortcuts', ['active' => 'other-invoice'])

<div class="card">
    <div class="card-body">
        <div class="other-invoice-filter">
            <div>
                <label class="form-label">Periode Tagihan</label>
                <select class="form-select" eazy-select2-active>
                    <option value="all" selected>Semua Periode Tagihan</option>
                    @foreach($static_school_years as $school_year)
                        @foreach($static_semesters as $semester)
                            <option value="{{ $school_year.'_'.$semester }}">{{ $school_year.' '.$semester }}</option>
                        @endforeach
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Komponen Tagihan</label>
                <select class="form-select" eazy-select2-active>
                    <option value="all" selected>Semua Komponen Tagihan</option>
                    @foreach($static_invoice_components as $invoice_component)
                        <option value="{{ $invoice_component }}">{{ $invoice_component }}</option>
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
                <label class="form-label">Program Studi</label>
                <select class="form-select" eazy-select2-active>
                    <option value="all" selected>Semua Program Studi</option>
                    @foreach($static_study_programs as $study_program)
                        <option value="{{ $study_program }}">{{ $study_program }}</option>
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
            <div class="d-flex align-items-end">
                <button class="btn btn-primary d-inline-block">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="other-invoice-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Program Studi / Fakultas</th>
                <th>Komponen Tagihan</th>
                <th>Jumlah Total</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection


@section('js_section')
<script>
    $(function(){
        _otherInvoiceTable.init()
    })

    const _otherInvoiceTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#other-invoice-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/dt/other-invoice',
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
                        name: 'unit',
                        orderable: false,
                        render: (data, _, row) => {
                            return `
                                <div class="${ row.is_child ? 'ps-2' : '' }">
                                    <a type="button" href="${_baseURL+'/generate/other-invoice-detail'}" class="btn btn-link">${row.unit_name}</a>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'invoice_component',
                        data: 'invoice_component',
                        orderable: false,
                        render: (data) => {
                            return `<span class="fw-bold">${data}</span>`;
                        }
                    },
                    {
                        name: 'invoice_total',
                        data: 'invoice_total',
                        orderable: false,
                        render: (data) => {
                            return `<span class="fw-bold">${Rupiah.format(data)}</span>`;
                        }
                    },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-center header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"other-invoice-actions">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>t' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function() {
                    $('.other-invoice-actions').html(`
                        <h5 class="mb-0">Daftar Tagihan Lainnya</h5>
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
                            <a class="dropdown-item" href="${_baseURL+'/generate/other-invoice-detail'}"><i data-feather="external-link"></i>&nbsp;&nbsp;Detail pada Unit ini</a>
                            <a onclick="_otherInvoiceTableActions.generate()" class="dropdown-item" href="javascript:void(0);"><i data-feather="mail"></i>&nbsp;&nbsp;Generate pada Unit ini</a>
                            <a onclick="_otherInvoiceTableActions.delete()" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete pada Unit ini</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const _otherInvoiceTableActions = {
        tableRef: _otherInvoiceTable,
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
