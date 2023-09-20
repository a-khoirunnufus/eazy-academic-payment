@extends('layouts.student.layout-master')

@section('page_title', 'Kelebihan Bayar')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')

@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <p>Total Saldo</p>
            <h1 id="overpayment-balance-amount">...</h1>
        </div>
    </div>

    <div class="card">
        <table id="table-overpayment-transaction" class="table table-striped">
            <thead>
                <tr>
                    <th>Saldo Masuk</th>
                    <th>Saldo Keluar</th>
                    <th>Keterangan</th>
                    <th>Waktu Transaksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
@endsection

@section('js_section')
<script>

    const userMaster = JSON.parse(`{!! json_encode($user, true) !!}`);

    $(function(){
        renderBalanceInfo();
        _overpaymentTransactionTable.init();
    });

    const _overpaymentTransactionTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#table-overpayment-transaction').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/student/overpayment/dt-transaction',
                    data: function(d) {
                        d.student_type = userMaster.participant ? 'new_student' : 'student';
                        d.student_email = userMaster.user_email;
                    },
                },
                stateSave: false,
                ordering: false,
                searching: false,
                columns: [
                    {
                        data: 'ovrt_cash_in',
                        render: (data) => {
                            return data ? this.template.currencyCell(data) : '-';
                        }
                    },
                    {
                        data: 'ovrt_cash_out',
                        render: (data) => {
                            return data ? this.template.currencyCell(data) : '-';
                        }
                    },
                    {
                        data: 'ovrt_remark',
                    },
                    {
                        data: 'ovrt_time',
                        render: (data) => {
                            return this.template.dateTimeCell(data);
                        }
                    },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-center header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"overpayment-transaction-actions d-flex align-items-end">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>t' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function() {
                    $('.overpayment-transaction-actions').html(`
                        <h5>Riwayat Transaksi</h5>
                    `)
                    feather.replace()
                },
            })
        },
        template: {
            ..._datatableTemplates,
            rowAction: function(id) {
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" onclick="_paidPaymentTableAction.detail(event)"><i data-feather="eye"></i>&nbsp;&nbsp;Detail</a>
                        </div>
                    </div>
                `
            },
        }
    }

    async function renderBalanceInfo() {
        let reqData = {};
        if (userMaster.student) {
            reqData = {
                student_number: userMaster.student.student_number,
                student_type: 'student',
            };
        }
        else if (userMaster.participant) {
            reqData = {
                participant_id: userMaster.participant.par_id,
                student_type: 'new_student',
            };
        }

        const balance = await $.ajax({
            url: `${_baseURL}/api/student/overpayment/balance`,
            data: reqData,
            processData: true,
            type: 'get'
        });

        $('#overpayment-balance-amount').text(Rupiah.format(balance.ovrb_balance));
    }

</script>
@endsection
