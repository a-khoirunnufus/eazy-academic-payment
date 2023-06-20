@extends('layouts.student.layout-master')

@section('page_title', 'Proses Pembayaran')
@section('sidebar-size', 'collapsed')
@section('url_back', url('student/payment'))

@section('css_section')
    <style>
        .eazy-table-info {
            display: inline-block;
        }
        .eazy-table-info td {
            padding: 10px;
        }
        .eazy-table-info td:first-child {
            padding-right: 1rem;
            font-weight: 500;
        }

        .nav-tabs.custom .nav-item {
            flex-grow: 1;
        }
        .nav-tabs.custom .nav-link {
            /* width: -webkit-fill-available !important; */
            flex-grow: 1;
            height: 50px !important;
        }
        .nav-tabs.custom .nav-link.active {
            background-color: #f2f2f2 !important;
        }

        .payment-method {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .payment-method .payment-method__item {
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #ebe9f1 !important;
            border-radius: 0.357rem !important;
            min-width: 90px;
            padding: 0 1rem;
            height: 70px;
            cursor: pointer;
        }
        .payment-method .payment-method__item[data-eazy-selected="true"] {
            border: 1px solid #7367f0 !important;
            box-shadow: 0 4px 24px 0 rgb(34 41 47 / 10%) !important;
        }
        .payment-method .payment-method__item span {
            font-size: calc(1.2964rem + 0.5568vw);
            display: block;
            margin-bottom: 0;
            color: #356CFF !important;
        }
    </style>
@endsection

@section('content')

<div class="card">
    <div class="card-body p-3">
        <div class="d-flex flex-row">
            <div id="select-payment-method" class="d-flex flex-column pe-3 border-end" style="width: 40%">
                <h5 class="mb-2">Pilih Metode Pembayaran</h5>
                <div class="mb-2">
                    <p>Bank Transfer</p>
                    <div class="payment-method">
                        <div class="payment-method__item" data-eazy-method="bca" data-eazy-selected="false">
                            <span>BCA</span>
                        </div>
                        <div class="payment-method__item" data-eazy-method="mandiri" data-eazy-selected="false">
                            <span>mandiri</span>
                        </div>
                        <div class="payment-method__item" data-eazy-method="bni" data-eazy-selected="false">
                            <span>BNI</span>
                        </div>
                    </div>
                </div>
                <div>
                    <button id="btn-select-payment-method" class="btn btn-primary {{ $payment->prr_method ? 'disabled' : '' }}">Pilih Metode</button>
                </div>
            </div>

            <div id="payment-detail" class="ps-3" style="width: 60%">
                <h5 class="mb-2">Detail Pembayaran</h5>
                <div class="mb-2">
                    <p class="text-secondary">Nomor Virtual Akun Bank</p>
                    <h4 class="text-primary">
                        @if($payment->prr_method)
                            <span>{{ $payment_method->mpm_name }} - {{ $payment_bill->prrb_invoice_num }}
                        @else
                            <span>-</span>
                        @endif
                    </h4>
                </div>
                <div class="d-flex mb-2" style="gap: 2rem">
                    <div class="d-inline-block">
                        <p class="text-secondary">Biaya Awal</p>
                        <h4 class="text-primary">
                            @if($payment->prr_method)
                                <span>Rp {{ number_format($payment_bill->prrb_amount,2,',','.') }}</span>
                            @else
                                <span>-</span>
                            @endif
                        </h4>
                    </div>
                    <div class="d-inline-block">
                        <p class="text-secondary">Biaya Admin</p>
                        <h4 class="text-primary">
                            @if($payment->prr_method)
                                <span>Rp {{ number_format($payment_bill->prrb_admin_cost,2,',','.') }}</span>
                            @else
                                <span>-</span>
                            @endif
                        </h4>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <div class="d-inline-block flex-grow-1">
                        <p class="text-secondary">Total yang harus dibayar</p>
                        <h4 class="text-success">
                            @if($payment->prr_method)
                                <span>Rp {{ number_format($payment->prr_total,2,',','.') }}</span>
                            @else
                                <span>-</span>
                            @endif
                        </h4>
                    </div>
                    <button type="button" class="btn btn-outline-success d-inline-block text-success me-1" style="height: fit-content">Tata cara pembayaran&nbsp;&nbsp;<i data-feather="book"></i></button>
                    <button class="btn btn-success btn-icon d-inline-block" style="height: fit-content">
                        <i data-feather="printer"></i>
                    </button>
                </div>

                @if($payment->prr_method)
                    <div class="mt-3">
                        <ul class="nav nav-tabs border-bottom" id="paymentDetailTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#payment-status" type="button" role="tab" aria-controls="payment-status">Status Pembayaran</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#payment-evidence" type="button" role="tab" aria-controls="upload-transfer-evidence">Upload Bukti Transfer</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="paymentDetailTabContent">
                            <div class="tab-pane fade show active" id="payment-status" role="tabpanel">
                                <table class="eazy-table-info">
                                    <tbody>
                                        <tr>
                                            <td>Status Pembayaran</td>
                                            <td>: {{ $payment_bill->prrb_paid_date == null ? 'Menunggu Pembayaran' : 'Lunas' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Batas Waktu Pembayaran</td>
                                            <td>: {{ \Carbon\Carbon::parse($payment_bill->prrb_expired_date)->format('d-m-Y') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button
                                    onclick="bootstrap.Tab.getInstance(paymentEvidenceTriggerEl).show()"
                                    class="btn btn-primary d-block mt-1"
                                >
                                    Saya Sudah Membayar
                                </button>
                            </div>
                            <div class="tab-pane fade" id="payment-evidence" role="tabpanel">
                                <div class="alert alert-info p-1 d-inine-block mb-1">Silahkan upload bukti pembayaran</div>
                                <form id="form-upload-payment-evidence">
                                    <div class="mb-1">
                                        <label class="form-label">Nama Pengirim</label>
                                        <input type="text" name="sender_name" class="form-control" placeholder="name@example.com">
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label">No Rekening Pengirim</label>
                                        <input type="text" name="sender_account_number" class="form-control" placeholder="name@example.com">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Bukti Transfer</label>
                                        <input type="file" name="transfer_evidence_file" class="form-control" placeholder="name@example.com">
                                    </div>
                                    <div>
                                        <button class="btn btn-primary">Upload Bukti Transfer</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


@section('js_section')
<script>
    const prrId = parseInt("{{ $prr_id }}");

    $(function(){
        $('.payment-method__item').click(function() {
            const isSelected = $(this).attr('data-eazy-selected') == 'true';
            if (!isSelected) {
                $(this).parent().children().each(function() {
                    $(this).attr('data-eazy-selected', 'false');
                });
                $(this).attr('data-eazy-selected', 'true');
            }
        });

        // Enable tabbable tabs via JavaScript
        triggerTabList.forEach(function (triggerEl) {
            const tabTrigger = new bootstrap.Tab(triggerEl)
            triggerEl.addEventListener('click', function (event) {
                event.preventDefault()
                tabTrigger.show()
            })
        });

        $('#btn-select-payment-method').click(async function() {
            const method = $('.payment-method .payment-method__item[data-eazy-selected="true"]');

            if (!method) {
                _toastr.warning('Silahkan Pilih Metode Pembayaran', 'Belum Dipilih');
                return;
            }

            const paymentMethod = method.attr('data-eazy-method');

            const res = await $.ajax({
                async: true,
                url: _baseURL+'/api/student/payment/select-method',
                type: 'post',
                data: {
                    prr_id: prrId,
                    method: paymentMethod,
                }
            });

            if (!res.success) {
                _toastr.error(res.message, 'Gagal');
            } else {
                _toastr.success(res.message, 'Berhasil');
                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            }
        });
    });

    const triggerTabList = [].slice.call(document.querySelectorAll('#paymentDetailTab button'))
    const paymentStatusTriggerEl = document.querySelector('#paymentDetailTab button[data-bs-target="#payment-status"]');
    const paymentEvidenceTriggerEl = document.querySelector('#paymentDetailTab button[data-bs-target="#payment-evidence"]');

</script>
@endsection
