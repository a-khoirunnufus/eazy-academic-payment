@extends('layouts.static_master')

@section('page_title', 'Approval Pembayaran Manual')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
<style>
    .eazy-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }
</style>
@endsection

@section('content')

@include('pages._payment.approval._shortcuts', ['active' => 'manual-payment'])

<div class="card">
    <div class="card-body">
        <div class="datatable-filter one-row">
            <div>
                <label class="form-label">Status</label>
                <select id="filter-status" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Status</option>
                    <option value="waiting">Menunggu Approval</option>
                    <option value="accepted">Diterima</option>
                    <option value="rejected">Ditolak</option>
                </select>
            </div>
            <div>
                <label class="form-label">Tipe Mahasiswa</label>
                <select id="filter-student-type" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Tipe Mahasiswa</option>
                    <option value="new_student">Mahasiswa Baru</option>
                    <option value="student">Mahasiswa Lama</option>
                </select>
            </div>
            <div>
                <label class="form-label">Jalur</label>
                <select id="path" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Jalur</option>
                    @foreach($path as $item)
                    <option value="{{$item->path_id}}">{{$item->path_name}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Periode</label>
                <select id="period" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Periode</option>
                    @foreach($period as $item)
                    <option value="{{$item->period_id}}">{{$item->period_name}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Fakultas</label>
                <select id="faculty" class="form-select" eazy-select2-active onchange="getProdi(this.value)">
                    <option value="#ALL" selected>Semua Fakultas</option>
                    @foreach($faculty as $item)
                    <option value="{{$item->faculty_id}}">{{$item->faculty_name}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Program Studi</label>
                <select id="prodi" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Program Studi</option>
                </select>
            </div>
            <div class="d-flex align-items-end">
                <button onclick="_paymentApprovalTable.reload()" class="btn btn-primary text-nowrap">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="table-payment-approval" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Nama - NIM - Tipe Mahasiswa</th>
                <th>Program Studi - Jenis Perkuliahan</th>
                <th>Tahun Masuk - Periode Masuk - Jalur Masuk</th>
                <th>Nominal Pembayaran</th>
                <th>Ringkasan Pembayaran</th>
                <th>Waktu Pengajuan</th>
                <th>Diproses Pada</th>
                <th>Status Approval</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="paymentApprovalModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-white" style="padding: 2rem 3rem 3rem 3rem">
                <h4 class="modal-title fw-bolder" id="paymentApprovalModalLabel">Approval Pembayaran</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 pt-0">
                <table id="table-payment-detail" class="table table-striped mb-2">
                    <tbody></tbody>
                </table>
                <div class="mb-2">
                    <label class="form-label">Catatan Approval</label>
                    <textarea id="textarea-approval-notes" class="form-control" rows="3"></textarea>
                </div>
                <div class="d-flex flex-row" style="gap: 3rem;">
                    <div class="flex-grow-1">
                        <button id="btn-accept-payment" onclick="_paymentApprovalTableAction.processApproval(event)" data-eazy-status="accepted" data-eazy-pmaId="" class="btn btn-success w-100">Terima</button>
                    </div>
                    <div class="flex-grow-1">
                        <button id="btn-reject-payment" onclick="_paymentApprovalTableAction.processApproval(event)" data-eazy-status="rejected" data-eazy-pmaId="" class="btn btn-danger w-100">Tolak</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@section('js_section')

<script>
    // var dt = null;
    $(function() {
        _paymentApprovalTable.init();
        select2Replace();
        // for (var i = 8; i <= 15; i++) {
        //     dt.column(i).visible(false)
        // }
    });

    const _paymentApprovalTable = {
        ..._datatable,
        init: function(search = '') {
            this.instance = $('#table-payment-approval').DataTable({
                serverSide: true,
                ajax: {
                    url: `${_baseURL}/api/payment/approval-manual-payment`,
                    // data: function(d) {
                    //     d.custom_filters = {
                    //         'status': $('select#filter-status').val(),
                    //         'student_type': $('select#filter-student-type').val(),
                    //         'path': $('select#path').val(),
                    //         'period': $('select#period').val(),
                    //         'faculty': $('select#faculty').val(),
                    //         'prodi': $('select#prodi').val(),
                    //     };
                    // },
                    // dataSrc: function(json) {
                    //     var data = [];
                    //     if (search != '') {
                    //         for (var i = 0; i < json.data.length; i++) {
                    //             var isFound = false;

                    //             var name = '' + json.data[i].student_name;
                    //             if (!isFound && (name.toLowerCase().search(search.toLowerCase()) >= 0)) {
                    //                 data.push(json.data[i]);
                    //                 isFound = true;
                    //             }

                    //             var student_type = '' + json.data[i].student_type == 'new_student' ? 'Mahasiswa Baru' : 'Mahasiswa Lama';
                    //             if (!isFound && (student_type.toLowerCase().search(search.toLowerCase()) >= 0)) {
                    //                 data.push(json.data[i]);
                    //                 isFound = true;
                    //             }

                    //             var participant = '' + json.data[i].par_number ?? '-';
                    //             if (!isFound && (participant.toLowerCase().search(search.toLowerCase()) >= 0)) {
                    //                 data.push(json.data[i]);
                    //                 isFound = true;
                    //             }

                    //             var nim = '' + json.data[i].student_id ?? '-';
                    //             if (!isFound && (nim.toLowerCase().search(search.toLowerCase()) >= 0)) {
                    //                 data.push(json.data[i]);
                    //                 isFound = true;
                    //             }

                    //             var bill = '' + json.data[i].bill_total;
                    //             if (!isFound && (bill.toLowerCase().search(search.toLowerCase()) >= 0)) {
                    //                 data.push(json.data[i]);
                    //                 isFound = true;
                    //             }

                    //             var bank = '' + json.data[i].bank_name;
                    //             if (!isFound && (bank.toLowerCase().search(search.toLowerCase()) >= 0)) {
                    //                 data.push(json.data[i]);
                    //                 isFound = true;
                    //             }

                    //             var sender = '' + json.data[i].sender_name;
                    //             if (!isFound && (sender.toLowerCase().search(search.toLowerCase()) >= 0)) {
                    //                 data.push(json.data[i]);
                    //                 isFound = true;
                    //             }

                    //             var bank_number = '' + json.data[i].sender_account_number;
                    //             if (!isFound && (bank_number.toLowerCase().search(search.toLowerCase()) >= 0)) {
                    //                 data.push(json.data[i]);
                    //                 isFound = true;
                    //             }

                    //             var status = '';
                    //             switch (json.data[i].approval_status) {
                    //                 case 'waiting':
                    //                     status = 'Menunggu Approval'
                    //                     break;
                    //                 case 'accepted':
                    //                     status = 'Diterima'
                    //                     break;
                    //                 case 'rejected':
                    //                     status = 'Ditolak'
                    //                     break;
                    //                 default:
                    //                     status = 'N/A';
                    //                     break;
                    //             }
                    //             if (!isFound && (status.toLowerCase().search(search.toLowerCase()) >= 0)) {
                    //                 data.push(json.data[i]);
                    //                 isFound = true;
                    //             }
                    //         }
                    //         return json.data = data;
                    //     } else {
                    //         return json.data;
                    //     }
                    // }
                },
                stateSave: false,
                columns: [
                    {
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: (data, _, row) => {
                            return this.template.rowAction(row.pma_approval_status);
                        }
                    },
                    {
                        name: 'student_data',
                        render: (data, _, row) => {
                            return this.template.listCell([
                                {text: row.pma_student_name, bold: true, small: false, nowrap: true},
                                {text: row.pma_student_id ?? '-', bold: false, small: true, nowrap: true},
                                {text: row.pma_student_type, bold: false, small: true, nowrap: true},
                            ]);
                        }
                    },
                    {
                        name: 'studyprogram_data',
                        render: (data, _, row) => {
                            return this.template.listCell([
                                {text: row.pma_student_studyprogram, bold: true, small: false, nowrap: true},
                                {text: row.pma_student_lecturetype, bold: false, small: true, nowrap: true},
                            ]);
                        }
                    },
                    {
                        name: 'registration_data',
                        render: (data, _, row) => {
                            return this.template.listCell([
                                {text: row.pma_student_reg_year, bold: true, small: false, nowrap: true},
                                {text: row.pma_student_reg_period, bold: false, small: true, nowrap: true},
                                {text: row.pma_student_reg_path, bold: false, small: true, nowrap: true},
                            ]);
                        }
                    },
                    {
                        name: 'amount',
                        data: 'pma_amount',
                        render: (data, _, row) => {
                            return this.template.currencyCell(data);
                        }
                    },
                    {
                        name: 'payment_data',
                        render: (data, _, row) => {
                            return this.template.listCell([
                                {text: 'Nama Pengirim: '+row.pma_sender_account_name, bold: false, small: false, nowrap: true},
                                {text: 'Norek Pengirim: '+row.pma_sender_account_number, bold: false, small: false, nowrap: true},
                                {text: 'Bank Pengirim: '+row.pma_sender_bank, bold: false, small: false, nowrap: true},
                            ]);
                        }
                    },
                    {
                        name: 'created_at',
                        data: 'created_at',
                        render: (data, _, row) => {
                            return this.template.dateTimeCell(data);
                        }
                    },
                    {
                        name: 'processed_at',
                        data: 'pma_processed_at',
                        render: (data, _, row) => {
                            return (data ? this.template.dateTimeCell(data) : '-');
                        }
                    },
                    {
                        name: 'approval_status',
                        data: 'pma_approval_status',
                        render: (data, _, row) => {
                            return this.template.badgeCell(
                                data == 'waiting' ? 'Menunggu Approval' :
                                data == 'accepted' ? 'Diterima' :
                                data == 'rejected' ? 'Ditolak' : 'N/A',
                                data == 'waiting' ? 'warning' :
                                data == 'accepted' ? 'success' :
                                data == 'rejected' ? 'danger' : 'secondary',
                            );
                        }
                    },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom: '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"manual-payment-actions d-flex align-items-end">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" <"search-filter">lB> >' +
                    '>' +
                    '<"eazy-table-wrapper" t>' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                // buttons: [{
                //     extend: 'collection',
                //     text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-external-link font-small-4 me-50"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>Export</span>',
                //     className: 'btn btn-outline-secondary dropdown-toggle',
                //     buttons: [{
                //             text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clipboard font-small-4 me-50"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>Pdf</span>',
                //             className: 'dropdown-item',
                //             extend: 'pdf',
                //             exportOptions: {
                //                 columns: [8, 9, 10, 11, 12, 13, 14, 15]
                //             }
                //         },
                //         {
                //             text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file font-small-4 me-50"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>Excel</span>',
                //             className: 'dropdown-item',
                //             extend: 'excel',
                //             exportOptions: {
                //                 columns: [8, 9, 10, 11, 12, 13, 14, 15]
                //             }
                //         },
                //         {
                //             text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text font-small-4 me-50"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>Csv</span>',
                //             className: 'dropdown-item',
                //             extend: 'csv',
                //             exportOptions: {
                //                 columns: [8, 9, 10, 11, 12, 13, 14, 15]
                //             }
                //         },
                //         {
                //             text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-copy font-small-4 me-50"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>Copy</span>',
                //             className: 'dropdown-item',
                //             extend: 'copy',
                //             exportOptions: {
                //                 columns: [8, 9, 10, 11, 12, 13, 14, 15]
                //             }
                //         }
                //     ]
                // }, ],
                initComplete: function() {
                    // $('.search-filter').html(`
                    //     <div id="table-payment-approval_filter" class="dataTables_filter">
                    //         <label>
                    //             <input type="search" class="form-control" placeholder="Cari Data" aria-controls="table-payment-approval" onkeydown="searchFilter(this, event)">
                    //         </label>
                    //     </div>
                    // `)
                    feather.replace();
                }
            });
            this.implementSearchDelay();
        },
        template: {
            rowAction: function(approvalStatus) {
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a
                                onclick="_paymentApprovalTableAction.openModalProcessApproval(event)"
                                class="dropdown-item ${approvalStatus != 'waiting' ? 'disabled' : ''}"
                            >
                                <i data-feather="check-circle"></i>&nbsp;&nbsp;Proses Approval
                            </a>
                            <a
                                onclick="_paymentApprovalTableAction.openModalDetailApproval(event)"
                                class="dropdown-item"
                            >
                                <i data-feather="eye"></i>&nbsp;&nbsp;Detail Approval
                            </a>
                        </div>
                    </div>
                `;
            },
            defaultCell: _datatableTemplates.defaultCell,
            badgeCell: _datatableTemplates.badgeCell,
            currencyCell: _datatableTemplates.currencyCell,
            listCell: _datatableTemplates.listCell,
            dateTimeCell: _datatableTemplates.dateTimeCell,
        }
    }

    const _paymentApprovalTableAction = {
        tableRef: _paymentApprovalTable,
        openModalProcessApproval: function(e) {
            const data = this.tableRef.getRowData(e.currentTarget);

            $('#paymentApprovalModal #table-payment-detail tbody').html(`
                <tr>
                    <td style="width: 300px;">Nama Rekening Pengirim</td>
                    <td>${data.pma_sender_account_name}</td>
                </tr>
                <tr>
                    <td style="width: 300px;">Nomor Rekening Pengirim</td>
                    <td>${data.pma_sender_account_number}</td>
                </tr>
                <tr>
                    <td style="width: 300px;">Bank Rekening Pengirim</td>
                    <td>${data.pma_sender_bank}</td>
                </tr>
                <tr>
                    <td style="width: 300px;">Nominal yang dibayar</td>
                    <td>${Rupiah.format(data.pma_amount)}</td>
                </tr>
                <tr>
                    <td style="width: 300px;">Nama Rekening Penerima</td>
                    <td>${data.pma_receiver_account_name}</td>
                </tr>
                <tr>
                    <td style="width: 300px;">Nomor Rekening Penerima</td>
                    <td>${data.pma_receiver_account_number}</td>
                </tr>
                <tr>
                    <td style="width: 300px;">Bank Rekening Penerima</td>
                    <td>${data.pma_receiver_bank}</td>
                </tr>
                <tr>
                    <td style="width: 300px;">Waktu Pembayaran</td>
                    <td>${data.pma_payment_time}</td>
                </tr>
                <tr>
                    <td style="width: 300px;">File Bukti Pembayaran</td>
                    <td>
                        <a href="${_baseURL+'/api/download-cloud?path='+data.pma_evidence}" class="p-0 btn btn-link btn-sm">
                            <i data-feather="download"></i>&nbsp;&nbsp;
                            Download Bukti Pembayaran
                        </a>
                    </td>
                </tr>
            `);
            feather.replace();

            $('#paymentApprovalModal #btn-accept-payment').attr('data-eazy-pmaId', data.pma_id);
            $('#paymentApprovalModal #btn-reject-payment').attr('data-eazy-pmaId', data.pma_id);

            _paymentApprovalModal.show();
        },
        openModalDetailApproval: function(e) {
            const approval = this.tableRef.getRowData(e.currentTarget);

            Modal.show({
                type: 'detail',
                modalTitle: 'Detail Approval Pembayaran',
                modalSize: 'lg',
                config: {
                    isTwoColumn: true,
                    fields: {
                        student_name: {
                            title: 'Nama Mahasiswa',
                            content: {
                                template: `:value`,
                                value: approval.pma_student_name,
                            },
                        },
                        student_id: {
                            title: 'NIM',
                            content: {
                                template: `:value`,
                                value: approval.pma_student_id ?? '-',
                            },
                        },
                        student_type: {
                            title: 'Tipe Mahasiswa',
                            content: {
                                template: `:value`,
                                value: approval.pma_student_type == 'student' ? 'Mahasiswa Lama' : 'Mahasiswa Baru',
                            },
                        },
                        student_studyprogram: {
                            title: 'Program Studi',
                            content: {
                                template: `:value`,
                                value: approval.pma_student_studyprogram,
                            },
                        },
                        student_lecturetype: {
                            title: 'Jenis Perkuliahan',
                            content: {
                                template: `:value`,
                                value: approval.pma_student_lecturetype,
                            },
                        },
                        student_reg_year: {
                            title: 'Tahun Masuk',
                            content: {
                                template: `:value`,
                                value: approval.pma_student_reg_year,
                            },
                        },
                        student_reg_period: {
                            title: 'Periode Masuk',
                            content: {
                                template: `:value`,
                                value: approval.pma_student_reg_period,
                            },
                        },
                        student_reg_path: {
                            title: 'Jalur Masuk',
                            content: {
                                template: `:value`,
                                value: approval.pma_student_reg_path,
                            },
                        },
                        sender_account_name: {
                            title: 'Nama Rekening Pengirim',
                            content: {
                                template: `:value`,
                                value: approval.pma_sender_account_name,
                            },
                        },
                        sender_account_number: {
                            title: 'Nomor Rekening Pengirim',
                            content: {
                                template: `:value`,
                                value: approval.pma_sender_account_number,
                            },
                        },
                        sender_bank: {
                            title: 'Bank Pengirim',
                            content: {
                                template: `:value`,
                                value: approval.pma_sender_bank,
                            },
                        },
                        amount: {
                            title: 'Nominal Pembayaran',
                            content: {
                                template: `:value`,
                                value: Rupiah.format(approval.pma_amount),
                            },
                        },
                        receiver_account_number: {
                            title: 'Nomor Rekening Tujuan',
                            content: {
                                template: `:value`,
                                value: approval.pma_receiver_account_number,
                            },
                        },
                        receiver_account_name: {
                            title: 'Nama Rekening Tujuan',
                            content: {
                                template: `:value`,
                                value: approval.pma_receiver_account_name,
                            },
                        },
                        receiver_bank: {
                            title: 'Bank Tujuan',
                            content: {
                                template: `:value`,
                                value: approval.pma_receiver_bank,
                            },
                        },
                        payment_time: {
                            title: 'Waktu Pembayaran',
                            content: {
                                template: `:value`,
                                value: moment(approval.pma_payment_time).format('DD/MM/YYYY HH:mm:ss'),
                            },
                        },
                        evidence: {
                            title: 'Bukti Pembayaran',
                            content: {
                                template: '<a href=":link" target="_blank">Download</a>',
                                link: _baseURL+'/api/download-cloud?path='+approval.pma_evidence,
                            },
                        },
                        approval_status: {
                            title: 'Status Approval',
                            content: {
                                template: `:value`,
                                value: approval.pma_approval_status == 'waiting' ? 'Menunggu Pembayaran'
                                    : approval.pma_approval_status == 'accepted' ? 'Diterima'
                                    : approval.pma_approval_status == 'rejected' ? 'Ditolak' : '-',
                            },
                        },
                        notes: {
                            title: 'Catatan Approval',
                            content: {
                                template: `:value`,
                                value: approval.pma_notes ?? '-',
                            },
                        },
                        processed_at: {
                            title: 'Diproses Pada',
                            content: {
                                template: `:value`,
                                value: approval.pma_processed_at ? moment(approval.pma_processed_at).format('DD/MM/YYYY HH:mm:ss') : '-',
                            },
                        },
                    },
                    callback: function() {
                        feather.replace();
                    }
                },
            });
        },
        processApproval: async function(e) {
            const target = $(e.currentTarget);
            const statusValue = target.attr('data-eazy-status');
            const pmaIdValue = target.attr('data-eazy-pmaId');
            const notes = $('#paymentApprovalModal #textarea-approval-notes').val();

            const confirmed = await _swalConfirmSync({
                title: 'Konfirmasi',
                text: `Apakah anda yakin ingin ${statusValue == 'accepted' ? 'MENERIMA' : 'MENOLAK'} pembayaran ini?`,
            });

            if (!confirmed) return;

            try {
                const res = await $.ajax({
                    async: true,
                    url: `${_baseURL}/api/payment/approval-manual-payment/${pmaIdValue}/process-approval`,
                    type: 'post',
                    data: {
                        status: statusValue,
                        notes: notes,
                    }
                });

                if (res.success) {
                    _toastr.success(res.message, 'Sukses');
                    _paymentApprovalModal.hide();
                    _paymentApprovalTable.reload();
                } else {
                    _toastr.error(res.message, 'Gagal');
                }

            } catch (error) {
                console.error(error);
                _toastr.error(error, 'Terjadi Error!');
            }
        },
    }

    const _paymentApprovalModal = new bootstrap.Modal(document.getElementById('paymentApprovalModal'));

    function searchFilter(elm, event) {
        if (event.key == 'Enter') {
            dt.clear().destroy();
            _paymentApprovalTable.init(elm.value);
        }
    }

    function getProdi(faculty) {
        console.log('getprodi')
        console.log(faculty);
        $('#prodi').html(
            `<option value="#ALL" selected>Semua Program Studi</option>`
        )

        if (faculty != '#ALL') {
            var xhr = new XMLHttpRequest();
            xhr.onload = function() {
                var data = JSON.parse(this.responseText);

                for (var i = 0; i < data.length; i++) {
                    $('#prodi').append(`
                    <option value="${data[i].studyprogram_id}">${data[i].studyprogram_name}</option>
                    `)
                }
            }
            xhr.open("GET", `${_baseURL}/api/payment/approval/prodi/${faculty}`);
            xhr.send()
        }

    }
</script>
@endsection
