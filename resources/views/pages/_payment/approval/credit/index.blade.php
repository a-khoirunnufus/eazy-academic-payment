@extends('layouts.static_master')


@section('page_title', 'Pengajuan Cicilan Pembayaran')
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

@include('pages._payment.approval._shortcuts', ['active' => 'credit'])

<div class="card">
    <table id="credit-submission-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Tahun Akademik</th>
                <th>Nama</th>
                <th>Fakultas <br>Prodi</th>
                <th>Total <br>Tagihan</th>
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
    // enabling multiple modal open
    $(document).on('show.bs.modal', '.modal', function() {
        const zIndex = 1040 + 10 * $('.modal:visible').length;
        $(this).css('z-index', zIndex);
        setTimeout(() => $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack'));
    });

    $(function(){
        _creditSubmissionTable.init();
    })

    const _creditSubmissionTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#credit-submission-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/payment/approval-credit/index',
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
                        name: 'mcs_school_year',
                        data: 'mcs_school_year',
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
                                    <a  onclick="_invoiceAction.detail(event,_creditSubmissionTable,'lazy')" href="javascript:void(0);" class="text-nowrap fw-bold">${(row.payment) ? Rupiah.format(row.payment.prr_total) : "-"}</a><br>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'mcs_status',
                        data: 'mcs_status',
                        searchable: false,
                        render: (data, _, row) => {
                            let status = "Ditolak";
                            let bg = "bg-danger";
                            if(row.mcs_status === 1){
                                status = "Disetujui";
                                bg = "bg-success";
                            }else if(row.mcs_status === 2){
                                status = "Menunggu Diproses";
                                bg = "bg-warning";
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
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"submission-credit-action d-flex align-items-end">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                    '>t' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function() {
                    $('.submission-credit-action').html(`
                        <div style="margin-bottom: 7px">
                            
                        </div>
                    `)
                    feather.replace()
                }
            })
            this.implementSearchDelay()
        },
        template: {
            rowAction: function(row) {
                let process = `<a onclick="_creditSubmissionTableActions.process(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="loader"></i>&nbsp;&nbsp;Proses Pengajuan</a>`;
                if(row.mcs_status != 2){
                    process = ``;
                }
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a onclick="_creditSubmissionTableActions.detail(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="eye"></i>&nbsp;&nbsp;Detail Pengajuan</a>
                            <a onclick="_invoiceAction.detail(event,_creditSubmissionTable,'lazy')" class="dropdown-item" href="javascript:void(0);"><i data-feather="eye"></i>&nbsp;&nbsp;Detail Tagihan</a>
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

    const _creditSubmissionTableActions = {
        detail: function(e) {
            const data = _creditSubmissionTable.getRowData(e);
            let decline_reason = ``;
            if(data.mcs_status == 0){
                decline_reason = `<tr>
                    <td class="fw-bolder">Alasan Penolakan</td>
                    <td class="fw-bolder">:&nbsp;&nbsp;${data.mcs_decline_reason}</td>
                </tr>`
            }
            Modal.show({
                type: 'detail',
                modalTitle: 'Detail Pengajuan Cicilan Pembayaran',
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
                                                <td>:&nbsp;&nbsp;${data.mcs_phone}</td>
                                            </tr>
                                            <tr>
                                                <td>Email</td>
                                                <td>:&nbsp;&nbsp;${data.mcs_email}</td>
                                            </tr>
                                            <tr>
                                                <td>Alasan</td>
                                                <td>:&nbsp;&nbsp;${data.mcs_reason}</td>
                                            </tr>
                                            <tr>
                                                <td>Bukti Pendukung</td>
                                                <td>:&nbsp;&nbsp;<a href="${'{{ url("file","student-credit") }}/'+data.mcs_id}" target="_blank">${data.mcs_proof_filename}</a></td>
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
        process: function(e) {
            let data = _creditSubmissionTable.getRowData(e);
            Modal.show({
                type: 'form',
                modalTitle: 'Proses Pengajuan',
                modalSize: 'md',
                config: {
                    formId: 'form-process-credit-submission',
                    formActionUrl: _baseURL + '/api/payment/approval-credit/store',
                    formType: 'add',
                    rowId: data.mcs_id,
                    fields: {
                        detail_credit_submission: {
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
                                                <td>:&nbsp;&nbsp;${data.mcs_phone}</td>
                                            </tr>
                                            <tr>
                                                <td>Email</td>
                                                <td>:&nbsp;&nbsp;${data.mcs_email}</td>
                                            </tr>
                                            <tr>
                                                <td>Alasan</td>
                                                <td>:&nbsp;&nbsp;${data.mcs_reason}</td>
                                            </tr>
                                            <tr>
                                                <td>Bukti Pendukung</td>
                                                <td>:&nbsp;&nbsp;<a href="${'{{ url("file","student-credit") }}/'+data.mcs_id}" target="_blank">${data.mcs_proof_filename}</a></td>
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
                                            <label class="form-label">Skema Cicilan</label>
                                            <select class="form-select select2" eazy-select2-active id="csId" name="cs_id">
                                                <option value="">Pilih Skema</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div id="schemaDeadline">
                                    </div>
                                </div>`
                            },
                        },
                    },
                    formSubmitLabel: 'Setujui',
                    isSecondButtonCustom: true,
                    SecondButtonCustom: `<a href="javascript:void(0);" onclick="_creditSubmissionTableActions.decline(${data.mcs_id})" class="btn btn-danger me-1">Tolak</a>`,
                    callback: function() {
                        _creditSubmissionTable.reload()
                    },
                },
            });
            $.get(_baseURL + '/api/payment/settings/paymentrates/schema', (d) => {
                JSON.parse(d).map(item => {
                    $("#csId").append(`
                        <option value="` + item['cs_id'] + `">` + item['cs_name'] + `</option>
                    `);
                });
                selectRefresh();
            });
            $("#csId").change(function() {
                $("#schemaDeadline").empty();
                cs_id = $(this).val();
                let count = 0;
                $.get(_baseURL + '/api/payment/settings/paymentrates/getdetailschemabyid/'+cs_id, (d) => {
                    JSON.parse(d).map(item => {
                        console.log(item);
                        _creditSubmissionTableActions.SchemaDeadlineField(item.cs_id, item.credit_schema.cs_name, item, data.payment.prr_total, count);
                        count++;
                    });
                })
            })
        },
        amountOfPercentage: function(total, csd_percentage) {
            if (total){
                return total * (csd_percentage / 100);
            }else{
                return 0;
            }
        },
        percentageOfAmount: function(total, total_credit) {
            if (total){
                return (total_credit / total) * 100;
            }else{
                return 0;
            }
        },
        SchemaDeadlineField: function(cs_id = 0, name = null, percentage = null, total = null,count) {
            let html = "";
            if (percentage != null) {
                let deadline = "";
                if (percentage.credit_schema_deadline) {
                    deadline = percentage.credit_schema_deadline.cse_deadline;
                } 
                let amount_percentage = _creditSubmissionTableActions.amountOfPercentage(total,percentage.csd_percentage);
                html += `
                <div class="d-flex flex-wrap align-items-center mb-1 SchemaDeadlineField" style="gap:10px"
                    id="comp-order-preview-0">
                    <div class="flex-fill">
                        <label class="form-label">Persentase Pembayaran</label>
                        <input type="number" class="form-control" name="" total="${total}" value="${percentage.csd_percentage}"
                            placeholder="Persentase Pembayaran" id="percentage${count}" required>
                    </div>
                    <div class="flex-fill">
                        <label class="form-label">Nominal</label>
                        <input type="number" class="form-control" name="cse_amount[]" total="${total}" value="${amount_percentage}"
                            placeholder="Total Pembayaran" id="amount${count}" required>
                    </div>
                    <div class="flex-fill">
                        <label class="form-label">Tenggat Pembayaran</label>
                        <input type="date" class="form-control" name="cse_deadline[]" value="${deadline}"
                            placeholder="Tenggat Pembayaran" required>
                        <input type="hidden" class="form-control" name="cse_order[]" value="${percentage.csd_order}">
                    </div>
                </div>
                `
                percentageVal = parseInt(percentageVal)+parseInt(percentage.csd_percentage);
                amountVal = parseInt(amountVal)+parseInt(amount_percentage);
            }
            $('#schemaDeadline').append(`
                <div id="schemaDeadlineTag${cs_id}">
                    <h5 class="fw-bolder mb-1 mt-2">Pengaturan Skema ${name}</h5>
                    ${html}
                </div>
            `);
            $(`#percentage${count}`).on("input", function() {
                let csd_percentage = $(this).val();
                let total = $(this).attr("total");
                let amount_percentage = _creditSubmissionTableActions.amountOfPercentage(total,csd_percentage);
                $(`#amount${count}`).val(amount_percentage);
            });
            $(`#amount${count}`).on("input", function() {
                let total_credit = $(this).val();
                let total = $(this).attr("total");
                let percentage = _creditSubmissionTableActions.percentageOfAmount(total,total_credit);
                $(`#percentage${count}`).val(percentage);
            });
        },
        decline: function(mcs_id){
            // let data = _creditSubmissionTable.getRowData(e);
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
                showLoaderOnConfirm: true,
                allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                if (result.isConfirmed) {
                    // console.log(result);
                    $.post(_baseURL + '/api/payment/approval-credit/decline', {
                            mcs_id: mcs_id,
                            mcs_decline_reason: result.value
                        }, function(data){
                        data = JSON.parse(data)
                        if(data.success){
                            Swal.fire({
                                icon: 'success',
                                text: data.message,
                            }).then(() => {
                                _creditSubmissionTable.reload()
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
@include('pages._payment.generate.student-invoice.invoice');
@endsection
