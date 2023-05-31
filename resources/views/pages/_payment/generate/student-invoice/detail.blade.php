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
        .new-student-invoice-filter {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            grid-gap: 1rem;
        }
        .nested-checkbox {
            list-style-type: none;
            padding-top: 2px;
            padding-left: 0px;
            font-size: 15px!important;
            font-weight: bold!important;
        }
        
        .nested-checkbox ul {
            list-style-type: none;
            padding-top: 3px;
            font-size: 15px!important;
            font-weight: bold!important;
        }

        .nested-checkbox ul li {
            list-style-type: none;
            padding-top: 3px;
            font-size: 15px!important;
            font-weight: bold!important;
        }

    </style>
@endsection

@section('content')

@include('pages._payment.generate._shortcuts', ['active' => null])
{{-- {{ dd($data) }} --}}
<div class="card">
    <div class="card-body">
        <div class="d-flex" style="gap: 2rem">
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
    <div class="card-body">
        <div class="new-student-invoice-filter">
            <div>
                <label class="form-label">Periode Pendaftaran</label>
                <select class="form-select" eazy-select2-active id="year-filter">
                    <option value="all" selected>Semua Periode Pendaftaran</option>
                    @foreach($year as $tahun)
                        <option value="{{ $tahun->msy_id }}">{{ $tahun->msy_year." ".($tahun->msy_semester%2 == 0 ? "GANJIL":"GENAP") }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Gelombang</label>
                <select class="form-select" eazy-select2-active id="period-filter">
                    <option value="all" selected>Semua Gelombang</option>
                    @foreach($period as $item)
                        <option value="{{ $item->period_id }}">{{ $item->period_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Jalur Pendaftaran</label>
                <select class="form-select" eazy-select2-active id="path-filter">
                    <option value="all" selected>Semua Jalur Pendaftaran</option>
                    @foreach($path as $item)
                        <option value="{{ $item->path_id }}">{{ $item->path_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex align-items-end">
                <button class="btn btn-primary" onclick="filters()">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="student-invoice-detail-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Nama -<br> NIM</th>
                <th>Tahun Masuk -<br> Jenis Perkuliahan</th>
                <th>Periode Masuk -<br> Jalur Masuk</th>
                <th>Total <br> Tagihan</th>
                <th class="text-center">Status <br> Tagihan</th>
                <th>Nama</th>
                <th>Nim</th>
                <th>Fakultas</th>
                <th>Prodi</th>
                <th>Tahun Masuk</th>
                <th>Periode Masuk</th>
                <th>Jalur Masuk</th>
                <th>Jenis Perkuliahan</th>
                <th>Status Mahasiswa</th>
                <th>Total Tagihan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection


@section('js_section')
<script>
    var dataTable = null;
    var header = null;
    $(function(){
        $.get(_baseURL + '/api/payment/generate/student-invoice/header?f={!! $data["f"] !!}&sp={!! $data["sp"] !!}', (d) => {
            $('#active').html(d.active);
            $('#faculty').html(d.faculty);
            $('#study_program').html(d.study_program);
            header = d;
        })
        _studentInvoiceDetailTable.init()
        dataTable.columns([6,7,8,9,10,11,12,13,14,15,16]).visible(false)
    })

    const _studentInvoiceDetailTable = {
        ..._datatable,
        init: function() {
            dataTable = this.instance = $('#student-invoice-detail-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/payment/generate/student-invoice/detail?f={!! $data["f"] !!}&sp={!! $data["sp"] !!}',
                    data: {
                        year: $('#year-filter').val(),
                        path: $('#path-filter').val(),
                        period: $('#period-filter').val()
                    },
                },
                columns: [
                    {
                        name: 'action',
                        data: 'student_id',
                        orderable: false,
                        render: (data, _, row) => {
                            console.log(row);
                            return this.template.rowAction(data)
                        }
                    },
                    {
                        name: 'student', 
                        render: (data, _, row) => {
                            let html = "";
                            if(row.student_type_id === 1) {
                                html += '<div class="badge bg-success" style="font-size: inherit">Aktif</div>'
                            } else {
                                html += '<div class="badge bg-danger" style="font-size: inherit">Tidak Aktif</div>'
                            }
                            return `
                                <div>
                                    <span class="text-nowrap fw-bold">${row.fullname} ${html}</span><br>
                                    <small class="text-nowrap text-secondary">${row.student_id}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'year.msy_year', 
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <span class="text-nowrap fw-bold">${(row.year) ? ((row.year.msy_semester === 1)? row.year.msy_year + " Genap" : row.year.msy_year + " Ganjil") : "-"}</span><br>
                                    <small class="text-nowrap text-secondary">${(row.lecture_type) ? row.lecture_type.mlt_name : "-"}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'period.period_name', 
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <span class="text-nowrap fw-bold">${(row.period) ? row.period.period_name : "-"}</span><br>
                                    <small class="text-nowrap text-secondary">${(row.path) ? row.path.path_name : "-"}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'payment.prr_id', 
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <a  onclick="_studentInvoiceDetailTableAction.detail(event)" href="javascript:void(0);" class="text-nowrap fw-bold">${(row.payment) ? Rupiah.format(row.payment.prr_total) : "-"}</a><br>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'payment.prr_id', 
                        render: (data, _, row) => {
                            var status = "";
                            if(row.payment){
                                if(row.payment.prr_status == 'lunas'){
                                    status = '<div class="badge bg-success" style="font-size: inherit">Lunas</div>'
                                }else{
                                    status = '<div class="badge bg-danger" style="font-size: inherit">Belum Lunas</div>'
                                }
                            }
                            return `
                                <div class="d-flex justify-content-center">
                                    ${status}
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'fullname',
                        data: 'fullname'
                    },
                    {
                        name: 'student_id', 
                        data: 'student_id'
                    },
                    {
                        name: 'study_program.faculty.faculty_name', 
                        data: 'study_program.faculty.faculty_name',
                        defaultContent: "-",
                    },
                    {
                        name: 'study_program.studyprogram_name', 
                        data: 'study_program.studyprogram_name',
                        render: (data, _, row) => {
                            return (row.study_program)? (row.study_program.studyprogram_type +' '+row.study_program.studyprogram_name) : '-';
                        }
                    },
                    {
                        name: 'year.msy_year', 
                        render: (data, _, row) => {
                            return (row.year) ? ((row.year.msy_semester === 1)? row.year.msy_year + " Genap" : row.year.msy_year + " Ganjil") : "-";
                        }
                    },
                    {
                        name: 'period.period_name', 
                        render: (data, _, row) => {
                            return (row.period)? row.period.period_name : '-';
                        }
                    },
                    {
                        name: 'path.path_name', 
                        render: (data, _, row) => {
                            return (row.path)? row.path.path_name : '-';
                        }
                    },
                    {
                        name: 'lecture_type.mlt_name', 
                        render: (data, _, row) => {
                            return (row.lecture_type)? row.lecture_type.mlt_name : '-';
                        }
                    },
                    {
                        name: 'student_status', 
                        data: 'student_status',
                        render: (data, _, row) => {
                            if(row.student_type_id === 1) {
                                return "Aktif";
                            } else {
                                return "Tidak Aktif"
                            }
                        }
                    },
                    {
                        name: 'payment.prr_id', 
                        render: (data, _, row) => {
                            return (row.payment) ? Rupiah.format(row.payment.prr_total) : "-";
                        }
                    },
                    {
                        name: 'payment.prr_id', 
                        render: (data, _, row) => {
                            if(row.payment){
                                if(row.payment.prr_status == 'lunas'){
                                    return "Lunas"
                                }else{
                                    return "Belum Lunas"
                                }
                            }else {
                                return ""
                            }
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
                buttons: [
                    {
                        extend: 'collection',
                        className: 'btn btn-outline-secondary dropdown-toggle',
                        text: feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + 'Export',
                        buttons: [
                            {
                                extend: 'print',
                                text: feather.icons['printer'].toSvg({class: 'font-small-4 me-50'}) + 'Print',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [6,7,8,9,10,11,12,13,14,15,16]
                                }
                            },
                            {
                                extend: 'csv',
                                text: feather.icons['file-text'].toSvg({class: 'font-small-4 me-50'}) + 'Csv',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [6,7,8,9,10,11,12,13,14,15,16]
                                }
                            },
                            {
                                extend: 'excel',
                                text: feather.icons['file'].toSvg({class: 'font-small-4 me-50'}) + 'Excel',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [6,7,8,9,10,11,12,13,14,15,16]
                                }
                            },
                            {
                                extend: 'pdf',
                                text: feather.icons['clipboard'].toSvg({class: 'font-small-4 me-50'}) + 'Pdf',
                                orientation: 'landscape',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [6,7,8,9,10,11,12,13,14,15,16]
                                }
                            },
                            {
                                extend: 'copy',
                                text: feather.icons['copy'].toSvg({class: 'font-small-4 me-50'}) + 'Copy',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [6,7,8,9,10,11,12,13,14,15,16]
                                }
                            }
                        ],
                    }
                ],
                initComplete: function() {
                    $('.student-invoice-detail-actions').html(`
                        <div style="margin-bottom: 7px">
                            <a onclick="_studentInvoiceDetailTableAction.generateForm()" class="btn btn-primary" href="javascript:void(0);">
                                <i data-feather="command"></i> Generate Tagihan Mahasiswa</a>
                            <a onclick="_studentInvoiceDetailTableAction.logGenerate()" class="btn btn-secondary" href="javascript:void(0);">
                            <i data-feather="book-open"></i> Log Generate</a>
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
                            <a onclick="_studentInvoiceDetailTableAction.generate(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="mail"></i>&nbsp;&nbsp;Generate Tagihan</a>
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
                modalSize: 'lg',
                config: {
                    fields: {
                        header: {
                            type: 'custom-field',
                            title: 'Data Mahasiswa',
                            content: {
                                template: `<div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3">
                                            <h6>Nama Lengkap</h6>
                                            <h1 class="h6 fw-bolder">${data.fullname}</h1>
                                        </div>
                                        <div class="col-lg-3 col-md-3">
                                            <h6>NIM</h6>
                                            <h1 class="h6 fw-bolder">${data.student_id}</h1>
                                        </div>
                                        <div class="col-lg-3 col-md-3">
                                            <h6>No Handphone</h6>
                                            <h1 class="h6 fw-bolder">${data.phone_number}</h1>
                                        </div>
                                        <div class="col-lg-3 col-md-3">
                                            <h6>Status Pembayaran</h6>
                                            <h1 class="h6 fw-bolder" id="statusPembayaran"></h1>
                                        </div>
                                    </div>
                                    <hr>
                                </div>`
                            },
                        },
                        tagihan: {
                            type: 'custom-field',
                            title: 'Detail Tagihan',
                            content: {
                                template: `
                                    <table class="table table-bordered" id="paymentDetail" style="line-height: 3">
                                        <tr class="bg-light">
                                            <th class="text-center">Komponen Tagihan</th>
                                            <th class="text-center">Harga</th>
                                        </tr>
                                        
                                    </table>
                                `
                            },
                        },
                        bill: {
                            type: 'custom-field',
                            title: 'Riwayat Transaksi',
                            content: {
                                template: `
                                    <table class="table table-bordered" id="paymentBill">
                                        <tr class="bg-light">
                                            <th class="text-center">Invoice ID</th>
                                            <th class="text-center">Expired Date</th>
                                            <th class="text-center">Amount</th>
                                            <th class="text-center">Fee</th>
                                            <th class="text-center">Paid Date</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </table>
                                `
                            },
                        },
                    },
                    callback: function() {
                        feather.replace();
                    }
                },
            });
            if(data.payment){
                
                // Status
                var status = "";
                if(data.payment){
                    if(data.payment.prr_status == 'lunas'){
                        status = '<div class="badge bg-success" style="font-size: inherit">Lunas</div>'
                    }else{
                        status = '<div class="badge bg-danger" style="font-size: inherit">Belum Lunas</div>'
                    }
                }
                $("#statusPembayaran").append(status);

                // Tagihan
                var total = 0;
                if (Object.keys(data.payment.payment_detail).length > 0) {
                    data.payment.payment_detail.map(item => {
                        total = total+item.prrd_amount;
                        _studentInvoiceDetailTableAction.rowDetail(item.prrd_component, item.prrd_amount,'paymentDetail');
                    });
                }
                $("#paymentDetail").append(`
                    <tr class="bg-light">
                        <td class="text-center fw-bolder">Total Tagihan</td>
                        <td class="text-center fw-bolder">${Rupiah.format(total)}</td>
                    </tr>
                `);
                // $("#paymentDetail").append(`
                //     <tr class="bg-light">
                //         <td class="text-center fw-bolder">Eazy Service</td>
                //         <td class="text-center" style="color:red!important">-${Rupiah.format({{ \App\Enums\Payment\FeeAmount::eazy }})}</td>
                //     </tr>
                // `);
                $("#paymentDetail").append(`
                    <tr style="background-color:#163485">
                        <td class="text-center fw-bolder" style="color:white!important">Total yang Diterima</td>
                        <td class="text-center fw-bolder" style="color:white!important">${Rupiah.format(data.payment.prr_paid_net)}</td>
                    </tr>
                `);

                var total_terbayar = 0;
                if (Object.keys(data.payment.payment_bill).length > 0) {
                    $("#paymentDetail").append(`
                        <tr class="bg-light">
                            <td class="text-center fw-bolder" colspan="2">Fee</th>
                        </tr>
                    `);
                    data.payment.payment_bill.map(item => {
                        if(item.prrb_status == "lunas"){
                            total_terbayar = total_terbayar + item.prrb_amount+item.prrb_admin_cost;
                        }
                        _studentInvoiceDetailTableAction.rowDetail('Biaya Transaksi - INV.'+item.prrb_invoice_num, item.prrb_admin_cost,'paymentDetail');
                    });
                }
                $("#paymentDetail").append(`
                    <tr style="background-color:#163485">
                        <td class="text-center fw-bolder" style="color:white!important">Total yang Harus Dibayarkan</td>
                        <td class="text-center fw-bolder" style="color:white!important">${Rupiah.format(data.payment.prr_total)}</td>
                    </tr>
                `);
                $("#paymentDetail").append(`
                    <tr class="bg-success">
                        <td class="text-center fw-bolder" style="color:white!important">Total Terbayar</td>
                        <td class="text-center fw-bolder" style="color:white!important">${Rupiah.format(total_terbayar)}</td>
                    </tr>
                `);


                // Transaksi
                if (Object.keys(data.payment.payment_bill).length > 0) {
                    data.payment.payment_bill.map(item => {
                        _studentInvoiceDetailTableAction.rowBill(item.prrb_id,item.prrb_expired_date, item.prrb_paid_date, item.prrb_amount, item.prrb_admin_cost, item.prrb_status,'paymentBill');
                    });
                }
            }
            
        },
        rowDetail(name,amount,id){
            $("#"+id+"").append(`
                <tr>
                    <td class="text-center fw-bolder">${name}</td>
                    <td class="text-center">${Rupiah.format(amount)}</td>
                </tr>
            `)
        },
        rowBill(inv_num,expired_date,paid_date,amount,fee,status,id){
            var stat = "";
            var expired = "";
            var paid = "";
            if(status == 'lunas'){
                stat = '<div class="badge badge-small bg-success" style="padding: 5px!important;">Lunas</div>';
                expired = '-';
                paid = (new Date(paid_date)).toLocaleString("id-ID");
            }else{
                stat = '<div class="badge bg-danger" style="padding: 5px!important;">Belum Lunas</div>';
                expired = (new Date(expired_date)).toLocaleString("id-ID");
                paid = '-';
            }
            $("#"+id+"").append(`
                <tr>
                    <td class="text-center fw-bolder">${inv_num}</td>
                    <td class="text-center">${expired}</td>
                    <td class="text-center">${Rupiah.format(amount)}</td>
                    <td class="text-center">${Rupiah.format(fee)}</td>
                    <td class="text-center">${paid}</td>
                    <td class="text-center">${stat}</td>
                </tr>
            `)
        },
        
        generate: function(e) {
            let data = _studentInvoiceDetailTable.getRowData(e);
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin generate tagihan mahasiswa '+data.fullname+' ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#356CFF',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Generate',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    let requestData = {
                        student_number: data.student_number
                    };
                    $.post(_baseURL + '/api/payment/generate/student-invoice/student', requestData, (data) => {
                        console.log(data);
                        data = JSON.parse(data)
                        Swal.fire({
                            icon: 'success',
                            text: data.message,
                        }).then(() => {
                            _studentInvoiceDetailTable.reload()
                        });
                    }).fail((error) => {
                        _responseHandler.generalFailResponse(error)
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
        generateForm: function() {
            Modal.show({
                type: 'form',
                modalTitle: 'Generate Tagihan Mahasiswa',
                modalSize: 'lg',
                config: {
                    formId: 'generateForm',
                    formActionUrl: _baseURL + '/api/payment/generate/student-invoice/bulk',
                    formType: 'add',
                    data: $("#generateForm").serialize(),
                    isTwoColumn: false,
                    fields: {
                        header: {
                            type: 'custom-field',
                            content: {
                                template: `<div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-4 col-md-4">
                                            <h6>Periode Tagihan</h6>
                                            <h1 class="h6 fw-bolder">${header.active}</h1>
                                        </div>
                                        <div class="col-lg-4 col-md-4">
                                            <h6>Fakultas</h6>
                                            <h1 class="h6 fw-bolder">${header.faculty}</h1>
                                        </div>
                                        <div class="col-lg-4 col-md-4">
                                            <h6>Program Studi</h6>
                                            <h1 class="h6 fw-bolder">${header.study_program}</h1>
                                        </div>
                                    </div>
                                    <hr>
                                </div>`
                            },
                        },
                        tagihan: {
                            type: 'custom-field',
                            content: {
                                template: `
                                <h4 class="fw-bolder mb-0">Konfirmasi Generate Tagihan <small class="fst-italic mb-0">(Centang checkbox untuk memilih)</small></h4>
                                <ul class="nested-checkbox">
                                    <li id="choice">
                                        <input type="checkbox" name="generate_checkbox[]" class="form-check-input" id="checkbox_header" /> ${header.study_program} <div class="badge" id="badge_header">Belum Digenerate</div>
                                    </li>
                                </ul>
                                `
                            },
                        },
                    },
                    formSubmitLabel: 'Generate Tagihan',
                    formSubmitNote: `
                    <small style="color:#163485">
                        *Pastikan Tagihan yang Ingin Anda Generate Sudah <strong>Sesuai</strong>
                    </small>`,
                    callback: function() {
                        feather.replace();
                    }
                },
            });
            var store = [];
            $.get(_baseURL + '/api/payment/generate/student-invoice/choice/{!! $data["f"] !!}/{!! $data["sp"] !!}', (data) => {
                console.log(data);
                if (Object.keys(data).length > 0) {
                    var total_student = 0;
                    var total_generate = 0;
                    data.map(item => {
                        var id = item.studyprogram_id+"_"+item.msy_id+"_"+item.path_id+"_"+item.period_id+"_"+item.mlt_id;
                        _studentInvoiceDetailTableAction.choiceRow(
                            'choice',
                            'msyId',
                            'msyId_'+item.msy_id,
                            item.msy_id+'_pathId',
                            'Tahun '+item.year.msy_year)

                        _studentInvoiceDetailTableAction.choiceRow(
                            item.msy_id+'_pathId',
                            item.msy_id+'_pathId',
                            item.msy_id+'_pathId_'+item.path_id,
                            item.msy_id+'_'+item.path_id+'_periodId',
                            item.path.path_name)

                        _studentInvoiceDetailTableAction.choiceRow(
                            item.msy_id+'_'+item.path_id+'_periodId',
                            item.msy_id+'_'+item.path_id+'_periodId',
                            item.msy_id+'_'+item.path_id+'_periodId_'+item.period_id,
                            item.msy_id+'_'+item.path_id+'_'+item.period_id+'_mltId',
                            item.period.period_name)
                            
                        _studentInvoiceDetailTableAction.choiceRow(
                            item.msy_id+'_'+item.path_id+'_'+item.period_id+'_mltId',
                            item.msy_id+'_'+item.path_id+'_'+item.period_id+'_mltId',
                            item.msy_id+'_'+item.path_id+'_'+item.period_id+'_mltId_'+item.mlt_id,
                            item.msy_id+'_'+item.path_id+'_'+item.period_id+'_'+item.mlt_id+'_end',
                            item.lecture_type.mlt_name,
                            item.total_student,
                            item.total_generate,
                            id)

                        // COUNTING 
                        // Period
                        let period = item.msy_id+'_'+item.path_id+'_periodId_'+item.period_id;
                        let student = item.total_student;
                        let generate = item.total_generate;
                        _studentInvoiceDetailTableAction.storeToArray(store,period,student,generate)
                        
                        // Path
                        let path = item.msy_id+'_pathId_'+item.path_id;
                        _studentInvoiceDetailTableAction.storeToArray(store,path,student,generate)

                        // Year
                        let year = 'msyId_'+item.msy_id
                        _studentInvoiceDetailTableAction.storeToArray(store,year,student,generate)

                        // Mlt
                        let mlt = item.msy_id+'_'+item.path_id+'_'+item.period_id+'_mltId_'+item.mlt_id;
                        _studentInvoiceDetailTableAction.storeToArray(store,mlt,student,generate)
                        
                        // Sum
                        total_student = total_student+student;
                        total_generate = total_generate+generate;
                    });

                    // Badges
                    for (let x of Object.keys(store)) {
                        let student = store[x]['student'];
                        let generate = store[x]['generate'];
                        $('#checkbox_'+x).attr('student',student);
                        $('#checkbox_'+x).attr('generate',generate);
                        _studentInvoiceDetailTableAction.badge(x,student,generate)
                        console.log(store[x]);
                    }

                    // Badges for master root
                    let student = total_student;
                    let generate = total_generate;
                    let x = "header";
                    _studentInvoiceDetailTableAction.badge(x,student,generate)
                }
            });
        },
        choiceRow(tag,grandparent,parent,child,data,total_student = 0,total_generate = 0, value = null){
            if(!$("#choice").find("[id='" + grandparent + "']")[0]){
                $('#'+tag).append(`
                    <ul id="${grandparent}">
                    </ul>
                `);
            }

            if(!$("#choice").find("[id='" + parent + "']")[0]){
                $('#'+grandparent).append(`
                    <li id="${parent}">
                        <input type="checkbox" class="form-check-input" name="generate_checkbox[]" id="checkbox_${parent}" student=${total_student} generate=${total_generate} value=${value} /> ${data} <div class="badge" id="badge_${parent}">${total_generate} / ${total_student}</div>
                        <ul id="${child}">
                        </ul>
                    </li>
                `);
            }
            
            $('li :checkbox').on('click', function () {
                console.log("hey");
                var $chk = $(this), $li = $chk.closest('li'), $ul, $parent;
                console.log($li);
                if ($li.has('ul')) {
                    $li.find(':checkbox').not(this).prop('checked', this.checked)
                }
                do {
                    $ul = $li.parent();
                    $parent = $ul.siblings(':checkbox');
                    if ($chk.is(':checked')) {
                        $parent.prop('checked', $ul.has(':checkbox:not(:checked)').length == 0)
                    } else {
                        $parent.prop('checked', false)
                    }
                    $chk = $parent;
                    $li = $chk.closest('li');
                } while ($ul.is(':not(.someclass)'));
            });
            
        },
        badge(x,student,generate){
            if(generate == 0){
                $('#badge_'+x).addClass('bg-danger');
                $('#badge_'+x).html('Belum Digenerate ('+ generate + '/' + student + ')');
            }else if(generate < student){
                $('#badge_'+x).addClass('bg-warning');
                $('#badge_'+x).html('Sebagian Telah Digenerate ('+ generate + '/' + student + ')');
            }else{
                $('#badge_'+x).addClass('bg-success');
                $('#badge_'+x).html('Sudah Digenerate ('+ generate + '/' + student + ')');
            }
        },
        storeToArray(store,key,student,generate){
            if(store[key]){
                store[key] = {'student' : store[key]['student']+student, 'generate' : store[key]['generate']+generate}
            }else{
                store[key] = {'student' : student, 'generate' : generate}
            }
        },
        logGenerate: function(e) {
            Modal.show({
                type: 'detail',
                modalTitle: 'Detail Mahasiswa',
                modalSize: 'lg',
                config: {
                    fields: {
                        header: {
                            type: 'custom-field',
                            title: 'Data Mahasiswa',
                            content: {
                                template: `
                                <div>
                                    <div class="bg-white">
                                        <div class="p-4 border-b">
                                        <div class="flex items-center mb-1">
                                            <h1 class="text-xl mr-4">Ini adalah contoh pesan</h1>
                                            <span class="badge bg-success">Finished</span>
                                        </div>
                                        <p class="text-sm text-gray-600">
                                            Deployed to <b>Server</b> by
                                            <b>Hafizh</b>
                                        </p>
                                        </div>
                                        <div class="border-b">
                                        <div class="flex justify-between items-center p-2 px-4">
                                            <div class="flex items-center">
                                            <span class="text-gray-700 text-sm">Test Label</span>
                                            </div>

                                            <div class="flex items-center">
                                            <span class="text-sm mr-2 text-gray-600"
                                                >2m 30s</span
                                            >
                                            <span class="badge bg-success">Finished</span>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                `
                            },
                        },
                        tagihan: {
                            type: 'custom-field',
                            title: 'Detail Tagihan',
                            content: {
                                template: `
                                    <table class="table table-bordered" id="paymentDetail" style="line-height: 3">
                                        <tr class="bg-light">
                                            <th class="text-center">Komponen Tagihan</th>
                                            <th class="text-center">Harga</th>
                                        </tr>
                                        
                                    </table>
                                `
                            },
                        },
                        bill: {
                            type: 'custom-field',
                            title: 'Riwayat Transaksi',
                            content: {
                                template: `
                                    <table class="table table-bordered" id="paymentBill">
                                        <tr class="bg-light">
                                            <th class="text-center">Invoice ID</th>
                                            <th class="text-center">Expired Date</th>
                                            <th class="text-center">Amount</th>
                                            <th class="text-center">Fee</th>
                                            <th class="text-center">Paid Date</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </table>
                                `
                            },
                        },
                    },
                    callback: function() {
                        feather.replace();
                    }
                },
            });
        },
    }

    function filters(){
        dataTable.destroy();
        _studentInvoiceDetailTable.init();
    }
</script>
@endsection
