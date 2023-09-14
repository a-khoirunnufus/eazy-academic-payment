@extends('tpl.vuexy.master-payment')


@section('page_title', 'Pengajuan Dispensasi Pembayaran')
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

@include('pages._payment.approval._shortcuts', ['active' => 'dispensation'])
<div class="card">
    <div class="card-body">
        <div class="datatable-filter one-row">
            <div>
                <label class="form-label">Tahun Akademik</label>
                <select name="year" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Tahun</option>
                    @foreach($year as $item)
                        <option value="{{$item->msy_code}}">{{$item->msy_year}} {{$item->msy_semester == 1 ? 'Ganjil':'Genap'}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Fakultas</label>
                <select name="faculty" class="form-select" eazy-select2-active onchange="getProdi(this.value)">
                    <option value="#ALL" selected>Semua Fakultas</option>
                    @foreach($faculty as $item)
                        <option value="{{$item->faculty_id}}">{{$item->faculty_name}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Program Studi</label>
                <select name="prodi" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Program Study</option>
                </select>
            </div>
            <div>
                <label class="form-label">Status</label>
                <select name="status" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Status</option>
                    <option value="0">Ditolak</option>
                    <option value="1">Disetujui</option>
                    <option value="2">Menunggu Diproses</option>
                </select>
            </div>
            <div class="d-flex align-items-end">
                <button onclick="_dispensationSubmissionTable.reload()" class="btn btn-info text-nowrap">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="dispensation-submission-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Tahun Akademik</th>
                <th>Nama</th>
                <th>Fakultas <br>Prodi</th>
                <th>Total <br>Tagihan</th>
                <th>Tanggal Dispensasi</th>
                <th>Status</th>
                <th>Nama</th>
                <th>Nim</th>
                <th>Fakultas</th>
                <th>Prodi</th>
                <th>Total Tagihan</th>
                <th>Tanggal Dispensasi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection


@section('js_section')
<script>
    var percentageVal = 0;
    var amountVal = 0;

    var dt;
    // enabling multiple modal open
    $(document).on('show.bs.modal', '.modal', function() {
        const zIndex = 1040 + 10 * $('.modal:visible').length;
        $(this).css('z-index', zIndex);
        setTimeout(() => $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack'));
    });

    var target_column = [];

    $(function(){
        _dispensationSubmissionTable.init();

        for(var i = 7; i <= 13; i++){
            dt.column(i).visible(false)
            target_column.push(i);
        }

        dt.buttons.exportData({
            columns: target_column
        })
    })

    const _dispensationSubmissionTable = {
        ..._datatable,
        init: function(searchFilter = '#ALL') {
            dt = this.instance = $('#dispensation-submission-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/payment/approval-dispensation/index',
                    data: function(d) {
                        d.custom_filters = {
                            'year': $('select[name="year"]').val(),
                            'faculty': $('select[name="faculty"]').val(),
                            'prodi': $('select[name="prodi"]').val(),
                            'status': $('select[name="status"]').val(),
                        };
                    },
                    dataSrc: (json) => {
                        if(searchFilter != '#ALL'){
                            var data = [];

                            for(var i = 0; i < json.data.length; i++){
                                var row = json.data[i];

                                var isFound = false;

                                if(!isFound && (''+row.period.msy_year+' '+(row.period.msy_semester == 1 ? 'Ganjil':'Genap')).toLowerCase().search(searchFilter.toLowerCase()) >= 0){
                                    data.push(row);
                                    isFound = true;
                                }

                                if(!isFound && ''+row.student.fullname.toLowerCase().search(searchFilter.toLowerCase()) >= 0){
                                    data.push(row);
                                    isFound = true;
                                }

                                if(!isFound && ''+row.student.student_id.toLowerCase().search(searchFilter.toLowerCase()) >= 0){
                                    data.push(row);
                                    isFound = true;
                                }

                                if(!isFound && ''+row.student.study_program.studyprogram_type+' '+row.student.study_program.studyprogram_name.toLowerCase().search(searchFilter.toLowerCase()) >= 0){
                                    data.push(row);
                                    isFound = true;
                                }

                                if(!isFound && ''+row.student.study_program.faculty.faculty_name.toLowerCase().search(searchFilter.toLowerCase()) >= 0){
                                    data.push(row);
                                    isFound = true;
                                }

                                var prr;
                                if(row.payment.prr_total != null){
                                    prr = row.payment.prr_total.toString();
                                }else {
                                    prr = '-'
                                }
                                if(!isFound && ''+prr.toLowerCase().search(searchFilter.toLowerCase()) >= 0){
                                    data.push(row);
                                    isFound = true;
                                }

                                var tglDispen = row.mds_deadline ? row.mds_deadline:'-';
                                if(!isFound && ''+tglDispen.toLowerCase().search(searchFilter.toLowerCase()) >= 0){
                                    data.push(row);
                                    isFound = true;
                                }

                                var status = '';
                                switch(row.mds_status){
                                    case 0:
                                        status = 'Ditolak';
                                        break;
                                    case 1:
                                        status = 'Disetujui';
                                        break;
                                    case 2:
                                        status = 'Menunggu Diproses';
                                        break;
                                }
                                if(!isFound && status.toLowerCase().search(searchFilter.toLowerCase()) >= 0){
                                    data.push(row);
                                    isFound = true;
                                }
                            }

                            json.data = data;
                        }
                        return json.data;
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
                            return this.template.rowAction(row)
                        }
                    },
                    {
                        name: 'mds_school_year',
                        data: 'mds_school_year',
                        searchable: false,
                        render: (data, _, row) => {
                            return row.period.msy_year + _helper.semester(row.period.msy_semester)
                        }
                    },
                    {
                        name: 'student_number',
                        data: 'student_number',
                        searchable: false,
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <span class="text-nowrap fw-bold">${row.student.fullname}</span><br>
                                    <small class="text-nowrap text-secondary">${row.student.student_id}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'student_number',
                        data: 'student_number',
                        searchable: false,
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <span class="text-nowrap fw-bold">${row.student.study_program.studyprogram_type} ${row.student.study_program.studyprogram_name}</span><br>
                                    <small class="text-nowrap text-secondary">${row.student.study_program.faculty.faculty_name}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'prr_id',
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <a  onclick="_invoiceAction.detail(event,_dispensationSubmissionTable,'lazy')" href="javascript:void(0);" class="text-nowrap fw-bold">${(row.payment) ? Rupiah.format(row.payment.prr_total) : "-"}</a><br>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'mds_deadline',
                        data: 'mds_deadline',
                        searchable: false,
                        render: (data, _, row) => {
                            let date = "-";
                            if(row.mds_deadline){
                                date = row.mds_deadline;
                            }
                            return `${date}`;
                        }
                    },
                    {
                        name: 'mds_status',
                        data: 'mds_status',
                        searchable: false,
                        render: (data, _, row) => {
                            let status = "Ditolak";
                            let bg = "bg-danger";
                            if(row.mds_status === 1){
                                status = "Disetujui";
                                bg = "bg-success";
                            }else if(row.mds_status === 2){
                                status = "Menunggu Diproses";
                                bg = "bg-warning";
                            }
                            return '<div class="badge '+bg+'">'+status+'</div>'
                        }
                    },
                    {
                        name: 'student_number',
                        data: 'student_number',
                        searchable: false,
                        render: (data, _, row) => {
                            return row.student.fullname;
                        }
                    },
                    {
                        name: 'student_number',
                        data: 'student_number',
                        searchable: false,
                        render: (data, _, row) => {
                            return row.student.student_id;
                        }
                    },
                    {
                        name: 'student_number',
                        data: 'student_number',
                        searchable: false,
                        render: (data, _, row) => {
                            return row.student.study_program.faculty.faculty_name;
                        }
                    },
                    {
                        name: 'student_number',
                        data: 'student_number',
                        searchable: false,
                        render: (data, _, row) => {
                            return `${row.student.study_program.studyprogram_type} ${row.student.study_program.studyprogram_name}`;
                        }
                    },
                    {
                        name: 'prr_id',
                        render: (data, _, row) => {
                            return (row.payment) ? row.payment.prr_total : "-"
                        }
                    },
                    {
                        name: 'mds_deadline',
                        data: 'mds_deadline',
                        searchable: false,
                        render: (data, _, row) => {
                            let date = "-";
                            if(row.mds_deadline){
                                date = row.mds_deadline;
                            }
                            return `${date}`;
                        }
                    },
                    {
                        name: 'mds_status',
                        data: 'mds_status',
                        searchable: false,
                        render: (data, _, row) => {
                            let status = "Ditolak";
                            if(row.mds_status === 1){
                                status = "Disetujui";
                            }else if(row.mds_status === 2){
                                status = "Menunggu Diproses";
                            }
                            return status
                        }
                    },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"submission-dispensation-action d-flex align-items-end">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" <"search-filter">lB> >' +
                    '>t' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                    buttons: [{
                    extend: 'collection',
                    text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-external-link font-small-4 me-50"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>Export</span>',
                    className: 'btn btn-outline-secondary dropdown-toggle',
                    buttons: [
                        {
                            text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file font-small-4 me-50"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>Excel</span>',
                            className: 'dropdown-item',
                            extend: 'excel',
                            exportOptions: {
                                columns: target_column
                            }
                        },
                        {
                            text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clipboard font-small-4 me-50"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>Pdf</span>',
                            className: 'dropdown-item',
                            extend: 'pdf',
                            exportOptions: {
                                columns: target_column
                            }
                        },
                        {
                            text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text font-small-4 me-50"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>Csv</span>',
                            className: 'dropdown-item',
                            extend: 'csv',
                            exportOptions: {
                                columns: target_column
                            }
                        },
                        {
                            text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-copy font-small-4 me-50"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>Copy</span>',
                            className: 'dropdown-item',
                            extend: 'copy',
                            exportOptions: {
                                columns: target_column
                            }
                        },
                    ]
                }, ],
                initComplete: function() {
                    $('.submission-dispensation-action').html(`
                        <div style="margin-bottom: 7px">
                            <button onclick="_dispensationSubmissionTableActions.add()" class="btn btn-info">
                                <span style="vertical-align: middle">
                                    <i data-feather="plus" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Tambah Pengajuan Dispensasi
                                </span>
                            </button>
                        </div>
                    `)

                    $('.search-filter').html(`
                    <div id="dispensation-submission-table_filter" class="dataTables_filter">
                        <label>
                            <input type="search" class="form-control" placeholder="Cari Data" aria-controls="dispensation-submission-table" onkeydown="searchFilter(event, this)">
                        </label>
                    </div>
                    `)
                    feather.replace()
                }
            })
            this.implementSearchDelay()
        },
        template: {
            rowAction: function(row) {
                let process = `<a onclick="_dispensationSubmissionTableActions.process(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="loader"></i>&nbsp;&nbsp;Proses Pengajuan</a>`;
                if(row.mds_status != 2){
                    process = ``;
                }
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a onclick="_dispensationSubmissionTableActions.detail(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="eye"></i>&nbsp;&nbsp;Detail Pengajuan</a>
                            <a onclick="_invoiceAction.detail(event,_dispensationSubmissionTable,'lazy')" class="dropdown-item" href="javascript:void(0);"><i data-feather="eye"></i>&nbsp;&nbsp;Detail Tagihan</a>
                            ${process}
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

    const _componentForm = {
        clearData: function(){
            FormDataJson.clear('#form-add-invoice-component')
            $("#form-add-invoice-component .select2").trigger('change')
            $(".form-alert").remove()
        },
        setData: function(data){
            $("[name=msc_name]").val(data.msc_name)
            $("[name=msc_description]").val(data.msc_description)
            _options.load({
                optionUrl: _baseURL + '/api/payment/settings/component-type',
                nameField: 'msct_id',
                idData: 'msct_id',
                nameData: 'msct_name',
                val: data.msct_id
            });
            data.msc_is_new_student == 1 ? $('[name=msc_is_new_student]').prop('checked', true) : '';
            data.msc_is_student == 1 ? $('[name=msc_is_student]').prop('checked', true) : '';
            data.msc_is_participant == 1 ? $('[name=msc_is_participant]').prop('checked', true) : '';
        }
    }

    const _dispensationSubmissionTableActions = {
        detail: function(e) {
            const data = _dispensationSubmissionTable.getRowData(e);
            let decline_reason = ``;
            if(data.mds_status == 0){
                decline_reason = `<tr>
                    <td class="fw-bolder">Alasan Penolakan</td>
                    <td class="fw-bolder">:&nbsp;&nbsp;${data.mds_decline_reason}</td>
                </tr>`
            }
            Modal.show({
                type: 'detail',
                modalTitle: 'Detail Pengajuan Dispensasi Pembayaran',
                modalSize: 'md',
                config: {
                    fields: {
                        header: {
                            type: 'custom-field',
                            title: '',
                            content: {
                                template: `
                                <div>
                                    <table class="eazy-table-info">
                                        <tbody>
                                            ${decline_reason}
                                            <tr>
                                                <td>Tahun Akademik</td>
                                                <td>:&nbsp;&nbsp;${data.period.msy_year + _helper.semester(data.period.msy_semester)}</td>
                                            </tr>
                                            <tr>
                                                <td>Nama</td>
                                                <td>:&nbsp;&nbsp;${data.student.fullname}</td>
                                            </tr>
                                            <tr>
                                                <td>NIM</td>
                                                <td>:&nbsp;&nbsp;${data.student.student_id}</td>
                                            </tr>
                                            <tr>
                                                <td>Fakultas</td>
                                                <td>:&nbsp;&nbsp;${data.student.study_program.faculty.faculty_name}</td>
                                            </tr>
                                            <tr>
                                                <td>Prodi</td>
                                                <td>:&nbsp;&nbsp;${data.student.study_program.studyprogram_type} ${data.student.study_program.studyprogram_name}</td>
                                            </tr>
                                            <tr>
                                                <td>No.HP</td>
                                                <td>:&nbsp;&nbsp;${data.mds_phone}</td>
                                            </tr>
                                            <tr>
                                                <td>Email</td>
                                                <td>:&nbsp;&nbsp;${data.mds_email}</td>
                                            </tr>
                                            <tr>
                                                <td>Alasan</td>
                                                <td>:&nbsp;&nbsp;${data.mds_reason}</td>
                                            </tr>
                                            <tr>
                                                <td>Bukti Pendukung</td>
                                                <td>:&nbsp;&nbsp;<a href="${'{{ url("file","student-dispensation") }}/'+data.mds_id}" target="_blank">${data.mds_proof_filename}</a></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>`
                            },
                        },
                    },
                    callback: function() {
                        feather.replace();
                    }
                },
            });
        },
        add: function() {
            Modal.show({
                type: 'form',
                modalTitle: 'Pengajuan Dispensasi',
                modalSize: 'lg',
                config: {
                    formId: 'form-add-dispensation-submission',
                    formActionUrl: _baseURL + '/api/student/dispensation/store',
                    formType: 'add',
                    isTwoColumn: true,
                    fields: {
                        name: {
                            title: 'Nama',
                            content: {
                                template:
                                    `<select class="form-select select2" eazy-select2-active id="studentId" name="student_number">
                                        <option value="">Pilih Mahasiswa</option>
                                    </select>`,
                            },
                        },
                        academic: {
                            title: 'Tahun Akademik',
                            content: {
                                template:
                                    `<input
                                        type="text"
                                        name="academic_year"
                                        class="form-control" value="{{ $activeYear }}" disabled="disabled"
                                    >
                                    <input type="hidden" name="mds_school_year" value="{{$yearCode}}">`,
                            },
                        },
                        no_telp: {
                            title: 'No Telepon',
                            content: {
                                template:
                                    `<input
                                        type="text"
                                        name="mds_phone"
                                        class="form-control"
                                    >`,
                            },
                        },
                        email: {
                            title: 'Email',
                            content: {
                                template:
                                    `<input
                                        type="text"
                                        name="mds_email"
                                        class="form-control"
                                    >`,
                            },
                        },
                        proof: {
                            title: 'Bukti Pendukung (.jpg/.pdf)',
                            content: {
                                template:
                                    `<input
                                        type="file"
                                        name="mds_proof"
                                        class="form-control"
                                    >
                                    `,
                            },
                        },
                        reason: {
                            title: 'Alasan',
                            content: {
                                template:
                                    `<textarea name="mds_reason" class="form-control"></textarea>
                                    `,
                            },
                        },
                    },
                    formSubmitLabel: 'Tambahkan',
                    callback: function(e) {
                        _dispensationSubmissionTable.reload()
                    },
                },
            });
            $.get(_baseURL + '/api/payment/approval-dispensation/getStudent', (data) => {
                JSON.parse(data).map(item => {
                    $("#studentId").append(`
                        <option value="` + item['student_number'] + `">` + item['fullname'] + ` - `+item['student_id']+`</option>
                    `)
                })
                selectRefresh();
            })
        },
        process: function(e) {
            let data = _dispensationSubmissionTable.getRowData(e);
            Modal.show({
                type: 'form',
                modalTitle: 'Proses Pengajuan',
                modalSize: 'md',
                config: {
                    formId: 'form-process-dispensation-submission',
                    formActionUrl: _baseURL + '/api/payment/approval-dispensation/store',
                    formType: 'add',
                    rowId: data.mds_id,
                    fields: {
                        detail_dispensation_submission: {
                            title: '<span class="fw-bolder">Detail Pengajuan Permohonan Kredit</span>',
                            content: {
                                template:
                                    `<div>
                                    <table class="eazy-table-info">
                                        <tbody>
                                            <tr>
                                                <td>Tahun Akademik</td>
                                                <td>:&nbsp;&nbsp;${data.period.msy_year + _helper.semester(data.period.msy_semester)}</td>
                                            </tr>
                                            <tr>
                                                <td>Nama</td>
                                                <td>:&nbsp;&nbsp;${data.student.fullname}</td>
                                            </tr>
                                            <tr>
                                                <td>NIM</td>
                                                <td>:&nbsp;&nbsp;${data.student.student_id}</td>
                                            </tr>
                                            <tr>
                                                <td>Fakultas</td>
                                                <td>:&nbsp;&nbsp;${data.student.study_program.faculty.faculty_name}</td>
                                            </tr>
                                            <tr>
                                                <td>Prodi</td>
                                                <td>:&nbsp;&nbsp;${data.student.study_program.studyprogram_type} ${data.student.study_program.studyprogram_name}</td>
                                            </tr>
                                            <tr>
                                                <td>No.HP</td>
                                                <td>:&nbsp;&nbsp;${data.mds_phone}</td>
                                            </tr>
                                            <tr>
                                                <td>Email</td>
                                                <td>:&nbsp;&nbsp;${data.mds_email}</td>
                                            </tr>
                                            <tr>
                                                <td>Alasan</td>
                                                <td>:&nbsp;&nbsp;${data.mds_reason}</td>
                                            </tr>
                                            <tr>
                                                <td>Bukti Pendukung</td>
                                                <td>:&nbsp;&nbsp;<a href="${'{{ url("file","student-dispensation") }}/'+data.mds_id}" target="_blank">${data.mds_proof_filename}</a></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>`,
                            },
                        },
                        detail_payment: {
                            title: '<span class="fw-bolder">Detail Tagihan</span>',
                            content: {
                                template:
                                    `<div>
                                    <table class="eazy-table-info">
                                        <tbody>
                                            <tr>
                                                <td>Total Tagihan</td>
                                                <td>:&nbsp;&nbsp;${Rupiah.format(data.payment.prr_total)}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>`,
                            },
                        },
                        schema: {
                            type: 'custom-field',
                            content: {
                                template: `<div class="mb-2">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12">
                                            <label class="form-label">Tanggal Dispensasi</label>
                                            <input type="date" class="form-control" name="prr_dispensation_date" required></input>
                                        </div>
                                    </div>
                                </div>`
                            },
                        },
                    },
                    formSubmitLabel: 'Setujui',
                    isSecondButtonCustom: true,
                    SecondButtonCustom: `<a href="javascript:void(0);" onclick="_dispensationSubmissionTableActions.decline(${data.mds_id})" class="btn btn-danger me-1">Tolak</a>`,
                    callback: function() {
                        _dispensationSubmissionTable.reload()
                    },
                },
            });
        },
        decline: function(mds_id){
            // let data = _dispensationSubmissionTable.getRowData(e);
            // console.log("hehe");
            Modal.close();
            Swal.fire({
                title: 'Alasan Penolakan',
                input: 'textarea',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Tolak',
                showLoaderOnConfirm: false,
                allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                if (result.isConfirmed) {
                    // console.log(result);
                    $.post(_baseURL + '/api/payment/approval-dispensation/decline', {
                            mds_id: mds_id,
                            mds_decline_reason: result.value
                        }, function(data){
                        data = JSON.parse(data)
                        if(data.success){
                            Swal.fire({
                                icon: 'success',
                                text: data.message,
                            }).then(() => {
                                _dispensationSubmissionTable.reload()
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

    function getProdi(val){
        console.log(val)

        $('select[name="prodi"]').html(`
            <option value="#ALL" selected>Semua Program Study</option>
        `)

        if(val != '#ALL'){
            var xhr = new XMLHttpRequest();
            xhr.onload = function(){
                var data = JSON.parse(this.responseText);

                for(var i = 0; i < data.length; i++){
                    var item = data[i];
                    $('select[name="prodi"]').append(`
                        <option value="${item.studyprogram_id}">${item.studyprogram_type} ${item.studyprogram_name}</option>
                    `)
                }
            }
            xhr.open('GET', _baseURL+'/api/payment/approval-dispensation/study-program/'+val);
            xhr.send()
        }
    }

    function searchFilter(event, elm){
        if(event.key == 'Enter'){
            var val = elm.value;
            elm.value = ''

            dt.clear().destroy();
            _dispensationSubmissionTable.init(val);
        }
    }
</script>
@include('pages._payment.generate.student-invoice.invoice');
@endsection
