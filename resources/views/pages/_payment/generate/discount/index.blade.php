@extends('tpl.vuexy.master-payment')


@section('page_title', 'Generate Potongan Mahasiswa')
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

@include('pages._payment.generate._shortcuts', ['active' => 'discount'])

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
                <button onclick="_discountReceiverTable.reload()" class="btn btn-info text-nowrap">
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
                <th>Potongan</th>
                <th>Periode </th>
                <th>Nominal</th>
                <th>Status Generate</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

@endsection


@section('js_section')
<script>
    $(function(){
        _discountReceiverTable.init();
    })

    const _discountReceiverTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#invoice-component-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/payment/generate/discount/index',
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
                            return this.template.rowAction(row.mdr_status_generate)
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
                                student_id = row.student.student_id;
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
                        name: 'md_id',
                        data: 'md_id',
                        searchable: false,
                        render: (data, _, row) => {
                            return row.discount.md_name
                        }
                    },
                    {
                        name: 'mdr_period',
                        data: 'mdr_period',
                        searchable: false,
                        render: (data, _, row) => {
                            return row.period.msy_year + _helper.semester(row.period.msy_semester)
                        }
                    },
                    {
                        name: 'mdr_nominal',
                        data: 'mdr_nominal',
                        render: (data, _, row) => {
                            return Rupiah.format(data)
                        }
                    },
                    {
                        name: 'mdr_status_generate',
                        data: 'mdr_status_generate',
                        searchable: false,
                        render: (data, _, row) => {
                            let status = "Belum Digenerate";
                            let bg = "bg-danger";
                            if(row.mdr_status_generate === 1){
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
                            <a onclick="_discountReceiverTableActions.generateBulk()" class="btn btn-info" href="javascript:void(0);">
                            <i data-feather="command"></i> Generate All</a>
                            <a onclick="_discountReceiverTableActions.deleteBulk()" class="btn btn-danger" href="javascript:void(0);">
                            <i data-feather="trash"></i> Delete All</a>
                            <a onclick="_discountReceiverTableActions.logActivityModal()" class="btn btn-secondary" href="javascript:void(0);">
                            <i data-feather="book-open"></i> Log Activity</a>
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
                let del = '<a onclick="_discountReceiverTableActions.delete(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete Potongan</a>';
                if(status != 1){
                    generate = '<a onclick="_discountReceiverTableActions.generate(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="command"></i>&nbsp;&nbsp;Generate Potongan</a>';
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

    const _discountReceiverTableActions = {
        generate: function(e) {
            let data = _discountReceiverTable.getRowData(e);
            let fullname = "";
            if(!data.student){
                fullname = data.new_student.participant.par_fullname;
            }else{
                fullname = data.student.fullname;
            }
            Swal.fire({
                title: 'Konfirmasi',
                html: 'Apakah anda yakin ingin generate potongan mahasiswa <span class="fw-bolder">'+fullname+'</span>?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Generate',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(_baseURL + '/api/payment/generate/discount/generate', {
                            mdr_id: data.mdr_id,
                            url: `{{ request()->path() }}`
                        }, function(data){
                        data = JSON.parse(data)
                        if(data.success){
                            Swal.fire({
                                icon: 'success',
                                text: data.message,
                            }).then(() => {
                                _discountReceiverTable.reload()
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
                title: 'Apakah anda yakin ingin generate seluruh potongan mahasiswa?',
                html: '<small>Mahasiswa yang belum memiliki tagihan tidak akan tergenerate</small>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Generate All',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(_baseURL + '/api/payment/generate/discount/generateBulk', {
                            url: `{{ request()->path() }}`
                        }, function(data){
                        data = JSON.parse(data)
                        if(data.success){
                            Swal.fire({
                                icon: 'success',
                                text: data.message,
                            }).then(() => {
                                _discountReceiverTable.reload()
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
            let data = _discountReceiverTable.getRowData(e);
            let fullname = "";
            if(!data.student){
                fullname = data.new_student.participant.par_fullname;
            }else{
                fullname = data.student.fullname;
            }
            Swal.fire({
                title: 'Konfirmasi',
                html: 'Apakah anda yakin ingin menghapus generate potongan <span class="fw-bolder">'+fullname+'</span> ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(_baseURL + '/api/payment/generate/discount/delete/' + data.mdr_id, {
                        _method: 'DELETE',
                        url: `{{ request()->path() }}`
                    }, function(data){
                        data = JSON.parse(data)
                        if(data.success){
                            Swal.fire({
                                icon: 'success',
                                text: data.message,
                            }).then(() => {
                                _discountReceiverTable.reload()
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
                title: 'Apakah anda yakin ingin menghapus seluruh potongan mahasiswa?',
                html: '<small>Seluruh tagihan mahasiswa akan kembali ke nominal awal</small>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Delete All',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(_baseURL + '/api/payment/generate/discount/deleteBulk', {
                        url: `{{ request()->path() }}`
                    },function(data){
                        data = JSON.parse(data)
                        if(data.success){
                            Swal.fire({
                                icon: 'success',
                                text: data.message,
                            }).then(() => {
                                _discountReceiverTable.reload()
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
        logActivityModal: function() {
            title = `Log Activity Potongan`;
            url = `{{ request()->path() }}`;
            logActivity(title,url);
        },
    }

</script>
@endsection
