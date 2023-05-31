@extends('layouts.static_master')


@section('page_title', 'Detail Tagihan Mahasiswa Baru')
@section('sidebar-size', 'collapsed')
@section('url_back', route('payment.generate.student-invoice'))

@section('css_section')
    <style>
        .eazy-table-wrapper {
            min-height: 300px;
            width: 100%;
            overflow-x: auto;
        }

        .table-info {
            display: inline-block;
        }
        .table-info td {
            padding: 10px 0;
        }
        .table-info td:first-child {
            padding-right: 1rem;
        }
    </style>
@endsection

@section('content')

@include('pages._payment.generate._shortcuts', ['active' => 'new-student-invoice'])

<div class="card">
    <div class="card-body">
        <div class="d-flex" style="gap: 2rem">
            <div class="flex-grow-1">
                <span class="text-secondary d-block" style="margin-bottom: 7px">Periode Tagihan</span>
                <h5 class="fw-bolder" id="info-invoice-period">N/A</h5>
            </div>
            @if($faculty)
                <div class="flex-grow-1">
                    <span class="text-secondary d-block" style="margin-bottom: 7px">Fakultas</span>
                    <h5 class="fw-bolder" id="info-faculty">{{ $faculty->faculty_name }}</h5>
                </div>
            @endif
            @if($studyprogram)
                <div class="flex-grow-1">
                    <span class="text-secondary d-block" style="margin-bottom: 7px">Program Studi</span>
                    <h5 class="fw-bolder" id="info-studyprogram">{{ strtoupper($studyprogram->studyprogram_type).' '.$studyprogram->studyprogram_name }}</h5>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="card">
    <table id="student-invoice-detail-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Nama / NIK</th>
                <th>Jalur / Periode Pendaftaran</th>
                <th>Program Studi / Jenis Perkuliahan</th>
                <th>Jenis Perkuliahan</th>
                <th class="text-center">Status Tagihan</th>
                <th>Jumlah Tagihan</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Invoice Detail Modal -->
<div class="modal fade" id="invoiceDetailModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-white" style="padding: 2rem 3rem 3rem 3rem">
                <h4 class="modal-title fw-bolder" id="invoiceDetailModalLabel">Detail Mahasiswa dan Tagihan</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 pt-0">
                <div id="student-data" class="mb-3">
                    <h4 class="fw-bolder mb-1">Data Mahasiwa</h4>
                    <div class="d-flex flex-row justify-content-between mb-4" style="gap: 2rem">
                        <div class="d-flex flex-column" style="gap: 5px">
                            <small class="d-block">Nama Lengkap dan NIK</small>
                            <span class="fw-bolder" id="detail-student-fullname">...</span>
                            <span class="text-secondary d-block" id="detail-student-nik">...</span>
                        </div>
                        <div class="d-flex flex-column" style="gap: 5px">
                            <small class="d-block">Jalur dan Periode Pendaftaran</small>
                            <span class="fw-bolder" id="detail-path">...</span>
                            <span class="text-secondary d-block" id="detail-period">...</span>
                        </div>
                        <div class="d-flex flex-column" style="gap: 5px">
                            <small class="d-block">Program Studi dan Jenis Perkuliahan</small>
                            <span class="fw-bolder" id="detail-studyprogram">...</span>
                            <span class="text-secondary d-block" id="detail-lecture-type">...</span>
                        </div>
                    </div>
                </div>

                <div id="invoice-data" class="mb-3">
                    <h4 class="fw-bolder mb-1">Data Invoice</h4>
                    <table class="table-info">
                        <tr>
                            <td>Nomor Invoice</td>
                            <td>
                                <span class="fw-bold">:&nbsp;&nbsp;<span id="detail-invoice-number">...</span><span>
                            </td>
                        </tr>
                        <tr>
                            <td>Digenerate Pada</td>
                            <td>
                                <span class="fw-bold">:&nbsp;&nbsp;<span id="detail-invoice-created">...</span></span>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="invoice-detail">
                    <h4 class="fw-bolder mb-2">Rincian Tagihan</h4>
                    <table id="table-detail-invoice-component" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Komponen Tagihan</th>
                                <th>Jumlah Tagihan</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th>Total Jumlah Tagihan (Ditambah Biaya Admin)</th>
                                <th id="detail-invoice-amount">...</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('js_section')
<script>
    DataTable.render.ellipsis = function ( cutoff ) {
        return function ( data, type, row ) {
            if ( type === 'display' ) {
                var str = data.toString(); // cast numbers

                return str.length < cutoff ?
                    str :
                    str.substr(0, cutoff-1) +'&#8230;';
            }

            // Search, order and type can use the original data
            return data;
        };
    };

    const scope = "{{ $scope }}";
    const facultyId = "{{ $faculty ? $faculty->faculty_id : 0 }}";
    const studyprogramId = "{{ $studyprogram ? $studyprogram->studyprogram_id : 0 }}";

    $(function(){
        _newStudentInvoiceDetailTable.init()
    })

    const _newStudentInvoiceDetailTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#student-invoice-detail-table').DataTable({
                serverSide: true,
                ajax: {
                    url: `${_baseURL}/api/payment/generate/new-student-invoice/detail`
                        +`?scope=${scope}${scope == 'faculty' ? '&faculty_id='+facultyId : '&studyprogram_id='+studyprogramId}`,
                },
                stateSave: false,
                columns: [
                    {
                        name: 'action',
                        data: 'participant_id',
                        orderable: false,
                        searchable: false,
                        render: (data, _, row) => {
                            return this.template.rowAction(data)
                        }
                    },
                    {
                        name: 'participant_fullname',
                        data: 'participant_fullname',
                        render: (data, _, row) => {
                            return this.template.titleWithSubtitleCell(row.participant_fullname, row.participant_nik);
                        }
                    },
                    {
                        name: 'registration_path_name',
                        data: 'registration_path_name',
                        render: (data, _, row) => {
                            return this.template.titleWithSubtitleCell(row.registration_path_name, row.registration_period_name);
                        }
                    },
                    {
                        name: 'studyprogram_name',
                        data: 'studyprogram_name',
                        visible: scope != 'studyprogram',
                        render: (data, _, row) => {
                            return this.template.titleWithSubtitleCell(
                                row.studyprogram_name ?? 'N/A',
                                row.lecture_type_name ?? 'N/A'
                            );
                        }
                    },
                    {
                        name: 'lecture_type_name',
                        data: 'lecture_type_name',
                        visible: scope != 'all' && scope != 'faculty',
                        render: (data) => {
                            return this.template.defaultCell(data ?? 'N/A');
                        }
                    },
                    {
                        name: 'invoice_status',
                        data: 'invoice_status',
                        render: (data) => {
                            return this.template.badgeCell(
                                data,
                                data == 'Sudah Digenerate' ? 'success' : 'secondary'
                            );
                        }
                    },
                    {
                        name: 'invoice_amount',
                        data: 'invoice_amount',
                        render: (data) => {
                            return this.template.currencyCell(data);
                        }
                    },
                    // search and export columns
                    {
                        title: 'Nama Mahasiswa',
                        name: 'participant_fullname',
                        data: 'participant_fullname',
                        visible: false,
                    },
                    {
                        title: 'NIK Mahasiswa',
                        name: 'participant_nik',
                        data: 'participant_nik',
                        visible: false,
                    },
                    {
                        title: 'Jalur Pendaftaran',
                        name: 'registration_path_name',
                        data: 'registration_path_name',
                        visible: false,
                    },
                    {
                        title: 'Periode Pendaftaran',
                        name: 'registration_period_name',
                        data: 'registration_period_name',
                        visible: false,
                    },
                    {
                        title: 'Program Studi',
                        name: 'studyprogram_name',
                        data: 'studyprogram_name',
                        visible: false,
                    },
                    {
                        title: 'Jenis Perkuliahan',
                        name: 'lecture_type_name',
                        data: 'lecture_type_name',
                        visible: false,
                    },
                    {
                        title: 'Jumlah Tagihan',
                        name: 'invoice_amount',
                        data: 'invoice_amount',
                        visible: false,
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
                                    columns: [7,8,9,10,11,12,5,6]
                                }
                            },
                            {
                                extend: 'csv',
                                text: feather.icons['file-text'].toSvg({class: 'font-small-4 me-50'}) + 'Csv',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [7,8,9,10,11,12,5,13]
                                }
                            },
                            {
                                extend: 'excel',
                                text: feather.icons['file'].toSvg({class: 'font-small-4 me-50'}) + 'Excel',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [7,8,9,10,11,12,5,13]
                                }
                            },
                            {
                                extend: 'pdf',
                                text: feather.icons['clipboard'].toSvg({class: 'font-small-4 me-50'}) + 'Pdf',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [7,8,9,10,11,12,5,6]
                                }
                            },
                            {
                                extend: 'copy',
                                text: feather.icons['copy'].toSvg({class: 'font-small-4 me-50'}) + 'Copy',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [7,8,9,10,11,12,5,13]
                                }
                            }
                        ],
                    }
                ],
                initComplete: function() {
                    $('.student-invoice-detail-actions').html(`
                        <div style="margin-bottom: 7px">
                            <h5>Detail Daftar Tagihan Mahasiswa Lama</h5>
                        </div>
                    `)
                    feather.replace()
                }
            });
            this.implementSearchDelay();
        },
        template: {
            rowAction: function(participant_id) {
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a onclick="_invoiceDetailModalActions.open(event)" class="dropdown-item"><i data-feather="eye"></i>&nbsp;&nbsp;Detail Mahasiswa & Tagihan</a>
                            <a onclick="_newStudentInvoiceDetailTableAction.generate(this)" class="dropdown-item disabled"><i data-feather="mail"></i>&nbsp;&nbsp;Generate Tagihan</a>
                            <a onclick="_newStudentInvoiceDetailTableAction.delete()" class="dropdown-item disabled"><i data-feather="trash"></i>&nbsp;&nbsp;Delete Tagihan</a>
                        </div>
                    </div>
                `
            },
            titleWithSubtitleCell: _datatableTemplates.titleWithSubtitleCell,
            defaultCell: _datatableTemplates.defaultCell,
            badgeCell: _datatableTemplates.badgeCell,
            currencyCell: _datatableTemplates.currencyCell,
        }
    }

    const _newStudentInvoiceDetailTableAction = {
        tableRef: _newStudentInvoiceDetailTable,
        detail: function(e) {
            return;
            const data = _newStudentInvoiceDetailTable.getRowData(e.currentTarget);
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
                        _newStudentInvoiceDetailTableAction.rowDetail(item.prrd_component, item.prrd_amount,'paymentDetail');
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
                        _newStudentInvoiceDetailTableAction.rowDetail('Biaya Transaksi - INV.'+item.prrb_invoice_num, item.prrb_admin_cost,'paymentDetail');
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
                        _newStudentInvoiceDetailTableAction.rowBill(item.prrb_id,item.prrb_expired_date, item.prrb_paid_date, item.prrb_amount, item.prrb_admin_cost, item.prrb_status,'paymentBill');
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
            let data = _newStudentInvoiceDetailTable.getRowData(e);
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
                            _newStudentInvoiceDetailTable.reload()
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
    }

    const InvoiceDetailModal = new bootstrap.Modal(document.getElementById('invoiceDetailModal'));

    const _invoiceDetailModalActions = {
        open: async function(e) {
            this.resetData();

            const studentData = _newStudentInvoiceDetailTable.getRowData(e.currentTarget);
            const {data: invoiceData} = await $.ajax({
                async: true,
                url: _baseURL+'/api/payment/generate/new-student-invoice/show-invoice/'+studentData.payment_re_register_id,
                type: 'get',
            });
            const {data: invoiceComponentData} = await $.ajax({
                async: true,
                url: _baseURL+'/api/payment/generate/new-student-invoice/show-invoice-component/'+studentData.payment_re_register_id,
                type: 'get',
            });

            // Student Data
            $('#detail-student-fullname').text(studentData.participant_fullname);
            $('#detail-student-nik').text(studentData.participant_nik);
            $('#detail-path').text(studentData.registration_path_name);
            $('#detail-period').text(studentData.registration_period_name);
            $('#detail-studyprogram').text(studentData.studyprogram_name);
            $('#detail-lecture-type').text(studentData.lecture_type_name);

            // Invoice Data
            $('#detail-invoice-number').text(invoiceData.prr_id);
            $('#detail-invoice-created').text(moment(invoiceData.created_at).format('DD-MM-YYYY'));

            // Invoice Componen Data
            $('#table-detail-invoice-component tbody').html(`
                ${
                    invoiceComponentData.map((item) => {
                        return `
                            <tr>
                                <td>${item.prrd_component}</td>
                                <td>${Rupiah.format(item.prrd_amount)}</td>
                            </tr>
                        `;
                    }).join('')
                }
            `);
            $('#detail-invoice-amount').text(Rupiah.format(studentData.invoice_amount));


            InvoiceDetailModal.show();
        },
        resetData: function() {
            $('#detail-student-fullname').text('...');
            $('#detail-student-nik').text('...');
            $('#detail-path').text('...');
            $('#detail-period').text('...');
            $('#detail-studyprogram').text('...');
            $('#detail-lecture-type').text('...');
            $('#detail-invoice-number').text('...');
            $('#detail-invoice-created').text('...');
            $('#table-detail-invoice-component tbody').html('');
            $('#detail-invoice-amount').text('...');
        }
    }

</script>
@endsection
