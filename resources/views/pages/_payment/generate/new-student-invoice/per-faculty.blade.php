@extends('layouts.static_master')


@section('page_title', 'Generate Tagihan')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('content')

@include('pages._payment.generate._shortcuts', ['active' => 'new-student-invoice'])

<button onclick="window.history.back()" class="btn btn-link" style="margin-bottom: 2rem">
    <i data-feather="arrow-left"></i>&nbsp;&nbsp;Kembali ke Tagihan Satu Institusi
</button>

<div class="card">
    <div class="card-body">
        <div class="d-flex flex-row" style="gap: 3rem">
            <div>
                <p>Periode Pendaftaran</p>
                <h4>{{ $period_path['period']['period_name'] }}</h4>
            </div>
            <div>
                <p>Jalur dan Gelombang</p>
                <h4>{{ $period_path['path']['path_name'] }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="new-student-invoice-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center" rowspan="2">Aksi</th>
                <th rowspan="2">Nama Fakultas</th>
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

    const periodPathId = "{{ $period_path_id }}";
    const periodPath = JSON.parse('{!! json_encode($period_path) !!}');

    const _newStudentInvoiceTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#new-student-invoice-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/payment/generate/new-student-invoice/get-faculties',
                    data: function(d) {
                        d.period_path_id = periodPathId;
                    }
                },
                columns: [
                    {
                        name: 'action',
                        orderable: false,
                        render: (data, _, row) => {
                            return this.template.rowAction()
                        }
                    },
                    {
                        name: 'faculty_name',
                        data: 'faculty_name',
                        render: (data, _, row) => {
                            return this.template.defaultCell(data);
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
                            <h5>Tagihan Setiap Fakultas</h5>
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
                            <a onclick="_newStudentInvoiceTableActions.openStudyprograms(event)" class="dropdown-item"><i data-feather="external-link"></i>&nbsp;&nbsp;Lihat Program Studi</a>
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
                    scope: 'faculty',
                    period_path_id: periodPathId,
                    faculty_id: data.faculty_id,
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
                                text: periodPath.period.schoolyear.msy_year,
                            },
                        },
                        period_name: {
                            title: 'Nama Periode Pendaftaran',
                            content: {
                                template: ':text',
                                text: periodPath.period.period_name,
                            },
                        },
                        period_range: {
                            title: 'Rentang Periode Pendaftaran',
                            content: {
                                template: ':start Sampai :end',
                                start: moment(periodPath.period.period_start).format('DD/MM/YYYY'),
                                end: moment(periodPath.period.period_end).format('DD/MM/YYYY'),
                            }
                        },
                        path_name: {
                            title: 'Nama Jalur dan Gelombang Pendaftaran',
                            content: {
                                template: ':text',
                                text: periodPath.path.path_name,
                            }
                        },
                        faculty_name: {
                            title: 'Nama Fakultas',
                            content: {
                                template: ':text',
                                text: data.faculty_name,
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
        openStudyprograms: function(e) {
            const facultyId = _newStudentInvoiceTable.getRowData(e.currentTarget).faculty_id;
            window.location.href = `${_baseURL}/payment/generate/new-student-invoice/per-studyprogram?period_path_id=${periodPathId}&faculty_id=${facultyId}`;
        },
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
