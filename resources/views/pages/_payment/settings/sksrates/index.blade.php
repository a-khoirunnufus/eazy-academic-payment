@extends('tpl.vuexy.master-payment')


@section('page_title', 'Pengaturan Tarif Per SKS')
@section('sidebar-size', 'collapsed')
@section('url_back', url('setting/rates'))

@section('css_section')
<style>
    .rates-per-course-filter {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        grid-gap: 1rem;
    }

    #myModalContainer {
        position: fixed;
        left: 0;
        top: 0;
        z-index: 1100;
        width: 100%;
        height: 100%;
        background-color: rgba(190, 192, 196, 0.3);
    }

    .mdl-container {
        border-radius: 20px;
        opacity: 1;
    }

    .cls-btn {
        position: absolute;
        right: -20px;
        top: -18px;
    }

    /* #MyFile {
        content: "";
        display: block;
        width: 100%;
        padding: 50px 0px 55px 200px;
        margin-top: 15px;
        border-style: dashed;
        border-width: 2px;
        text-align: center;
        background-color: #dfe1e6;
    }

    #MyFile::-webkit-file-upload-button {
        visibility: hidden;
    } */

    .preview {
        width: 100%;
        height: calc(100% - 210px);
        overflow: scroll;
    }

    .actions {
        position: absolute;
        bottom: 15px;
        right: 15px;
    }
</style>
@endsection

@section('content')

@include('pages._payment.settings._shortcuts', ['active' => 'sks-rates'])
<!-- <div id="myModalContainer" class="d-flex justify-content-center align-items-center">
    <div class="w-75 h-75 bg-white position-relative mdl-container p-1">
        <button type="button" class="cls-btn btn bg-danger rounded text-white" onclick="closeImport()">X</button>
        <input type="file" name="" id="MyFile" onchange="myImport('preview')">
        <div class="preview mt-1">
            <table id="prevTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th rowspan="2" class="text-center align-middle">Program Study</th>
                        <th rowspan="2" class="text-center align-middle">Mata Kuliah</th>
                        <th colspan="3" class="text-center align-middle">Tarif per Tingkat</th>
                    </tr>
                    <tr>
                        <th>Tngkat</th>
                        <th>Tarif</th>
                        <th>Paket</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="actions d-flex justify-content-end">
            <button class="btn bg-primary text-white mx-1" type="button" onclick="downloadTemplate()">Download</button>
            <button class="btn bg-success text-white" type="button" onclick="myImport('import')">Import</button>
        </div>
    </div>
</div> -->

<div class="card">
    <div class="card-body">
        <div class="rates-per-course-filter">
            <div>
                <label class="form-label">Fakultas</label>
                <select class="form-select select2" name="faculty-filter" onchange="setProdiFilter(this.value)">
                    <option value="#ALL" selected>Semua Fakultas</option>
                    @foreach($faculty as $item)
                    <option value="{{ $item->faculty_id }}">{{ $item->faculty_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Program Studi</label>
                <select class="form-select select2" name="studyprogram-filter">
                    <option value="#ALL" selected>Semua Program Studi</option>
                    <!-- @foreach($studyProgram as $item)
                    <option value="{{ $item->studyprogram_id }}">{{ $item->studyprogram_type." ".$item->studyprogram_name }}</option>
                    @endforeach -->
                </select>
            </div>
            <div class="d-flex align-items-end">
                <button class="btn btn-info" onclick="_ratesPerSKSTable.reload()">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="rates-per-sks-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Jurusan</th>
                <th>Tingkat</th>
                <th>Tarif SKS Normal</th>
                <th>Tarif SKS Praktikum</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
{{--
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-fullscreen modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom d-flex flex-column align-items-start">
                <div class="d-flex justify-content-between w-100 mb-1">
                    <h4 class="modal-title fw-bolder" id="importModalLabel">Import Tarif Matakuliah</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-2">
                <div class="d-flex flex-column" style="gap: 1.5rem">
                    <div>
                        <button onclick="downloadTemplate()" class="btn btn-link px-0"><i data-feather="download"></i>&nbsp;&nbsp;Download Template</button>
                        <small class="d-flex align-items-center">
                            <i data-feather="info" style="margin-right: .5rem"></i>
                            <span>File template khusus untuk tarif matakuliah<span>
                        </small>
                        <small class="d-flex align-items-center">
                            <i data-feather="info" style="margin-right: .5rem"></i>
                            <span>informasi untuk program studi dan matakuliah terdapat pada sheet info<span>
                        </small>
                    </div>
                    <div>
                        <form id="form-upload-file">
                            <div class="form-group">
                                <label class="form-label">File Import</label>
                                <div class="input-group" style="width: 500px">
                                    <!-- <input name="file" type="file" class="form-control"> -->
                                    <input type="file" name="file" id="MyFile" class="form-control">
                                    <a onclick="myImport('preview')" class="btn btn-info" type="button">
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
                    <table id="prevTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th rowspan="2" class="text-center align-middle">Program Study</th>
                                <th rowspan="2" class="text-center align-middle">Mata Kuliah</th>
                                <th colspan="3" class="text-center align-middle">Tarif per Tingkat</th>
                            </tr>
                            <tr>
                                <th>Tngkat</th>
                                <th>Tarif</th>
                                <th>Paket</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <div class="d-flex justify-content-end">
                    <button class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Batal</button>
                    <button onclick="myImport('import')" class="btn btn-info">Import Komponen</button>
                </div>
            </div>
        </div>
    </div>
</div> --}}
@endsection


@section('js_section')
<script>
    var dataCopy = null;
    var dataRecent = null;
    var isCopied = false;
    var searchInput = '#ALL';
    var dt;

    $('#myModalContainer').addClass("d-none").removeClass("d-flex");
    $(document).on('keydown', function(e) {
        if (e.keyCode === 27) {
            $('#myModalContainer').addClass("d-none").removeClass("d-flex");
        }
    });

    // $('#prevTable').DataTable();

    $(function() {
        _ratesPerSKSTable.init()
    })

    const _Filter = {
        init: function() {
            _options.load({
                optionUrl: _baseURL + '/api/payment/settings/sksrates/studyprogram',
                nameField: 'msr_studyprogram_id',
                idData: 'studyprogram_id',
                nameData: 'studyprogram_name'
            });
        }
    }

    const _ratesPerSKSTable = {
        ..._datatable,
        init: function() {
            dt = this.instance = $('#rates-per-sks-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL + '/api/payment/settings/sksrates/index',
                    data: function(d) {
                        d.custom_filter = {
                            'studyprogram_id': $('select[name="studyprogram-filter"]').val(),
                            'faculty_id': $('select[name="faculty-filter"]').val(),
                            'filtering': searchInput,
                        };
                    }
                },
                columns: [
                    {
                        name: 'action',
                        data: 'msr_id',
                        orderable: false,
                        render: (data, _, row) => {
                            return this.template.rowAction(row)
                        }
                    },
                    {
                        name: 'study_program.studyprogram_name',
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <span class="fw-bold">${row.study_program.studyprogram_name}</span><br>
                                    <small class="text-secondary">${row.study_program.studyprogram_type}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'msr_tingkat',
                        data: 'msr_tingkat',
                        render: (data) => {
                            return 'Tingkat ' + data;
                        }
                    },
                    {
                        name: 'msr_rate',
                        data: 'msr_rate',
                        render: (data) => {
                            return Rupiah.format(data);
                        }
                    },
                    {
                        name: 'msr_rate_practicum',
                        data: 'msr_rate_practicum',
                        render: (data) => {
                            return Rupiah.format(data);
                        }
                    },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom: '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"rate-per-course-actions d-flex align-items-end">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" <"myFilter">lB> >' +
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
                                orientation: 'landscape',
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
                    $('.rate-per-course-actions').html(`
                        <div style="margin-bottom: 7px">
                            <button onclick="_ratesPerSKSTableActions.add()" class="btn btn-info me-1">
                                <span style="vertical-align: middle">
                                    <i data-feather="plus" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Tambah Tarif Per SKS
                                </span>
                            </button>
                        </div>
                    `)
                    $('.myFilter').html(`
                        <div id="rates-per-sks-table_filter" class="dataTables_filter">
                            <label><input type="text" class="form-control" placeholder="Cari Data" id="searchInput" onkeypress="keyListener(event)"></label>
                        </div>
                    `)
                    searchInput = '#ALL';
                    feather.replace()
                }
            })
        },
        template: {
            rowAction: function(row) {
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a onclick="_ratesPerSKSTableActions.edit(${row.study_program.studyprogram_id})" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="_ratesPerSKSTableActions.copy(this)" class="dropdown-item" href="javascript:void(0);"><i data-feather="clipboard"></i>&nbsp;&nbsp;Salin</a>
                            <a onclick="_ratesPerSKSTableActions.delete(this)" class="dropdown-item"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const _ratesPerSKSTableActions = {
        tableRef: _ratesPerSKSTable,
        SKSRateInputField: function(id = 0, rate = 0, rate_practicum = 0, tingkat = null) {
            let rand = (Math.floor(Math.random()*500));
            let tarifSKS = "tarif_sks_"+rand;
            let tarifPracticum = "tarif_practicum_"+rand;
            $('#SKSRateInput').append(`
                <div class="d-flex flex-wrap align-items-center mb-1 SKSRateInputField" style="gap:10px"
                    id="comp-order-preview-0">
                    <input type="hidden" name="id[]" value="${id}">
                    <div class="flex-fill">
                        <label class="form-label">Tingkat</label>
                        <select class="form-select select2" eazy-select2-active name="msr_tingkat[]" id="tingkat${id}" value="">
                            <option value="1">Tingkat 1</option>
                            <option value="2">Tingkat 2</option>
                            <option value="3">Tingkat 3</option>
                            <option value="4">Tingkat 4</option>
                            <option value="5">Tingkat > 4</option>
                        </select>
                    </div>
                    <div class="flex-fill">
                        <label class="form-label">Tarif SKS Normal</label>
                        <input type="text" class="form-control comp_price ${tarifSKS}" value="${rate}"
                            placeholder="Tarif SKS Normal">
                    </div>
                    <div class="flex-fill">
                        <label class="form-label">Tarif SKS Praktikum</label>
                        <input type="text" class="form-control comp_price ${tarifPracticum}" value="${rate_practicum}"
                            placeholder="Tarif Praktikum">
                    </div>
                    <div class="d-flex align-content-end">
                        <div class="">
                            <label class="form-label" style="opacity: 0">#</label>
                            <a class="btn btn-danger text-white btn-sm d-flex" style="height: 36px"
                            onclick="_ratesPerSKSTableActions.courseRateDeleteField(this,${id})"> <i class="bx bx-trash m-auto"></i> </a>
                        </div>
                    </div>
                </div>
            `);
            _numberCurrencyFormat.load(tarifSKS,'msr_rate',1);
            _numberCurrencyFormat.load(tarifPracticum,'msr_rate_practicum',1);
            if (tingkat) {
                $("#tingkat" + id + "").val(tingkat);
                $("#tingkat" + id + "").trigger('change');
            }
        },
        courseRateDeleteField: function(e, id) {
            if (id === 0) {
                $(e).parents('.SKSRateInputField').get(0).remove();
            } else {
                _ratesPerSKSTableActions.delete(e, id);
            }
        },

        add: function() {
            let paste = `<button type="button" class="btn btn-success" disabled>Paste</button>`;
            if(isCopied){
                paste = `<button type="button" class="btn btn-success" onclick="_ratesPerSKSTableActions.paste('component')">Paste</button>`;
            }
            Modal.show({
                type: 'form',
                modalTitle: 'Tambah Tarif Per SKS',
                modalSize: 'lg',
                config: {
                    formId: 'form-add-rates-per-course',
                    formActionUrl: _baseURL + '/api/payment/settings/sksrates/store',
                    formType: 'add',
                    isTwoColumn: false,
                    fields: {
                        selections: {
                            type: 'custom-field',
                            content: {
                                template: `<div class="mb-2">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12">
                                            <label class="form-label">Program Studi</label>
                                            <select class="form-select select2" eazy-select2-active id="programStudy" name="msr_studyprogram_id">
                                            </select>
                                        </div>
                                    </div>
                                </div>`
                            },
                        },
                        input_fields: {
                            type: 'custom-field',
                            content: {
                                template: `
                                <div class="d-flex flex-wrap align-items-center justify-content-between mb-1" style="gap:10px">
                                    <h4 class="fw-bolder mb-0">Tarif Per Tingkat</h4>
                                    <div>
                                    <button type="button"
                                        class="btn btn-info text-white edit-component waves-effect waves-float waves-light"
                                        onclick="_ratesPerSKSTableActions.SKSRateInputField()"> <i class="bx bx-plus m-auto"></i> Tambah Tingkat
                                    </button>
                                    ${paste}
                                    </div>

                                </div>
                                <div id="SKSRateInput">
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
                        _ratesPerSKSTable.reload()
                    },
                },
            });
            $('#programStudy').empty().trigger("change");
            $('#SKSRateInput').empty();
            _options.load({
                optionUrl: _baseURL + '/api/payment/settings/sksrates/studyprogram',
                nameField: 'msr_studyprogram_id',
                idData: 'studyprogram_id',
                nameData: 'studyprogram_name'
            });

            $("#programStudy").change(function() {
                $('#SKSRateInput').empty();
                _ratesPerSKSTableActions.tarif("#programStudy");
            })
        },
        tarif: function(e) {
            studyProgram = $(e).val();
            if (studyProgram) {
                $.post(_baseURL + '/api/payment/settings/sksrates/getbystudyprogramid/' + studyProgram, {
                    _method: 'GET'
                }, function(data) {
                    data = JSON.parse(data)
                    $('#SKSRateInput').empty();
                    if (Object.keys(data).length > 0) {
                        dataRecent = data;
                        data.map(item => {
                            _ratesPerSKSTableActions.SKSRateInputField(item.msr_id, item.msr_rate, item.msr_rate_practicum, item.msr_tingkat)
                        })
                    }
                }).fail((error) => {
                    Swal.fire({
                        icon: 'error',
                        text: data.text,
                    });
                })
            }
        },
        edit: function(spId) {

            Modal.show({
                type: 'form',
                modalTitle: 'Edit Tarif SKS',
                modalSize: 'lg',
                config: {
                    formId: 'form-edit-sks-per-course',
                    formActionUrl: _baseURL + '/api/payment/settings/sksrates/store',
                    formType: 'edit',
                    isTwoColumn: false,
                    fields: {
                        selections: {
                            type: 'custom-field',
                            content: {
                                template: `<div class="mb-2">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12">
                                            <label class="form-label">Program Studi</label>
                                            <select class="form-select select2" eazy-select2-active id="programStudy" name="msr_studyprogram_id" disabled>
                                            </select>
                                        </div>
                                        <input type="hidden" name="msr_studyprogram_id" value="${spId}" />
                                    </div>
                                </div>`
                            },
                        },
                        input_fields: {
                            type: 'custom-field',
                            content: {
                                template: `
                                <div class="d-flex flex-wrap align-items-center justify-content-between mb-1" style="gap:10px">
                                    <h4 class="fw-bolder mb-0">Tarif Per Tingkat</h4>
                                    <button type="button"
                                        class="btn btn-info text-white edit-component waves-effect waves-float waves-light"
                                        onclick="_ratesPerSKSTableActions.SKSRateInputField()"> <i class="bx bx-plus m-auto"></i> Tambah Tingkat
                                    </button>
                                </div>
                                <div id="SKSRateInput">
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
                        _ratesPerSKSTable.reload()
                    },
                },
            });

            $('#programStudy').empty().trigger("change");
            $('#SKSRateInput').empty();
            _options.load({
                optionUrl: _baseURL + '/api/payment/settings/sksrates/studyprogram',
                nameField: 'msr_studyprogram_id',
                idData: 'studyprogram_id',
                nameData: 'studyprogram_name',
                val: spId
            });

            $("#programStudy").change(function() {
                $('#SKSRateInput').empty();
                studyProgram = $(this).val();
                if (studyProgram) {
                    $.post(_baseURL + '/api/payment/settings/sksrates/getbystudyprogramid/' + studyProgram, {
                        _method: 'GET'
                    }, function(data) {
                        data = JSON.parse(data)
                        $('#SKSRateInput').empty();
                        if (Object.keys(data).length > 0) {
                            data.map(item => {
                                _ratesPerSKSTableActions.SKSRateInputField(item.msr_id, item.msr_rate, item.msr_rate_practicum, item.msr_tingkat)
                            })
                        }
                    }).fail((error) => {
                        Swal.fire({
                            icon: 'error',
                            text: data.text,
                        });
                    })
                }
            })
        },
        delete: function(e, id = 0) {
            let data = _ratesPerSKSTable.getRowData(e);
            let msrId = 0;
            if (id == 0) {
                msrId = data.msr_id;
            } else {
                msrId = id;
            }
            console.log(msrId);
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin menghapus tarif SKS ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(_baseURL + '/api/payment/settings/sksrates/delete/' + msrId, {
                        _method: 'DELETE'
                    }, function(data) {
                        data = JSON.parse(data)
                        Swal.fire({
                            icon: 'success',
                            text: data.message,
                        }).then(() => {
                            _ratesPerSKSTable.reload();
                            if (id != 0) {
                                _ratesPerSKSTableActions.tarif("#programStudy");
                            }
                        });
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
        copy: function(e) {
            dataCopy = _ratesPerSKSTable.getRowData(e);
            isCopied = true;
        },
        paste: function(type) {
            let pasted = false;
            if(dataCopy){
                if(dataRecent){
                    $('#SKSRateInput').empty();
                    dataRecent.map(item => {
                        if(item.msr_tingkat == dataCopy.msr_tingkat){
                            _ratesPerSKSTableActions.SKSRateInputField(item.msr_id, dataCopy.msr_rate, dataCopy.msr_rate_practicum, item.msr_tingkat)
                            pasted = true;
                        }else{
                            _ratesPerSKSTableActions.SKSRateInputField(item.msr_id, item.msr_rate, item.msr_rate_practicum, item.msr_tingkat)
                        }
                    })
                }
            }
            console.log(pasted)
            if(!pasted){
                _ratesPerSKSTableActions.SKSRateInputField(0, dataCopy.msr_rate, dataCopy.msr_rate_practicum, dataCopy.msr_tingkat)
            }
        }
    }

    function setProdiFilter(id) {
        $($('select[name="studyprogram-filter"]')[0]).html('');
        $($('select[name="studyprogram-filter"]')[0]).append(`
            <option value="#ALL" selected>Semua Program Studi</option>
        `)
        var xhr = new XMLHttpRequest();
        xhr.onload = function() {
            var data = JSON.parse(this.responseText);
            data.map(item => {
                $($('select[name="studyprogram-filter"]')[0]).append(`
                    <option value="${item.studyprogram_id}">${item.studyprogram_type} ${item.studyprogram_name}</option>
                `)
            })
        }
        xhr.open("GET", _baseURL + "/api/payment/settings/sksrates/studyprogram/" + id, true);
        xhr.send();
    }

    function keyListener(event){
        if(event.which == 13){ //ENTER
            searchInput = $('#searchInput').val();
            $('#searchInput').val('');
            // dt.ajax.reload();
            _ratesPerSKSTable.reload();
        }
    }
</script>
@endsection
