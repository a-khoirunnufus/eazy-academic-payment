@extends('tpl.vuexy.master-payment')


@section('page_title', 'Mahasiswa Penerima Beasiswa')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
<style>
    .form-control.w-200,
    .form-select.w-200 {
        width: 200px !important;
    }

    table.dtr-details-custom td {
        padding: 10px 0;
    }
    table.dtr-details-custom td > * {
        padding-left: 0;
        padding-right: 0;
    }
    .dtr-bs-modal .modal-dialog {
        max-width: max-content;
    }
</style>
@endsection

@section('content')

@include('pages._payment.scholarship._shortcuts', ['active' => 'receiver'])

<div class="card">
    <div class="card-body">
        <div class="datatable-filter multiple-row">
            <div>
                <label class="form-label">Periode Awal</label>
                <select name="md_period_start_filter" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Periode</option>
                    @foreach ($period as $item)
                    <option value="{{$item->msy_id}}">{{$item->msy_year}} {{ ($item->msy_semester == 1)? 'Ganjil' : 'Genap' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Periode Akhir</label>
                <select name="md_period_end_filter" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Periode</option>
                    @foreach ($period as $item)
                    <option value="{{$item->msy_id}}">{{$item->msy_year}} {{ ($item->msy_semester == 1)? 'Ganjil' : 'Genap' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Nama Beasiswa</label>
                <select name="schoolarship_filter" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Beasiswa</option>
                    @foreach ($schoolarship as $item)
                    <option value="{{$item->ms_id}}">{{$item->ms_name}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Fakultas</label>
                <select name="faculty_filter" class="form-select" eazy-select2-active onchange="getStudyProgram(this)">
                    <option value="#ALL" selected>Semua Fakultas</option>
                    @foreach ($faculty as $item)
                    <option value="{{$item->faculty_id}}">{{$item->faculty_name}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Studi Program</label>
                <select name="program_study_filter" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Program Studi</option>
                </select>
            </div>
            <div class="d-flex align-items-end">
                <button onclick="_scholarshipReceiverTable.reload()" class="btn btn-info text-nowrap">
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
                @include('pages._payment.scholarship.tab-student')
            </div>
            <div class="tab-pane fade" id="navs-new-student" role="tabpanel">
                @include('pages._payment.scholarship.tab-new-student')
            </div>
        </div>
    </div>
</div>

<div class="modal fade dtr-bs-modal" id="modal-scholarship-detail" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Beasiswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="custom-body"></div>
        </div>
    </div>
</div>

@endsection


@section('js_section')
<script>
    // enabling multiple modal open
    $(document).on('show.bs.modal', '.modal', function() {
        const zIndex = 1040 + 10 * $('.modal:visible').length;
        $(this).css('z-index', zIndex);
        setTimeout(() => $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack'));
    });
</script>
@endsection
