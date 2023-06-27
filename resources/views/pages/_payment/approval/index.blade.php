@extends('layouts.static_master')

@section('page_title', 'Approval Pembayaran Manual')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
    <style>
    </style>
@endsection

@section('content')

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
                <th>Nama</th>
                <th>Tipe Mahasiswa</th>
                <th>Nomor Partisipan</th>
                <th>NIM</th>
                <th>Jumlah Tagihan</th>
                <th>Data Pembayaran</th>
                <th>Status</th>
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
                    <label class="form-label">Catatan</label>
                    <textarea id="textarea-approval-notes" class="form-control" rows="3"></textarea>
                </div>
                <div class="d-flex flex-row" style="gap: 3rem;">
                    <div class="flex-grow-1">
                        <button
                            id="btn-accept-payment"
                            onclick="_paymentApprovalTableAction.processApproval(event)"
                            data-eazy-status="accepted"
                            data-eazy-prrbId=""
                            class="btn btn-success w-100"
                        >Terima</button>
                    </div>
                    <div class="flex-grow-1">
                        <button
                            id="btn-reject-payment"
                            onclick="_paymentApprovalTableAction.processApproval(event)"
                            data-eazy-status="rejected"
                            data-eazy-prrbId=""
                            class="btn btn-danger w-100"
                        >Tolak</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@section('js_section')

<script>

    $(function(){
        _paymentApprovalTable.init();
        select2Replace();
    });

    const _paymentApprovalTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#table-payment-approval').DataTable({
                serverSide: true,
                ajax: {
                    url: `${_baseURL}/api/payment/approval`,
                    data: function(d) {
                        d.custom_filters = {
                            'status': $('select#filter-status').val(),
                            'student_type': $('select#filter-student-type').val(),
                        };
                    }
                },
                stateSave: false,
                columns: [
                    {
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: (data, _, row) => {
                            return this.template.rowAction(row.approval_status);
                        }
                    },
                    {
                        name: 'student_name',
                        data: 'student_name',
                        render: (data, _, row) => {
                            return this.template.defaultCell(data);
                        }
                    },
                    {
                        name: 'student_type',
                        data: 'student_type',
                        render: (data, _, row) => {
                            return this.template.badgeCell(
                                data == 'new_student' ? 'Mahasiswa Baru' : 'Mahasiswa Lama',
                                'primary',
                            );
                        }
                    },
                    {
                        name: 'par_number',
                        data: 'par_number',
                        render: (data, _, row) => {
                            return this.template.defaultCell(data ?? '-');
                        }
                    },
                    {
                        name: 'student_id',
                        data: 'student_id',
                        render: (data, _, row) => {
                            return this.template.defaultCell(data ?? '-');
                        }
                    },
                    {
                        name: 'bill_total',
                        data: 'bill_total',
                        render: (data, _, row) => {
                            return this.template.currencyCell(data);
                        }
                    },
                    {
                        name: 'payment_data',
                        render: (data, _, row) => {
                            return `
                            <div>
                                <span>Bank: ${row.bank_name}</span><br>
                                <span>Nama Pengirim: ${row.sender_name}</span><br>
                                <span>Nomor Rekening: ${row.sender_account_number}</span>
                            </div>
                            `;
                        }
                    },
                    {
                        name: 'approval_status',
                        data: 'approval_status',
                        render: (data, _, row) => {
                            return this.template.badgeCell(
                                data == 'waiting' ? 'Menunggu Approval'
                                    : data == 'accepted' ? 'Diterima'
                                        : data = 'rejected' ? 'Ditolak' : 'N/A',
                                data == 'waiting' ? 'warning'
                                    : data == 'accepted' ? 'success'
                                        : data = 'rejected' ? 'danger' : 'secondary',
                            );
                            return this.template.defaultCell(data);
                        }
                    },

                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"manual-payment-actions d-flex align-items-end">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>' +
                    '<"eazy-table-wrapper" t>' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function() {
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
        }
    }

    const _paymentApprovalTableAction = {
        tableRef: _paymentApprovalTable,
        openModalProcessApproval: function(e) {
            const data = this.tableRef.getRowData(e.currentTarget);

            $('#paymentApprovalModal #table-payment-detail tbody').html(`
                <tr>
                    <td style="width: 250px;">Nomor Tagihan</td>
                    <td>INV/${data.prr_id}</td>
                </tr>
                <tr>
                    <td style="width: 250px;">Nominal Jumlah Tagihan</td>
                    <td>${Rupiah.format(data.bill_total)}</td>
                </tr>
                <tr>
                    <td style="width: 250px;">Nama Pengirim</td>
                    <td>${data.sender_name}</td>
                </tr>
                <tr>
                    <td style="width: 250px;">Bank Pengirim</td>
                    <td>${data.bank_name}</td>
                </tr>
                <tr>
                    <td style="width: 250px;">Nomor Rekening Pengirim</td>
                    <td>${data.sender_account_number}</td>
                </tr>
                <tr>
                    <td style="width: 250px;">File Bukti Pembayaran</td>
                    <td>
                        <a href="${_baseURL+'/api/download-cloud?path='+data.file_payment_evidence}" class="p-0 btn btn-link btn-sm">
                            <i data-feather="download"></i>&nbsp;&nbsp;
                            Download Bukti Pembayaran
                        </a>
                    </td>
                </tr>
            `);
            feather.replace();

            $('#paymentApprovalModal #btn-accept-payment').attr('data-eazy-prrbId', data.prrb_id);
            $('#paymentApprovalModal #btn-reject-payment').attr('data-eazy-prrbId', data.prrb_id);

            _paymentApprovalModal.show();
        },
        openModalDetailApproval: function(e) {
            const data = this.tableRef.getRowData(e.currentTarget);

            Modal.show({
                type: 'detail',
                modalTitle: 'Detail Approval',
                modalSize: 'md',
                config: {
                    fields: {
                        invoice_number: {
                            title: 'Nomor Tagihan',
                            content: {
                                template: ':code',
                                code: 'INV/'+data.prr_id
                            },
                        },
                        invoice_total: {
                            title: 'Nominal Jumlah Tagihan',
                            content: {
                                template: ':number',
                                number: Rupiah.format(data.bill_total)
                            },
                        },
                        sender_name: {
                            title: 'Nama Pengirim',
                            content: {
                                template: ':text',
                                text: data.sender_name
                            },
                        },
                        bank_name: {
                            title: 'Bank Pengirim',
                            content: {
                                template: ':text',
                                text: data.bank_name
                            },
                        },
                        sender_account_number: {
                            title: 'Nomor Rekening Pengirim',
                            content: {
                                template: ':text',
                                text: data.sender_account_number
                            },
                        },
                        file_payment_evidence: {
                            title: 'File Bukti Pembayaran',
                            content: {
                                template: `
                                    <a href="${_baseURL}/api/download-cloud?path=:path" class="p-0 btn btn-link btn-sm">
                                        <i data-feather="download"></i>&nbsp;&nbsp;
                                        Download Bukti Pembayaran
                                    </a>
                                `,
                                path: data.file_payment_evidence
                            }
                        },
                        approval_status: {
                            title: 'Status Approval',
                            content: {
                                template: `
                                    ${
                                        data.approval_status == 'waiting' ?
                                            '<span class="badge bg-warning">Menunggu Approval</span>'
                                            : data.approval_status == 'rejected' ?
                                                '<span class="badge bg-danger">Ditolak</span>'
                                                : data.approval_status == 'accepted' ?
                                                    '<span class="badge bg-success">Disetujui</span>'
                                                    : 'N/A'
                                    }
                                `,
                            },
                        },
                        approval_notes: {
                            title: 'Catatan',
                            content: {
                                template: ':text',
                                text: data.approval_notes ?? '-'
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
            const prrbIdValue = target.attr('data-eazy-prrbId');
            const notes = $('#paymentApprovalModal #textarea-approval-notes').val();

            const confirmed = await _swalConfirmSync({
                title: 'Konfirmasi',
                text: `Apakah anda yakin ingin ${statusValue == 'accepted' ? 'MENERIMA' : 'MENOLAK'} pembayaran ini?`,
            });

            if(!confirmed) return;

            try {
                const res = await $.ajax({
                    async: true,
                    url: `${_baseURL}/api/payment/approval/${prrbIdValue}/process-approval`,
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

</script>
@endsection
