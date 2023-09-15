@extends('layouts.student.layout-master')

@section('page_title', 'Kelebihan Bayar')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')

@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <p>Sisa Saldo</p>
            <h1>Rp 1.500.000,00</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5>Riwayat Transaksi</h5>
        </div>
        <table id="table-overpayment-transaction" class="table table-striped">
            <thead>
                <tr>
                    <th class="text-center">Saldo Masuk</th>
                    <th class="text-center">Saldo Keluar</th>
                    <th>Keterangan</th>
                    <th>Waktu Transaksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-success text-center">Rp 4.000.000,00</td>
                    <td class="text-danger text-center">-</td>
                    <td>Kelebihan Pembayaran Tagihan</td>
                    <td>15/09/2023 12:00</td>
                </tr>
                <tr>
                    <td class="text-success text-center">-</td>
                    <td class="text-danger text-center">Rp 2.500.000,00</td>
                    <td>Pemakaian Saldo</td>
                    <td>15/09/2023 15:00</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection

@section('js_section')
<script>

    const userMaster = JSON.parse(`{!! json_encode($user, true) !!}`);

    $(function(){

    });

</script>
@endsection
