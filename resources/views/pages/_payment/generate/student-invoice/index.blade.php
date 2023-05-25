@extends('layouts.static_master')


@section('page_title', 'Generate Tagihan')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
    <style>
        .new-student-invoice-filter {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            grid-gap: 1rem;
        }
    </style>
@endsection

@section('content')

@include('pages._payment.generate._shortcuts', ['active' => 'student-invoice'])

<div class="card">
    <div class="card-body">
        <div class="new-student-invoice-filter">
            <div>
                <label class="form-label">Periode Pendaftaran</label>
                <select class="form-select" eazy-select2-active id="year-filter">
                    <option value="all" selected>Semua Periode Pendaftaran</option>
                    @foreach($year as $tahun)
                        <option value="{{ $tahun->msy_id }}">{{ $tahun->msy_year." ".($tahun->msy_semester%2 == 0 ? "GANJIL":"GENAP") }}</option>
                    @endforeach
                </select>
            </div>
            <!-- <div>
                <label class="form-label">Pilih Jenjang Studi</label>
                <select class="form-select" eazy-select2-active>
                    <option value="all" selected>Semua Jenjang Studi</option>
                    @foreach($static_study_levels as $study_level)
                        <option value="{{ $study_level }}">{{ $study_level }}</option>
                    @endforeach
                </select>
            </div> -->
            <div>
                <label class="form-label">Gelombang</label>
                <select class="form-select" eazy-select2-active id="period-filter">
                    <option value="all" selected>Semua Gelombang</option>
                    @foreach($period as $item)
                        <option value="{{ $item->period_id }}">{{ $item->period_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Jalur Pendaftaran</label>
                <select class="form-select" eazy-select2-active id="path-filter">
                    <option value="all" selected>Semua Jalur Pendaftaran</option>
                    @foreach($static_registration_paths as $registration_path)
                        <option value="{{ $registration_path }}">{{ $registration_path }}</option>
                    @endforeach
                    @foreach($path as $item)
                        <option value="{{ $item->path_id }}">{{ $item->path_name }}</option>
                    @endforeach
                </select>
            </div>
            <!-- <div>
                <label class="form-label">Jenis Tagihan</label>
                <select class="form-select" eazy-select2-active>
                    <option value="all" selected>Semua Jenis Tagihan</option>
                    @foreach($static_invoice_types as $invoice_type)
                        <option value="{{ $invoice_type }}">{{ $invoice_type }}</option>
                    @endforeach
                </select>
            </div> -->
            <div class="d-flex align-items-end">
                <button class="btn btn-primary" onclick="filters()">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="new-student-invoice-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Program Studi / Fakultas</th>
                <th>Total Pembayaran</th>
                {{-- <th rowspan="2">Jumlah Total</th> --}}
            </tr>
            
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection


@section('js_section')
<script>
    var dataTable = null;
    $(function(){
        _newStudentInvoiceTable.init()
    })

    const _newStudentInvoiceTable = {
        ..._datatable,
        init: function() {
            dataTable = this.instance = $('#new-student-invoice-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/payment/generate/student-invoice/index',
                    data: {
                        year: $('#year-filter').val(),
                        path: $('#path-filter').val(),
                        period: $('#period-filter').val()
                    },
                },
                columns: [
                    {
                        name: 'action',
                        orderable: false,
                        render: (data, _, row) => {
                            console.log(row);
                            var sp = 0;
                            var f = 0;
                            if(row.study_program){
                                sp = row.study_program.studyprogram_id;
                            }
                            if(row.faculty){
                                f = row.faculty.faculty_id;
                            }
                            return this.template.rowAction(f, sp)
                        }
                    },
                    {
                        name: 'faculty',
                        searchable: true,
                        render: (data, _, row) => {
                            return `
                                <div class="${ row.study_program ? 'ps-2' : '' }">
                                    <a type="button" href="${_baseURL+'/payment/generate/student-invoice/detail'}" class="btn btn-link">${row.faculty ? row.faculty.faculty_name : (row.study_program.studyprogram_type.toUpperCase()+' '+row.study_program.studyprogram_name)}</a>
                                </div>
                            `;
                        }
                    },
                    // {
                    //     name: 'components', 
                    //     searchable: true,
                    //     render: (data, _, row) => {
                    //         let html = '<div class="d-flex flex-wrap" style="gap:10px">';
                    //         if(row.components){
                    //             if(Object.keys(row.components).length > 0){
                    //                 for (const key in row.components) {
                    //                     // console.log(`${key}: ${row.components[key]}`);
                    //                     var path = "Unknown";
                    //                     var period = "Unknown";
                    //                     var lecture_type = "Unknown";
                    //                     if(row.components[key].path){
                    //                         path = row.components[key].path.path_name;
                    //                     }
                    //                     if(row.components[key].period){
                    //                         period = row.components[key].period.period_name;
                    //                     }
                    //                     if(row.components[key].lecture_type){
                    //                         lecture_type = row.components[key].lecture_type.mlt_name;
                    //                     }
                    //                     html += `<span class="badge bg-primary">${lecture_type} (${path}) - ${period}: ${Rupiah.format(row.components[key].total)}</span>`;
                    //                 }
                    //             }else{
                    //                 if(row.study_program){
                    //                     html += `<a href="{!! route('payment.settings.payment-rates') !!}" target="_blank"> Atur Tagihan Disini </a>`;
                    //                 }
                    //             }
                    //         }
                    //         html += '</div>';
                    //         return html;
                    //     }
                    // },
                    {
                        name: 'total', 
                        data: 'total',
                        render: (data) => {
                            return Rupiah.format(1000000000)
                        }
                    },
                    // {
                    //     name: 'discount', 
                    //     data: 'discount',
                    //     render: (data) => {
                    //         return Rupiah.format(data)
                    //     }
                    // },
                    // {
                    //     name: 'total', 
                    //     data: 'total',
                    //     render: (data) => {
                    //         return Rupiah.format(data)
                    //     }
                    // },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"new-student-invoice-actions d-flex align-items-end">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>t' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function() {
                    $('.new-student-invoice-actions').html(`
                        <div style="margin-bottom: 7px">
                            <h5>Daftar Tagihan</h5>
                        </div>
                    `)
                    feather.replace()
                }
            })
        },
        template: {
            rowAction: function(faculty_id,studyprogram_id) {
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="${_baseURL+'/payment/generate/student-invoice/detail?f='+faculty_id+'&sp='+studyprogram_id}"><i data-feather="external-link"></i>&nbsp;&nbsp;Detail pada Unit ini</a>
                            <a onclick="_newStudentInvoiceTableActions.generate()" class="dropdown-item" href="javascript:void(0);"><i data-feather="mail"></i>&nbsp;&nbsp;Generate pada Unit ini</a>
                            <a onclick="_newStudentInvoiceTableActions.delete()" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete pada Unit ini</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const _newStudentInvoiceTableActions = {
        tableRef: _newStudentInvoiceTable,
        generate: function() {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin generate tagihan pada unit ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#356CFF',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Generate',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    // ex: do ajax request
                    Swal.fire({
                        icon: 'success',
                        text: 'Berhasil generate tagihan',
                    })
                }
            })
        },
        delete: function() {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin menghapus tagihan pada unit ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    // ex: do ajax request
                    Swal.fire({
                        icon: 'success',
                        text: 'Berhasil menghapus tagihan',
                    })
                }
            })
        },
    }

    function filters(){
        dataTable.destroy();
        _newStudentInvoiceTable.init();
    }
</script>
@endsection
