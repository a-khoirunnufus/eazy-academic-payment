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
    <table id="student-invoice-detail-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Nama / NIM</th>
                <th>Jalur Masuk / Jenis Perkuliahan</th>
                <th class="text-center">Status Mahasiswa</th>
                <th>Total Tagihan</th>
                <th class="text-center">Status Tagihan</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection


@section('js_section')
<script>
    $(function(){
        $.get(_baseURL + '/api/payment/generate/student-invoice/header?f={!! $data["f"] !!}&sp={!! $data["sp"] !!}', (d) => {
            $('#active').html(d.active);
            $('#faculty').html(d.faculty);
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
                    url: _baseURL+'/api/payment/generate/student-invoice/detail?f={!! $data["f"] !!}&sp={!! $data["sp"] !!}',
                },
                columns: [
                    {
                        name: 'action',
                        data: 'student_id',
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
                                    <span class="text-nowrap fw-bold">${(row.period) ? row.period.period_name : "-"}</span><br>
                                    <small class="text-nowrap text-secondary">${(row.lecture_type) ? row.lecture_type.mlt_name : "-"}</small>
                                </div>
                            `;
                        }
                    },
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
                $("#paymentDetail").append(`
                    <tr class="bg-light">
                        <td class="text-center fw-bolder">Eazy Service</td>
                        <td class="text-center" style="color:red!important">-${Rupiah.format({{ \App\Enums\Payment\FeeAmount::eazy }})}</td>
                    </tr>
                `);
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
