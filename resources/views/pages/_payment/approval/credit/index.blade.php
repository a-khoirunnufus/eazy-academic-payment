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
                <th>Komponen <br>Tagihan</th>
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
                        name: 'msy_id',
                        data: 'msy_id',
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
                    {name: 'mcs_phone', data: 'mcs_phone'},
                    {name: 'mcs_email', data: 'mcs_email'},
                    {name: 'mcs_reason', data: 'mcs_reason'},
                    {name: 'mcs_method', data: 'mcs_method'},
                    {
                        name: 'mcs_proof',
                        data: 'mcs_proof',
                        searchable: false,
                        render: (data, _, row) => {
                            let link = '{{ url("file","student-credit") }}/'+row.mcs_id;
                            return '<a href="'+link+'" target="_blank">'+row.mcs_proof_filename+'</a>';
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
                            <button onclick="_creditTableActions.add()" class="btn btn-primary">
                                <span style="vertical-align: middle">
                                    <i data-feather="plus" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Pengajuan Cicilan
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
            rowAction: function(id) {
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a onclick="_creditSubmissionTableActions.edit(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="_creditSubmissionTableActions.delete(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
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
        add: function() {
            Modal.show({
                type: 'form',
                modalTitle: 'Tambah Komponen Tagihan',
                modalSize: 'md',
                config: {
                    formId: 'form-add-invoice-component',
                    formActionUrl: _baseURL + '/api/payment/settings/component/store',
                    formType: 'add',
                    fields: {
                        invoice_component_code: {
                            title: 'Kode Komponen Tagihan',
                            content: {
                                template:
                                    `<input
                                        type="text"
                                        name="msc_name"
                                        class="form-control"
                                    >`,
                            },
                        },
                        invoice_component_name: {
                            title: 'Nama Komponen Tagihan',
                            content: {
                                template:
                                    `<input
                                        type="text"
                                        name="msc_description"
                                        class="form-control"
                                    >`,
                            },
                        },
                        component_type: {
                            title: 'Jenis Komponen Tagihan',
                            content: {
                                template:
                                    `<select name="msct_id" class="form-control select2">
                                        <option value="">Pilih Jenis Komponen</option>
                                    </select>`,
                            },
                        },
                        subjects: {
                            title: 'Tersedia Bagi',
                            type: 'checkbox',
                            content: {
                                template: `
                                    <table class="table table-bordered">
                                        <tr class="bg-light">
                                            <th class="text-center">Mahasiswa Lama</th>
                                            <th class="text-center">Mahasiswa Baru</th>
                                            <th class="text-center">Pendaftar</th>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><input type="checkbox" name="msc_is_student" class="form-check-input" /></td>
                                            <td class="text-center"><input type="checkbox" name="msc_is_new_student" class="form-check-input" /></td>
                                            <td class="text-center"><input type="checkbox" name="msc_is_participant" class="form-check-input" /></td>
                                        </tr>
                                    </table>
                                `
                            },
                        },
                    },
                    formSubmitLabel: 'Tambah Komponen',
                    callback: function(e) {
                        _creditSubmissionTable.reload()
                    },
                },
            });
            _options.load({
                optionUrl: _baseURL + '/api/payment/settings/component-type',
                nameField: 'msct_id',
                idData: 'msct_id',
                nameData: 'msct_name'
            });
        },
        edit: function(e) {
            let data = _creditSubmissionTable.getRowData(e);
            Modal.show({
                type: 'form',
                modalTitle: 'Edit Komponen Tagihan',
                modalSize: 'md',
                config: {
                    formId: 'form-edit-transaction-group',
                    formActionUrl: _baseURL + '/api/payment/settings/component/store',
                    formType: 'edit',
                    rowId: data.msc_id,
                    fields: {
                        invoice_component_code: {
                            title: 'Kode Komponen Tagihan',
                            content: {
                                template:
                                    `<input
                                        type="text"
                                        name="msc_name"
                                        class="form-control"
                                        value=""
                                    >`,
                            },
                        },
                        invoice_component_name: {
                            title: 'Nama Komponen Tagihan',
                            content: {
                                template:
                                    `<input
                                        type="text"
                                        name="msc_description"
                                        class="form-control"
                                        value=""
                                    >`,
                            },
                        },
                        component_type: {
                            title: 'Jenis Komponen Tagihan',
                            content: {
                                template:
                                    `<select name="msct_id" id="msct_id" class="form-control select2">
                                        <option value="">Pilih Jenis Komponen</option>
                                    </select>`,
                            },
                        },
                        subjects: {
                            title: null,
                            type: 'checkbox',
                            content: {
                                template: `
                                    <table class="table table-bordered">
                                        <tr class="bg-light">
                                            <th class="text-center">Mahasiswa Lama</th>
                                            <th class="text-center">Mahasiswa Baru</th>
                                            <th class="text-center">Pendaftar</th>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><input type="checkbox" name="msc_is_student" class="form-check-input" /></td>
                                            <td class="text-center"><input type="checkbox" name="msc_is_new_student" class="form-check-input" /></td>
                                            <td class="text-center"><input type="checkbox" name="msc_is_participant" class="form-check-input" /></td>
                                        </tr>
                                    </table>
                                `
                            },
                        },
                    },
                    formSubmitLabel: 'Edit Komponen',
                    callback: function() {
                        _creditSubmissionTable.reload()
                    },
                },
            });
            _componentForm.clearData()
            _componentForm.setData(data)
            _creditSubmissionTable.selected = data
        },
        delete: function(e) {
            let data = _creditSubmissionTable.getRowData(e);
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
                    $.post(_baseURL + '/api/payment/settings/component/delete/' + data.msc_id, {
                        _method: 'DELETE'
                    }, function(data){
                        data = JSON.parse(data)
                        Swal.fire({
                            icon: 'success',
                            text: data.text,
                        }).then(() => {
                            _creditSubmissionTable.reload()
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
@endsection
