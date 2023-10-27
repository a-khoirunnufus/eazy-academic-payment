@extends('tpl.vuexy.master-payment')

@section('page_title', 'Penarikan Saldo Mahasiswa')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
@endsection

@section('content')

@include('pages._payment.students-balance._shortcuts', ['active' => 'withdraw'])

<div class="card">
    penarikan saldo mahasiswa
</div>

@endsection


@section('js_section')
@endsection
