@extends('layouts.static_master')


@section('page_title', 'Detail Tagihan Mahasiswa Lama/Baru')
@section('sidebar-size', 'collapsed')
@section('url_back', url('generate/old-student-invoice'))

@section('css_section')
    <style>
        .eazy-table-wrapper {
            min-height: 300px;
            width: 100%;
            overflow-x: auto;
        }
    </style>
@endsection

@section('content')

@include('pages._payment.generate._shortcuts', ['active' => 'new-student-invoice'])

<button onclick="window.history.back()" class="btn btn-link" style="margin-bottom: 2rem">
    <i data-feather="arrow-left"></i>&nbsp;&nbsp;Kembali ke Tagihan Setiap Program Studi
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
            <div>
                <p>Fakultas</p>
                <h4>{{ $faculty['faculty_name'] }}</h4>
            </div>
            <div>
                <p>Program Studi</p>
                <h4>{{ $studyprogram->studyprogram_name }}</h4>
            </div>
            <div>
                <p>Jenis Perkuliahan</p>
                <h4>{{ $lecture_type->mlt_name }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="student-invoice-detail-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Nama Mahasiswa</th>
                <th>Total / Rincian Tagihan</th>
                <th>Total / Rincian Tagihan</th>
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
    const facultyId = "{{ $faculty_id }}";
    const studyprogramLectureTypeId = "{{ $studyprogram_lecture_type_id }}";
    const periodPath = JSON.parse('{!! json_encode($period_path) !!}');
    const faculty = JSON.parse('{!! json_encode($faculty) !!}');
    const studyprogram = JSON.parse('{!! json_encode($studyprogram) !!}');
    const lectureType = JSON.parse('{!! json_encode($lecture_type) !!}');

    const _newStudentInvoiceTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#student-invoice-detail-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/payment/generate/new-student-invoice/get-students',
                    data: function(d) {
                        d.period_path_id = periodPathId;
                        d.studyprogram_lecture_type_id = studyprogramLectureTypeId;
                    }
                },
                columns: [
                    {
                        name: 'action',
                        orderable: false,
                        render: (data, _, row) => {
                            return this.template.rowAction(row.student_id);
                        }
                    },
                    {
                        name: 'student_name',
                        render: (data, _, row) => {
                            return this.template.defaultCell(row.fullname, {bold: true});
                        }
                    },
                    {
                        name: 'invoice',
                        render: (data, _, row) => {
                            return this.template.defaultCell('N/A');
                        }
                    },
                    {
                        name: 'discount',
                        render: (data, _, row) => {
                            return this.template.defaultCell('N/A');
                        }
                    },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"student-invoice-detail-actions d-flex align-items-end">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>' +
                    '<"eazy-table-wrapper" t>' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function() {
                    $('.student-invoice-detail-actions').html(`
                        <div style="margin-bottom: 7px">
                            <h5>Tagihan Setiap Mahasiswa</h5>
                        </div>
                    `)
                    feather.replace()
                }
            })
        },
        template: {
            ..._datatableTemplates,
            rowAction: function(id) {
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a onclick="_newStudentInvoiceTableActions.detail(event)" class="dropdown-item"><i data-feather="eye"></i>&nbsp;&nbsp;Lihat Informasi Detail</a>
                            <a onclick="_newStudentInvoiceTableActions.generate()" class="dropdown-item disabled"><i data-feather="mail"></i>&nbsp;&nbsp;Generate Semua Tagihan pada Periode, Jalur dan Gelombang ini</a>
                            <a onclick="_newStudentInvoiceTableActions.delete()" class="dropdown-item disabled"><i data-feather="trash"></i>&nbsp;&nbsp;Hapus Semua Tagihan pada Periode, Jalur dan Gelombang ini</a>
                        </div>
                    </div>
                `
            },
        }
    }

    const _newStudentInvoiceTableActions = {
        tableRef: _newStudentInvoiceTable,
        detail: function(e) {
            const data = _newStudentInvoiceTable.getRowData(e.currentTarget);

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
                                text: faculty.faculty_name,
                            }
                        },
                        studyprogram_name: {
                            title: 'Nama Program Studi',
                            content: {
                                template: ':text',
                                text: studyprogram.studyprogram_name,
                            }
                        },
                        studyprogram_lecture_type: {
                            title: 'Jenis Perkuliahan',
                            content: {
                                template: ':text',
                                text: lectureType.mlt_name,
                            }
                        },
                        student_fullname: {
                            title: 'Nama Lengkap Mahasiswa',
                            content: {
                                template: ':text',
                                text: data.fullname,
                            },
                        },
                        student_nik: {
                            title: 'NIK Mahasiswa',
                            content: {
                                template: ':text',
                                text: data.nik,
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
                                                <th>Rincian</th>
                                                <th>Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Tagihan</td>
                                                <td>N/A</td>
                                                <td>${Rupiah.format(0)}</td>
                                            </tr>
                                            <tr>
                                                <td>Potongan</td>
                                                <td>N/A</td>
                                                <td>${Rupiah.format(0)}</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="2">Total Jumlah</th>
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
        generate: function() {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin generate tagihan mahasiswa ini?',
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
                text: 'Apakah anda yakin ingin menghapus tagihan mahasiswa ini?',
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
