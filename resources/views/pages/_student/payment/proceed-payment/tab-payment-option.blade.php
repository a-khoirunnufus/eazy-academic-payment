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

<div id="payment-option-selected">...</div>

<div id="payment-option-unselected">
    <div class="d-flex flex-column align-items-center">
        <h4>Pilih Opsi Pembayaran Daftar Ulang</h4>
        <p></p>
        <p>Silahkan pilih opsi pembayaran untuk dapat mengetahui informasi pembayaran daftar ulang yang akan anda lakukan!</p>
        <div class="mt-2">
            <div class="input-group" style="width: 400px">
                <select id="select-payment-option" class="form-select">
                </select>
                <button onclick="paymentOptionTab.selectPaymentOption()" id="btn-select-payment-option" class="btn btn-primary" type="button">Pilih Opsi</button>
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
            const payment = await getRequestCache(`${_baseURL}/api/student/payment/detail/${prrId}`);

            const studentType = payment.register ? 'new_student' : 'student';

            if (payment.payment_bill.length > 0) {
                $('#payment-option-selected').html(`
                    <div class="alert alert-success p-1 d-inline-block"><i data-feather="check"></i>&nbsp;&nbsp;Opsi Pembayaran Telah Dipilih</div>
                `);

                $('#nav-payment-option #payment-option-selected').addClass('show');
                $('#nav-payment-option #payment-option-unselected').removeClass('show');
                feather.replace();
            }
            else {
                const creditSchemas = await getRequestCache(`${_baseURL}/api/student/payment/credit-schemas/${prrId}?student_type=${studentType}`);
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
            const payment = await getRequestCache(`${_baseURL}/api/student/payment/detail/${prrId}`);
            const {ppm_id: ppmId} = await getRequestCache(`${_baseURL}/api/student/payment/ppm/${prrId}`);
            const optionPreview = await getRequestCache(`${_baseURL}/api/student/payment/payment-option-preview/${cs_id}?ppm_id=${ppmId ?? 0}`);

            const invoiceTotal = payment.payment_detail.reduce((acc, curr) => {
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

            const payment = await getRequestCache(`${_baseURL}/api/student/payment/detail/${prrId}`);

            const selectedCreditSchemaId = $('#select-payment-option').val();

            try {
                const res = await $.ajax({
                    async: true,
                    url: `${_baseURL}/api/student/payment/create-bill/${prrId}`,
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
