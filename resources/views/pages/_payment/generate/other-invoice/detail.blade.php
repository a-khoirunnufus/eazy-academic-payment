@extends('tpl.vuexy.master-payment')


@section('page_title', 'Detail Tagihan Mahasiswa Lama')
@section('sidebar-size', 'collapsed')
@section('url_back', route('payment.generate.student-invoice'))

@section('css_section')
    <style>
        .eazy-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }
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

@include('pages._payment.generate._shortcuts', ['active' => null])
{{-- {{ dd($data) }} --}}
<div class="card">
    <div class="card-body">
        <div class="d-flex" style="gap: 2rem">
            <div class="flex-grow-1">
                <span class="text-secondary d-block" style="margin-bottom: 7px">Periode Tagihan</span>
                <h5 class="fw-bolder" id="active"></h5>
            </div>
            <div class="flex-grow-1">
                <span class="text-secondary d-block" style="margin-bottom: 7px">Fakultas</span>
                <h5 class="fw-bolder" id="faculty"></h5>
            </div>
            <div class="flex-grow-1">
                <span class="text-secondary d-block" style="margin-bottom: 7px">Program Studi</span>
                <h5 class="fw-bolder" id="study_program"></h5>
            </div>

            {{-- <div class="flex-grow-1">
                <span class="text-secondary d-block" style="margin-bottom: 7px">Sistem Kuliah</span>
                <h5 class="fw-bolder">Reguler</h5>
            </div>
            <div class="flex-grow-1">
                <span class="text-secondary d-block" style="margin-bottom: 7px">Angkatan</span>
                <h5 class="fw-bolder">Angkatan 2021</h5>
            </div> --}}
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="new-student-invoice-filter">
            <div>
                <label class="form-label">Periode Tagihan</label>
                <select class="form-select" eazy-select2-active id="prr-school-year">
                    @foreach($year as $tahun)
                        <option value="{{ $tahun->msy_code }}" {{ $data["year"] == $tahun->msy_code ? 'selected' : '' }}>Semester {{ ($tahun->msy_semester == 1 ? "Ganjil":"Genap")." ".$tahun->msy_year }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Periode Pendaftaran</label>
                <select class="form-select" eazy-select2-active id="year-filter">
                    <option value="all" selected>Semua Periode Pendaftaran</option>
                    @foreach($year as $tahun)
                        <option value="{{ $tahun->msy_id }}">{{ $tahun->msy_year." ".($tahun->msy_semester%2 == 0 ? "GANJIL":"GENAP") }}</option>
                    @endforeach
                </select>
            </div>
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
                    @foreach($path as $item)
                        <option value="{{ $item->path_id }}">{{ $item->path_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex align-items-end">
                <button class="btn btn-info" onclick="filters()">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="student-invoice-detail-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Nama -<br> NIM</th>
                <th>Tahun Masuk -<br> Jenis Perkuliahan</th>
                <th>Periode Masuk -<br> Jalur Masuk</th>
                <th>Jenis <br> Tagihan</th>
                <th>Total <br> Tagihan</th>
                <th class="text-center">Status <br> Tagihan</th>
                <th>Nama</th>
                <th>Nim</th>
                <th>Fakultas</th>
                <th>Prodi</th>
                <th>Tahun Masuk</th>
                <th>Periode Masuk</th>
                <th>Jalur Masuk</th>
                <th>Jenis Perkuliahan</th>
                <th>Status Mahasiswa</th>
                <th>Total Tagihan</th>
                <th>Status</th>
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
        _studentInvoiceDetailTableAction.header()
        _studentInvoiceDetailTable.init()
        dataTable.columns([6,7,8,9,10,11,12,13,14,15,16]).visible(false)


    })

    const _studentInvoiceDetailTable = {
        ..._datatable,
        init: function() {
            dataTable = this.instance = $('#student-invoice-detail-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/payment/generate/student-invoice/detail?f={!! $data["f"] !!}&sp={!! $data["sp"] !!}',
                    data: {
                        yearFilter: $('#year-filter').val(),
                        path: $('#path-filter').val(),
                        period: $('#period-filter').val(),
                        year: $('#prr-school-year').val()
                    },
                },
                columns: [
                    {
                        name: 'action',
                        data: 'student_id',
                        orderable: false,
                        render: (data, _, row) => {
                            console.log(row);
                            return this.template.rowAction(row)
                        }
                    },
                    {
                        name: 'student',
                        render: (data, _, row) => {
                            let html = "";
                            if(row.student_type_id === 1) {
                                html += '<div class="badge bg-success" style="font-size: inherit">Aktif</div>'
                            } else {
                                html += '<div class="badge bg-danger" style="font-size: inherit">Tidak Aktif</div>'
                            }
                            return `
                                <div>
                                    <span class="text-nowrap fw-bold">${row.fullname} ${html}</span><br>
                                    <small class="text-nowrap text-secondary">${row.student_id}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'year.msy_year',
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <span class="text-nowrap fw-bold">${(row.year) ? ((row.year.msy_semester === 1)? row.year.msy_year + " Genap" : row.year.msy_year + " Ganjil") : "-"}</span><br>
                                    <small class="text-nowrap text-secondary">${(row.lecture_type) ? row.lecture_type.mlt_name : "-"}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'period.period_name',
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <span class="text-nowrap fw-bold">${(row.period) ? row.period.period_name : "-"}</span><br>
                                    <small class="text-nowrap text-secondary">${(row.path) ? row.path.path_name : "-"}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'payment.payment_type',
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <span class="text-nowrap fw-bold">${(row.payment) ? row.payment.payment_type.msct_name : "-"}</span>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'payment.prr_id',
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <a  onclick="_invoiceAction.detail(event,_studentInvoiceDetailTable)" href="javascript:void(0);" class="text-nowrap fw-bold">${(row.payment) ? Rupiah.format(row.payment.prr_total) : "-"}</a><br>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'fullname',
                        data: 'fullname'
                    },
                    {
                        name: 'student_id',
                        data: 'student_id'
                    },
                    {
                        name: 'study_program.faculty.faculty_name',
                        data: 'study_program.faculty.faculty_name',
                        defaultContent: "-",
                    },
                    {
                        name: 'study_program.studyprogram_name',
                        data: 'study_program.studyprogram_name',
                        render: (data, _, row) => {
                            return (row.study_program)? (row.study_program.studyprogram_type +' '+row.study_program.studyprogram_name) : '-';
                        }
                    },
                    {
                        name: 'year.msy_year',
                        render: (data, _, row) => {
                            return (row.year) ? ((row.year.msy_semester === 1)? row.year.msy_year + " Genap" : row.year.msy_year + " Ganjil") : "-";
                        }
                    },
                    {
                        name: 'period.period_name',
                        render: (data, _, row) => {
                            return (row.period)? row.period.period_name : '-';
                        }
                    },
                    {
                        name: 'path.path_name',
                        render: (data, _, row) => {
                            return (row.path)? row.path.path_name : '-';
                        }
                    },
                    {
                        name: 'lecture_type.mlt_name',
                        render: (data, _, row) => {
                            return (row.lecture_type)? row.lecture_type.mlt_name : '-';
                        }
                    },
                    {
                        name: 'student_status',
                        data: 'student_status',
                        render: (data, _, row) => {
                            if(row.student_type_id === 1) {
                                return "Aktif";
                            } else {
                                return "Tidak Aktif"
                            }
                        }
                    },
                    {
                        name: 'payment.prr_id',
                        render: (data, _, row) => {
                            return (row.payment) ? Rupiah.format(row.payment.prr_total) : "-";
                        }
                    },
                    {
                        name: 'payment.prr_id',
                        render: (data, _, row) => {
                            if(row.payment){
                                if(row.payment.prr_status == 'lunas'){
                                    return "Lunas"
                                }else{
                                    return "Belum Lunas"
                                }
                            }else {
                                return ""
                            }
                        }
                    },
                    {
                        name: 'payment.prr_id',
                        render: (data, _, row) => {
                            var status = "";
                            if(row.payment){
                                if(row.payment.prr_status == 'lunas'){
                                    status = '<div class="badge bg-success" style="font-size: inherit">Lunas</div>'
                                }else{
                                    status = '<div class="badge bg-danger" style="font-size: inherit">Belum Lunas</div>'
                                }
                            }
                            return `
                                <div class="d-flex justify-content-center">
                                    ${status}
                                </div>
                            `;
                        }
                    },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"student-invoice-detail-actions d-flex align-items-end">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>' +
                    '<"eazy-table-wrapper" t>' +
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
                                    columns: [6,7,8,9,10,11,12,13,14,15,16]
                                }
                            },
                            {
                                extend: 'csv',
                                text: feather.icons['file-text'].toSvg({class: 'font-small-4 me-50'}) + 'Csv',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [6,7,8,9,10,11,12,13,14,15,16]
                                }
                            },
                            {
                                extend: 'excel',
                                text: feather.icons['file'].toSvg({class: 'font-small-4 me-50'}) + 'Excel',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [6,7,8,9,10,11,12,13,14,15,16]
                                }
                            },
                            {
                                extend: 'pdf',
                                text: feather.icons['clipboard'].toSvg({class: 'font-small-4 me-50'}) + 'Pdf',
                                orientation: 'landscape',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [6,7,8,9,10,11,12,13,14,15,16]
                                }
                            },
                            {
                                extend: 'copy',
                                text: feather.icons['copy'].toSvg({class: 'font-small-4 me-50'}) + 'Copy',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [6,7,8,9,10,11,12,13,14,15,16]
                                }
                            }
                        ],
                    }
                ],
                initComplete: function() {
                    $('.student-invoice-detail-actions').html(`
                        <div style="margin-bottom: 7px">
                            <a onclick="_studentInvoiceDetailTableAction.generateForm()" class="btn btn-info" href="javascript:void(0);">
                                <i data-feather="command"></i> Generate Tagihan Mahasiswa</a>
                            <a onclick="_studentInvoiceDetailTableAction.logActivityModal()" class="btn btn-secondary" href="javascript:void(0);">
                            <i data-feather="book-open"></i> Log Activity</a>
                        </div>
                    `)
                    feather.replace()
                }
            })
        },
        template: {
            rowAction: function(row) {
                let action = `<a onclick="_studentInvoiceDetailTableAction.generate(this,1)" class="dropdown-item" href="javascript:void(0);"><i data-feather="mail"></i>&nbsp;&nbsp;Generate Tagihan</a>`;
                let edit = `<a class="dropdown-item disabled" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit Tagihan</a>`;
                let optional = `<a class="dropdown-item disabled" href="javascript:void(0);"><i data-feather="refresh-cw"></i>&nbsp;&nbsp;Regenerate Tagihan</a>`;
                let deleteAction = '';
                if(row.payment){
                    edit = `<a onclick="_studentInvoiceDetailTableAction.edit(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit Tagihan</a>`;
                    deleteAction = `<a onclick="_studentInvoiceDetailTableAction.delete(event)" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete Tagihan</a>`;
                    if(row.payment.prr_type == 6 || row.payment.prr_type == 5){
                        optional = ``;
                    }else{
                        optional = `<a onclick="_studentInvoiceDetailTableAction.regenerate(event)" class="dropdown-item" href="javascript:void(0);"><i data-feather="refresh-cw"></i>&nbsp;&nbsp;Regenerate Tagihan</a>`;
                    }
                    action = '';
                }
                if(row.registration){
                    if(row.payment){
                        if(row.payment.prr_type == 3 && row.payment.prr_status != 'belum lunas'){
                            action = `<a onclick="_studentInvoiceDetailTableAction.generate(this,3)" class="dropdown-item" href="javascript:void(0);"><i data-feather="mail"></i>&nbsp;&nbsp;Generate Tagihan Mata Kuliah</a>`;
                        }else if(row.payment.prr_type == 2 && row.payment.prr_status != 'belum lunas'){
                            action = `<a onclick="_studentInvoiceDetailTableAction.generate(this,2)" class="dropdown-item" href="javascript:void(0);"><i data-feather="mail"></i>&nbsp;&nbsp;Generate Tagihan SKS</a>`;
                        }
                    }
                }
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a onclick="_invoiceAction.detail(event,_studentInvoiceDetailTable)" class="dropdown-item" href="javascript:void(0);"><i data-feather="eye"></i>&nbsp;&nbsp;Detail Mahasiswa</a>
                            ${edit}
                            ${action}
                            ${deleteAction}
                            ${optional}
                        </div>
                    </div>
                `
            },
            invoiceDetailCell: function(invoiceItems, invoiceTotal) {
                let html = '<div class="d-flex flex-column" style="gap: .5rem">'
                html += `<div class="fw-bold text-nowrap">Total : ${Rupiah.format(invoiceTotal)}</div>`;
                html += '<div class="d-flex flex-row" style="gap: 1rem">';

                const minItemPerColumn = 2;
                const half = invoiceItems.length > minItemPerColumn ? Math.ceil(invoiceItems.length/2) : invoiceItems.length;
                let firstCol = invoiceItems.slice(0, half);
                firstCol = firstCol.map(item => {
                    return `
                        <div class="text-secondary text-nowrap">${item.name} : ${Rupiah.format(item.nominal)}</div>
                    `;
                }).join('');
                html += `<div class="d-flex flex-column" style="gap: .5rem">${firstCol}</div>`;

                if (half < invoiceItems.length) {
                    let secondCol = invoiceItems.slice(half);
                    secondCol = secondCol.map(item => {
                        return `
                            <div class="text-secondary text-nowrap">${item.name} : ${Rupiah.format(item.nominal)}</div>
                        `;
                    }).join('');
                    html += `<div class="d-flex flex-column" style="gap: .5rem">${secondCol}</div>`;
                }

                html += '</div></div>';
                return html;
            }
        }
    }

    const _studentInvoiceDetailTableAction = {
        header: function(){
            $.get(_baseURL + '/api/payment/generate/student-invoice/header?f={!! $data["f"] !!}&sp={!! $data["sp"] !!}&year='+$("#prr-school-year").val(), (d) => {
                $('#active').html();
                $('#faculty').html();
                $('#study_program').html();
                $('#active').html(d.active);
                $('#faculty').html(d.faculty);
                $('#study_program').html(d.study_program);
                header = d;
            })
        },
        generate: function(e,prr_type) {
            let data = _studentInvoiceDetailTable.getRowData(e);
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin generate tagihan mahasiswa '+data.fullname+' ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#356CFF',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Generate',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    let requestData = {
                        student_number: data.student_number,
                        prr_type: prr_type,
                        url: `{{ request()->path() }}`
                    };
                    $.post(_baseURL + '/api/payment/generate/student-invoice/student', requestData, (data) => {
                        console.log(data);
                        data = JSON.parse(data);
                        if(data.success){
                            Swal.fire({
                                icon: 'success',
                                text: data.message,
                            }).then(() => {
                                _studentInvoiceDetailTable.reload()
                            });
                        }else{
                            Swal.fire({
                                icon: 'error',
                                text: data.message,
                            })
                        }

                    }).fail((error) => {
                        _responseHandler.generalFailResponse(error)
                    })
                }
            })
        },
        delete: function(e) {
            const data = _studentInvoiceDetailTable.getRowData(e.currentTarget);
            Swal.fire({
                title: 'Konfirmasi',
                html: `Apakah anda yakin ingin menghapus tagihan mahasiswa ${data.fullname}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    // ex: do ajax request
                    if(data.payment){
                        console.log(data);
                        $.post(_baseURL + '/api/payment/generate/student-invoice/delete-one/' + data.payment.prr_id, {
                            _method: 'DELETE',
                            url: `{{ request()->path() }}`
                        }, function(data){
                            data = JSON.parse(data)
                            if(data.success){
                                Swal.fire({
                                    icon: 'success',
                                    text: data.message,
                                }).then(() => {
                                    _studentInvoiceDetailTable.reload()
                                });
                            }else{
                                Swal.fire({
                                    icon: 'error',
                                    text: data.message,
                                })
                            }
                        }).fail((error) => {
                            Swal.fire({
                                icon: 'error',
                                text: data.message,
                            });
                            _responseHandler.generalFailResponse(error)
                        })
                    }else{
                        Swal.fire({
                            icon: 'error',
                            text: 'Data Tagihan Tidak Ditemukan',
                        })
                    }
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
                                        <div class="col-lg-4 col-md-4">
                                            <h6>Periode Tagihan</h6>
                                            <h1 class="h6 fw-bolder">${header.active}</h1>
                                        </div>
                                        <div class="col-lg-4 col-md-4">
                                            <h6>Fakultas</h6>
                                            <h1 class="h6 fw-bolder">${header.faculty}</h1>
                                        </div>
                                        <div class="col-lg-4 col-md-4">
                                            <h6>Program Studi</h6>
                                            <h1 class="h6 fw-bolder">${header.study_program}</h1>
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
                                        <div class="col-4 ps-0">
                                            Scope
                                        </div>
                                        <div class="col-4">
                                            Status Generate
                                        </div>
                                        <div class="col-4">
                                            Status Komponen Tagihan
                                        </div>
                                    </li>
                                </ul>
                                <ul class="nested-checkbox px-1 mb-0">
                                    <li id="choice" class="row">
                                        <div class="row border-bottom py-05" style="padding-left: 2%!important">
                                            <div class="col-4" style="padding-left: 0px!important">
                                                <input type="checkbox" name="generate_checkbox[]" class="form-check-input" id="checkbox_header" value="null" /><span id="checkbox_header_title" class=""> ${header.study_program} </span>
                                            </div>
                                            <div class="col-4">
                                            <div class="badge" id="badge_header">Belum Digenerate</div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="from" value="detail">
                                        <input type="hidden" name="url" value="{{ request()->path() }}">
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
            $.get(_baseURL + '/api/payment/generate/student-invoice/choice/{!! $data["f"] !!}/{!! $data["sp"] !!}/{!! $data["year"] !!}', (data) => {
                if (Object.keys(data).length > 0) {
                    var total_student = 0;
                    var total_generate = 0;
                    var is_component = [];
                    data.map(item => {
                        if(is_component[item.msy_id] != 0){
                            is_component[item.msy_id] = item.is_component;
                        }
                        if(is_component[item.msy_id+'_'+item.path_id] != 0){
                        is_component[item.msy_id+'_'+item.path_id] = item.is_component;
                        }
                        if(is_component[item.msy_id+'_'+item.path_id+'_'+item.period_id] != 0){
                            is_component[item.msy_id+'_'+item.path_id+'_'+item.period_id] = item.is_component;
                        }
                    });

                    data.map(item => {
                        var id = item.studyprogram_id+"_"+item.msy_id+"_"+item.path_id+"_"+item.period_id+"_"+item.mlt_id;
                        _studentInvoiceDetailTableAction.choiceRow(
                            'choice',
                            'msyId',
                            'msyId_'+item.msy_id,
                            item.msy_id+'_pathId',
                            'Tahun '+item.year.msy_year,
                            is_component[item.msy_id],
                            2)

                        _studentInvoiceDetailTableAction.choiceRow(
                            item.msy_id+'_pathId',
                            item.msy_id+'_pathId',
                            item.msy_id+'_pathId_'+item.path_id,
                            item.msy_id+'_'+item.path_id+'_periodId',
                            item.path.path_name,
                            is_component[item.msy_id+'_'+item.path_id],
                            3)

                        _studentInvoiceDetailTableAction.choiceRow(
                            item.msy_id+'_'+item.path_id+'_periodId',
                            item.msy_id+'_'+item.path_id+'_periodId',
                            item.msy_id+'_'+item.path_id+'_periodId_'+item.period_id,
                            item.msy_id+'_'+item.path_id+'_'+item.period_id+'_mltId',
                            item.period.period_name,
                            is_component[item.msy_id+'_'+item.path_id+'_'+item.period_id],
                            4)

                        _studentInvoiceDetailTableAction.choiceRow(
                            item.msy_id+'_'+item.path_id+'_'+item.period_id+'_mltId',
                            item.msy_id+'_'+item.path_id+'_'+item.period_id+'_mltId',
                            item.msy_id+'_'+item.path_id+'_'+item.period_id+'_mltId_'+item.mlt_id,
                            item.msy_id+'_'+item.path_id+'_'+item.period_id+'_'+item.mlt_id+'_end',
                            item.lecture_type.mlt_name,
                            null,
                            5,
                            item.total_student,
                            item.total_generate,
                            id,
                            item.component_filter,
                            'last')

                        // COUNTING
                        // Period
                        let period = item.msy_id+'_'+item.path_id+'_periodId_'+item.period_id;
                        let student = item.total_student;
                        let generate = item.total_generate;
                        _studentInvoiceDetailTableAction.storeToArray(store,period,student,generate)

                        // Path
                        let path = item.msy_id+'_pathId_'+item.path_id;
                        _studentInvoiceDetailTableAction.storeToArray(store,path,student,generate)

                        // Year
                        let year = 'msyId_'+item.msy_id
                        _studentInvoiceDetailTableAction.storeToArray(store,year,student,generate)

                        // Mlt
                        let mlt = item.msy_id+'_'+item.path_id+'_'+item.period_id+'_mltId_'+item.mlt_id;
                        _studentInvoiceDetailTableAction.storeToArray(store,mlt,student,generate)

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
                        _studentInvoiceDetailTableAction.badge(x,student,generate)
                    }
                    let root_is_component = null;
                    for (let x of Object.keys(is_component)) {
                        if(root_is_component != 0){
                            root_is_component = is_component[x];
                        }
                    }
                    if(root_is_component != 1){
                        $('#checkbox_header').attr("type", 'radio');
                        $("#checkbox_header").attr("disabled", true);
                        $("#checkbox_header_title").addClass("text-muted");
                    }
                    // Badges for master root
                    let student = total_student;
                    let generate = total_generate;
                    let x = "header";
                    _studentInvoiceDetailTableAction.badge(x,student,generate)
                }
            });
        },
        choiceRow(tag,grandparent,parent,child,data,is_component = null,padding = 0,total_student = 0,total_generate = 0, value = null,total_component,position= null){
            let status_component = "";
            let status_disabled = "";
            let text_color = "";
            let type = "checkbox"
            if(position === 'last'){
                if(total_component <= 0){
                    status_component = "<div class='badge bg-danger'>Belum Ada Komponen Tagihan</div>";
                    type = "radio"
                    status_disabled = "disabled";
                    text_color = "text-muted";
                }
            }

            if(is_component != null){
                if(is_component == 0){
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
                            <div class="col-4" style="padding-left: ${padding}%!important;">
                                <input type="${type}" class="form-check-input" name="generate_checkbox[]" id="checkbox_${parent}" student=${total_student} generate=${total_generate} value=${value} ${status_disabled} />
                                <span class="${text_color}"> ${data} </span>
                            </div>
                            <div class="col-4">
                                <div class="badge" id="badge_${parent}">${total_generate} / ${total_student}</div>
                            </div>
                            <div class="col-4">
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
        logActivityModal: function() {
            title = `Log Tagihan Mahasiswa Lama`;
            url = `{{ request()->path() }}`;
            logActivity(title,url);
        },
        regenerate: function(e) {
            const data = _studentInvoiceDetailTable.getRowData(e.currentTarget);
            Swal.fire({
                title: 'Konfirmasi',
                html: `Apakah anda yakin ingin menggenerate ulang tagihan mahasiswa ${data.fullname}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Regenerate',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    // ex: do ajax request
                    $.post(_baseURL + '/api/payment/generate/student-invoice/regenerate/student/' + data.payment.prr_id, {
                        _method: 'DELETE',
                        url: `{{ request()->path() }}`,
                    }, function(data){
                        data = JSON.parse(data)
                        if(data.success){
                            Swal.fire({
                                icon: 'success',
                                text: data.message,
                            }).then(() => {
                                _studentInvoiceDetailTable.reload()
                            });
                        }else{
                            Swal.fire({
                                icon: 'error',
                                text: data.message,
                            })
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

        edit: function(e) {
            let data = _studentInvoiceDetailTable.getRowData(e);
            console.log(data);
            Modal.show({
                type: 'form',
                modalTitle: `Pengaturan Tagihan ${data.fullname ? (data.fullname) : (data.student.fullname)}`,
                modalSize: 'lg',
                config: {
                    formId: 'paymentRateForm',
                    formActionUrl: _baseURL + '/api/payment/generate/student-invoice/component/update',
                    formType: 'add',
                    data: $("#paymentRateForm").serialize(),
                    isTwoColumn: false,
                    fields: {
                        header: {
                            type: 'custom-field',
                            title: 'Data Mahasiswa',
                            content: {
                                template: `<div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3">
                                            <h6>Nama Lengkap</h6>
                                            <h1 class="h6 fw-bolder">${data.fullname ? (data.fullname) : (data.student.fullname)}</h1>
                                        </div>
                                        <div class="col-lg-3 col-md-3">
                                            <h6>NIM</h6>
                                            <h1 class="h6 fw-bolder">${data.student_id ? (data.student_id) : (data.student.student_id)}</h1>
                                        </div>
                                        <div class="col-lg-3 col-md-3">
                                            <h6>No Handphone</h6>
                                            <h1 class="h6 fw-bolder">${data.phone_number ? (data.phone_number) : (data.student.phone_number)}</h1>
                                        </div>
                                        <div class="col-lg-3 col-md-3">
                                            <h6>Status Pembayaran</h6>
                                            <h1 class="h6 fw-bolder" id="statusPembayaran"></h1>
                                        </div>
                                    </div>
                                    <hr>
                                </div>`
                            },
                        },
                        input_fields: {
                            type: 'custom-field',
                            content: {
                                template: `
                                <div class="d-flex flex-wrap align-items-center justify-content-between mb-1" style="gap:10px">
                                    <h4 class="fw-bolder mb-0">Komponen Tagihan</h4>
                                    <div>
                                        <button type="button"
                                            class="btn btn-info text-white edit-component waves-effect waves-float waves-light"
                                            onclick="_studentInvoiceDetailTableAction.PaymentRateNewField(${data.payment.prr_id},0)"> <i class="bx bx-plus m-auto"></i> Tambah Komponen
                                        </button>
                                    </div>
                                    <input type="hidden" value="{{ request()->path() }}" name="url">
                                    <input type="hidden" value="${data.fullname ? (data.fullname) : (data.student.fullname)} - ${data.student_id ? (data.student_id) : (data.student.student_id)}" name="title">
                                </div>
                                <div id="PaymentRateInput">
                                </div>
                                `
                            },
                        },
                    },
                    formSubmitLabel: 'Simpan',
                    formSubmitNote: `
                    <small style="color:#163485">
                        *Pastikan Data Yang Anda Masukkan <strong>Lengkap</strong> dan <strong>Benar</strong>
                    </small>`,
                    callback: function() {
                        // ex: reload table
                        _studentInvoiceDetailTable.reload()
                    },
                },
            });
            // Status
            let source = data.payment;
            let status = "";
            if(source){
                if(source.prr_status == 'lunas'){
                    status = '<div class="badge bg-success" style="font-size: inherit">Lunas</div>'
                }else if(source.prr_status == 'kredit'){
                    status = '<div class="badge bg-warning" style="font-size: inherit">Kredit</div>'
                }else{
                    status = '<div class="badge bg-danger" style="font-size: inherit">Belum Lunas</div>'
                }
                if (Object.keys(source.payment_detail).length > 0) {
                    source.payment_detail.map(item => {
                        if(item.type == 'component'){
                            _studentInvoiceDetailTableAction.PaymentRateInputField(item)
                        }
                    })
                }
            }
            $("#statusPembayaran").append(status);

        },
        PaymentRateInputField: function(item) {
            $('#PaymentRateInput').append(`
                <div class="d-flex flex-wrap align-items-center mb-1 PaymentRateInputField" style="gap:10px"
                    id="comp-order-preview-0">
                    <input type="hidden" name="prr_id[]" value="${item.prr_id}">
                    <input type="hidden" name="prrd_id[]" value="${item.prrd_id}">
                    <div class="flex-fill">
                        <label class="form-label">Nama Komponen</label>
                        <input type="text" class="form-control" name="prrd_component[]" value="${item.prrd_component}"
                            placeholder="Nama Komponen">
                    </div>
                    <div class="flex-fill">
                        <label class="form-label">Harga Komponen Biaya</label>
                        <input type="text" class="form-control comp_price" name="prrd_amount[]" value="${item.prrd_amount}"
                            placeholder="Tarif Komponen">
                    </div>
                    <div class="d-flex align-content-end">
                        <div class="">
                            <label class="form-label" style="opacity: 0">#</label>
                            <a class="btn btn-danger text-white btn-sm d-flex" style="height: 36px"
                            onclick="_studentInvoiceDetailTableAction.paymentRateDeleteField(this,${item.prrd_id})"> <i class="bx bx-trash m-auto"></i> </a>
                        </div>
                    </div>
                </div>
            `);
        },
        PaymentRateNewField: function(prr_id,is_admission) {
            $('#PaymentRateInput').append(`
                <div class="d-flex flex-wrap align-items-center mb-1 PaymentRateInputField" style="gap:10px"
                    id="comp-order-preview-0">
                    <input type="hidden" name="prr_id[]" value="${prr_id}">
                    <input type="hidden" name="prrd_id[]" value="0">
                    <div class="flex-fill">
                        <label class="form-label">Nama Komponen</label>
                        <select class="form-select select2" eazy-select2-active name="prrd_component[]" id="component${prr_id}" value="">
                        </select>
                    </div>
                    <div class="flex-fill">
                        <label class="form-label">Harga Komponen Biaya</label>
                        <input type="text" class="form-control comp_price" name="prrd_amount[]" value=""
                            placeholder="Tarif Komponen">
                    </div>
                    <div class="d-flex align-content-end">
                        <div class="">
                            <label class="form-label" style="opacity: 0">#</label>
                            <a class="btn btn-danger text-white btn-sm d-flex" style="height: 36px"
                            onclick="_studentInvoiceDetailTableAction.paymentRateDeleteField(this,0)"> <i class="bx bx-trash m-auto"></i> </a>
                        </div>
                    </div>
                </div>
            `);
            $.get(_baseURL + '/api/payment/settings/paymentrates/component/'+is_admission, (data) => {
                JSON.parse(data).map(item => {
                    $("#component" + prr_id).append(`
                        <option value="` + item['msc_name'] + `">` + item['msc_name'] + `</option>
                    `)
                })
                selectRefresh();
            })

        },
        paymentRateDeleteField: function(e, id) {
            if (id === 0) {
                $(e).parents('.PaymentRateInputField').get(0).remove();
            } else {
                _studentInvoiceDetailTableAction.deleteComponent(e, id);
            }
        },
        deleteComponent: function(e, id) {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin menghapus komponen tagihan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(_baseURL + '/api/payment/generate/student-invoice/component/delete/' + id, {
                        _method: 'DELETE',
                        url: `{{ request()->path() }}`
                    }, function(data) {
                        data = JSON.parse(data)
                        Swal.fire({
                            icon: 'success',
                            text: data.message,
                        }).then(() => {
                            $(e).parents('.PaymentRateInputField').get(0).remove();
                        });
                    }).fail((error) => {
                        Swal.fire({
                            icon: 'error',
                            text: data.text,
                        });
                        _responseHandler.generalFailResponse(error)
                    })
                }
            })
        },
    }

    function filters(){
        dataTable.destroy();
        _studentInvoiceDetailTable.init();
        _studentInvoiceDetailTableAction.header();
    }
</script>
@include('pages._payment.generate.student-invoice.invoice');
@endsection