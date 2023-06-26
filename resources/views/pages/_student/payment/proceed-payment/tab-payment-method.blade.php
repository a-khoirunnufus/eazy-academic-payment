@push('styles')
    <style>
        #payment-method-selected,
        #payment-method-unselected {
            display: none;
        }
        #payment-method-selected.show,
        #payment-method-unselected.show {
            display: block;
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
@endpush

<div id="payment-method-selected">...</div>

<div id="payment-method-unselected">
    <div id="select-payment-method" class="d-flex flex-column w-100">
        <h4 class="mb-2">Pilih Metode Pembayaran</h4>
        <div>
            <p>Bank Transfer</p>
            <div class="payment-method">...</div>
        </div>
        <div class="mt-3">
            <button id="btn-select-payment" onclick="paymentMethodTab.selectMethod()" class="btn btn-primary">Pilih Metode Pembayaran</button>
        </div>
    </div>

    <!-- <button type="button" class="btn btn-outline-success d-inline-block text-success me-1" style="height: fit-content">Tata cara pembayaran&nbsp;&nbsp;<i data-feather="book"></i></button>
    <button class="btn btn-success btn-icon d-inline-block" style="height: fit-content">
        <i data-feather="printer"></i>
    </button> -->

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
            // get selected payment method
            const payment = await getRequestCache(`${_baseURL}/api/student/payment/detail/${prrId}`);
            const selectedMethod = payment.payment_method ?? {mpm_key: null};

            if (selectedMethod.mpm_key) {
                $('#nav-payment-method #payment-method-selected').html(`
                    <div
                        class="alert alert-success p-1 d-inline-block"
                        style="width: fit-content; margin: 0 auto;"
                    >
                        <i data-feather="check"></i>&nbsp;&nbsp;
                        Anda telah memilih metode pembayaran <strong>Bank Transfer ${selectedMethod.mpm_name}</strong>
                    </div>
                `);
                feather.replace();

                $('#nav-payment-method #payment-method-selected').addClass('show');
                $('#nav-payment-method #payment-method-unselected').removeClass('show');
            } else {
                // get all payment method available
                const paymentMethods = await getRequestCache(`${_baseURL}/api/student/payment-method`);
                $('#nav-payment-method #select-payment-method .payment-method').html(`
                    ${
                        paymentMethods.map(item => {
                            return `
                                <div
                                    class="payment-method__item"
                                    data-eazy-method="${item.mpm_key}"
                                    data-eazy-selected="${item.mpm_key == selectedMethod.mpm_key ? 'true' : 'false'}"
                                >
                                    <span>${item.mpm_name}</span>
                                </div>
                            `;
                        }).join('')
                    }
                `);
                paymentMethodTab.setupClickableMethod();

                $('#nav-payment-method #payment-method-selected').removeClass('show');
                $('#nav-payment-method #payment-method-unselected').addClass('show');
            }
        },
        selectMethod: async function() {
            try {
                const selectedMethod = $('.payment-method__item[data-eazy-selected="true"]').attr('data-eazy-method');

                if(!selectedMethod) {
                    _toastr.warning('Silahkan pilih metode pembayaran terlebih dahulu.', 'Metode Belum Dipilih');
                    throw new Error('Metode belum dipilih');
                }

                const confirmed = await _swalConfirmSync({
                    title: 'Konfirmasi',
                    text: 'Apakah anda yakin ingin memilih metode pembayaran ini?',
                });

                if(!confirmed) return;

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

                tabManager.updateDisableState();

            } catch (error) {
                console.error('Something Wrong!', error);
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
