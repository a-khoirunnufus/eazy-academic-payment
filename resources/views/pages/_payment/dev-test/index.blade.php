@extends('pages._payment.dev-test.layout')

@prepend('styles')
    <style>
        .section-wrapper > div {
            border-radius: 10px;
            border: 1px solid gainsboro;
            padding: 2rem;
            background: white;
        }
    </style>
@endprepend

@section('content')
    <div class="container my-3">
        <div class="section-wrapper">
            @include('pages._payment.dev-test._regenerate-va')
        </div>
    </div>
@endsection
