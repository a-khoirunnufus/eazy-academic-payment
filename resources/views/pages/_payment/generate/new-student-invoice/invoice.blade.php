<script type="text/javascript">
    const _invoiceAction = {
        detail: function(e,table,type = 'eager') {
            const data = table.getRowData(e.currentTarget);
            Modal.show({
                type: 'detail',
                modalTitle: 'Detail Mahasiswa',
                modalSize: 'xl',
                config: {
                    fields: {
                        header: {
                            type: 'custom-field',
                            title: 'Data Mahasiswa',
                            content: {
                                template: `<div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3">
                                            <h6>Nama Lengkap</h6>
                                            <h1 class="h6 fw-bolder">${data.participant ? (data.participant.par_fullname) : '-'}</h1>
                                        </div>
                                        <div class="col-lg-3 col-md-3">
                                            <h6>Nomor Pendaftaran</h6>
                                            <h1 class="h6 fw-bolder">${data.reg_number ? (data.reg_number) : '-'}</h1>
                                        </div>
                                        <div class="col-lg-3 col-md-3">
                                            <h6>No Handphone</h6>
                                            <h1 class="h6 fw-bolder">${data.participant ? (data.participant.par_phone) : '-'}</h1>
                                        </div>
                                        <div class="col-lg-3 col-md-3">
                                            <h6>Status Pembayaran</h6>
                                            <h1 class="h6 fw-bolder" id="statusPembayaran"></h1>
                                        </div>
                                    </div>
                                    <hr>
                                </div>`
                            },
                        },
                        tagihan: {
                            type: 'custom-field',
                            title: 'Detail Tagihan',
                            content: {
                                template: `
                                    <table class="table table-bordered" id="paymentDetail" style="line-height: 3">
                                        <tr class="bg-light">
                                            <th class="text-center">Komponen Tagihan</th>
                                            <th class="text-center">Nominal</th>
                                        </tr>

                                    </table>
                                `
                            },
                        },
                        bill: {
                            type: 'custom-field',
                            title: 'Riwayat Transaksi',
                            content: {
                                template: `
                                    <table class="table table-bordered" id="paymentBill">
                                        <tr class="bg-light">
                                            <th class="text-center">Invoice ID</th>
                                            <th class="text-center">Expired Date</th>
                                            <th class="text-center">Amount</th>
                                            <th class="text-center">Fee</th>
                                            <th class="text-center">Paid Date</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </table>
                                `
                            },
                        },
                    },
                    callback: function() {
                        feather.replace();
                    }
                },
            });
            let source = data.payment;
            if(source){

                // Status
                var status = "";
                if(source){
                    if(source.prr_status == 'lunas'){
                        status = '<div class="badge bg-success" style="font-size: inherit">Lunas</div>'
                    }else if(source.prr_status == 'kredit'){
                        status = '<div class="badge bg-warning" style="font-size: inherit">Kredit</div>'
                    }else{
                        status = '<div class="badge bg-danger" style="font-size: inherit">Belum Lunas</div>'
                    }
                }
                $("#statusPembayaran").append(status);

                // Tagihan
                var total = 0;
                if (Object.keys(source.payment_detail).length > 0) {
                    source.payment_detail.map(item => {
                        if(item.is_plus === 1){
                            total = total+item.prrd_amount;
                            _invoiceAction.rowDetail(item.prrd_component, item.prrd_amount,'paymentDetail');
                        }
                    });
                    $("#paymentDetail").append(`
                        <tr class="bg-light">
                            <td class="text-center fw-bolder">Total</td>
                            <td class="text-center fw-bolder">${Rupiah.format(total)}</td>
                        </tr>
                    `);
                    let is_header = false;
                    source.payment_detail.map(item => {
                        if(item.is_plus != 1){
                            if(!is_header){
                                $("#paymentDetail").append(`
                                    <tr class="bg-light">
                                        <td class="text-center fw-bolder" colspan="2">Potongan & Beasiswa</td>
                                    </tr>
                                `);
                                is_header = true;
                            }
                            total = total-item.prrd_amount;
                            _invoiceAction.rowDetail(item.prrd_component, item.prrd_amount,'paymentDetail');
                        }
                    });
                }
                // $("#paymentDetail").append(`
                //     <tr class="bg-light">
                //         <td class="text-center fw-bolder">Eazy Service</td>
                //         <td class="text-center" style="color:red!important">-${Rupiah.format({{ \App\Enums\Payment\FeeAmount::eazy }})}</td>
                //     </tr>
                // `);
                $("#paymentDetail").append(`
                    <tr style="background-color:#163485">
                        <td class="text-center fw-bolder" style="color:white!important">Total yang Diterima</td>
                        <td class="text-center fw-bolder" style="color:white!important">${Rupiah.format(source.prr_paid_net)}</td>
                    </tr>
                `);

                var total_terbayar = 0;
                if (Object.keys(source.payment_bill).length > 0) {
                    $("#paymentDetail").append(`
                        <tr class="bg-light">
                            <td class="text-center fw-bolder" colspan="2">Fee</th>
                        </tr>
                    `);
                    source.payment_bill.map(item => {
                        if(item.prrb_status == "lunas"){
                            total_terbayar = total_terbayar + item.prrb_amount+item.prrb_admin_cost;
                        }
                        _invoiceAction.rowDetail('Biaya Transaksi - INV.'+item.prrb_id, item.prrb_admin_cost,'paymentDetail');
                    });
                }
                $("#paymentDetail").append(`
                    <tr style="background-color:#163485">
                        <td class="text-center fw-bolder" style="color:white!important">Total Tagihan</td>
                        <td class="text-center fw-bolder" style="color:white!important">${Rupiah.format(source.prr_total)}</td>
                    </tr>
                `);

                $("#paymentDetail").append(`
                    <tr class="bg-success">
                        <td class="text-center fw-bolder" style="color:white!important">Total Terbayar</td>
                        <td class="text-center fw-bolder" style="color:white!important">${Rupiah.format(total_terbayar)}</td>
                    </tr>
                `);


                // Transaksi
                if (Object.keys(source.payment_bill).length > 0) {
                    source.payment_bill.map(item => {
                        _invoiceAction.rowBill(item.prrb_id,item.prrb_due_date, item.prrb_paid_date, item.prrb_amount, item.prrb_admin_cost, item.prrb_status,'paymentBill');
                    });
                }
            }

        },
        rowDetail(name,amount,id){
            $("#"+id+"").append(`
                <tr>
                    <td class="text-center fw-bolder">${name}</td>
                    <td class="text-center">${Rupiah.format(amount)}</td>
                </tr>
            `)
        },
        rowBill(inv_num,expired_date,paid_date,amount,fee,status,id){
            var stat = "";
            var expired = "";
            var paid = "";
            if(status == 'lunas'){
                stat = '<div class="badge badge-small bg-success" style="padding: 5px!important;">Lunas</div>';
            }else{
                stat = '<div class="badge bg-danger" style="padding: 5px!important;">Belum Lunas</div>';
            }
            $("#"+id+"").append(`
                <tr>
                    <td class="text-center fw-bolder">${inv_num}</td>
                    <td class="text-center">${expired_date ? (new Date(expired_date)).toLocaleString("id-ID") : "-"}</td>
                    <td class="text-center">${Rupiah.format(amount)}</td>
                    <td class="text-center">${Rupiah.format(fee)}</td>
                    <td class="text-center">${paid_date ? (new Date(paid_date)).toLocaleString("id-ID") : "-"}</td>
                    <td class="text-center">${stat}</td>
                </tr>
            `)
        },
    }
</script>