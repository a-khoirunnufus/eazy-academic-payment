@extends('tpl.vuexy.master-payment')

@section('page_title', 'Saldo Saya')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')

@endsection

@section('content')

    @include('pages._payment.student.student-balance._shortcuts', ['active' => 'index'])

    <div class="card">
        <div class="card-body">
            <p>Total Saldo</p>
            <h1 id="student-balance-amount">...</h1>
        </div>
    </div>

    <div class="card">
        <table id="table-balance-transaction" class="table table-striped">
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

    const studentMaster = JSON.parse(`{!! json_encode($student, true) !!}`);

    $(function(){
        renderBalanceInfo();
        _balanceTransactionTable.init();
    });

    async function renderBalanceInfo() {
        const res = await $.ajax({
            url: `${_baseURL}/api/payment/student-balance/transaction`,
            data: {student_id: studentMaster.student_id},
            processData: true,
            type: 'get'
        });

        const balance = res?.sbt_closing_balance ?? 0;

        $('#student-balance-amount').text(Rupiah.format(balance));
    }

    const _balanceTransactionTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#table-balance-transaction').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/payment/student-balance/transaction-datatable',
                    data: function(d) {
                        d.withData = ['type'];
                        d.withFilter = [
                            {
                                column: 'student_number',
                                operator: '=',
                                value: studentMaster.student_number
                            }
                        ];
                    },
                },
                stateSave: false,
                ordering: false,
                searching: false,
                columns: [
                    {
                        render: (data, _, row) => {
                            const cashIn = row.type.sbtt_is_cash_in;
                            if (cashIn) {
                                return this.template.currencyCell(row.sbt_amount)
                            }
                            return '-';
                        }
                    },
                    {
                        render: (data, _, row) => {
                            const cashOut = !row.type.sbtt_is_cash_in;
                            if (cashOut) {
                                return this.template.currencyCell(row.sbt_amount)
                            }
                            return '-';
                        }
                    },
                    {
                        data: 'type.sbtt_description',
                    },
                    {
                        data: 'sbt_time',
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
                buttons: _datatableBtnExportTemplate({
                    btnTypes: ['print', 'csv', 'excel', 'pdf', 'copy'],
                    exportColumns: [0, 1, 2, 3],
                }),
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

</script>
@endsection
