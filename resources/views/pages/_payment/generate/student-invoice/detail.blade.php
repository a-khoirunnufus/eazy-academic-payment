@extends('layouts.static_master')


@section('page_title', 'Detail Tagihan Mahasiswa Lama')
@section('sidebar-size', 'collapsed')
@section('url_back', route('payment.generate.student-invoice'))

@section('css_section')
    <style>
        .eazy-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }
    </style>
@endsection

@section('content')

@include('pages.generate._shortcuts', ['active' => null])
{{-- {{ dd($data) }} --}}
<div class="card">
    <div class="card-body">
        <div class="d-flex" style="gap: 2rem">
            <div class="flex-grow-1">
                <span class="text-secondary d-block" style="margin-bottom: 7px">Periode Masuk</span>
                <h5 class="fw-bolder" id="msy_year"></h5>
            </div>
            <div class="flex-grow-1">
                <span class="text-secondary d-block" style="margin-bottom: 7px">Periode Tagihan</span>
                <h5 class="fw-bolder" id="active"></h5>
            </div>
            <div class="flex-grow-1">
                <span class="text-secondary d-block" style="margin-bottom: 7px">Fakultas</span>
                <h5 class="fw-bolder" id="faculty"></h5>
            </div>
            <div class="flex-grow-1">
                <span class="text-secondary d-block" style="margin-bottom: 7px">Program Studi</span>
                <h5 class="fw-bolder" id="study_program"></h5>
            </div>
            {{-- <div class="flex-grow-1">
                <span class="text-secondary d-block" style="margin-bottom: 7px">Sistem Kuliah</span>
                <h5 class="fw-bolder">Reguler</h5>
            </div>
            <div class="flex-grow-1">
                <span class="text-secondary d-block" style="margin-bottom: 7px">Angkatan</span>
                <h5 class="fw-bolder">Angkatan 2021</h5>
            </div> --}}
        </div>
    </div>
</div>

<div class="card">
    <table id="student-invoice-detail-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Nama / NIM</th>
                <th>Jalur Masuk / Jenis Perkuliahan</th>
                <th>Total / Rincian Tagihan</th>
                <th>Status Mahasiswa</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection


@section('js_section')
<script>
    $(function(){
        $.get(_baseURL + '/api/payment/generate/student-invoice/header?msy={!! $data["msy"] !!}&f={!! $data["f"] !!}&sp={!! $data["sp"] !!}', (d) => {
            $('#active').html(d.active);
            $('#faculty').html(d.faculty);
            $('#msy_year').html(d.msy_year);
            $('#study_program').html(d.study_program);
        })
        _studentInvoiceDetailTable.init()
    })

    const _studentInvoiceDetailTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#student-invoice-detail-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/payment/generate/student-invoice/detail?msy={!! $data["msy"] !!}&f={!! $data["f"] !!}&sp={!! $data["sp"] !!}',
                },
                columns: [
                    {
                        name: 'action',
                        data: 'id',
                        orderable: false,
                        render: (data, _, row) => {
                            // console.log(row);
                            return this.template.rowAction(data)
                        }
                    },
                    {
                        name: 'student', 
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <span class="text-nowrap fw-bold">${row.fullname}</span><br>
                                    <small class="text-nowrap text-secondary">${row.student_id}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'lecture_type.mlt_name', 
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <span class="text-nowrap fw-bold">${(row.period.period_name) ? row.period.period_name : ""}</span><br>
                                    <small class="text-nowrap text-secondary">${row.lecture_type.mlt_name}</small>
                                </div>
                            `;
                        }
                    },
                    // {
                    //     name: 'invoice', 
                    //     render: (data, _, row) => {
                    //         return this.template.invoiceDetailCell(row.invoice_detail, row.invoice_total);
                    //     }    
                    // },
                    {
                        name: 'student_status', 
                        data: 'student_status',
                        render: (data) => {
                            var html = '<div class="d-flex justify-content-center">'
                            if(data == 'active') {
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
                            <h5>Detail Daftar Tagihan Mahasiswa Lama</h5>
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
                            <a onclick="_studentInvoiceDetailTableAction.detail(event)" class="dropdown-item" href="javascript:void(0);"><i data-feather="eye"></i>&nbsp;&nbsp;Detail Mahasiswa</a>
                            <a onclick="_studentInvoiceDetailTableAction.generate()" class="dropdown-item" href="javascript:void(0);"><i data-feather="mail"></i>&nbsp;&nbsp;Generate Tagihan</a>
                            <a onclick="_studentInvoiceDetailTableAction.delete()" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete Tagihan</a>
                        </div>
                    </div>
                `
            },
            invoiceDetailCell: function(invoiceItems, invoiceTotal) {
                let html = '<div class="d-flex flex-column" style="gap: .5rem">'
                html += `<div class="fw-bold text-nowrap">Total : ${Rupiah.format(invoiceTotal)}</div>`;
                html += '<div class="d-flex flex-row" style="gap: 1rem">';
                
                const minItemPerColumn = 2;
                const half = invoiceItems.length > minItemPerColumn ? Math.ceil(invoiceItems.length/2) : invoiceItems.length;
                let firstCol = invoiceItems.slice(0, half);
                firstCol = firstCol.map(item => {
                    return `
                        <div class="text-secondary text-nowrap">${item.name} : ${Rupiah.format(item.nominal)}</div>
                    `;
                }).join('');
                html += `<div class="d-flex flex-column" style="gap: .5rem">${firstCol}</div>`;

                if (half < invoiceItems.length) {
                    let secondCol = invoiceItems.slice(half);
                    secondCol = secondCol.map(item => {
                        return `
                            <div class="text-secondary text-nowrap">${item.name} : ${Rupiah.format(item.nominal)}</div>
                        `;
                    }).join('');
                    html += `<div class="d-flex flex-column" style="gap: .5rem">${secondCol}</div>`;
                }

                html += '</div></div>';
                return html;
            }
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
                                text: data.phone_number,
                            }
                        },
                        birth: {
                            title: 'TTL',
                            content: {
                                template: ':birthplace, :birthday',
                                birthplace: data.birthplace,
                                birthday: data.birthdate,
                            }
                        },
                        gender: {
                            title: 'Jenis Kelamin',
                            content: {
                                template: ':text',
                                text: data.gender,
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
