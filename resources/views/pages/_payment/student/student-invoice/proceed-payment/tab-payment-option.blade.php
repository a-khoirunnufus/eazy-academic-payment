@push('styles')
    <style>
        #payment-option-selected,
        #payment-option-unselected {
            display: none;
        }
        #payment-option-selected.show,
        #payment-option-unselected.show {
            display: block;
        }
    </style>
@endpush

<div id="payment-option-selected">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Pembayaran</th>
                <th>Nominal Pembayaran</th>
                <th>Tenggat Pembayaran</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<div id="payment-option-unselected">
    <div class="d-flex flex-column align-items-center">
        <h4>Pilih Opsi Pembayaran</h4>
        <p></p>
        <p>Silahkan pilih opsi pembayaran untuk dapat mengetahui informasi pembayaran yang akan anda lakukan!</p>
        <div class="mt-2">
            <div class="input-group" style="width: 400px">
                <select id="select-payment-option" class="form-select">
                </select>
                <button onclick="paymentOptionTab.selectPaymentOption()" id="btn-select-payment-option" class="btn btn-info" type="button">Pilih Opsi</button>
            </div>
        </div>

        <table id="table-payment-option-preview" class="table table-bordered table-striped mt-3">
            <thead>
                <tr>
                    <th>Pembayaran</th>
                    <th>Nominal Pembayaran</th>
                    <th>Tenggat Pembayaran</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

@prepend('scripts')
<script>

    /**
     * @var integer prrId
     * @var object tabManager
     * @func getRequestCache()
     */

    $(function(){
        $('#select-payment-option').change(function(){
            const creditSchemaId = $(this).val();
            paymentOptionTab.renderOptionPreview(creditSchemaId);
        });
    });

    const paymentOptionTab = {
        showHandler: async function() {
            const billMaster = await $.ajax({
                async: true,
                url: `${_baseURL}/api/payment/student-invoice/${prrId}`,
                data: {
                    withData: ['paymentBill'],
                }
            });

            const studentType = 'student';

            if (billMaster.payment_bill.length > 0) {
                $('#payment-option-selected > table > tbody').html(`
                    ${
                        billMaster.payment_bill.map((item) => {
                            return `
                                <tr>
                                    <td>Cicilan Ke-${item.prrb_order}</td>
                                    <td>${Rupiah.format(item.prrb_amount)}</td>
                                    <td>${moment(item.prrb_expired_date).format('DD-MM-YYYY')}</td>
                                </tr>
                            `;
                        }).join('')
                    }
                `);

                $('#nav-payment-option #payment-option-selected').addClass('show');
                $('#nav-payment-option #payment-option-unselected').removeClass('show');
                feather.replace();
            }
            else {
                const creditSchemas = await $.ajax({
                    async: true,
                    url: `${_baseURL}/api/payment/student-invoice/${prrId}/credit-schemas?student_type=${studentType}`,
                    type: 'get',
                });
                $('#select-payment-option').html(`
                    ${
                        creditSchemas.map(item => {
                            return `<option value="${item.cs_id}">${item.cs_name}</option>`;
                        })
                    }
                `);

                const selectedCreditSchemaId = creditSchemas[0].cs_id;
                $('#select-payment-option').val(selectedCreditSchemaId);
                paymentOptionTab.renderOptionPreview(selectedCreditSchemaId);

                $('#nav-payment-option #payment-option-selected').removeClass('show');
                $('#nav-payment-option #payment-option-unselected').addClass('show');
            }
        },
        renderOptionPreview: async function(cs_id) {
            const billMaster = await $.ajax({
                async: true,
                url: `${_baseURL}/api/payment/student-invoice/${prrId}`,
                data: {
                    withData: ['paymentDetail'],
                }
            });
            const {ppm_id: ppmId} = await getRequestCache(`${_baseURL}/api/payment/student-invoice/${prrId}/ppm`);
            const optionPreview = await $.ajax({
                async: true,
                url: `${_baseURL}/api/payment/student-invoice/${prrId}/payment-option-preview?ppm_id=${ppmId ?? 0}&cs_id=${cs_id}`,
                type: 'get',
            });

            const invoiceTotal = billMaster.payment_detail.reduce((acc, curr) => {
                return parseInt(curr.is_plus) == 1 ?
                    acc + parseInt(curr.prrd_amount)
                    : acc - parseInt(curr.prrd_amount);
            }, 0);
            $('#table-payment-option-preview tbody').html(`
                ${
                    optionPreview.credit_schema.credit_schema_detail.map((item) => {
                        return `
                            <tr>
                                <td>Cicilan Ke-${item.csd_order}</td>
                                <td>${Rupiah.format(
                                    invoiceTotal * ( parseInt(item.csd_percentage) / 100 )
                                )}</td>
                                <td>${moment(item.credit_schema_deadline.cse_deadline).format('DD-MM-YYYY')}</td>
                            </tr>
                        `;
                    }).join('')
                }
            `);
        },
        selectPaymentOption: async function() {
            const confirmed = await _swalConfirmSync({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin memilih opsi pembayaran ini?',
            });

            if(!confirmed) return;

            const selectedCreditSchemaId = $('#select-payment-option').val();

            try {
                const res = await $.ajax({
                    async: true,
                    url: `${_baseURL}/api/payment/student-invoice/${prrId}/select-option`,
                    type: 'post',
                    data: {
                        cs_id: selectedCreditSchemaId,
                    },
                });

                if (res.success) {
                    _toastr.success(res.message, 'Sukses');
                    tabManager.updateDisableState();
                } else {
                    _toastr.error(res.message, 'Gagal');
                }
            } catch (error) {
                _toastr.error('Gagal memilih opsi pembayaran!', 'Gagal');
            }
        },
    }
</script>
@endprepend
