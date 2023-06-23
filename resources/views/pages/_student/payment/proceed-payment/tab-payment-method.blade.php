@push('styles')
    <style>
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
@endpush

<div class="d-flex flex-row">
    <div id="select-payment-method" class="d-flex flex-column pe-3 w-50">
        <h4 class="mb-2">Pilih Metode Pembayaran</h4>
        <div class="mb-2">
            <p>Bank Transfer</p>
            <div class="payment-method">...</div>
        </div>
        <div>
            <button onclick="paymentMethodTab.selectMethod()" class="btn btn-primary">Pilih Metode Pembayaran</button>
        </div>
    </div>

    <div id="payment-detail" class="ps-3 w-50">
        <h4 class="mb-2">Detail Pembayaran</h4>
        <div class="mb-2">
            <p class="text-secondary">Nomor Virtual Akun Bank</p>
            <h4 class="text-primary" id="bank-va-number">...</h4>
        </div>
        <div class="d-flex mb-2" style="gap: 2rem">
            <div class="d-inline-block">
                <p class="text-secondary">Biaya Awal</p>
                <h4 class="text-primary" id="initial-cost">...</h4>
            </div>
            <div class="d-inline-block">
                <p class="text-secondary">Biaya Admin</p>
                <h4 class="text-primary" id="admin-fee">...</h4>
            </div>
        </div>
        <!-- <button type="button" class="btn btn-outline-success d-inline-block text-success me-1" style="height: fit-content">Tata cara pembayaran&nbsp;&nbsp;<i data-feather="book"></i></button>
        <button class="btn btn-success btn-icon d-inline-block" style="height: fit-content">
            <i data-feather="printer"></i>
        </button> -->
    </div>
</div>

@prepend('scripts')
<script>

    /**
     * @var integer prrId
     * @var object tabManager
     * @func getRequestCache()
     */

    const paymentMethodTab = {
        showHandler: async function() {
            console.log('payment method tab opened');

            // get all payment method available
            const paymentMethods = await getRequestCache(`${_baseURL}/api/student/payment-method`);
            $('#nav-payment-method #select-payment-method .payment-method').html(`
                ${
                    paymentMethods.map(item => {
                        return `
                            <div class="payment-method__item" data-eazy-method="${item.mpm_key}" data-eazy-selected="false">
                                <span>${item.mpm_name}</span>
                            </div>
                        `;
                    }).join('')
                }
            `);
            paymentMethodTab.setupClickableMethod();

            const payment = await getRequestCache(`${_baseURL}/api/student/payment/${prrId}`);
            const selectedMethod = paymentMethods.filter(item => item.mpm_key == payment.prr_method);
            $('#nav-payment-method #payment-detail #bank-va-number').text(
                selectedMethod[0] ? selectedMethod[0].mpm_name+' - '+selectedMethod[0].mpm_account_number : '-'
            );
            $('#nav-payment-method #payment-detail #admin-fee').text(
                selectedMethod[0] ? Rupiah.format(selectedMethod[0].mpm_fee) : '-'
            );

            $('#nav-payment-method #payment-detail #initial-cost').text(`
                ${Rupiah.format(
                    payment.payment_detail.reduce((acc, curr) => {
                        return parseInt(curr.is_plus) == 1 ?
                            acc + parseInt(curr.prrd_amount)
                            : acc - parseInt(curr.prrd_amount);
                    }, 0)
                )}
            `);
        },
        selectMethod: async function() {
            try {
                const selectedMethod = $('.payment-method__item[data-eazy-selected="true"]').attr('data-eazy-method');

                if(!selectedMethod) {
                    _toastr.warning('Silahkan pilih metode pembayaran terlebih dahulu.', 'Metode Belum Dipilih');
                    throw new Error('Metode belum dipilih');
                }

                const res = await $.ajax({
                    url: `${_baseURL}/api/student/payment/select-method`,
                    type: 'post',
                    data: {
                        prr_id: prrId,
                        method: selectedMethod,
                    }
                });

                if (!res.success) {
                    throw new Error(res.message);
                }

                _toastr.success(res.message, 'Berhasil');

                this.showHandler();

                tabManager.updateDisableState();

            } catch (error) {
                console.error('Something Wrong!', error);
            }
        },
        nextStep: async function() {
            const payment = await getRequestCache(`${_baseURL}/api/student/payment/${prrId}`);
            if (!payment.prr_method) {
                _toastr.warning('Silahkan pilih metode pembayaran terlebih dahulu.', 'Metode Belum Dipilih');
            }
        },
        setupClickableMethod: function() {
            $('.payment-method__item').click(function() {
                const isSelected = $(this).attr('data-eazy-selected') == 'true';
                if (!isSelected) {
                    $(this).parent().children().each(function() {
                        $(this).attr('data-eazy-selected', 'false');
                    });
                    $(this).attr('data-eazy-selected', 'true');
                }
            });
        },
    }
</script>
@endprepend
