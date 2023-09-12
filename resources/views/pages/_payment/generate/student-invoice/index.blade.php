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
         .nested-checkbox {
            list-style-type: none;
            padding-left: 0px;
            font-size: 15px!important;
            font-weight: bold!important;
        }

        .nested-checkbox ul {
            list-style-type: none;
            padding-left: 0px;
            font-size: 15px!important;
            font-weight: bold!important;
        }

        .nested-checkbox ul li {
            list-style-type: none;
            font-size: 15px!important;
            font-weight: bold!important;
        }

        .py-05{
            padding-top: 0.5%!important;
            padding-bottom: 0.5%!important;
        }

    </style>
@endsection

@section('content')

@include('pages._payment.generate._shortcuts', ['active' => 'student-invoice'])
<div class="card">
    <table id="student-invoice-table" class="table table-striped">
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
    var header = null;
    $(function(){
        _studentInvoiceTable.init()
        $.get(_baseURL + '/api/payment/generate/student-invoice/headerall', (d) => {
            header = d;
        })
        $(document).on('click', '.pagination a', function(event){
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            _studentInvoiceTableActions.fetchLogGenerate(page);
        });
    })

    const _studentInvoiceTable = {
        ..._datatable,
        init: function() {
            dataTable = this.instance = $('#student-invoice-table').DataTable({
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
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"student-invoice-actions d-flex align-items-end">>' +
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
                    $('.student-invoice-actions').html(`
                        <div style="margin-bottom: 7px">
                            <a onclick="_studentInvoiceTableActions.generateForm()" class="btn btn-primary" href="javascript:void(0);">
                                <i data-feather="command"></i> Generate Tagihan Mahasiswa</a>
                            <a onclick="_studentInvoiceTableActions.logGenerate()" class="btn btn-secondary" href="javascript:void(0);">
                            <i data-feather="book-open"></i> Log Generate</a>
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
                            <a onclick="_studentInvoiceTableActions.delete(${faculty_id},${studyprogram_id})" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete pada Unit ini</a>
                            <a onclick="_studentInvoiceTableActions.regenerate(${faculty_id},${studyprogram_id})" class="dropdown-item" href="javascript:void(0);"><i data-feather="refresh-cw"></i>&nbsp;&nbsp;Regenerate pada Unit ini</a>
                        </div>
                    </div>
                `
            }
        }
    }
    // <a onclick="_studentInvoiceTableActions.generate()" class="dropdown-item" href="javascript:void(0);"><i data-feather="mail"></i>&nbsp;&nbsp;Generate pada Unit ini</a>
    const _studentInvoiceTableActions = {
        tableRef: _studentInvoiceTable,
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
        delete: function(faculty_id,studyprogram_id) {
            Swal.fire({
                title: 'Konfirmasi',
                html: `Apakah anda yakin ingin menghapus tagihan pada unit ini? <br> <small class="text-danger">Seluruh pengaturan pembayaran seperti beasiswa, potongan, cicilan, dispensasi akan ikut terhapus<small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    // ex: do ajax request
                    $url = _baseURL + "/api/payment/generate/student-invoice/delete/";
                    var unit_id = 0;
                    if (faculty_id == 0) {
                        $url += "prodi/"
                        unit_id = studyprogram_id
                    } else {
                        $url += "faculty/"
                        unit_id = faculty_id
                    }

                    var xhr = new XMLHttpRequest();
                    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                    xhr.onload = function() {
                        var data = JSON.parse(this.responseText);
                        Swal.fire({
                            icon: data.status == true ? 'success':'error',
                            text: data.msg
                        })
                        _newStudentInvoiceTable.reload();
                    }
                    xhr.open("DELETE", $url+unit_id, true);
                    xhr.send();
                }
            })
        },
        generateForm: function() {
            Modal.show({
                type: 'form',
                modalTitle: 'Generate Tagihan Mahasiswa',
                modalSize: 'xl',
                config: {
                    formId: 'generateForm',
                    formActionUrl: _baseURL + '/api/payment/generate/student-invoice/bulk',
                    formType: 'add',
                    data: $("#generateForm").serialize(),
                    isTwoColumn: false,
                    title: 'Konfirmasi Generate Tagihan',
                    textConfirm: 'Generate tagihan hanya akan memproses fakultas atau prodi yang sudah memiliki komponen tagihan',
                    fields: {
                        header: {
                            type: 'custom-field',
                            content: {
                                template: `<div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6">
                                            <h6>Periode Tagihan</h6>
                                            <h1 class="h6 fw-bolder">${header.active}</h1>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <h6>Universitas</h6>
                                            <h1 class="h6 fw-bolder">${header.university}</h1>
                                        </div>
                                    </div>
                                    <hr>
                                </div>`
                            },
                        },
                        tagihan: {
                            type: 'custom-field',
                            content: {
                                template: `
                                <h4 class="fw-bolder mb-0">Konfirmasi Generate Tagihan <small class="fst-italic mb-0">(Centang checkbox untuk memilih)</small></h4>
                                <div class="border border-bottom-0">
                                <ul class="nested-checkbox bg-light mb-0">
                                    <li class="row border-bottom py-1 mx-1">
                                        <div class="col-6 ps-0">
                                            Scope
                                        </div>
                                        <div class="col-3">
                                            Status Generate
                                        </div>
                                        <div class="col-3">
                                            Status Komponen Tagihan
                                        </div>
                                    </li>
                                </ul>
                                <ul class="nested-checkbox px-1 mb-0">
                                    <li id="choice" class="row">
                                        <div class="row border-bottom py-05" style="padding-left: 2%!important">
                                            <div class="col-6" style="padding-left: 0px!important">
                                                <input type="checkbox" name="generate_checkbox[]" class="form-check-input" id="checkbox_header" value="null" /> ${header.university}
                                            </div>
                                            <div class="col-3">
                                            <div class="badge" id="badge_header">Belum Digenerate</div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="from" value="detail">
                                    </li>
                                </ul>
                                </div>
                                `
                            },
                        },
                    },
                    formSubmitLabel: 'Generate Tagihan',
                    formSubmitNote: `
                    <small style="color:#163485">
                        *Pastikan Tagihan yang Ingin Anda Generate Sudah <strong>Sesuai</strong>
                    </small>`,
                    callback: function() {
                        _studentInvoiceDetailTable.reload();
                        feather.replace();
                    }
                },
            });
            var store = [];
            $.get(_baseURL + '/api/payment/generate/student-invoice/choiceall', (data) => {
                console.log(data);
                if (Object.keys(data).length > 0) {
                    var total_student = 0;
                    var total_generate = 0;
                    data.map(item => {
                        var id = item.faculty_id+"_"+item.studyprogram_id;
                        _studentInvoiceTableActions.choiceRow(
                            'choice',
                            'facultyId',
                            'facultyId_'+item.faculty_id,
                            item.faculty_id+'_spId',
                            'Fakultas '+item.study_program.faculty.faculty_name,
                            2)

                        _studentInvoiceTableActions.choiceRow(
                            item.faculty_id+'_spId',
                            item.faculty_id+'_spId',
                            item.faculty_id+'_spId_'+item.studyprogram_id,
                            item.faculty_id+'_'+item.studyprogram_id+'_end',
                            item.study_program.studyprogram_name,
                            3,
                            item.total_student,
                            item.total_generate,
                            id,
                            item.component_filter,
                            'last')

                        // _studentInvoiceTableActions.choiceRow(
                        //     item.msy_id+'_'+item.path_id+'_periodId',
                        //     item.msy_id+'_'+item.path_id+'_periodId',
                        //     item.msy_id+'_'+item.path_id+'_periodId_'+item.period_id,
                        //     item.msy_id+'_'+item.path_id+'_'+item.period_id+'_mltId',
                        //     item.period.period_name)

                        // _studentInvoiceTableActions.choiceRow(
                        //     item.msy_id+'_'+item.path_id+'_'+item.period_id+'_mltId',
                        //     item.msy_id+'_'+item.path_id+'_'+item.period_id+'_mltId',
                        //     item.msy_id+'_'+item.path_id+'_'+item.period_id+'_mltId_'+item.mlt_id,
                        //     item.msy_id+'_'+item.path_id+'_'+item.period_id+'_'+item.mlt_id+'_end',
                        //     item.lecture_type.mlt_name,
                        //     item.total_student,
                        //     item.total_generate,
                        //     id,
                        //     item.component_filter,
                        //     'last')

                        // COUNTING
                        // Study Program
                        let sp = item.faculty_id+'_spId_'+item.studyprogram_id;
                        let student = item.total_student;
                        let generate = item.total_generate;
                        _studentInvoiceTableActions.storeToArray(store,sp,student,generate)

                        // Faculty
                        let faculty = 'facultyId_'+item.faculty_id;
                        _studentInvoiceTableActions.storeToArray(store,faculty,student,generate)

                        // Sum
                        total_student = total_student+student;
                        total_generate = total_generate+generate;
                    });

                    // Badges
                    for (let x of Object.keys(store)) {
                        let student = store[x]['student'];
                        let generate = store[x]['generate'];
                        $('#checkbox_'+x).attr('student',student);
                        $('#checkbox_'+x).attr('generate',generate);
                        _studentInvoiceTableActions.badge(x,student,generate)
                        console.log(store[x]);
                    }

                    // Badges for master root
                    let student = total_student;
                    let generate = total_generate;
                    let x = "header";
                    _studentInvoiceTableActions.badge(x,student,generate)
                }
            });
        },
        choiceRow(tag,grandparent,parent,child,data,padding = 0,total_student = 0,total_generate = 0, value = null,total_component,position= null){
            let status_component = "";
            let status_disabled = "";
            let type = "checkbox";
            let text_color = "";
            if(position === 'last'){
                if(total_component <= 0){
                    status_component = "<div class='badge bg-danger'>Belum Ada Komponen Tagihan</div>";
                    type = "radio"
                    status_disabled = "disabled";
                    text_color = "text-muted";
                }
            }

            if(!$("#choice").find("[id='" + grandparent + "']")[0]){
                $('#'+tag).append(`
                    <ul id="${grandparent}" class="col-12" style="padding-left:calc(var(--bs-gutter-x) * .5)">
                    </ul>
                `);
            }

            if(!$("#choice").find("[id='" + parent + "']")[0]){
                $('#'+grandparent).append(`
                    <li id="${parent}">
                        <div class="row border-bottom py-05">
                            <div class="col-6" style="padding-left: ${padding}%!important;">
                                <input type="${type}" class="form-check-input" name="generate_checkbox[]" id="checkbox_${parent}" student=${total_student} generate=${total_generate} value=${value} ${status_disabled} />
                                <span class="${text_color}"> ${data} </span>
                            </div>
                            <div class="col-3">
                                <div class="badge" id="badge_${parent}">${total_generate} / ${total_student}</div>
                            </div>
                            <div class="col-3">
                                ${status_component}
                            </div>
                        </div>
                        <ul id="${child}" class="col-12">
                        </ul>
                    </li>
                `);
            }

            $('li :checkbox').on('click', function () {
                console.log("hey");
                var $chk = $(this), $li = $chk.closest('li'), $ul, $parent;
                console.log($li);
                if ($li.has('ul')) {
                    $li.find(':checkbox').not(this).prop('checked', this.checked)
                }
                do {
                    $ul = $li.parent();
                    $parent = $ul.siblings(':checkbox');
                    if ($chk.is(':checked')) {
                        $parent.prop('checked', $ul.has(':checkbox:not(:checked)').length == 0)
                    } else {
                        $parent.prop('checked', false)
                    }
                    $chk = $parent;
                    $li = $chk.closest('li');
                } while ($ul.is(':not(.someclass)'));
            });

        },
        badge(x,student,generate){
            if(generate == 0){
                $('#badge_'+x).addClass('bg-danger');
                $('#badge_'+x).html('Belum Digenerate ('+ generate + '/' + student + ')');
            }else if(generate < student){
                $('#badge_'+x).addClass('bg-warning');
                $('#badge_'+x).html('Sebagian Telah Digenerate ('+ generate + '/' + student + ')');
            }else{
                $('#badge_'+x).addClass('bg-success');
                $('#badge_'+x).html('Sudah Digenerate ('+ generate + '/' + student + ')');
            }
        },
        storeToArray(store,key,student,generate){
            if(store[key]){
                store[key] = {'student' : store[key]['student']+student, 'generate' : store[key]['generate']+generate}
            }else{
                store[key] = {'student' : student, 'generate' : generate}
            }
        },
        fetchLogGenerate: function(page){
            $.get(_baseURL + '/api/payment/generate/student-invoice/log-invoice?page='+page, (log) => {
                $('#logList').html(log);
            })
        },
        logGenerate: function(e) {
            Modal.show({
                type: 'detail',
                modalTitle: 'Log Generate',
                modalSize: 'lg',
                config: {
                    fields: {
                        header: {
                            type: 'custom-field',
                            title: '',
                            content: {
                                template: `<div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6">
                                            <h6>Periode Tagihan</h6>
                                            <h1 class="h6 fw-bolder">${header.active}</h1>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <h6>Universitas</h6>
                                            <h1 class="h6 fw-bolder">${header.university}</h1>
                                        </div>
                                    </div>
                                    <hr>
                                </div>`
                            },
                        },
                        tagihan: {
                            type: 'custom-field',
                            title: '',
                            content: {
                                template: `<div id="logList">@include('pages._payment.generate.student-invoice.log')</div>`
                            },
                        },

                    },
                    callback: function() {
                        feather.replace();
                    }
                },
            });
        },
        regenerate: function(faculty_id, studyprogram_id) {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin menggenerate ulang tagihan pada unit ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    // ex: do ajax request
                    $url = _baseURL + "/api/payment/generate/student-invoice/regenerate/";
                    var unit_id = 0;
                    if (faculty_id == 0) {
                        $url += "prodi/"
                        unit_id = studyprogram_id
                    } else {
                        $url += "faculty/"
                        unit_id = faculty_id
                    }

                    var xhr = new XMLHttpRequest();
                    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                    xhr.onload = function() {
                        var data = JSON.parse(this.responseText);
                        Swal.fire({
                            icon: data.status == true ? 'success':'error',
                            text: data.msg
                        })
                        _newStudentInvoiceTable.reload();
                    }
                    xhr.open("DELETE", $url+unit_id, true);
                    xhr.send();
                }
            })
        }
    }

    function filters(){
        dataTable.destroy();
        _studentInvoiceTable.init();
    }
</script>
@endsection
