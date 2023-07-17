@extends('layouts.static_master')


@section('page_title', 'Dispensasi Pembayaran')
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

<!-- Modal Import Komponen Tagihan -->
<div class="modal fade" id="importComponentModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-fullscreen modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h4 class="modal-title fw-bolder" id="importComponentModalLabel">Import Komponen Tagihan</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-2">
                <div class="d-flex flex-column" style="gap: 1.5rem">
                    <div>
                        <button onclick="downloadTemplate()" class="btn btn-link px-0"><i data-feather="download"></i>&nbsp;&nbsp;Download Template</button>
                    </div>
                    <div>
                        <form id="form-upload-file">
                            <div class="form-group">
                                <label class="form-label">File Import</label>
                                <div class="input-group" style="width: 500px">
                                    <input name="file" type="file" class="form-control">
                                    <a onclick="_uploadFileForm.submit()" class="btn btn-primary" type="button">
                                        <i data-feather="upload"></i>&nbsp;&nbsp;Upload File Import
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <hr style="margin: 2rem 0" />
                <div>
                    <h4>Preview Import</h4>
                    <small class="d-flex align-items-center">
                        <i data-feather="info" style="margin-right: .5rem"></i>
                        <span>Preview akan muncul setelah file diupload.<span>
                    </small>
                    <small class="d-flex align-items-center">
                        <i data-feather="info" style="margin-right: .5rem"></i>
                        <span>Tekan tombol Import Komponen untuk memproses Data(hanya Data Valid) untuk diimport.<span>
                    </small>
                </div>
                <div class="mt-2">
                    <div class="my-1">
                        <span class="d-inline-block me-1">Data Valid: <span id="valid-import-data-count"></span></span>
                        <span class="d-inline-block">Data Tidak Valid: <span id="invalid-import-data-count"></span></span>
                    </div>
                    <table id="table-import-preview" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Komponen</th>
                                <th>Ditagihkan Kepada</th>
                                <th>Tipe Komponen</th>
                                <th>Status Aktif</th>
                                <th>Deskripsi Komponen</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <div class="d-flex justify-content-end">
                    <button class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Batal</button>
                    <button onclick="importComponent()" class="btn btn-primary">Import Komponen</button>
                </div>
            </div>
        </div>
    </div>
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
        _importPreviewTable.init();
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
                            return this.template.rowAction(data)
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
                    // {name: 'mcs_phone', data: 'mcs_phone'},
                    // {name: 'mcs_email', data: 'mcs_email'},
                    // {name: 'mcs_reason', data: 'mcs_reason'},
                    // {name: 'mcs_method', data: 'mcs_method'},
                    // {
                    //     name: 'mcs_proof',
                    //     data: 'mcs_proof',
                    //     searchable: false,
                    //     render: (data, _, row) => {
                    //         let link = '{{ url("file","student-credit") }}/'+row.mcs_id;
                    //         return '<a href="'+link+'" target="_blank">'+row.mcs_proof_filename+'</a>';
                    //     }
                    // },
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
                            let status = "Tidak Disetujui";
                            let bg = "bg-danger";
                            if(row.mcs_status === 1){
                                status = "Disetujui";
                                bg = "bg-success";
                            }else if(row.mcs_status === 2){
                                status = "Sedang Diproses";
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
            rowAction: function(id) {
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a onclick="_creditSubmissionTableActions.detail(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="eye"></i>&nbsp;&nbsp;Detail</a>
                            <a onclick="_creditSubmissionTableActions.process(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="loader"></i>&nbsp;&nbsp;Proses Pengajuan</a>
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
            Modal.show({
                type: 'detail',
                modalTitle: 'Detail Pengajuan Kredit Pembayaran',
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
                                                <td>Metode Pembayaran</td>
                                                <td>:&nbsp;&nbsp;${data.mcs_method}</td>
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
                    formActionUrl: _baseURL + '/api/payment/settings/component/store',
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
                                                <td>Metode Pembayaran</td>
                                                <td>:&nbsp;&nbsp;${data.mcs_method}</td>
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
                    isDecline: true,
                    declineFunction: _creditSubmissionTableActions.decline(),
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
                        <input type="number" class="form-control" name="cse_percentage[]" total="${total}" value="${percentage.csd_percentage}"
                            placeholder="Persentase Pembayaran" id="percentage${count}">
                    </div>
                    <div class="flex-fill">
                        <label class="form-label">Nominal</label>
                        <input type="number" class="form-control" name="cse_amount[]" total="${total}" value="${amount_percentage}"
                            placeholder="Total Pembayaran" id="amount${count}">
                    </div>
                    <div class="flex-fill">
                        <label class="form-label">Tenggat Pembayaran</label>
                        <input type="date" class="form-control" name="cse_deadline[]" value="${deadline}"
                            placeholder="Tenggat Pembayaran" required>
                        <input type="hidden" name="cse_cs_id[]" value="${cs_id}">
                        <input type="hidden" name="cse_csd_id[]" value="${percentage.csd_id}">
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
        decline: function(){
            // let data = _creditSubmissionTable.getRowData(e);
            Swal.fire({
                title: 'Submit your Github username',
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Look up',
                showLoaderOnConfirm: true,
                preConfirm: (login) => {
                    return fetch(`//api.github.com/users/${login}`)
                    .then(response => {
                        if (!response.ok) {
                        throw new Error(response.statusText)
                        }
                        return response.json()
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                        `Request failed: ${error}`
                        )
                    })
                },
                allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                    title: `${result.value.login}'s avatar`,
                    imageUrl: result.value.avatar_url
                    })
                }
            })
        },
    }

    function downloadTemplate() {
        window.location.href = _baseURL+'/api/download?storage=local&type=excel-template&filename=import-invoice-component-template-1683776326.xlsx';
    }

    const _uploadFileForm = {
        clearInput: () => {
            $('#form-upload-file input[name="file"]').val('');
        },
        submit: () => {
            _toastr.info('Sedang memproses file, mungkin membutukan beberapa waktu.', 'Memproses');

            let formData = new FormData(document.getElementById('form-upload-file'));

            $.ajax({
                url: _baseURL+'/api/payment/settings/component/upload-file-for-import',
                type: 'POST',
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    _uploadFileForm.clearInput();
                    if(data.success){
                        const import_id = data.payload.import_id ?? 0;
                        setLocalStorageWithExpiry('eazy-academic-payment.settings.component.import_id', import_id, 24*60*60*1000);

                        _toastr.success(data.message, 'Selesai');
                        _importPreviewTable.reload();
                    } else {
                        _toastr.error(data.message, 'Failed')
                    }
                },
                error: function(jqXHR) {
                    _responseHandler.formFailResponse(jqXHR);
                }
            });
        }
    }

    const _importPreviewTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#table-import-preview').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/payment/settings/component/dt-import-preview',
                    data: function(d) {
                        d.custom_payload = {
                            import_id: getLocalStorageWithExpiry('eazy-academic-payment.settings.component.import_id') ?? 0,
                        };
                    },
                    dataSrc: function (e){
                        const data = e.data;
                        let validCount = 0;
                        let invalidCount = 0;
                        data.forEach(item => {
                            if (item.status == "invalid") {
                                invalidCount++;
                            } else if (item.status == "valid") {
                                validCount++;
                            }
                        });
                        // console.log(validCount,invalidCount);
                        $('#valid-import-data-count').text(validCount);
                        $('#invalid-import-data-count').text(invalidCount);
                        return e.data;
                    },
                },
                columns: [
                    {
                        name: 'component_name',
                        data: 'component_name',
                        render: (data) => {
                            return this.template.defaultCell(data);
                        }
                    },
                    {
                        name: 'payer_type',
                        render: (data, _, row) => {
                            let payer_type_arr = [];
                            row.is_participant == 1 && payer_type_arr.push('Mahasiswa Pendaftar');
                            row.is_new_student == 1 && payer_type_arr.push('Mahasiswa Baru');
                            row.is_student == 1 && payer_type_arr.push('Mahasiswa Lama');
                            return this.template.defaultCell(payer_type_arr.join(', '));
                        }
                    },
                    {
                        name: 'component_type',
                        data: 'component_type',
                        render: (data) => {
                            return this.template.defaultCell(data);
                        }
                    },
                    {
                        name: 'component_active_status',
                        data: 'component_active_status',
                        render: (data) => {
                            return this.template.defaultCell(data == 1 ? 'Aktif' : 'Tidak Aktif');
                        }
                    },
                    {
                        name: 'component_description',
                        data: 'component_description',
                        render: (data) => {
                            return this.template.defaultCell(data ?? '', {nowrap: false});
                        }
                    },
                    {
                        name: 'status',
                        data: 'status',
                        render: (data) => {
                            return this.template.badgeCell(
                                data == 'valid' ? 'Valid' : 'Invalid',
                                data == 'valid' ? 'success' : 'danger'
                            );
                        }
                    },
                    {
                        name: 'notes',
                        data: 'notes',
                        render: (data, _, row) => {
                            if (row.status == 'valid') {
                                return '';
                            } else {
                                return `
                                    <div class="d-flex justify-content-center align-items-center">
                                        <button onclick="openImportError(event)" class="btn btn-secondary btn-sm btn-icon round"><i data-feather="info"></i></button>
                                    </div>
                                `;
                            }
                        }
                    },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"eazy-table-wrapper" t>' +
                    '<"d-flex justify-content-between row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                buttons: [],
                initComplete: function() {}
            })
        },
        template: {
            ..._datatableTemplates,
        }
    }

    function openImportError(e) {
        let notes = _importPreviewTable.getRowData(e.currentTarget).notes;
        // remove ';' char from start and end of string
        notes = notes.replace(/^\;+|\;+$/g, '');

        Modal.show({
            type: 'detail',
            modalTitle: 'Detail Error',
            modalSize: 'lg',
            config: {
                fields: {
                    errors: {
                        title: 'Daftar Error',
                        content: {
                            template: `
                                <ul>
                                    ${notes.split(';').map((item) => {
                                        return `<li>${item}</li>`;
                                    }).join('')}
                                </ul>
                            `,
                        },
                    },
                },
                callback: function() {
                    feather.replace();
                }
            },
        });
    }

    var ImportComponentModal = new bootstrap.Modal(document.getElementById('importComponentModal'));

    function importComponent() {
        _toastr.info('Sedang mengimport data, mungkin membutukan beberapa waktu.', 'Mengimport');

        $.ajax({
            url: _baseURL+'/api/payment/settings/component/import',
            type: 'POST',
            data: {
                import_id: getLocalStorageWithExpiry('eazy-academic-payment.settings.component.import_id') ?? 0,
            },
            success: function(data) {
                if(data.success){
                    localStorage.removeItem('eazy-academic-payment.settings.component.import_id');

                    ImportComponentModal.hide();
                    _toastr.success(data.message, 'Success');
                    _importPreviewTable.reload();
                    _creditSubmissionTable.reload();
                } else {
                    _toastr.error(data.message, 'Failed')
                }
            },
            error: function(jqXHR) {
                _responseHandler.generalFailResponse(jqXHR);
            }
        });
    }
</script>
@include('pages._payment.generate.student-invoice.invoice');
@endsection
