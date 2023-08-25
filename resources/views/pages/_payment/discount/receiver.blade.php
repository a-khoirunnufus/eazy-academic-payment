@extends('layouts.static_master')


@section('page_title', 'Mahasiswa Penerima Potongan')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
<style>
    .eazy-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }
</style>
@endsection

@section('content')

@include('pages._payment.discount._shortcuts', ['active' => 'receiver'])

<div class="card">
    <div class="card-body">
        <div class="datatable-filter one-row">
            <div>
                <label class="form-label">Periode</label>
                <select name="period" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Periode</option>
                    @foreach ($period as $item)
                    <option value="{{$item->msy_id}}">{{$item->msy_year}} {{ ($item->msy_semester == 1)? 'Ganjil' : 'Genap' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Potongan</label>
                <select name="discount_filter" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Potongan</option>
                    @foreach ($discount as $item)
                    <option value="{{$item->md_id}}">{{$item->md_name}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Fakultas</label>
                <select name="faculty_filter" class="form-select" eazy-select2-active onchange="getStudyProgram(this.value)">
                    <option value="#ALL" selected>Semua Fakultas</option>
                    @foreach ($faculty as $item)
                    <option value="{{$item->faculty_id}}">{{$item->faculty_name}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Program Studi</label>
                <select name="study_program_filter" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Program Studi</option>
                </select>
            </div>
            <div class="d-flex align-items-end">
                <button onclick="_discountReceiverTable.reload()" class="btn btn-primary text-nowrap">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="nav-tabs-shadow nav-align-top">
        <ul class="nav nav-tabs custom border-bottom mb-0" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-student" aria-controls="navs-student" aria-selected="true">Mahasiswa Lama</button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-new-student" aria-controls="navs-new-student" aria-selected="false">Mahasiswa baru</button>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="navs-student" role="tabpanel">
                @include('pages._payment.discount.tab-student')
            </div>
            <div class="tab-pane fade" id="navs-new-student" role="tabpanel">
                @include('pages._payment.discount.tab-new-student')
            </div>
        </div>
    </div>
</div>

@endsection

@section('js_section')
@stack('scripts')
@endsection
