@extends('tpl.vuexy.master-payment')

@section('page_title', 'Tagihan Mahasiswa')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
    <style>
        .eazy-table-info {
            display: inline-block;
        }
        .eazy-table-info td {
            padding: 5px 10px 5px 0;
        }
        .eazy-table-info.lg td {
            padding: 10px 10px 10px 0;
        }
        .eazy-table-info td:first-child {
            padding-right: 1rem;
            font-weight: 500;
        }

        .nav-tabs.custom .nav-item {
            /* flex-grow: 1; */
        }
        .nav-tabs.custom .nav-link {
            /* width: -webkit-fill-available !important; */
            height: 50px !important;
        }
        .nav-tabs.custom .nav-link.active {
            background-color: #f2f2f2 !important;
        }

        .eazy-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .eazy-header {
            display: flex;
            flex-direction: row;
            gap: 4rem;
            flex-wrap: wrap;
        }
        .eazy-header .eazy-header__item {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 1rem;
        }
        .eazy-header .eazy-header__item .item__icon {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 65px;
            height: 65px;
            border-radius: 1.5rem;
            background-color: rgba(246, 246, 246, 1) !important;
        }
        .eazy-header .eazy-header__item .item__icon svg {
            width: 35px;
            height: 35px;
        }
        .eazy-header .eazy-header__item .item__text {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .eazy-header .eazy-header__item .item__text .text__subtitle {
            display: block;
        }
        .eazy-header .eazy-header__item .item__text .text__title {
            display: block;
            font-weight: 700;
            font-size: 16px;
        }
        .line {
            text-decoration: line-through;
        }
        #report-unpaid_wrapper, #report-paid_wrapper {
            display: none;
        }
        #cicilan-invoice {
            display: none;
        }
    </style>
@endsection

@section('content')

<div class="card">
    <div class="nav-tabs-shadow nav-align-top">
        <ul class="nav nav-tabs custom border-bottom" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-invoice_n_va" aria-controls="navs-invoice_n_va" aria-selected="true">Tagihan Belum Lunas</button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-payment" aria-controls="navs-payment" aria-selected="false">Tagihan Lunas</button>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="navs-invoice_n_va" role="tabpanel">
                @include('pages._payment.student.student-invoice.tab-unpaid-invoice')
            </div>
            <div class="tab-pane fade" id="navs-payment" role="tabpanel">
                @include('pages._payment.student.student-invoice.tab-paid-invoice')
            </div>
        </div>
    </div>
</div>

<!-- Invoice Detail Modal -->
<div class="modal fade" id="invoiceDetailModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-white" style="padding: 2rem 3rem 3rem 3rem">
                <h4 class="modal-title fw-bolder" id="invoiceDetailModalLabel">Tagihan Mahasiswa</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 pt-0" id="detail-invoice">
            </div>
        </div>
    </div>
</div>

<div class="card" id="cicilan-invoice">
    <div class="container">
        <div class="row text-center">
            <h3 class="text-uppercase text-center mt-3" style="font-size: 40px;">Invoice</h3>
        </div>
        <div class="row">
            <div class="col">
                <p>Nomor Invoice : INV/459</p>
                <p>Digenerate Pada : 17-08-2023</p>
                <p>Status Tagihan : belum lunas</p>
            </div>
            <div class="col text-end">
                <p>Kepada : Oman</p>
                <p>S1 Informatika</p>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">TAGIHAN</th>
                    <th scope="col">NOMINAL TAGIHAN</th>
                    <th scope="col">BIAYA ADMIN</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('js_section')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>

    const studentMaster =  JSON.parse(`{!! json_encode($student, true) !!}`);

    const invoiceDetailModal = {
        bsModal: new bootstrap.Modal(document.getElementById('invoiceDetailModal')),
        open: async function(e, datatableObj) {
            invoiceDetailModal._resetContent();

            const masterBill = datatableObj.getRowData(e.currentTarget);

            const bills = await $.ajax({
                async: true,
                url: `${_baseURL}/api/payment/student-invoice/${masterBill.prr_id}/bill`,
                data: {
                    withAppend: [
                        'computed_due_date',
                        'computed_dispensation_applied',
                        'computed_payment_status',
                        'computed_paid_date',
                    ],
                }
            });

            // invoiceDetailModal._renderInvoiceNotes(masterBill.invoice_student_type, masterBill.notes);

            invoiceDetailModal._renderInvoiceData(masterBill);

            invoiceDetailModal._renderInvoiceDetail(masterBill);

            invoiceDetailModal._renderInvoiceBill(bills);

            invoiceDetailModal._renderProceedPayment(masterBill);

            feather.replace();

            invoiceDetailModal.bsModal.show();
        },
        _resetContent: function() {
            $('#invoiceDetailModal .modal-body').html('');
        },
        _renderInvoiceNotes: function(studentType, notes) {
            if (studentType == 'new_student') {
                $('#invoiceDetailModal .modal-body').append(`
                    <div class="mb-4">
                        <h4 class="fw-bolder mb-1">Keterangan Tagihan</h4>
                        <div>${notes}</div>
                    </div>
                `);
            }
        },
        _renderInvoiceData: function(masterBill) {
            let paymentStatusHtml = '<span class="badge bg-secondary" style="font-size: 1rem">N/A</span>';
            if (masterBill.computed_payment_status == 'belum lunas')
                paymentStatusHtml = '<span class="badge bg-danger" style="font-size: 1rem">Belum Lunas</span>';
            if (masterBill.computed_payment_status == 'kredit')
                paymentStatusHtml = '<span class="badge bg-warning" style="font-size: 1rem">Kredit</span>';
            if (masterBill.computed_payment_status == 'lunas')
                paymentStatusHtml = '<span class="badge bg-success" style="font-size: 1rem">Lunas</span>';

            $('#invoiceDetailModal .modal-body').append(`
                <div>
                    <h4 class="fw-bolder mb-1">Data Tagihan</h4>
                    <table class="eazy-table-info">
                        <tbody>
                            <tr>
                                <td>Nomor Invoice</td>
                                <td>:&nbsp;&nbsp;${masterBill.prr_id}</td>
                            </tr>
                            <tr>
                                <td>Digenerate Pada</td>
                                <td>:&nbsp;&nbsp;${moment(masterBill.created_at).utcOffset('+0700').format('DD/MM/YYYY')}</td>
                            </tr>
                            <tr>
                                <td>Status Tagihan</td>
                                <td>:&nbsp;&nbsp;${paymentStatusHtml}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            `);
        },
        _renderInvoiceDetail: function(masterBill) {
            /**
             * FIX THIS
             */
            const invoiceDetail = masterBill.computed_component_list;
            const invoiceTotal = masterBill.computed_component_total_amount;
            const discountDetail = masterBill.computed_discount_list;
            const discountTotal = masterBill.computed_discount_total_amount;
            const scholarshipDetail = masterBill.computed_scholarship_list;
            const scholarshipTotal = masterBill.computed_scholarship_total_amount;
            const penaltyDetail = [];
            const penaltyTotal = 0;
            const totalAmount = masterBill.computed_final_bill;

            $('#invoiceDetailModal .modal-body').append(`
                <div class="mt-3">
                    <h4 class="fw-bolder mb-1">Detail Tagihan</h4>
                    <table id="table-invoice-detail" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Komponen Tagihan</th>
                                <th>Biaya Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${
                                invoiceDetail.map(item => {
                                    return `
                                        <tr>
                                            <td>${item.prrd_component}</td>
                                            <td>${Rupiah.format(item.prrd_amount)}</td>
                                        </tr>
                                    `;
                                }).join('')
                                +
                                discountDetail.map(item => {
                                    return `
                                        <tr>
                                            <td>${item.prrd_component}</td>
                                            <td>- ${Rupiah.format(item.prrd_amount)}</td>
                                        </tr>
                                    `;
                                }).join('')
                                +
                                scholarshipDetail.map(item => {
                                    return `
                                        <tr>
                                            <td>${item.prrd_component}</td>
                                            <td>- ${Rupiah.format(item.prrd_amount)}</td>
                                        </tr>
                                    `;
                                }).join('')
                                +
                                penaltyDetail.map(item => {
                                    return `
                                        <tr>
                                            <td>${item.prrd_component}</td>
                                            <td>${Rupiah.format(item.prrd_amount)}</td>
                                        </tr>
                                    `;
                                }).join('')
                            }
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total Tagihan</th>
                                <th>${Rupiah.format(totalAmount)}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            `);
        },
        _renderInvoiceBill: function(bills) {

            if (bills.length == 0) return;

            $('#invoiceDetailModal .modal-body').append(`
                <div class="mt-3">
                    <h4 class="fw-bolder mb-1">Cicilan</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tenggat Pembayaran</th>
                                <th>Nominal Tagihan</th>
                                <th>Biaya Admin</th>
                                <th>Dibayar Pada</th>
                                <th>Status</th>
                                <th class="cetak-cicilan-act">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${
                                bills.map(bill => {
                                    let paymentStatusHtml = '<span class="badge bg-secondary" style="font-size: 1rem">N/A</span>';
                                    if (bill.computed_payment_status == 'belum lunas')
                                        paymentStatusHtml = '<span class="badge bg-danger" style="font-size: 1rem">Belum Lunas</span>';
                                    if (bill.computed_payment_status == 'kredit')
                                        paymentStatusHtml = '<span class="badge bg-warning" style="font-size: 1rem">Kredit</span>';
                                    if (bill.computed_payment_status == 'lunas')
                                        paymentStatusHtml = '<span class="badge bg-success" style="font-size: 1rem">Lunas</span>';

                                    return `
                                        <tr>
                                            <td>Cicilan ke-${bill.prrb_order}</td>
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
                                            <td>${Rupiah.format(bill.prrb_amount)}</td>
                                            <td>${Rupiah.format(bill.prrb_admin_cost)}</td>
                                            <td>${bill.computed_paid_date != null ? moment(bill.computed_paid_date).format('DD-MM-YYYY HH:mm') : '-'}</td>
                                            <td>${paymentStatusHtml}</td>
                                            <td class="cetak-cicilan-act"><button class="btn btn-primary" onclick="cetakCicilan(${JSON.stringify(bill).replaceAll('"',"'")})"> <i data-feather="printer"></i> Cetak</button>
                                        </tr>
                                    `;

                                    // console.log(bill)
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
                                    //             <td>Cicilan ke-${bill.prrb_order}</td>
                                    //             <td>
                                    //                 ${
                                    //                     response == null ? moment(bill.prrb_due_date).format('DD-MM-YYYY')
                                    //                         : `
                                    //                             <p class="line">${moment(bill.prrb_due_date).format('DD-MM-YYYY')}</p>
                                    //                             <p>${moment(response.mds_deadline).format('DD-MM-YYYY')}</p>
                                    //                         `
                                    //                 }
                                    //             </td>
                                    //             <td>${Rupiah.format(bill.prrb_amount)}</td>
                                    //             <td>${Rupiah.format(bill.prrb_admin_cost)}</td>
                                    //             <td>${bill.prrb_paid_date != null ? moment(bill.prrb_paid_date).format('DD-MM-YYYY HH:mm') : '-'}</td>
                                    //             <td>${
                                    //                 bill.prrb_status == 'belum lunas' ?
                                    //                     '<span class="badge bg-danger" style="font-size: 1rem">Belum Lunas</span>'
                                    //                     : bill.prrb_status == 'lunas' ?
                                    //                         '<span class="badge bg-success" style="font-size: 1rem">Lunas</span>'
                                    //                         : '<span class="badge bg-secondary" style="font-size: 1rem">N/A</span>'
                                    //             }</td>
                                    //             <td class="cetak-cicilan-act"><button class="btn btn-primary" onclick="cetakCicilan(${JSON.stringify(bill).replaceAll('"',"'")})"> <i data-feather="printer"></i> Cetak</button>
                                    //         </tr>
                                    //     `;
                                    // }
                                    // xhr.open("GET", _baseURL+`/api/payment/student-dispensation/spesific-payment/${bill.prr_id}`, false);
                                    // xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                                    // xhr.send();
                                    // return row;
                                }).join('')
                            }
                        </tbody>
                    </table>
                </div>
            `);

            return new Promise(resolve => resolve(true));
        },
        _renderProceedPayment: function(masterBill) {

            if (masterBill.computed_payment_status == 'lunas') return;

            $('#invoiceDetailModal .modal-body').append(`
                <div id="proceed-payment" class="mt-4">
                    <div class="d-flex justify-content-start" style="gap: 1rem">
                        <a type="button" id="btn-proceed-payment" data-eazy-prr-id="${masterBill.prr_id}" onclick="proceedPayment(event)" class="btn btn-success d-inline-block">
                            Halaman Pembayaran&nbsp;&nbsp;<i data-feather="arrow-right"></i>
                        </a>
                        <button class="btn btn-primary" onclick="cetakSemua('${masterBill.prr_id}')"> <i data-feather="printer"></i> Cetak </button>
                    </div>
                </div>
            `);
        },
    }

    function proceedPayment(e) {
        const prrId = $(e.currentTarget).attr('data-eazy-prr-id');
        window.location.href = `${_baseURL}/payment/student-invoice/${prrId}/proceed-payment`;
    }

    function cetakCicilan(prrb){
        console.log('cicilan :')
        console.log(prrb)
        var xhr = new XMLHttpRequest();
        xhr.onload = function(){
            var { data } = JSON.parse(this.responseText)
            console.log(data);

            var student = {
                name: '',
                number: '',
                studyprogram: '',
            }

            if('register' in data){
                student.name = data.register.participant.par_fullname;
                student.number = data.register.participant.par_number;
                student.studyprogram = data.register.studyprogram.studyprogram_type + " " + data.register.studyprogram.studyprogram_name
            }else {
                student.name = data.student.fullname;
                student.number = data.student.student_id;
                student.studyprogram = data.student.studyprogram.studyprogram_type + " " + data.student.studyprogram.studyprogram_name
            }
            const date = new Date(data.created_at);
            const dueDate = new Date(prrb.prrb_due_date);
            $('#cicilan-invoice').html(`
            <div class="container">
                <div class="row text-center">
                    <h3 class="text-uppercase text-center mt-3" style="font-size: 40px;">Invoice</h3>
                </div>
                <div class="row">
                    <div class="col">
                        <p>Nomor Invoice : INV/${prrb.prr_id}</p>
                        <p>Digenerate Pada : ${date.getDate()}-${date.getMonth()}-${date.getFullYear()}</p>
                        <p>Status Tagihan : ${prrb.prrb_status}</p>
                    </div>
                    <div class="col text-end">
                        <p>Kepada : ${student.name}</p>
                        <p>id mahasiswa : ${student.number}</p>
                        <p>program Studi : ${student.studyprogram}</p>
                    </div>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">TAGIHAN</th>
                            <th scope="col">TENGGAT PEMBAYARAN</th>
                            <th scope="col">NOMINAL TAGIHAN</th>
                            <th scope="col">BIAYA ADMIN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Cicilan ke-${prrb.prrb_order}</td>
                            <td>${dueDate.getDate()}-${dueDate.getMonth()}-${dueDate.getFullYear()}</td>
                            <td>${prrb.prrb_amount}</td>
                            <td>${prrb.prrb_admin_cost}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            `)
            var textContent =
            '<div class="container">'+
                '<div class="row text-center">' +
                    '<h3 class="text-uppercase text-center mt-3" style="font-size: 40px;">Invoice</h3>'+
                '</div>'+
                '<div class="row">'+
                    '<div class="col">'+
                        `<p>Nomor Invoice : INV/${prrb.prr_id}</p>`+
                        `<p>Digenerate Pada : ${date.getDate()}-${date.getMonth()}-${date.getFullYear()}</p>`+
                        `<p>Status Tagihan : ${prrb.prrb_status}</p>`+
                    '</div>'+
                    '<div class="col text-end">'+
                        `<p>Kepada : ${student.name}</p>`+
                        `<p>id mahasiswa : ${student.number}</p>`+
                        `<p>program Studi : ${student.studyprogram}</p>`+
                    '</div>'+
                '</div>'+
                '<table class="table">'+
                    '<thead>'+
                        '<tr>'+
                            '<th scope="col">TAGIHAN</th>'+
                            '<th scope="col">TENGGAT PEMBAYARAN</th>'+
                            '<th scope="col">NOMINAL TAGIHAN</th>'+
                            '<th scope="col">BIAYA ADMIN</th>'+
                        '</tr>'+
                    '</thead>'+
                    '<tbody>'+
                        '<tr>'+
                            `<td>Cicilan ke-${prrb.prrb_order}</td>`+
                            `<td>${dueDate.getDate()}-${dueDate.getMonth()}-${dueDate.getFullYear()}</td>`+
                            `<td>${prrb.prrb_amount}</td>`+
                            `<td>${prrb.prrb_admin_cost}</td>`+
                        '</tr>'+
                    '</tbody>'+
                '</table>'+
            '</div>';
            var winPrint = window.open(location.origin+'/payment/student-invoice/invoice-cicilan?content='+textContent);
        }
        xhr.open("GET", _baseURL+"/api/payment/student-invoice/"+prrb.prr_id);
        xhr.setRequestHeader("X-CSRF-TOKEN", '{{ csrf_token() }}');
        xhr.send();
    }

    function cetakSemua(prr_id){
        var xhr = new XMLHttpRequest();
        xhr.onload = function(){
            var { data } = JSON.parse(this.responseText)
            console.log(data);

            var student = {
                name: '',
                number: '',
                studyprogram: '',
            }

            if('register' in data){
                student.name = data.register.participant.par_fullname;
                student.number = data.register.participant.par_number;
                student.studyprogram = data.register.studyprogram.studyprogram_type + " " + data.register.studyprogram.studyprogram_name
            }else {
                student.name = data.student.fullname;
                student.number = data.student.student_id;
                student.studyprogram = data.student.studyprogram.studyprogram_type + " " + data.student.studyprogram.studyprogram_name
            }
            const date = new Date(data.created_at);

            var total_komponen = 0;
            var tableKomponen =
                '<table class="table">'+
                    '<thead>'+
                        '<tr>'+
                            '<th scope="col">KOMPONEN TAGIHAN</th>'+
                            '<th scope="col">BIAYA BAYAR</th>'+
                        '</tr>'+
                    '</thead>'+
                    '<tbody>';

            data.payment_detail.forEach(item => {
                tableKomponen +=
                '<tr>'+
                    '<td>'+ item.prrd_component + '</td>' +
                    '<td>'+ item.prrd_amount + '</td>' +
                '</tr>';

                if(item.is_plus == 1){
                    total_komponen += item.prrd_amount
                }else {
                    total_komponen -= item.prrd_amount
                }
            })

            tableKomponen +=
            '<tr>'+
                '<td>TOTAL TAGIHAN</td>'+
                '<td>' + total_komponen + '</td>'+
            '</tr>'+
            '</tbody>'+
            '</table>'

            var total_cicilan = 0;
            var total_admin = 0;
            var tableCicilan =
            '<table class="table mt-2">'+
                    '<thead>'+
                        '<tr>'+
                            '<th scope="col">TAGIHAN</th>'+
                            '<th scope="col">TENGGAT PEMBAYARAN</th>'+
                            '<th scope="col">NOMINAL TAGIHAN</th>'+
                            '<th scope="col">BIAYA ADMIN</th>'+
                        '</tr>'+
                    '</thead>'+
                    '<tbody>';

            data.payment_bill.forEach(item => {
                var prrb = item;
                const dueDate = new Date(prrb.prrb_due_date);
                tableCicilan +=
                    '<tr>'+
                        `<td>Cicilan ke-${prrb.prrb_order}</td>`+
                        `<td>${dueDate.getDate()}-${dueDate.getMonth()}-${dueDate.getFullYear()}</td>`+
                        `<td>${prrb.prrb_amount}</td>`+
                        `<td>${prrb.prrb_admin_cost}</td>`+
                    '</tr>';

                total_admin += prrb.prrb_admin_cost;
                total_cicilan += prrb.prrb_amount;
            })

            tableCicilan +=
                    '<tr>'+
                        '<td colspan="2">TOTAL BIAYA</td>'+
                        '<td>'+ total_cicilan +'</td>'+
                        '<td>'+ total_admin +'</td>'+
                    '</tr>'+
                '</tbody>'+
            '</table>';

            $('#cicilan-invoice').html(`
            <div class="container">
                <div class="row text-center">
                    <h3 class="text-uppercase text-center mt-3" style="font-size: 40px;">Invoice</h3>
                </div>
                <div class="row">
                    <div class="col">
                        <p>Nomor Invoice : INV/${prr_id}</p>
                        <p>Digenerate Pada : ${date.getDate()}-${date.getMonth()}-${date.getFullYear()}</p>
                        <p>Status Tagihan : ${data.prr_status}</p>
                    </div>
                    <div class="col text-end">
                        <p>Kepada : ${student.name}</p>
                        <p>id mahasiswa : ${student.number}</p>
                        <p>program Studi : ${student.studyprogram}</p>
                    </div>
                </div>
                ${tableKomponen}
                ${tableCicilan}
            </div>
            `)
            var textContent =
            '<div class="container">'+
                '<div class="row text-center">' +
                    '<h3 class="text-uppercase text-center mt-3" style="font-size: 40px;">Invoice</h3>'+
                '</div>'+
                '<div class="row">'+
                    '<div class="col">'+
                        `<p>Nomor Invoice : INV/${prr_id}</p>`+
                        `<p>Digenerate Pada : ${date.getDate()}-${date.getMonth()}-${date.getFullYear()}</p>`+
                        `<p>Status Tagihan : ${data.prr_status}</p>`+
                    '</div>'+
                    '<div class="col text-end">'+
                        `<p>Kepada : ${student.name}</p>`+
                        `<p>id mahasiswa : ${student.number}</p>`+
                        `<p>program Studi : ${student.studyprogram}</p>`+
                    '</div>'+
                '</div>'+
                tableKomponen +
                tableCicilan +
            '</div>';
            var winPrint = window.open(location.origin+'/payment/student-invoice/invoice-cicilan?content='+textContent);
        }
        xhr.open("GET", _baseURL+"/api/payment/student-invoice/"+prr_id);
        xhr.setRequestHeader("X-CSRF-TOKEN", '{{ csrf_token() }}');
        xhr.send();
    }

    // async function renderHeaderInfo() {
    //     const studentType = userMaster.participant ? 'new_student' : 'student';
    //     const studentId = studentType == 'new_student' ? userMaster.participant.par_id : userMaster.student.student_id;
    //     const queryParam = `student_type=${studentType}&${studentType == 'new_student' ? 'par_id=' : 'student_id='}${studentId}`;

    //     const studentDetail = await $.ajax({
    //         async: true,
    //         url: `${_baseURL}/api/student/detail?${queryParam}`,
    //         type: 'get'
    //     });

    //     if (studentType == 'new_student') {
    //         $('#header-info-student').html(`
    //             <div class="eazy-header">
    //                 <div class="eazy-header__item">
    //                     <div class="item__icon">
    //                         <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
    //                     </div>
    //                     <div class="item__text">
    //                         <small class="text__subtitle">Nama Lengkap</small>
    //                         <span class="text__title">${studentDetail.par_fullname}</span>
    //                     </div>
    //                 </div>
    //                 <div class="eazy-header__item">
    //                     <div class="item__icon">
    //                         <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-hash"><line x1="4" y1="9" x2="20" y2="9"></line><line x1="4" y1="15" x2="20" y2="15"></line><line x1="10" y1="3" x2="8" y2="21"></line><line x1="16" y1="3" x2="14" y2="21"></line></svg>
    //                     </div>
    //                     <div class="item__text">
    //                         <small class="text__subtitle">NIK</small>
    //                         <span class="text__title">${studentDetail.par_nik}</span>
    //                     </div>
    //                 </div>
    //             </div>
    //         `);
    //     } else if (studentType == 'student') {
    //         $('#header-info-student').html(`
    //             <div class="eazy-header">
    //                 <div class="eazy-header__item">
    //                     <div class="item__icon">
    //                         <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
    //                     </div>
    //                     <div class="item__text">
    //                         <small class="text__subtitle">Nama Lengkap dan NIM</small>
    //                         <span class="text__title">${studentDetail.fullname}</span>
    //                         <span class="d-block">${studentDetail.student_id}</span>
    //                     </div>
    //                 </div>
    //                 <div class="eazy-header__item">
    //                     <div class="item__icon">
    //                         <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-book-open"><line x1="4" y1="9" x2="20" y2="9"></line><line x1="4" y1="15" x2="20" y2="15"></line><line x1="10" y1="3" x2="8" y2="21"></line><line x1="16" y1="3" x2="14" y2="21"></line></svg>
    //                     </div>
    //                     <div class="item__text">
    //                         <small class="text__subtitle">Fakultas</small>
    //                         <span class="text__title">${studentDetail.studyprogram.faculty.faculty_name}</span>
    //                     </div>
    //                 </div>
    //                 <div class="eazy-header__item">
    //                     <div class="item__icon">
    //                         <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bookmark"><line x1="4" y1="9" x2="20" y2="9"></line><line x1="4" y1="15" x2="20" y2="15"></line><line x1="10" y1="3" x2="8" y2="21"></line><line x1="16" y1="3" x2="14" y2="21"></line></svg>
    //                     </div>
    //                     <div class="item__text">
    //                         <small class="text__subtitle">Program Studi</small>
    //                         <span class="text__title">${studentDetail.studyprogram.studyprogram_name}</span>
    //                     </div>
    //                 </div>
    //             </div>
    //         `);
    //     }
    // }
</script>
@endsection
