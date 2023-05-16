@extends('layouts.static_master')


@section('page_title', 'Generate Tagihan')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('content')

@include('pages._payment.generate._shortcuts', ['active' => 'new-student-invoice'])

<div class="alert alert-secondary d-flex fw-normal p-1" style="width: 100%; max-width: 800px; line-height: 2rem; margin-bottom: 2rem">
    <span class="d-inline-block">
        <i data-feather="info"></i>
    </span>
    <span class="d-inline-block ms-1">
        Menampilkan semua tagihan mahasiswa baru pada Universitas Pembangunan Nasional Veteran Yogyakarta.
        Dikelompokkan berdasarkan periode, jalur dan gelombang pendaftaran.
    </span>
</div>

<div class="card">
    <div class="card-body">
        <div class="datatable-filter one-row">
            <div>
                <label class="form-label">Periode Pendaftaran</label>
                <select name="filter-period" class="form-select">
                    <option value="#ALL" selected>Semua Periode Pendaftaran</option>
                    @foreach($registration_periods as $period)
                        <option value="{{ $period->period_id }}">{{ $period->period_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Jalur dan Gelombang</label>
                <select name="filter-path" class="form-select">
                    <option value="#ALL" selected>Semua Jalur dan Gelombang</option>
                    @foreach($registration_paths as $path)
                        <option value="{{ $path->path_id }}">{{ $path->path_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex align-items-end">
                <button onclick="_newStudentInvoiceTable.reload()" class="btn btn-primary text-nowrap">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="new-student-invoice-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center" rowspan="2">Aksi</th>
                <th rowspan="2">Periode Pendaftaran</th>
                <th rowspan="2">Jalur dan Gelombang</th>
                <th rowspan="1" colspan="2" class="text-center">Jenis Tagihan</th>
                <th rowspan="2">Jumlah Total</th>
            </tr>
            <tr>
                <th colspan="1">Tagihan</th>
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
        _newStudentInvoiceTable.init()
    })

    const _newStudentInvoiceTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#new-student-invoice-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/payment/generate/new-student-invoice/get-period-path',
                    data: function(d) {
                        d.custom_filters = {
                            'pr.period_id': $('select[name="filter-period"]').val(),
                            'pt.path_id': $('select[name="filter-path"]').val(),
                        };
                    }
                },
                columns: [
                    {
                        name: 'action',
                        orderable: false,
                        render: (data, _, row) => {
                            return this.template.rowAction();
                        }
                    },
                    {
                        name: 'registration_period',
                        data: 'period_name',
                        render: (data, _, row) => {
                            return this.template.defaultCell(data, {nowrap: false});
                        }
                    },
                    {
                        name: 'registration_path_wave',
                        data: 'path_name',
                        render: (data, _, row) => {
                            return this.template.defaultCell(data, {nowrap: false});
                        }
                    },
                    {
                        name: 'invoice_amount',
                        render: (data) => {
                            return this.template.currencyCell(0);
                        }
                    },
                    {
                        name: 'discount_amount',
                        render: (data) => {
                            return this.template.currencyCell(0);
                        }
                    },
                    {
                        name: 'total_amount',
                        render: (data) => {
                            return this.template.currencyCell(0);
                        }
                    },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"new-student-invoice-actions d-flex align-items-end">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>t' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function() {
                    $('.new-student-invoice-actions').html(`
                        <div style="margin-bottom: 7px">
                            <h5>Tagihan Satu Institusi</h5>
                        </div>
                    `)
                    feather.replace()
                }
            })
        },
        template: {
            defaultCell: _datatableTemplates.defaultCell,
            currencyCell: _datatableTemplates.currencyCell,
            rowAction: function() {
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a onclick="_newStudentInvoiceTableActions.detail(event)" class="dropdown-item"><i data-feather="eye"></i>&nbsp;&nbsp;Lihat Informasi Detail</a>
                            <a onclick="_newStudentInvoiceTableActions.openFaculties(event)" class="dropdown-item"><i data-feather="external-link"></i>&nbsp;&nbsp;Lihat Fakultas</a>
                            <a onclick="_newStudentInvoiceTableActions.generate()" class="dropdown-item disabled"><i data-feather="mail"></i>&nbsp;&nbsp;Generate Semua Tagihan pada Periode, Jalur dan Gelombang ini</a>
                            <a onclick="_newStudentInvoiceTableActions.delete()" class="dropdown-item disabled"><i data-feather="trash"></i>&nbsp;&nbsp;Hapus Semua Tagihan pada Periode, Jalur dan Gelombang ini</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const _newStudentInvoiceTableActions = {
        tableRef: _newStudentInvoiceTable,
        detail: async function(e) {
            const data = _newStudentInvoiceTable.getRowData(e.currentTarget);

            const studentCount = await $.ajax({
                url: _baseURL+'/api/payment/generate/new-student-invoice/get-student-count',
                method: 'get',
                data: {
                    scope: 'institution',
                    period_path_id: data.ppd_id,
                }
            });

            Modal.show({
                type: 'detail',
                modalTitle: 'Detail Tagihan',
                modalSize: 'md',
                config: {
                    fields: {
                        academic_year: {
                            title: 'Tahun Ajaran',
                            content: {
                                template: ':text',
                                text: data.academic_year,
                            },
                        },
                        period_name: {
                            title: 'Nama Periode Pendaftaran',
                            content: {
                                template: ':text',
                                text: data.period_name,
                            },
                        },
                        period_range: {
                            title: 'Rentang Periode Pendaftaran',
                            content: {
                                template: ':start Sampai :end',
                                start: moment(data.period_start).format('DD/MM/YYYY'),
                                end: moment(data.period_end).format('DD/MM/YYYY'),
                            }
                        },
                        path_name: {
                            title: 'Nama Jalur dan Gelombang Pendaftaran',
                            content: {
                                template: ':text',
                                text: data.path_name,
                            }
                        },
                        student_amount: {
                            title: 'Jumlah Mahasiswa yang Ditagih',
                            content: {
                                template: ':text',
                                text: `${studentCount.count}`,
                            }
                        },
                        invoices_table: {
                            title: 'Detail Tagihan',
                            content: {
                                template: `
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Jenis Tagihan</th>
                                                <th>Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Tagihan</td>
                                                <td>${Rupiah.format(0)}</td>
                                            </tr>
                                            <tr>
                                                <td>Potongan</td>
                                                <td>${Rupiah.format(0)}</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Total Jumlah</th>
                                                <th>${Rupiah.format(0)}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                `
                            }
                        }
                    },
                    callback: function() {
                        feather.replace();
                    }
                },
            });

        },
        openFaculties: function(e) {
            const periodPathId = _newStudentInvoiceTable.getRowData(e.currentTarget).ppd_id;
            window.location.href = _baseURL+'/payment/generate/new-student-invoice/per-faculty?period_path_id='+periodPathId;
        },
        generate: function() {
        },
        delete: function() {
        },
    }
</script>
@endsection
