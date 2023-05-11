@extends('layouts.static_master')


@section('page_title', 'Detail Tagihan Mahasiswa Lama/Baru')
@section('sidebar-size', 'collapsed')
@section('url_back', url('generate/old-student-invoice'))

@section('css_section')
    <style>
        .eazy-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }
    </style>
@endsection

@section('content')

@include('pages._payment.generate._shortcuts', ['active' => 'new-student-invoice'])

<div class="alert alert-warning d-inline-block p-1" role="alert">
    <div>
        Note:<br>
        - Belum filter berdasarkan fakultas / program studi.<br>
        - Menampilkan pendaftar yang telah lulus fase registrasi, dan sedang dalam berada dalam fase daftar ulang.<br>
        - Belum ada komponen denda, potongan dan beasiswa.
    </div>
</div>

<div class="card">
    <table id="student-invoice-detail-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Nama</th>
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
        _studentInvoiceDetailTable.init()
    })

    const _studentInvoiceDetailTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#student-invoice-detail-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/payment/generate/new-student-invoice/index',
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
                        name: 'student',
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
                            <h5>Detail Daftar Tagihan Mahasiswa Baru</h5>
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
                            <a onclick="_studentInvoiceDetailTableAction.detail(event)" class="dropdown-item" href="javascript:void(0);"><i data-feather="eye"></i>&nbsp;&nbsp;Detail Mahasiswa</a>
                            <a onclick="_studentInvoiceDetailTableAction.generate()" class="dropdown-item" href="javascript:void(0);"><i data-feather="mail"></i>&nbsp;&nbsp;Generate Tagihan</a>
                            <a onclick="_studentInvoiceDetailTableAction.delete()" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete Tagihan</a>
                        </div>
                    </div>
                `
            },
        }
    }

    const _studentInvoiceDetailTableAction = {
        tableRef: _studentInvoiceDetailTable,
        detail: function(e) {
            const data = _studentInvoiceDetailTable.getRowData(e.currentTarget);

            Modal.show({
                type: 'detail',
                modalTitle: 'Detail Mahasiswa',
                modalSize: 'md',
                config: {
                    fields: {
                        fullname: {
                            title: 'Nama Lengkap',
                            content: {
                                template: ':text',
                                text: data.fullname,
                            },
                        },
                        nik: {
                            title: 'NIK',
                            content: {
                                template: ':text',
                                text: data.nik,
                            }
                        },
                        phone: {
                            title: 'No HP',
                            content: {
                                template: ':text',
                                text: data.phone,
                            }
                        },
                        birth: {
                            title: 'TTL',
                            content: {
                                template: ':birthplace, :birthday',
                                birthplace: data.birthplace,
                                birthday: data.birthday,
                            }
                        },
                        gender: {
                            title: 'Jenis Kelamin',
                            content: {
                                template: ':text',
                                text: data.gender == 'm' ? 'Laki-Laki' : (data.gender == 'f' ? 'Perempuan' : 'Tidak Diketahui'),
                            }
                        },
                        religion: {
                            title: 'Agama',
                            content: {
                                template: ':text',
                                text: data.religion,
                            }
                        },
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
