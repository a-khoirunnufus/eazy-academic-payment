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
    <table id="new-student-invoice-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Program Studi / Fakultas</th>
                <th>Jumlah Mahasiswa</th>
                <th>Total Tagihan</th>
                <th>Status Generate</th>
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
                        searchable: false,
                        orderable: false,
                        render: (data, _, row) => {
                            var sp = 0;
                            var f = 0;
                            if(row.study_program){
                                sp = row.study_program.studyprogram_id;
                            }
                            if(row.faculty){
                                f = row.faculty.faculty_id;
                            }
                            return `
                                <div class="${ row.study_program ? 'ps-2' : '' }">
                                    <a type="button" href="${_baseURL+'/payment/generate/student-invoice/detail?f='+f+'&sp='+sp}" class="btn btn-link">${row.faculty ? row.faculty.faculty_name : (row.study_program.studyprogram_type.toUpperCase()+' '+row.study_program.studyprogram_name)}</a>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'total_student',
                        data: 'total_student',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        name: 'total_invoice', 
                        data: 'total_invoice',
                        searchable: false,
                        orderable: false,
                        render: (data, _, row) => {
                            return Rupiah.format(row.total_invoice)
                        }
                    },
                    {
                        name: 'total_generate', 
                        searchable: false,
                        orderable: false,
                        render: (data, _, row) => {
                            let status = "Belum Digenerate";
                            let bg = "bg-danger";
                            if(row.total_generate === row.total_student && row.total_student != 0){
                                status = "Sudah Digenerate";
                                bg = "bg-success";
                            }else if(row.total_generate < row.total_student && row.total_generate != 0){
                                status = "Sebagian Telah Digenerate";
                                bg = "bg-warning";
                            }
                            return '<div class="badge '+bg+'">'+status+' ('+row.total_generate+'/'+row.total_student+')</div>'
                        }
                    },
                    {
                        name: 'faculty.faculty_name',
                        data: 'faculty.faculty_name',
                        defaultContent: "",
                        visible: false,
                    },
                    {
                        name: 'study_program.studyprogram_name',
                        data: 'study_program.studyprogram_name',
                        defaultContent: "",
                        visible: false,
                    },
                    {
                        name: 'study_program.studyprogram_type',
                        data: 'study_program.studyprogram_type',
                        defaultContent: "",
                        visible: false,
                    },
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
                buttons: [
                    {
                        extend: 'collection',
                        className: 'btn btn-outline-secondary dropdown-toggle',
                        text: feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + 'Export',
                        buttons: [
                            {
                                extend: 'print',
                                text: feather.icons['printer'].toSvg({class: 'font-small-4 me-50'}) + 'Print',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [1,2,3,4]
                                }
                            },
                            {
                                extend: 'csv',
                                text: feather.icons['file-text'].toSvg({class: 'font-small-4 me-50'}) + 'Csv',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [1,2,3,4]
                                }
                            },
                            {
                                extend: 'excel',
                                text: feather.icons['file'].toSvg({class: 'font-small-4 me-50'}) + 'Excel',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [1,2,3,4]
                                }
                            },
                            {
                                extend: 'pdf',
                                text: feather.icons['clipboard'].toSvg({class: 'font-small-4 me-50'}) + 'Pdf',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [1,2,3,4]
                                }
                            },
                            {
                                extend: 'copy',
                                text: feather.icons['copy'].toSvg({class: 'font-small-4 me-50'}) + 'Copy',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [1,2,3,4]
                                }
                            }
                        ],
                    }
                ],
                initComplete: function() {
                    $('.new-student-invoice-actions').html(`
                        <div style="margin-bottom: 7px">
                            <h5>Daftar Tagihan</h5>
                        </div>
                    `)
                    feather.replace()
                }
            });
            this.implementSearchDelay();
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
