<!-- Parent Id = 'nav-invoice-data' -->

<!-- <div id="invoice-notes" class=" mb-3">
    <h4 class="fw-bolder mb-1">Keterangan Tagihan</h4>
    <div id="invoice-notes-text">...</div>
</div> -->

<div id="invoice-data" class="mb-2">
    <!-- <h4 class="fw-bolder mb-1">Data Tagihan</h4> -->
    <table class="eazy-table-info">
        <tbody>
            <tr>
                <td>Nomor Tagihan</td>
                <td>
                    <span>
                        :&nbsp;&nbsp;<span id="invoice-data-number">...</span>
                    </span>
                </td>
            </tr>
            <tr>
                <td>Tanggal Dibuat</td>
                <td>
                    <span>
                        :&nbsp;&nbsp;<span id="invoice-data-created">...</span>
                    </span>
                </td>
            </tr>
            <!-- <tr>
                <td>Status Pembayaran</td>
                <td>
                    <span>
                        :&nbsp;&nbsp;<span id="invoice-data-status">...</span>
                    </span>
                </td>
            </tr> -->
            <tr>
                <td>Keterangan</td>
                <td>
                    <span>
                        :&nbsp;&nbsp;<span id="invoice-data-notes">...</span>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div id="invoice-detail">
    <!-- <h4 class="fw-bolder mb-1">Detail Tagihan</h4> -->
    <table id="table-invoice-detail" class="table table-bordered">
        <thead>
            <tr>
                <th>Komponen Tagihan</th>
                <th>Biaya Bayar</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot></tfoot>
    </table>
    <small class="d-block my-1">*Total tagihan belum termasuk biaya tambahan.</small >
</div>

@prepend('scripts')
<script>

    /**
     * @var prrId
     * @func getRequestCache()
     */

    const invoiceDataTab = {
        showHandler: async function() {
            try {
                const payment = await getRequestCache(`${_baseURL}/api/student/payment/${prrId}`);

                const studentType = payment.register ? 'new_student' : 'student';

                $('#nav-invoice-data #invoice-data #invoice-data-number').text(payment.prr_id);
                $('#nav-invoice-data #invoice-data #invoice-data-created').text(moment(payment.created_at).format('DD-MM-YYYY'));
                // $('#nav-invoice-data #invoice-data #invoice-data-status').html(
                //     payment.prr_status == 'lunas' ?
                //         '<div class="badge bg-success" style="font-size: inherit">Lunas</div>'
                //         : '<div class="badge bg-danger" style="font-size: inherit">Belum Lunas</div>'
                // );

                $('#nav-invoice-data #invoice-data #invoice-data-notes').text(
                    `Tagihan ${studentType == 'new_student' ? 'Daftar Ulang' : 'Registrasi Semester Baru'}
                    Program Studi ${ studentType == 'new_student' ? `
                            ${payment.register.studyprogram.studyprogram_type.toUpperCase()}
                            ${payment.register.studyprogram.studyprogram_name}
                            ${payment.register.lecture_type?.mlt_name ?? 'N/A'}
                        ` : `
                            ${payment.student.studyprogram.studyprogram_type.toUpperCase()}
                            ${payment.student.studyprogram.studyprogram_name}
                            ${payment.student.lecture_type?.mlt_name ?? 'N/A'}
                        `
                    }
                    Tahun Ajaran ${payment.year.msy_year}
                    Semester ${payment.year.msy_semester}.`
                );

                $('#nav-invoice-data #invoice-detail #table-invoice-detail tbody').html(`
                    ${
                        payment.payment_detail.map(item => {
                            return `
                                <tr>
                                    <td>${item.prrd_component}</td>
                                    <td>${parseInt(item.is_plus) == 0 ? '- ' : '' }${Rupiah.format(item.prrd_amount)}</td>
                                </tr>
                            `;
                        }).join('')
                    }
                `);
                $('#nav-invoice-data #invoice-detail #table-invoice-detail tfoot').html(`
                    <tr>
                        <th>Total Tagihan*</th>
                        <th>
                            ${Rupiah.format(
                                payment.payment_detail.reduce((acc, curr) => {
                                    return parseInt(curr.is_plus) == 1 ?
                                        acc + parseInt(curr.prrd_amount)
                                        : acc - parseInt(curr.prrd_amount);
                                }, 0)
                            )}
                        </th>
                    </tr>
                `);

            } catch (error) {
                console.log('Something Error', error);
            }
        },
    }
</script>
@endprepend
