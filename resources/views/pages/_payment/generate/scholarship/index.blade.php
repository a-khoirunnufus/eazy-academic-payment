@extends('tpl.vuexy.master-payment')


@section('page_title', 'Generate Beasiswa Mahasiswa')
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

@include('pages._payment.generate._shortcuts', ['active' => 'scholarship'])

<div class="card">
    <div class="card-body">
        <div class="datatable-filter one-row">
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
            <div class="d-flex align-items-end">
                <button onclick="_scholarshipReceiverTable.reload()" class="btn btn-primary text-nowrap">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="invoice-component-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Mahasiswa</th>
                <th>Fakultas - Prodi</th>
                <th>Beasiswa</th>
                <th>Periode </th>
                <th>Nominal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

@endsection


@section('js_section')
<script>
    $(function(){
        _scholarshipReceiverTable.init();
    })

    const _scholarshipReceiverTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#invoice-component-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/payment/generate/scholarship/index',
                    data: function(d) {
                        d.custom_filters = {
                            'md_period_start_filter': $('select[name="md_period_start_filter"]').val(),
                            'md_period_end_filter': $('select[name="md_period_end_filter"]').val(),
                        };
                    }
                },
                columns: [
                    {
                        name: 'action',
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: (data, _, row) => {
                            console.log(row);
                            return this.template.rowAction(row.msr_status_generate)
                        }
                    },
                    {
                        name: 'student_number',
                        data: 'student_number',
                        searchable: false,
                        render: (data, _, row) => {
                            let fullname = "";
                            let student_id = "";
                            if(!row.student){
                                fullname = row.new_student.participant.par_fullname;
                                student_id = row.new_student.reg_number;
                            }else{
                                fullname = row.student.fullname;
                                student_id = row.student.fullname;
                            }
                            return `
                                <div>
                                    <span class="text-nowrap fw-bold">${fullname}</span><br>
                                    <small class="text-nowrap text-secondary">${student_id}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'student_number',
                        data: 'student_number',
                        searchable: false,
                        render: (data, _, row) => {
                            let studyprogram_type = "";
                            let studyprogram_name = "";
                            let faculty_name = "";
                            if(!row.student){
                                studyprogram_type = row.new_student.studyprogram.studyprogram_type;
                                studyprogram_name = row.new_student.studyprogram.studyprogram_name;
                                faculty_name = row.new_student.studyprogram.faculty.faculty_name;
                            }else{
                                studyprogram_type = row.student.study_program.studyprogram_type;
                                studyprogram_name = row.student.study_program.studyprogram_name;
                                faculty_name = row.student.study_program.faculty.faculty_name;
                            }
                            return `
                                <div>
                                    <span class="text-nowrap fw-bold">${studyprogram_type} ${studyprogram_name}</span><br>
                                    <small class="text-nowrap text-secondary">${faculty_name}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'ms_id',
                        data: 'ms_id',
                        searchable: false,
                        render: (data, _, row) => {
                            let company = (row.scholarship.ms_from) ? row.scholarship.ms_from : "";
                            return "<span class='fw-bolder'>"+row.scholarship.ms_name +"</span> <br>"+company;
                        }
                    },
                    {
                        name: 'msr_period',
                        data: 'msr_period',
                        searchable: false,
                        render: (data, _, row) => {
                            return row.period.msy_year + _helper.semester(row.period.msy_semester)
                        }
                    },
                    {
                        name: 'msr_nominal',
                        data: 'msr_nominal',
                        render: (data, _, row) => {
                            return Rupiah.format(data)
                        }
                    },
                    {
                        name: 'msr_status_generate',
                        data: 'msr_status_generate',
                        searchable: false,
                        render: (data, _, row) => {
                            let status = "Belum Digenerate";
                            let bg = "bg-danger";
                            if(row.msr_status_generate === 1){
                                status = "Sudah Digenerate";
                                bg = "bg-success";
                            }
                            return '<div class="badge '+bg+'">'+status+'</div>'
                        }
                    },
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
                    $('.invoice-component-actions').html(`
                        <div style="margin-bottom: 7px">
                            <button onclick="_scholarshipReceiverTableActions.generateBulk()" class="btn btn-primary">
                                <span style="vertical-align: middle">
                                    <i data-feather="command" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Generate All
                                </span>
                            </button>
                            <button onclick="_scholarshipReceiverTableActions.deleteBulk()" class="btn btn-danger">
                                <span style="vertical-align: middle">
                                    <i data-feather="trash" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Delete All
                                </span>
                            </button>
                        </div>
                    `)
                    feather.replace()
                }
            })
            this.implementSearchDelay()
        },
        template: {
            rowAction: function(status) {
                let generate = '';
                let del = '<a onclick="_scholarshipReceiverTableActions.delete(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>';
                if(status != 1){
                    generate = '<a onclick="_scholarshipReceiverTableActions.generate(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="command"></i>&nbsp;&nbsp;Generate</a>';
                    del = '';
                }
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            ${generate}
                            ${del}
                        </div>
                    </div>
                `
            }
        }
    }

    const _helper = {
        semester: function(msy_semester){
            var semester = ' Genap';
            if(msy_semester == 1) {
                semester = ' Ganjil';
            }
            return semester;
        }
    }

    const _scholarshipReceiverTableActions = {
        generate: function(e) {
            let data = _scholarshipReceiverTable.getRowData(e);
            let fullname = "";
            if(!data.student){
                fullname = data.new_student.participant.par_fullname;
            }else{
                fullname = data.student.fullname;
            }
            Swal.fire({
                title: 'Konfirmasi',
                html: 'Apakah anda yakin ingin generate beasiswa mahasiswa <span class="fw-bolder">'+fullname+'</span>?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Generate',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(_baseURL + '/api/payment/generate/scholarship/generate', {
                            msr_id: data.msr_id,
                        }, function(data){
                        data = JSON.parse(data)
                        if(data.success){
                            Swal.fire({
                                icon: 'success',
                                text: data.message,
                            }).then(() => {
                                _scholarshipReceiverTable.reload()
                            });
                        }else{
                            Swal.fire({
                                icon: 'error',
                                text: data.message,
                            });
                        }

                    }).fail((error) => {
                        Swal.fire({
                            icon: 'error',
                            text: data.message,
                        });
                        _responseHandler.generalFailResponse(error)
                    })
                }
            })
        },
        generateBulk: function() {
            Swal.fire({
                title: 'Apakah anda yakin ingin generate seluruh beasiswa mahasiswa?',
                html: '<small>Mahasiswa yang belum memiliki tagihan tidak akan tergenerate</small>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Generate All',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(_baseURL + '/api/payment/generate/scholarship/generateBulk', function(data){
                        data = JSON.parse(data)
                        if(data.success){
                            Swal.fire({
                                icon: 'success',
                                text: data.message,
                            }).then(() => {
                                _scholarshipReceiverTable.reload()
                            });
                        }else{
                            Swal.fire({
                                icon: 'error',
                                text: data.message,
                            });
                        }

                    }).fail((error) => {
                        Swal.fire({
                            icon: 'error',
                            text: data.message,
                        });
                        _responseHandler.generalFailResponse(error)
                    })
                }
            })
        },
        delete: function(e) {
            let data = _scholarshipReceiverTable.getRowData(e);
            if(!data.student){
                fullname = data.new_student.participant.par_fullname;
            }else{
                fullname = data.student.fullname;
            }
            Swal.fire({
                title: 'Konfirmasi',
                html: 'Apakah anda yakin ingin menghapus generate beasiswa <span class="fw-bolder">'+fullname+'</span> ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(_baseURL + '/api/payment/generate/scholarship/delete/' + data.msr_id, {
                        _method: 'DELETE'
                    }, function(data){
                        data = JSON.parse(data)
                        if(data.success){
                            Swal.fire({
                                icon: 'success',
                                text: data.message,
                            }).then(() => {
                                _scholarshipReceiverTable.reload()
                            });
                        }else{
                            Swal.fire({
                                icon: 'error',
                                text: data.message,
                            });
                        }
                    }).fail((error) => {
                        Swal.fire({
                            icon: 'error',
                            text: data.message,
                        });
                        _responseHandler.generalFailResponse(error)
                    })
                }
            })
        },
        deleteBulk: function() {
            Swal.fire({
                title: 'Apakah anda yakin ingin menghapus seluruh beasiswa mahasiswa?',
                html: '<small>Seluruh tagihan mahasiswa akan kembali ke nominal awal</small>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Delete All',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(_baseURL + '/api/payment/generate/scholarship/deleteBulk', function(data){
                        data = JSON.parse(data)
                        if(data.success){
                            Swal.fire({
                                icon: 'success',
                                text: data.message,
                            }).then(() => {
                                _scholarshipReceiverTable.reload()
                            });
                        }else{
                            Swal.fire({
                                icon: 'error',
                                text: data.message,
                            });
                        }

                    }).fail((error) => {
                        Swal.fire({
                            icon: 'error',
                            text: data.message,
                        });
                        _responseHandler.generalFailResponse(error)
                    })
                }
            })
        },
    }

</script>
@endsection
