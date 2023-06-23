<div class="d-flex flex-column align-items-center">
    <h4>Pilih Opsi Pembayaran Daftar Ulang</h4>
    <p></p>
    <p>Silahkan pilih opsi pembayaran untuk dapat mengetahui informasi pembayaran daftar ulang yang akan anda lakukan!</p>
    <div class="mt-2">
        <div class="input-group" style="width: 400px">
            <select class="form-select" aria-label="Default select example">
                <option selected>Pilih Opsi Pembayaran</option>
                <option value="1">Full 100%</option>
                <option value="2">Pembayaran Cicilan 2X</option>
                <option value="2">Pembayaran Cicilan 3X</option>
                <option value="3">Pembayaran Cicilan 4X</option>
            </select>
            <button class="btn btn-primary" type="button" id="button-addon2">Terapkan</button>
        </div>
    </div>

    <table class="table table-bordered table-striped mt-3">
        <thead>
            <tr>
                <th>Pembayaran</th>
                <th>Persen Pembayaran</th>
                <th>Tenggat Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Cicilan Ke-1</td>
                <td>40%</td>
                <td>30-06-2023</td>
            </tr>

            <tr>
                <td>Cicilan Ke-2</td>
                <td>30%</td>
                <td>30-06-2023</td>
            </tr>

            <tr>
                <td>Cicilan Ke-3</td>
                <td>30%</td>
                <td>30-06-2023</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-end mt-3">
    <a href="{{ url('student/payment/proceed-payment/'.$prr_id.'/select-payment-option') }}" class="btn btn-primary">Simpan dan Lanjutkan</a>
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
            const creditSchemas = await getRequestCache(`${_baseURL}/api/student/payment-method`);

        },
        commitPayment: async function() {
            const payment = await getRequestCache(`${_baseURL}/api/student/payment/${prrId}`);
            if (!payment.prr_method) {
                _toastr.warning('Silahkan pilih metode pembayaran terlebih dahulu.', 'Metode Belum Dipilih');
            }
        },
    }
</script>
@endprepend
