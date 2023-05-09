@extends('layouts.static_master')


@section('page_title', 'Setting Tagihan, Tarif, dan Pembayaran')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
    <style>
        .rates-filter {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            grid-gap: 1rem;
        }
    </style>
@endsection

@section('content')

@include('pages._payment.settings._shortcuts', ['active' => 'payment-rates'])

<div class="card">
    <div class="card-body">
        <div class="d-flex flex-column" style="gap: 2rem">
            <div class="rates-filter" style="flex-grow: 1">
                <div>
                    <label class="form-label">Periode Masuk</label>
                    <select class="form-select">
                        <option value="all" selected>Semua Periode Masuk</option>
                        @foreach($static_school_years as $school_year)
                            <option value="{{ $school_year }}">{{ $school_year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Gelombang</label>
                    <select class="form-select">
                        <option value="all" selected>Semua Gelombang</option>
                        @foreach($static_registration_periods as $registration_period)
                            <option value="{{ $registration_period }}">{{ $registration_period }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Jalur Pendaftaran</label>
                    <select class="form-select">
                        <option value="all" selected>Semua Jalur Pendaftaran</option>
                        @foreach($static_registration_paths as $registration_path)
                            <option value="{{ $registration_path }}">{{ $registration_path }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Sistem Kuliah</label>
                    <select class="form-select">
                        <option value="all" selected>Semua Sistem Kuliah</option>
                        @foreach($static_study_systems as $study_system)
                            <option value="{{ $study_system }}">{{ $study_system }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Fakultas</label>
                    <select class="form-select">
                        <option value="all" selected>Semua Fakultas</option>
                        @foreach($static_faculties as $faculty)
                            <option value="{{ $faculty }}">{{ $faculty }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Program Studi</label>
                    <select class="form-select">
                        <option value="all" selected>Semua Program Studi</option>
                        @foreach($static_study_programs as $study_program)
                            <option value="{{ $study_program }}">{{ $study_program }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Komponen Tagihan</label>
                    <select class="form-select">
                        <option value="all" selected>Semua Komponen Tagihan</option>
                        @foreach($static_invoice_components as $invoice_component)
                            <option value="{{ $invoice_component }}">{{ $invoice_component }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-flex align-items-end">
                    <button class="btn btn-primary">
                        <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="rates-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Tahun Ajar</th>
                <th>Periode Masuk</th>
                <th>Jalur / Gelombang</th>
                <th width="50%">Program Studi - Jenis Perkuliahan</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection


@section('js_section')
<script>
    $(function(){
        _ratesTable.init()

        select2Replace();
    })

    const _ratesTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#rates-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/payment/settings/paymentrates/index',
                },
                columns: [
                    {
                        name: 'action',
                        data: 'ppd_id',
                        orderable: false,
                        render: (data, _, row) => {
                            // console.log(row)
                            return this.template.rowAction(data)
                        }
                    },
                    {
                        name: 'path.path_name',
                        render: (data, _, row) => {
                            var year = "Unknown";
                            var semester = "Unknown";
                            if(row.period.schoolyear){
                                year = row.period.schoolyear.msy_year;
                                if(row.period.schoolyear.msy_semester == 1){
                                    semester = "Ganjil";
                                }else{
                                    semester = "Genap";
                                }
                            }
                            return `
                                <div>
                                    <span class="fw-bold">${year} - ${semester}</span><br>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'period.period_name', 
                        data: 'period.period_name',
                        render: (data) => {
                            return `<span class="fw-bold">${data}</span>`;
                        }
                    },
                    {
                        name: 'path.path_name',
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <span class="fw-bold">${row.path.path_name}</span><br>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'major.ppm_id',
                        render: (data, _, row) => {
                            let html = '<div class="d-flex flex-wrap" style="gap:10px">';
                            if(Object.keys(row.major).length > 0){
                                row.major.map(item => {
                                    var study_program = "Unknown";
                                    var study_program_type = "";
                                    var lecture_type = "Unknown";
                                    if(item.major_lecture_type){
                                        if(item.major_lecture_type.study_program){
                                            study_program_type = item.major_lecture_type.study_program.studyprogram_type
                                            study_program = item.major_lecture_type.study_program.studyprogram_name
                                        }
                                        if(item.major_lecture_type.lecture_type){
                                            lecture_type = item.major_lecture_type.lecture_type.mlt_name
                                        }
                                    }
                                    html += `<span class="badge bg-primary">${study_program_type} ${study_program} - ${lecture_type}</span>`;
                                })
                            }
                            html += '</div>';
                            return html;
                        }
                    }
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"invoice-component-actions d-flex align-items-end">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>t' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function() {
                    feather.replace()
                }
            })
        },
        template: {
            rowAction: function(id) {
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            
                            <a href="${_baseURL}/payment/settings/payment-rates/detail/${id}" class="dropdown-item"><i data-feather="dollar-sign"></i>&nbsp;&nbsp;Edit Komponen Biaya</a>
                        </div>
                    </div>
                `
            }
        }
    }
</script>
@endsection
