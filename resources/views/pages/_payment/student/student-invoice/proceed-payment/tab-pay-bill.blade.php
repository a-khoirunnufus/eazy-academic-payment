@prepend('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bs-stepper/dist/css/bs-stepper.min.css"/>
@endprepend

@push('styles')
    <style>
        #reset-payment-section {
            display: none;
        }
        #reset-payment-section.show {
            display: flex;
            justify-content: end;
        }

        /* .payment-method-list {
            display: flex;
            flex-direction: column;
            padding-left: 0;
            margin-bottom: 0;
            border-radius: 0.357rem;
        }
        .payment-method-list > .payment-method-list__item {
            position: relative;
            display: block;
            padding: 0.75rem 1.25rem;
            color: #6e6b7b;
            background-color: #fff;
            border: 1px solid rgba(34, 41, 47, 0.125);
        }
        .payment-method-list > .payment-method-list__item.active {
            border: 1px solid #7367f0 !important;
            box-shadow: 0 4px 24px 0 rgb(34 41 47 / 10%) !important;
        } */
        .line {
            text-decoration: line-through;
        }
        .section-border {
            border: 1px solid gainsboro;
            border-radius: 6px;
            padding: 1rem;
        }

        .list-payment-method {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .list-payment-method__item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 1px solid gainsboro;
            border-radius: 10px;
            padding: 1.5rem;
            width: 200px;
            cursor: pointer;
            position: relative;
        }
        .list-payment-method__item.active {
            border: 3px solid rgb(13, 110, 253);
        }
        .list-payment-method__item.active:after {
            content: '';
            background-image: url('/images/icons/check-white.png');
            background-size: 20px 20px;
            background-repeat: no-repeat;
            background-position: center;
            object-fit: contain;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            background-color: rgb(13, 110, 253);
            position: absolute;
            right: -12.5px;
            top: -12.5px;
        }
        .list-payment-method__item:hover {
            background-color: rgba(13, 110, 253, .1);
        }
        .list-payment-method__item > .item__logo {
            display: block;
            flex-grow: 1;
            width: 100px;
            height: 33px;
            object-fit: contain;
        }
        .list-payment-method__item > .item__text {
            margin-bottom: 0;
            margin-top: 1rem;
            text-align: center;
            font-weight: 500;
        }
    </style>
@endpush

<div id="reset-payment-section" class="show mb-2">
    <button onclick="payBillTab.resetPayment()" class="btn btn-outline-warning">Ganti Opsi Pembayaran</button>
</div>

<div>
    <table id="table-pay-bill" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Kode Cicilan</th>
                <th>Jumlah Tagihan</th>
                <th>Tenggat Pembayaran</th>
                <th>Keterangan</th>
                <th class="text-center">Status Pembayaran</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<div id="paymentMethodManager"></div>

<!-- Payment Method Modal -->
<div class="modal fade" id="paymentMethodModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-white" style="padding: 2rem 3rem 3rem 3rem">
                <h4 class="modal-title fw-bolder" id="paymentMethodModalLabel">Pembayaran</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 pt-0">
                <div>
                    <h4 class="mb-1">Metode Pembayaran</h4>
                    <form class="accordion border" id="accordionPaymentMethod">
                    </form>
                    <form id="form-payment-method">
                        <input type="hidden" name="prrb_id" value="" />
                        <input type="hidden" name="use_student_balance" value="0" />
                        <input type="hidden" name="student_balance_spend" value="0" />
                        <input type="hidden" name="payment_method" value="" />
                    </form>
                </div>

                <div id="use-student-balance"></div>

                <div class="mt-3">
                    <h4 class="mb-1">Ringkasan Pembayaran</h4>
                    <div id="payment-summary">
                        ...
                    </div>
                </div>
            </div>
            <div class="modal-footer px-3 py-2">
                <div class="d-flex flex-row justify-content-end w-100">
                    <button onclick="payBillModal.selectPaymentMethod()" class="d-block btn btn-success" style="width: 200px">Bayar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Method Modal V2 -->
<div class="modal fade" id="paymentMethodModalV2" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Pembayaran</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="stepper-pay-bill" class="bs-stepper linear">
                    <div class="bs-stepper-header" role="tablist">
                        <div class="step active" data-target="#step-1">
                            <button type="button" class="step-trigger" role="tab" id="stepper1trigger1" aria-controls="step-1"
                                aria-selected="true">
                                <span class="bs-stepper-circle">1</span>
                                <span class="bs-stepper-label">Layanan Pembayaran</span>
                            </button>
                        </div>
                        <div class="bs-stepper-line"></div>
                        <div class="step" data-target="#step-2">
                            <button type="button" class="step-trigger" role="tab" id="stepper1trigger2" aria-controls="step-2"
                                aria-selected="false" disabled="disabled">
                                <span class="bs-stepper-circle">2</span>
                                <span class="bs-stepper-label">Metode Pembayaran</span>
                            </button>
                        </div>
                        <div class="bs-stepper-line"></div>
                        <div class="step" data-target="#step-3">
                            <button type="button" class="step-trigger" role="tab" id="stepper1trigger3" aria-controls="step-3"
                                aria-selected="false" disabled="disabled">
                                <span class="bs-stepper-circle">3</span>
                                <span class="bs-stepper-label">Lanjutan</span>
                            </button>
                        </div>
                        <div class="bs-stepper-line"></div>
                        <div class="step" data-target="#step-4">
                            <button type="button" class="step-trigger" role="tab" id="stepper1trigger4" aria-controls="step-4"
                                aria-selected="false" disabled="disabled">
                                <span class="bs-stepper-circle">4</span>
                                <span class="bs-stepper-label">Ringkasan Pembayaran</span>
                            </button>
                        </div>
                    </div>
                    <div class="bs-stepper-content">
                        <form onsubmit="return false">

                            <div id="step-1" role="tabpanel" class="bs-stepper-pane active dstepper-block" aria-labelledby="stepper1trigger1">

                                <div class="step-content mt-2">
                                </div>

                                <button class="btn btn-primary mt-3" onclick="stepper.next()">Next</button>
                            </div>

                            <div id="step-2" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger2">

                                <div class="mt-2">
                                    <h5 class="fw-bold mb-1">Bank Transfer Manual</h5>
                                    <div class="list-payment-method">
                                        <div class="list-payment-method__item">
                                            <img class="item__logo" src="{{ url('images/payment-logo/bank-bca.png') }}" alt="Bank BCA">
                                            <p class="item__text">Bank BCA</p>
                                        </div>

                                        <div class="list-payment-method__item">
                                            <img class="item__logo" src="{{ url('images/payment-logo/bank-bni.png') }}" alt="Bank BNI">
                                            <p class="item__text">Bank BNI</p>
                                        </div>

                                        <div class="list-payment-method__item">
                                            <img class="item__logo" src="{{ url('images/payment-logo/bank-mandiri.png') }}" alt="Bank Mandiri">
                                            <p class="item__text">Bank Mandiri</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <h5 class="fw-bold mb-1">Virtual Account</h5>
                                    <div class="list-payment-method">
                                        <div class="list-payment-method__item">
                                            <img class="item__logo" src="{{ url('images/payment-logo/bank-mandiri.png') }}" alt="Bank Mandiri">
                                            <p class="item__text">Virtual Account Mandiri</p>
                                        </div>

                                        <div class="list-payment-method__item">
                                            <img class="item__logo" src="{{ url('images/payment-logo/bank-bni.png') }}" alt="Bank BNI">
                                            <p class="item__text">Virtual Account BNI</p>
                                        </div>

                                        <div class="list-payment-method__item">
                                            <img class="item__logo" src="{{ url('images/payment-logo/bank-btn.png') }}" alt="Bank BTN">
                                            <p class="item__text">Virtual Account BTN</p>
                                        </div>

                                        <div class="list-payment-method__item">
                                            <img class="item__logo" src="{{ url('images/payment-logo/bank-mega.png') }}" alt="Bank Mega">
                                            <p class="item__text">Virtual Account Mega</p>
                                        </div>

                                        <div class="list-payment-method__item">
                                            <img class="item__logo" src="{{ url('images/payment-logo/bank-bsi.png') }}" alt="Bank BSI">
                                            <p class="item__text">Virtual Account BSI</p>
                                        </div>

                                        <div class="list-payment-method__item">
                                            <img class="item__logo" src="{{ url('images/payment-logo/bank-permata.png') }}" alt="Bank Permata">
                                            <p class="item__text">Virtual Account Permata</p>
                                        </div>

                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button class="btn btn-primary" onclick="stepper.previous()">Previous</button>
                                    <button class="btn btn-primary" onclick="stepper.next()">Next</button>
                                </div>
                            </div>

                            <div id="step-3" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger3">
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Password</label>
                                    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                                </div>
                                <button class="btn btn-primary" onclick="stepper.previous()">Previous</button>
                                <button class="btn btn-primary" onclick="stepper.next()">Next</button>
                            </div>

                            <div id="step-4" role="tabpanel" class="bs-stepper-pane text-center" aria-labelledby="stepper1trigger4">
                                <button class="btn btn-primary mt-5" onclick="stepper.previous()">Previous</button>
                                <button type="submit" class="btn btn-primary mt-5">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Instruction Modal -->
<div class="modal fade" id="paymentInstructionModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-white" style="padding: 2rem 3rem 3rem 3rem">
                <h4 class="modal-title fw-bolder" id="paymentInstructionModalLabel">Pembayaran</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 pt-0">
                <div id="va-number-exp-warning">
                    ...
                </div>

                <table id="table-pay-data" class="table table-bordered">
                    <tbody></tbody>
                </table>

                <div id="payment-evidence-section"></div>

                <div class="section-border mt-3">
                    <h4 class="mb-1">Riwayat Transaksi</h4>
                    <table id="table-transaction-history" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Total Bayar</th>
                                <th>Waktu Bayar</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <div id="overpayment-history-section"></div>

                <div class="section-border mt-3">
                    <h4 class="mb-1">Petunjuk Pembayaran</h4>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptatem quis sapiente repellendus dignissimos quam aliquam reprehenderit dolorum? Libero, totam magnam iusto reprehenderit consequuntur ullam ipsam modi hic! Eaque, officia. Ratione!</p>
                    <ul>
                        <li><a href="https://simulator.sandbox.midtrans.com/bca/va/index" target="_blank">BCA Virtual Account Payment Simulator</a></li>
                        <li><a href="https://simulator.sandbox.midtrans.com/bni/va/index" target="_blank">BNI Virtual Account Payment Simulator</a></li>
                        <li><a href="https://simulator.sandbox.midtrans.com/mandiri/bill/index" target="_blank">Mandiri Bill Payment Simulator</a></li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer px-3 py-2"></div>
        </div>
    </div>
</div>


@prepend('scripts')
<script src="https://cdn.jsdelivr.net/npm/bs-stepper/dist/js/bs-stepper.min.js"></script>
<script>

    /**
     * @var integer prrId
     * @var object FormDataJson
     * @func getRequestCache()
     */

    // enabling multiple modal open
    $(document).on('show.bs.modal', '.modal', function() {
        const zIndex = 1040 + 10 * $('.modal:visible').length;
        $(this).css('z-index', zIndex);
        setTimeout(() => $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack'));
    });

    $(function(){
        $('#accordionPaymentMethod').change(payBillModal.paymentMethodChangeHandler);
        paymentMethodMaster.setupManager();
        feather.replace();

    });

    $(function(){
        paymentMethodModal.show();

        $('#step-2 .list-payment-method__item').click(function() {
            $('#step-2 .list-payment-method__item').removeClass('active');
            $(this).addClass('active');
        });

        paymentMethodMaster.renderStepOne();
    })

    // setup stepper
    const stepper = new Stepper($('.bs-stepper')[0]);

    document.getElementById('stepper-pay-bill').addEventListener('show.bs-stepper', function (event) {
        console.log('step show', event.detail);

        const indexStep = event.detail.indexStep;

        if (indexStep == 1) {
            if (paymentMethodState.paymentType == null) {

            }
        }
    });

    const paymentMethodMaster = {
        state: {
            paymentService: null,
            paymentType: null,
        },
        renderStepOne: async () => {
            const isMidtransActive = await $.ajax({async: true, url: `${_baseURL}/api/payment/resource/master-setting/payment_with_midtrans_active`});
            const isFinpayActive = await $.ajax({async: true, url: `${_baseURL}/api/payment/resource/master-setting/payment_with_finpay_active`});
            const isManualActive = await $.ajax({async: true, url: `${_baseURL}/api/payment/resource/master-setting/payment_with_manual_active`});

            const html = `
                <div class="list-payment-method">
                    ${isMidtransActive.value === 'true' ? `
                        <div class="list-payment-method__item" data-code="midtrans">
                            <img class="item__logo" src="{{ url('images/payment-logo/service-midtrans.png') }}" alt="Midtrans">
                            <p class="item__text">Midtrans</p>
                        </div>
                    ` : ''}

                    ${isFinpayActive.value === 'true' ? `
                        <div class="list-payment-method__item" data-code="finpay">
                            <img class="item__logo" src="{{ url('images/payment-logo/service-finpay.png') }}" alt="Finpay">
                            <p class="item__text">Finpay</p>
                        </div>
                    ` : ''}

                    ${isManualActive.value === 'true' ? `
                        <div class="list-payment-method__item" data-code="manual">
                            <img class="item__logo" src="{{ url('images/payment-logo/service-manual.png') }}" alt="Manual">
                            <p class="item__text">Manual</p>
                        </div>
                    ` : ''}
                </div>
            `;

            $('#stepper-pay-bill #step-1 .step-content').html(html);

            $('#step-1 .list-payment-method__item').click(function() {
                $('#step-1 .list-payment-method__item').removeClass('active');
                $(this).addClass('active');
                paymentMethodMaster.state.paymentService = $(this).dataset(code);
                paymentMethodMaster.
            });
        },
        stepOneChange: () => {
            console.log('step 1 data change');
            // reset/render step 2
        },
        renderStepTwo: () => {

        },
        stepTwoChange: () => {
            console.log('step 2 data change');

            // reset/render step 3
        }

    }

    const payBillTab = {
        alwaysAllowReset: true,
        showHandler: async function() {
            const billMaster = await $.ajax({
                async: true,
                url: `${_baseURL}/api/payment/student-invoice/${prrId}`,
                data: {
                    withData: [
                        'paymentBill',
                        'student.studyprogram',
                        'student.lectureType',
                        'year',
                    ],
                    withAppend: [
                        'computed_has_paid_bill',
                    ],
                }
            });

            const bills = await $.ajax({
                async: true,
                url: `${_baseURL}/api/payment/student-invoice/${prrId}/bill`,
                data: {
                    withAppend: [
                        'computed_due_date',
                        'computed_dispensation_applied',
                        'computed_payment_status',
                    ],
                }
            });

            const studentType = 'student';

            if (billMaster.computed_has_paid_bill) {
                !payBillTab.alwaysAllowReset && $('#reset-payment-section').removeClass('show');
            }

            // render index table
            $('#table-pay-bill tbody').html(`
                ${bills.map(bill => {

                    let paymentStatusHtml = '<span class="badge bg-secondary" style="font-size: 1rem">N/A</span>';
                    if (bill.computed_payment_status == 'belum lunas')
                        paymentStatusHtml = '<span class="badge bg-danger" style="font-size: 1rem">Belum Lunas</span>';
                    if (bill.computed_payment_status == 'kredit')
                        paymentStatusHtml = '<span class="badge bg-warning" style="font-size: 1rem">Kredit</span>';
                    if (bill.computed_payment_status == 'lunas')
                        paymentStatusHtml = '<span class="badge bg-success" style="font-size: 1rem">Lunas</span>';

                    return `
                        <tr>
                            <td>${bill.prrb_order}</td>
                            <td>${bill.prrb_id}</td>
                            <td>${Rupiah.format(bill.prrb_amount)}</td>
                            <td class="text-start">
                                <p class="m-0">${moment(bill.computed_due_date).format('DD/MM/YYYY')}</p>
                                ${
                                    bill.computed_dispensation_applied ? `
                                        <small class="d-block line" style="margin-top: 6px">${
                                            moment(bill.prrb_due_date).format('DD/MM/YYYY')
                                        }</small>
                                    ` : ''
                                }
                            </td>
                            <td>
                                Cicilan Ke-${bill.prrb_order} Pembayaran ${studentType == 'new_student' ? 'Daftar Ulang' : 'Registrasi Semester Baru'}
                                Program Studi ${ studentType == 'new_student' ? `
                                        ${billMaster.register.studyprogram.studyprogram_type.toUpperCase()}
                                        ${billMaster.register.studyprogram.studyprogram_name}
                                        ${billMaster.register.lecture_type?.mlt_name ?? 'N/A'}
                                    ` : `
                                        ${billMaster.student.studyprogram.studyprogram_type.toUpperCase()}
                                        ${billMaster.student.studyprogram.studyprogram_name}
                                        ${billMaster.student.lecture_type?.mlt_name ?? 'N/A'}
                                    `
                                }
                                Tahun Ajaran ${billMaster.year.msy_year}
                                Semester ${billMaster.year.msy_semester}
                            </td>
                            <td class="text-center">${paymentStatusHtml}</td>
                            <td class="text-center">
                                <button class="btn btn-${['belum lunas', 'kredit'].includes(bill.computed_payment_status) ? `success` : `primary`}" onclick="payBillModal.open(${bill.prrb_id})" style="white-space: nowrap;">
                                    ${['belum lunas', 'kredit'].includes(bill.computed_payment_status) ? `Bayar` : `Detail Pembayaran`}
                                </button>
                            </td>
                        </tr>
                    `;
                    // var row = null;
                    // var xhr = new XMLHttpRequest()
                    // xhr.onload = function(){
                    //     var response = JSON.parse(this.responseText);
                    //     if(response == null){
                    //         console.log('not found');
                    //     }
                    //     console.log(response);
                    //     row = `
                    //         <tr>
                    //             <td>${item.prrb_order}</td>
                    //             <td>
                    //                 Cicilan Ke-${item.prrb_order} Pembayaran ${studentType == 'new_student' ? 'Daftar Ulang' : 'Registrasi Semester Baru'}
                    //                 Program Studi ${ studentType == 'new_student' ? `
                    //                         ${payment.register.studyprogram.studyprogram_type.toUpperCase()}
                    //                         ${payment.register.studyprogram.studyprogram_name}
                    //                         ${payment.register.lecture_type?.mlt_name ?? 'N/A'}
                    //                     ` : `
                    //                         ${payment.student.studyprogram.studyprogram_type.toUpperCase()}
                    //                         ${payment.student.studyprogram.studyprogram_name}
                    //                         ${payment.student.lecture_type?.mlt_name ?? 'N/A'}
                    //                     `
                    //                 }
                    //                 Tahun Ajaran ${payment.year.msy_year}
                    //                 Semester ${payment.year.msy_semester}
                    //             </td>
                    //             <td>${Rupiah.format(item.prrb_amount)}</td>
                    //             <td>${ response == null ? moment(item.prrb_due_date).format('DD/MM/YYYY') : '<p class="line">'+moment(item.prrb_due_date).format('DD/MM/YYYY')+'</p><br><p>'+moment(response.mds_deadline).format('DD/MM/YYYY')}</td>
                    //             <td class="text-center">
                    //                 ${item.prrb_status == 'lunas' ?
                    //                     '<span class="badge bg-success">Lunas</span>'
                    //                     : item.prrb_status == 'belum lunas' ?
                    //                         '<span class="badge bg-danger">Belum Lunas</span>'
                    //                         : 'N/A'
                    //                 }
                    //             </td>
                    //             <td class="text-center">
                    //                 <button class="btn btn-${item.prrb_status == 'belum lunas' ? `success` : `primary`}" onclick="payBillModal.open(${item.prrb_id})" style="white-space: nowrap;">
                    //                     ${item.prrb_status == 'belum lunas' ? `Bayar` : `Detail Pembayaran`}
                    //                 </button>
                    //             </td>
                    //         </tr>
                    //     `;
                    // }
                    // xhr.open("GET", _baseURL+"/api/student/dispensation/spesific-payment/{{ $prr_id }}", false);
                    // xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                    // xhr.send();
                    // return row;
                })}
            `);
        },
        resetPayment: async function() {
            const confirmed = await _swalConfirmSync({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin mengganti metode pembayaran?',
            });

            if(!confirmed) return;

            try {
                const res = await $.ajax({
                    async: true,
                    url: `${_baseURL}/api/payment/student-invoice/${prrId}/reset-payment`,
                    type: 'post',
                });

                if (res.success) {
                    _toastr.success(res.message, 'Berhasil');
                    tabManager.updateDisableState();
                } else {
                    _toastr.error(res.message, 'Gagal');
                }
            } catch (error) {
                _toastr.error('Gagal mengganti metode pembayaran!', 'Gagal');
            }
        },
    };

    const paymentMethodMasterOld = {
        state: {
            bill: null,
            paymentMethod: null,
            studentBalanceSpend: 0,
        },
        managerElm: document.querySelector('#paymentMethodManager'),
        setupManager: function() {
            this.managerElm.addEventListener('paymentMethodChange', async (event) => {
                // console.log('event payload', event.detail);

                const paymentMethodKey = event.detail.paymentMethod.mpm_key;
                const paymentMethodName = event.detail.paymentMethod.mpm_name;
                const billAmount = event.detail.bill.prrb_amount;
                const adminFee = event.detail.paymentMethod.mpm_fee;
                const studentBalanceSpend = event.detail.studentBalanceSpend;
                const totalBill = billAmount + adminFee - studentBalanceSpend;

                if (studentBalanceSpend) {
                    // check is balance sufficient
                    let studentBalance = 0;
                    try {
                        // get student balance
                        const res = await $.ajax({
                            url: `${_baseURL}/api/payment/student-balance/transaction`,
                            data: {student_id: studentMaster.student_id},
                            processData: true,
                            type: 'get'
                        });
                        studentBalance = res.sbt_closing_balance;
                    } catch (error) {
                        studentBalance = 0;
                    }
                    if (parseInt(studentBalanceSpend) > parseInt(studentBalance)) {
                        _toastr.error('Nominal Saldo yang dimasukkan tidak mencukupi!');
                        return;
                    }
                }

                FormDataJson.fromJson('#form-payment-method', {
                    payment_method: paymentMethodKey,
                    use_student_balance: (studentBalanceSpend ? 1 : 0),
                    student_balance_spend: studentBalanceSpend,
                });

                $('#paymentMethodModal #payment-summary').html(`
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td style="width: 50%">Metode Pembayaran</td>
                                <td>${paymentMethodName}</td>
                            </tr>
                            <tr>
                                <td style="width: 50%">Tagihan Daftar Ulang</td>
                                <td>${Rupiah.format(billAmount)}</td>
                            </tr>
                            <tr>
                                <td style="width: 50%">Biaya Admin</td>
                                <td>${Rupiah.format(adminFee)}</td>
                            </tr>
                            ${
                                studentBalanceSpend ? `
                                    <tr>
                                        <td style="width: 50%">Pemakaian Saldo Mahasiswa</td>
                                        <td>${Rupiah.format(studentBalanceSpend)}</td>
                                    </tr>
                                ` : ''
                            }
                            <tr>
                                <th style="width: 50%">Total Tagihan Akhir</th>
                                <th>${Rupiah.format(totalBill)}</th>
                            </tr>
                        </tbody>
                    </table>
                `);
            });
        },
        createChangeEvent: function() {
            const options = { detail: this.state };
            return new CustomEvent('paymentMethodChange', options);
        },
    };

    const payBillModal = {
        alwaysAllowUploadEvidence: true,
        open: async function(prrbId) {
            const bill = await $.ajax({
                async: true,
                url: `${_baseURL}/api/payment/student-invoice/${prrId}/bill/${prrbId}`,
                data: {
                    withData: ['paymentManualApproval'],
                    withAppend:  ['computed_nominal_paid_gross', 'computed_payment_status'],
                }
            });

            if (bill.prrb_payment_method != null) {
                payBillModal.openPaymentInstructionModal(bill);
            } else {
                payBillModal.openPaymentMethodModal(prrbId);
            }
        },
        openPaymentMethodModal: async function(prrbId) {
            // clear value
            $('#paymentMethodModal #payment-summary').html('...');
            $('#paymentMethodModal #footer-payment-method').text('...');
            $('#paymentMethodModal #footer-bill-total-amount').text('...');
            FormDataJson.clear('#form-payment-method');
            $('#paymentMethodModal #form-payment-method .payment-method-list__item').each(function() {
                $(this).removeClass('active');
            });

            let paymentMethods = await $.ajax({
                async: true,
                url: `${_baseURL}/api/payment/payment-method/type-group`,
                type: 'get',
            });
            paymentMethods = paymentMethods.filter(item => item.payment_methods.length > 0);

            $('#paymentMethodModal #accordionPaymentMethod').html(`
                ${
                    paymentMethods.map(type => {
                        return `
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_${type.code}">
                                        ${type.name}
                                    </button>
                                </h2>
                                <div id="collapse_${type.code}" class="accordion-collapse collapse">
                                    <div class="accordion-body">
                                        <ul class="payment-method-list">
                                            ${
                                                type.payment_methods.map(method => {
                                                    return `
                                                        <li class="payment-method-list__item">
                                                            <input class="form-check-input me-1" type="radio" name="payment_method" value="${method.mpm_key}" />
                                                            <span>${method.mpm_name}</span>
                                                        </li>
                                                    `;
                                                }).join('')
                                            }
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('')
                }
            `);

            // clear 'use student balance' section
            $('#paymentMethodModal #use-student-balance').html('');

            $('#paymentMethodModal #form-payment-method input[name="prrb_id"]').val(prrbId);
            paymentMethodModal.show();
        },
        openPaymentInstructionModal: async function(bill) {
            const paymentMethod = await $.ajax({
                async: true,
                url: `${_baseURL}/api/payment/payment-method/${bill.prrb_payment_method}`,
                type: 'get',
            });

            const paymentApprovals = await $.ajax({
                async: true,
                url: `${_baseURL}/api/payment/student-invoice/${prrId}/bill/${bill.prrb_id}/approval`,
                type: 'get',
            });

            const paymentTransactions = await $.ajax({
                async: true,
                url: `${_baseURL}/api/payment/student-invoice/${prrId}/bill/${bill.prrb_id}/transaction`,
                type: 'get',
            });

            const paymentOverpayments = await $.ajax({
                async: true,
                url: `${_baseURL}/api/payment/student-invoice/${prrId}/bill/${bill.prrb_id}/overpayment`,
                type: 'get',
            });

            const {amount: studentBalanceSpent} = await $.ajax({
                async: true,
                url: `${_baseURL}/api/payment/student-invoice/${prrId}/bill/${bill.prrb_id}/balance-use`,
                type: 'get',
            });

            const nominalInvoice = bill.prrb_amount + bill.prrb_admin_cost;
            const nominalHasToPaid = bill.prrb_amount + bill.prrb_admin_cost - studentBalanceSpent;

            const nominalPaid = bill.computed_nominal_paid_gross;

            let nominalUnpaid = (bill.prrb_amount + bill.prrb_admin_cost) - (nominalPaid + studentBalanceSpent);
            if (nominalUnpaid < 0) nominalUnpaid = 0;

            if (
                bill.computed_payment_status != 'lunas'
                && paymentMethod.mpm_type != 'bank_transfer_manual'
            ) {

                if (moment().isAfter(moment(bill.prrb_midtrans_transaction_exp))) {
                    // expire time exceeded
                    $('#paymentInstructionModal #va-number-exp-warning').html(`
                        <div class="alert p-1 alert-warning d-flex flex-row align-items-start">
                            <div class="me-1">
                                <i data-feather="alert-triangle"></i>
                            </div>
                            <div>
                                <p class="d-block me-1">Nomor Virtual Account anda telah kadaluarsa, silahkan generate nomor virtual account yang baru.</p>
                                <button class="btn btn-warning" onclick="regenerateVAN(${bill.prrb_id})">Generate No Virtual Account Baru</button>
                            </div>
                        </div>
                    `);
                } else {
                    $('#paymentInstructionModal #va-number-exp-warning').html(`
                        <div class="alert p-1 alert-warning d-flex flex-row align-items-start">
                            <div class="me-1">
                                <i data-feather="alert-triangle"></i>
                            </div>
                            <div>
                                ${paymentMethod.mpm_type == 'bank_transfer_va' ? 'Nomor Virtual Account' : 'Kode Bill'} akan kadaluwarsa pada ${moment(bill.prrb_midtrans_transaction_exp).format('DD-MM-YYYY HH:mm')}. Segera selesaikan pembayaran Anda.
                            </div>
                        </div>
                    `);
                }
            } else {
                $('#paymentInstructionModal #va-number-exp-warning').html('');
            }

            // RENDER PAYMENT INFORMATION
            $('#paymentInstructionModal #table-pay-data tbody').html(`
                <tr>
                    <td style="width: 300px" class="table-light fw-bolder">Metode Pembayaran</td>
                    <td>${paymentMethod.mpm_name}</td>
                </tr>

                ${paymentMethod.mpm_type == 'bank_transfer_va' ? `
                    <tr>
                        <td style="width: 300px" class="table-light fw-bolder">Nomor Virtual Account</td>
                        <td>${
                            moment().isAfter(moment(bill.prrb_midtrans_transaction_exp)) ? '-'
                                : bill.prrb_va_number
                        }</td>
                    </tr>
                ` : ''}

                ${paymentMethod.mpm_type == 'bank_transfer_manual' ? `
                    <tr>
                        <td style="width: 300px" class="table-light fw-bolder">Nomor Rekening</td>
                        <td>${bill.prrb_account_number}</td>
                    </tr>
                ` : ''}

                ${paymentMethod.mpm_type == 'bank_transfer_bill_payment' ? `
                    <tr>
                        <td style="width: 300px" class="table-light fw-bolder">Biller Code</td>
                        <td>${bill.prrb_mandiri_biller_code}</td>
                    </tr>
                    <tr>
                        <td style="width: 300px" class="table-light fw-bolder">Bill Key</td>
                        <td>${bill.prrb_mandiri_bill_key}</td>
                    </tr>
                ` : ''}

                <tr>
                    <td style="width: 300px" class="table-light fw-bolder">Jumlah Tagihan Awal</td>
                    <td>${Rupiah.format(bill.prrb_amount)}</td>
                </tr>

                <tr>
                    <td style="width: 300px" class="table-light fw-bolder">Biaya Admin</td>
                    <td>${Rupiah.format(paymentMethod.mpm_fee)}</td>
                </tr>

                ${studentBalanceSpent > 0 ? `
                    <tr>
                        <td style="width: 300px" class="table-light fw-bolder">Jumlah Tagihan Awal</td>
                        <td>${Rupiah.format(nominalInvoice)}</td>
                    </tr>

                    <tr>
                        <td style="width: 300px" class="table-light fw-bolder">Potongan</td>
                        <td>
                            <small class="text-nowrap">Penggunaan saldo mahasiswa: <strong>${Rupiah.format(studentBalanceSpent)}</strong><small>
                        </td>
                    </tr>
                `: ''}

                <tr>
                    <td style="width: 300px" class="table-light fw-bolder">Jumlah Tagihan Akhir</td>
                    <td>${Rupiah.format(nominalHasToPaid)}</td>
                </tr>

                <tr>
                    <td style="width: 300px; color: #0BA44C !important;" class="table-success text-success fw-bolder">Jumlah yang Telah Dibayar</td>
                    <td style="color: #0BA44C !important;" class="text-success fw-bolder">${Rupiah.format(nominalPaid)}</td>
                </tr>
                <tr>
                    <td style="width: 300px; color: #ff9f43 !important;" class="table-warning text-warning fw-bolder">Jumlah yang Belum Dibayar</td>
                    <td style="color: #ff9f43 !important;" class="text-warning fw-bolder">${Rupiah.format(nominalUnpaid)}</td>
                </tr>
            `);

            // RENDER EVIDENCE PAYMENT HISTORY
            $('#paymentInstructionModal #payment-evidence-section').html('');
            if (paymentMethod.mpm_type == 'bank_transfer_manual') {
                $('#paymentInstructionModal #payment-evidence-section').html(`
                    <div class="section-border mt-3">
                        <h4 class="mb-0">Riwayat Bukti Pembayaran</h4>
                        <table id="table-approval-history" class="table table-bordered mt-1">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Total Bayar</th>
                                    <th>Waktu Bayar</th>
                                    <th class="text-center">Status Approval</th>
                                    <th class="text-center">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${
                                    paymentApprovals.length <= 0 ? `
                                        <tr>
                                            <td colspan="5" class="text-center">Belum ada bukti pembayaran</td>
                                        </tr>
                                    ` : paymentApprovals.map((item, index) => {
                                            let status = '';
                                            if (item.pma_approval_status == 'accepted') status = '<span class="badge bg-success">Diterima</span>';
                                            if (item.pma_approval_status == 'rejected') status = '<span class="badge bg-danger">Ditolak</span>';
                                            if (item.pma_approval_status == 'waiting') status = '<span class="badge bg-warning">Menunggu</span>';

                                            return `
                                                <tr>
                                                    <td>${index+1}</td>
                                                    <td>${Rupiah.format(item.pma_amount)}</td>
                                                    <td>${moment(item.pma_payment_time).format('DD/MM/YYYY HH:mm')}</td>
                                                    <td class="text-center">${status}</td>
                                                    <td class="text-center">
                                                        <button class="btn btn-info btn-sm btn-icon rounded" onclick="payBillModal.openPaymentApprovalDetailModal(${item.prrb_id},${item.pma_id})">
                                                            <i data-feather="eye"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            `
                                        }).join('')
                                }
                            </tbody>
                        </table>
                        <div id="upload-payment-evidence-section">
                            ${
                                ['belum lunas', 'kredit'].includes(bill.computed_payment_status) || payBillModal.alwaysAllowUploadEvidence ? `
                                    <div class="d-flex justify-content-center mt-1">
                                        <button onclick="payBillModal.openUploadEvidenceModal(${bill.prrb_id})" class="btn btn-success">Unggah Bukti Pembayaran</button>
                                    </div>
                                ` : ''
                            }
                        </div>
                    </div>
                `);
            }

            // RENDER TRANSACTION HISTORY
            $('#paymentInstructionModal #table-transaction-history tbody').html(`
                <tr>
                    <td colspan="4" class="text-center">Belum ada transaksi</td>
                </tr>
            `);
            if (paymentTransactions.length > 0) {
                $('#paymentInstructionModal #table-transaction-history tbody').html(`
                    ${
                        paymentTransactions.map((item, index) => {
                            let desc = 'Descrpition not available';
                            const payment_type = item.payment_method.mpm_type;

                            if (payment_type == 'bank_transfer_manual') {
                                desc = _datatableTemplates.listCell([
                                    {text: `Metode Pembayaran : ${nullSafeView(item.payment_method.mpm_name)}`, bold: true, small: true, nowrap: true},
                                    {text: `Nomor Rekening Pengirim : ${nullSafeView(item.prrt_sender_account_number)}`, bold: true, small: true, nowrap: true},
                                    {text: `Nomor Rekening Tujuan : ${nullSafeView(item.prrt_receiver_account_number)}`, bold: true, small: true, nowrap: true},
                                ]);
                            }
                            else if (payment_type == 'bank_transfer_va') {
                                desc = _datatableTemplates.listCell([
                                    {text: `Metode Pembayaran : ${nullSafeView(item.payment_method.mpm_name)}`, bold: true, small: true, nowrap: true},
                                    {text: `Nomor Virtual Account : ${nullSafeView(item.prrt_va_number)}`, bold: true, small: true, nowrap: true},
                                ]);
                            }
                            else if (payment_type == 'bank_transfer_bill_payment') {
                                desc = _datatableTemplates.listCell([
                                    {text: `Metode Pembayaran : ${nullSafeView(item.payment_method.mpm_name)}`, bold: true, small: true, nowrap: true},
                                    {text: `Bill Key : ${nullSafeView(item.prrt_mandiri_bill_key)}`, bold: true, small: true, nowrap: true},
                                    {text: `Biller Code : ${nullSafeView(item.prrt_mandiri_biller_code)}`, bold: true, small: true, nowrap: true},
                                ]);
                            }

                            return `
                                <tr>
                                    <td>${index+1}</td>
                                    <td>${Rupiah.format(item.computed_initial_amount)}</td>
                                    <td>${moment(item.prrt_time).format('DD/MM/YYYY HH:mm')}</td>
                                    <td>${desc}</td>
                                </tr>
                            `
                        }).join('')
                    }
                `);
            }

            // RENDER OVERPAYMENT HISTORY
            $('#paymentInstructionModal #overpayment-history-section').html('');
            if (paymentMethod.mpm_type == 'bank_transfer_manual') {
                $('#paymentInstructionModal #overpayment-history-section').html(`
                    <div class="section-border mt-3">
                        <h4 class="mb-1">Riwayat Kelebihan Bayar</h4>
                        <table id="table-overpayment-history" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Saldo Masuk</th>
                                    <th>Keterangan</th>
                                    <th>Waktu Transaksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${
                                    paymentOverpayments.length <= 0 ? `
                                        <tr>
                                            <td colspan="4" class="text-center">Belum ada kelebihan bayar</td>
                                        </tr>
                                    ` : paymentOverpayments.map((item, index) => {
                                            return `
                                                <tr>
                                                    <td>${Rupiah.format(item.sbt_amount)}</td>
                                                    <td>${item.type.sbtt_description}</td>
                                                    <td>${moment(item.sbt_time).format('DD/MM/YYYY HH:mm')}</td>
                                                </tr>
                                            `
                                        }).join('')
                                }
                            </tbody>
                        </table>
                    </div>
                `);
            }

            // RENDER RESET PAYMENT METHOD BUTTON
            if (paymentMethod.mpm_type == 'bank_transfer_va' && ['belum lunas', 'kredit'].includes(bill.computed_payment_status)) {
                $('#paymentInstructionModal .modal-footer').html(`
                    <div class="d-flex justify-content-end">
                        <button onclick="payBillModal.resetPaymentMethod(${bill.prrb_id})" class="btn btn-outline-warning">Ganti Metode Pembayaran</button>
                    </div>
                `);
            }
            else if (paymentMethod.mpm_type == 'bank_transfer_manual' && bill.payment_manual_approval.length == 0) {
                $('#paymentInstructionModal .modal-footer').html(`
                    <div class="d-flex justify-content-end">
                        <button onclick="payBillModal.resetPaymentMethod(${bill.prrb_id})" class="btn btn-outline-warning">Ganti Metode Pembayaran</button>
                    </div>
                `);
            }
            else {
                $('#paymentInstructionModal .modal-footer').html('');
            }

            feather.replace();
            paymentInstructionModal.show();
        },
        openUploadEvidenceModal: function(prrbId) {
            Modal.show({
                type: 'form',
                modalTitle: 'Unggah Bukti Pembayaran',
                modalSize: 'lg',
                config: {
                    formId: 'form-upload-evidence',
                    formActionUrl: `${_baseURL}/api/payment/student-invoice/${prrId}/bill/${prrbId}/evidence`,
                    formType: 'add',
                    isTwoColumn: true,
                    fields: {
                        student_type: {
                            isHidden: true,
                            content: {
                                template: '<input type="hidden" name="student_type" value=":value" />',
                                value: 'student'
                            },
                        },
                        sender_account_name: {
                            title: 'Nama Rekening Anda',
                            content: {
                                template: '<input type="text" name="sender_account_name" class="form-control" />',
                            },
                        },
                        sender_account_number: {
                            title: 'Nomor Rekening Anda',
                            content: {
                                template: '<input type="text" name="sender_account_number" class="form-control" />',
                            },
                        },
                        sender_bank: {
                            title: 'Bank Rekening Anda',
                            content: {
                                template: '<input type="text" name="sender_bank" class="form-control" />',
                            },
                        },
                        amount: {
                            title: 'Nominal yang dibayar',
                            content: {
                                template: '<input type="text" class="form-control input-evidence-transfer-amount" />',
                            },
                        },
                        receiver_account_number: {
                            title: 'Nomor Rekening Tujuan',
                            content: {
                                template: '<input type="text" name="receiver_account_number" class="form-control" />',
                            },
                        },
                        receiver_account_name: {
                            title: 'Nama Rekening Tujuan',
                            content: {
                                template: '<input type="text" name="receiver_account_name" class="form-control" />',
                            },
                        },
                        receiver_bank: {
                            title: 'Bank Tujuan',
                            content: {
                                template: '<input type="text" name="receiver_bank" class="form-control" />',
                            },
                        },
                        payment_date: {
                            title: 'Waktu Pembayaran',
                            content: {
                                template: '<input type="datetime-local" name="payment_time" class="form-control" />',
                            },
                        },
                        evidence: {
                            title: 'Bukti Pembayaran',
                            content: {
                                template: '<input type="file" name="evidence" class="form-control" />',
                            },
                        },
                    },
                    formSubmitLabel: 'Unggah',
                    callback: function(e) {
                        // close payment instruction modal
                        paymentInstructionModal.hide();

                        // reopen payment instruction modal
                        payBillModal.open(prrbId);
                    },
                },
            });

            _numberCurrencyFormat.load('input-evidence-transfer-amount', 'amount');

            $("#form-upload-evidence input[name=payment_time]").flatpickr({
                altInput: true,
                altFormat: "d/m/Y H:i",
                dateFormat: 'Y-m-d H:i:s',
                enableTime: true,
                time_24hr: true,
                allowInput: true,
                hourIncrement: 1,
                minuteIncrement: 1,
            });
        },
        openPaymentApprovalDetailModal: async function(prrbId, pmaId) {
            const approval = await $.ajax({
                async: true,
                url: `${_baseURL}/api/payment/student-invoice/${prrId}/bill/${prrbId}/approval/${pmaId}`,
                type: 'get',
            });

            Modal.show({
                type: 'detail',
                modalTitle: 'Detail Approval Pembayaran',
                modalSize: 'xl',
                config: {
                    isThreeColumn: true,
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
                        student_studyprogram: {
                            title: 'Program Studi',
                            content: {
                                template: `:value`,
                                value: approval.pma_student_studyprogram,
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
                                value: approval.pma_approval_status == 'waiting' ? 'Menunggu'
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
                    },
                    callback: function() {
                        feather.replace();
                    }
                },
            });

        },
        paymentMethodChangeHandler: async function(e) {
            // console.log('payment method changed');

            let studentBalance = 0;
            try {
                // get student balance
                const res = await $.ajax({
                    url: `${_baseURL}/api/payment/student-balance/transaction`,
                    data: {student_id: studentMaster.student_id},
                    processData: true,
                    type: 'get'
                });
                studentBalance = res.sbt_closing_balance;
            } catch (error) {
                studentBalance = 0;
            }

            // clear selected payment method (ui)
            $('.payment-method-list__item').each(function() {
                $(this).removeClass('active');
            });

            // get prrb_id value
            const prrbId = parseInt($('#form-payment-method input[name="prrb_id"]').val());

            const paymentMethodKey = e.target.value;
            $(`.payment-method-list__item > input[value="${paymentMethodKey}"]`).parent().addClass('active');

            // fetch bill data
            const bill = await $.ajax({
                async: true,
                url: `${_baseURL}/api/payment/student-invoice/${prrId}/bill/${prrbId}`,
                type: 'get',
            });

            // fetch payment method data
            const paymentMethodRes = await $.ajax({
                async: true,
                url: `${_baseURL}/api/payment/payment-method/${paymentMethodKey}`,
                type: 'get',
            });

            // update payment method state
            paymentMethodMaster.state = {
                bill: bill,
                paymentMethod: paymentMethodRes,
                studentBalanceSpend: 0,
            };

            // Dispatch paymentMethodChange Event
            paymentMethodMaster.managerElm.dispatchEvent(paymentMethodMaster.createChangeEvent());

            // render 'use student balance' section
            if (studentBalance != null && studentBalance > 0) {

                let totalBill = bill.prrb_amount + paymentMethodRes.mpm_fee;
                let max = studentBalance;
                if (totalBill < max) max = totalBill;

                $('#paymentMethodModal #use-student-balance').html(`
                    <div class="mt-3 alert alert-info border p-1 d-flex flex-column" style="gap: 1rem">
                        <div class="text-dark fw-normal">
                            Anda memiliki saldo kelebihan bayar.<br>
                            Total Saldo : <strong>${Rupiah.format(studentBalance)}</strong>
                        </div>
                        <form id="form-use-student-balance" action="javascript:void(0);" onsubmit="payBillModal.useStudentBalance()">
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="text" class="form-control input-balance-spend" placeholder="Masukan nominal">
                                <button class="btn btn-primary" type="submit">Pakai Saldo</button>
                            </div>
                        </form>
                    </div>
                `);
                _numberCurrencyFormat.load('input-balance-spend', 'balance_spend');
            }
        },
        useStudentBalance: function() {
            const { balance_spend: balanceSpend } = FormDataJson.toJson('#paymentMethodModal #form-use-student-balance');

            // update payment method state
            paymentMethodMaster.state = {
                ...paymentMethodMaster.state,
                studentBalanceSpend: balanceSpend,
            };

            // Dispatch paymentMethodChange Event
            paymentMethodMaster.managerElm.dispatchEvent(paymentMethodMaster.createChangeEvent());
        },
        selectPaymentMethod: async function() {
            const confirmed = await _swalConfirmSync({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin memilih metode pembayaran ini?',
            });

            if(!confirmed) return;

            // fields: prrb_id, payment_method, use_student_balance, student_balance_amount
            const reqData = FormDataJson.toJson('#paymentMethodModal #form-payment-method');
            // console.log(reqData);
            // return;
            try {
                const res = await $.ajax({
                    async: true,
                    url: `${_baseURL}/api/payment/student-invoice/${prrId}/bill/${reqData.prrb_id}/select-method`,
                    type: 'post',
                    data: reqData,
                });

                if (res.success) {
                    _toastr.success(res.message, 'Sukses');
                    // hide payment method modal
                    paymentMethodModal.hide();
                    // open payment instruction modal
                    payBillModal.open(reqData.prrb_id);
                }

            } catch (error) {
                // console.error(error);
                const res = error.responseJSON;
                _toastr.error(res.message, 'Gagal');
            }
        },
        resetPaymentMethod: async function(prrbId) {
            const confirmed = await _swalConfirmSync({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin mengganti metode pembayaran?',
            });

            if(!confirmed) return;

            try {
                const res = await $.ajax({
                    async: true,
                    url: `${_baseURL}/api/payment/student-invoice/${prrId}/bill/${prrbId}/reset-method`,
                    type: 'post',
                });

                if (res.success) {
                    _toastr.success(res.message, 'Sukses');
                    // hide payment method modal
                    paymentInstructionModal.hide();
                    // open payment method modal
                    payBillModal.open(prrbId);
                }

            } catch (error) {
                const res = error.responseJSON;
                _toastr.error(res.message, 'Gagal');
            }
        },
        uploadPaymentEvidence: async function(e) {
            e.preventDefault();

            const target = e.currentTarget;
            const formData = new FormData(target);
            const prrIdValue = formData.get('prr_id'); formData.delete('prr_id');
            const prrbIdValue = formData.get('prrb_id'); formData.delete('prrb_id');

            // for (const value of formData.values()) { console.log(value) }; return;

            try {
                const res = await $.ajax({
                    async: true,
                    type: "POST",
                    url: `${_baseURL}/api/payment/student-invoice/${prrIdValue}/bill/${prrbIdValue}/evidence`,
                    data: formData,
                    processData: false,
                    contentType: false,
                    cache: false,
                });

                if (res.success) {
                    _toastr.success(res.message, 'Sukses');
                    paymentInstructionModal.hide();
                    await deleteRequestCache(`${_baseURL}/api/payment/student-invoice/${prrId}`);
                    payBillModal.open(prrbIdValue);
                } else {
                    _toastr.error(res.message, 'Gagal');
                }

            } catch (error) {
                console.error('Error Happen', error);
            }
        },
    }

    const paymentMethodModal = new bootstrap.Modal(document.getElementById('paymentMethodModalV2'));
    const paymentInstructionModal = new bootstrap.Modal(document.getElementById('paymentInstructionModal'));

    function nullSafeView(value) {
        return value ?? '<span class="badge bg-secondary">n/a</span>';
    }

    async function regenerateVAN(billId) {
        const confirmed = await _swalConfirmSync({
            title: 'Konfirmasi',
            text: 'Apakah anda yakin ingin melakukan regenerate va?',
        });

        if(!confirmed) return;

        const billMasterId = prrId;

        const res = await $.ajax({
            async: true,
            url: `${_baseURL}/api/payment/student-invoice/${billMasterId}/bill/${billId}/regenerate-va`,
            type: 'post',
        });

        if (res.success) {
            console.log('success regenerate va.')
            _toastr.success(res.message, 'Sukses');

            // reopen payment instruction modal
            paymentInstructionModal.hide();
            payBillModal.open(billId);
        } else {
            console.log('failed regenerate va!');
            _toastr.error(res.message, 'Gagal');
        }
    }
</script>
@endprepend
