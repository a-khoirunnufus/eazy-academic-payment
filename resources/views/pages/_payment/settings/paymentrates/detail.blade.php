@extends('tpl.vuexy.master-payment')


@section('page_title', 'Pengaturan Tagihan, Tarif, dan Pembayaran')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
    <style>
        .eazy-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .rates-filter {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            grid-gap: 1rem;
        }
        .custom-list {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }
        .custom-list-item {
            padding: .5rem 0;
            white-space: nowrap;
        }
    </style>
@endsection

@section('content')

@include('pages._payment.settings._shortcuts', ['active' => 'payment-rates'])
<input type="file" name="import" id="myFiles" style="display:none;" onchange="myImport()">
<div class="card">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-lg-1">
                <div class="fw-bold text-feeder bg-light-primary text-white avatar" style="height:50px; width:50px;">
                    <i class="bx bx-book-alt m-auto" style="font-size: 30px"></i>
                </div>
            </div>
            <div class="col-lg-2">
                <label class="form-label">Tahun Ajaran</label>
                <h4 id="head-year">{{ $data->tahun }}</h4>
            </div>
            <div class="col-lg-2">
                <label class="form-label">Nama Jalur</label>
                <h4 id="head-path">{{ $data->jalur }}</h4>
            </div>
            <div class="col-lg-5">
                <label class="form-label">Nama Periode</label>
                <h4 id="head-period">{{ $data->periode }}</h4>
            </div>
            <div class="col-lg-2">
                <a href="{{ route('payment.settings.payment-rates')}}" class="btn btn-info waves-effect waves-float waves-light w-100"><i class='bx bx-arrow-back m-auto'></i> Kembali</a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="rates-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Program Studi</th>
                <th>Jenis Perkuliahan</th>
                <th>Komponen Tagihan</th>
                <th>Cicilan</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Modal Import Komponen Tagihan -->
<div class="modal fade" id="importInvoiceComponentModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-fullscreen modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom d-flex flex-column align-items-start">
                <div class="d-flex justify-content-between w-100 mb-1">
                    <h4 class="modal-title fw-bolder" id="importInvoiceComponentModalLabel">Import Komponen Tagihan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="d-flex w-100" style="gap: 2rem">
                    <div>
                        Periode: Periode Mei
                    </div>
                    <div>
                        Jalur: Rapor
                    </div>
                    <div>
                        Tahun Ajaran: 2023/2034
                    </div>
                </div>
            </div>
            <div class="modal-body p-2">
                <div class="d-flex flex-column" style="gap: 1.5rem">
                    <div>
                        <button onclick="downloadTemplate()" class="btn btn-link px-0"><i data-feather="download"></i>&nbsp;&nbsp;Download Template</button>
                        <small class="d-flex align-items-center">
                            <i data-feather="info" style="margin-right: .5rem"></i>
                            <span>File template khusus untuk Periode, Jalur dan Tahun Ajaran yang dipilih.<span>
                        </small>
                    </div>
                    <div>
                        <form id="form-upload-file">
                            <input type="hidden" name="period_path_id" value="{{ request()->route('id') }}" />
                            <div class="form-group">
                                <label class="form-label">File Import</label>
                                <div class="input-group" style="width: 500px">
                                    <input name="file" type="file" class="form-control">
                                    <a onclick="_uploadFileForm.submit()" class="btn btn-info" type="button">
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
                                <th>Program Studi</th>
                                <th>Jenis Perkuliahan</th>
                                <th class="text-nowrap">Komponen Tagihan<br>(Nama | Nominal)</th>
                                <th class="text-nowrap">Cicilan<br>(Persentase | Tenggat)</th>
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
                    <button onclick="importSettingFee()" class="btn btn-info">Import Komponen</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('js_section')
<script>
    const periodPathId = "{{ request()->route('id') }}";

    var dataCopy = null;
    var skema_cicilan = [];
    let isImporting = false;

    // enabling multiple modal open
    $(document).on('show.bs.modal', '.modal', function() {
        const zIndex = 1040 + 10 * $('.modal:visible').length;
        $(this).css('z-index', zIndex);
        setTimeout(() => $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack'));
    });

    $(function(){
        _ratesTable.init();
        _importPreviewTable.init();

        select2Replace();
    })

    const _ratesTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#rates-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL + '/api/payment/settings/paymentrates/detail/{!! $id !!}',
                },
                columns: [{
                        name: 'action',
                        data: 'ppm.ppm_id',
                        orderable: false,
                        render: (data, _, row) => {
                            // console.log(row)
                            return this.template.rowAction(data)
                        }
                    },
                    {
                        name: 'ppm.mma_lt_id',
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <span class="fw-bold">${row.ppm.major_lecture_type.study_program.studyprogram_name}</span><br>
                                    <small class="text-secondary">${row.ppm.major_lecture_type.study_program.studyprogram_type}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'ppm.major_lecture_type.lecture_type.mlt_name',
                        data: 'ppm.major_lecture_type.lecture_type.mlt_name',
                        render: (data) => {
                            return `<span class="fw-bold">${data}</span>`;
                        }
                    },
                    {
                        name: 'component',
                        render: (data, _, row) => {
                            let html = '<div><ul>';
                            if (Object.keys(row.component).length > 0) {
                                row.component.map(item => {
                                    let component = "";
                                    if (item.component) {
                                        component = `<li class="fw-bold">${item.component.msc_name}: ${Rupiah.format(item.cd_fee)}</li>`;
                                    }
                                    html += component;
                                })
                            }
                            html += '</ul></div>';
                            return html;
                        }
                    },
                    {
                        name: 'ppm.credit.cspp_id',
                        render: (data, _, row) => {
                            let html = '<div><ul>';
                            if (Object.keys(row.ppm.credit).length > 0) {
                                row.ppm.credit.map(item => {
                                    let credit = "";
                                    if (item.credit_schema) {
                                        credit = `<li class="fw-bold">${item.credit_schema.cs_name}</li>`;
                                    }
                                    html += credit;
                                })
                            }
                            html += '</ul></div>';
                            return html;
                        }
                    },

                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom: '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
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
                            <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#importInvoiceComponentModal">
                                <span style="vertical-align: middle">
                                    <i data-feather="file-text" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Import Komponen Tagihan
                                </span>
                            </button>
                        </div>
                    `);
                    feather.replace();
                }
            })
        },
        template: {
            rowAction: function(id) {
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a onclick="_ratesTableActions.edit(this)" class="dropdown-item"><i data-feather="dollar-sign"></i> Komponen Tagihan</a>
                            <a onclick="_ratesTableActions.copy(this)" class="dropdown-item"><i data-feather="clipboard"></i> Salin Data</a>
                        </div>
                    </div>
                `
            }
        }
    }
    var count = -9999;
    const _ratesTableActions = {
        tableRef: _ratesTable,
        PaymentRateInputField: function(id = 0, rate = 0, component = null, increment = 0, mma_id = 0, period_id = 0, path_id = 0, msy_id = 0, mlt_id = 0, ppm_id = 0) {
            let isId = 0;
            if (increment === 1) {
                isId = count++;
            } else {
                isId = id;
            }
            $('#PaymentRateInput').append(`
                <div class="d-flex flex-wrap align-items-center mb-1 PaymentRateInputField" style="gap:10px"
                    id="comp-order-preview-0">
                    <input type="hidden" name="cd_id[]" value="${id}">
                    <input type="hidden" name="mma_id[]" value="${mma_id}">
                    <input type="hidden" name="period_id[]" value="${period_id}">
                    <input type="hidden" name="path_id[]" value="${path_id}">
                    <input type="hidden" name="msy_id[]" value="${msy_id}">
                    <input type="hidden" name="mlt_id[]" value="${mlt_id}">
                    <input type="hidden" name="ppm_id[]" value="${ppm_id}">
                    <div class="flex-fill">
                        <label class="form-label">Nama Komponen</label>
                        <select class="form-select select2" eazy-select2-active name="msc_id[]" id="component${isId}" value="">
                        </select>
                    </div>
                    <div class="flex-fill">
                        <label class="form-label">Harga Komponen Biaya</label>
                        <input type="text" class="form-control comp_price" name="cd_fee[]" value="${rate}"
                            placeholder="Tarif Komponen">
                    </div>
                    <div class="d-flex align-content-end">
                        <div class="">
                            <label class="form-label" style="opacity: 0">#</label>
                            <a class="btn btn-danger text-white btn-sm d-flex" style="height: 36px"
                            onclick="_ratesTableActions.paymentRateDeleteField(this,${id})"> <i class="bx bx-trash m-auto"></i> </a>
                        </div>
                    </div>
                </div>
            `);
            $.get(_baseURL + '/api/payment/settings/paymentrates/component', (data) => {
                JSON.parse(data).map(item => {
                    $("#component" + isId).append(`
                        <option value="` + item['msc_id'] + `">` + item['msc_name'] + `</option>
                    `)
                    // var count_elm = $(".PaymentRateInputField").length;
                    // $(
                    //     $(
                    //         $(
                    //             $(".PaymentRateInputField")[count_elm - 1]
                    //         ).children(".flex-fill")[0]
                    //     ).children("select")[0]
                    // ).append(`
                    //     <option value="` + item['msc_id'] + `">` + item['msc_name'] + `</option>
                    // `)
                })
                component ? $("#component" + isId).val(component) : ""
                $("#component" + isId).trigger('change')
                selectRefresh()
            })

        },
        paymentRateDeleteField: function(e, id) {
            if (id === 0) {
                $(e).parents('.PaymentRateInputField').get(0).remove();
            } else {
                _ratesTableActions.deleteComponent(e, id);
            }
        },

        edit: function(e) {
            let data = _ratesTable.getRowData(e);
            // console.log(data);
            Modal.show({
                type: 'form',
                modalTitle: 'Pengaturan Komponen Tagihan',
                modalSize: 'lg',
                config: {
                    formId: 'paymentRateForm',
                    formActionUrl: _baseURL + '/api/payment/settings/paymentrates/update',
                    formType: 'add',
                    data: $("#paymentRateForm").serialize(),
                    isTwoColumn: false,
                    fields: {
                        selections: {
                            type: 'custom-field',
                            content: {
                                template: `<div>
                                    <div class="d-flex flex-wrap justify-content-between align-items-center" style="gap:10px">
                                        <h1 class="h4 fw-bolder mb-0">Lengkapi Data Di Bawah!</h1>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-6">
                                            <h6>Tahun</h6>
                                            <h1 class="h6 fw-bolder" id="tahun-name">{!! $data->tahun !!}</h1>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <h6>Periode</h6>
                                            <h1 class="h6 fw-bolder" id="period-name">{!! $data->periode !!}</h1>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <h6>Jalur</h6>
                                            <h1 class="h6 fw-bolder" id="path-name">{!! $data->jalur !!}</h1>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <h6>Program Studi</h6>
                                            <h1 class="h6 fw-bolder" id="prodi-name">${data.ppm.major_lecture_type.study_program.studyprogram_type} - ${data.ppm.major_lecture_type.study_program.studyprogram_name} - ${data.ppm.major_lecture_type.lecture_type.mlt_name}</h1>
                                        </div>
                                        <input type="hidden" name="main_ppm_id" value="${data.ppm.ppm_id}">
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
                                        onclick="_ratesTableActions.paste(${data.ppm.major_lecture_type.mma_id}, ${data.ppm.period_path.period_id}, ${data.ppm.period_path.path_id}, ${data.ppm.period_path.period.msy_id}, ${data.ppm.major_lecture_type.mlt_id}, ${data.ppm.ppm_id})"> Paste
                                    </button>
                                    <button type="button"
                                        class="btn btn-info text-white edit-component waves-effect waves-float waves-light"
                                        onclick="_ratesTableActions.PaymentRateInputField(0,0,null,1,${data.ppm.major_lecture_type.mma_id}, ${data.ppm.period_path.period_id}, ${data.ppm.period_path.path_id}, ${data.ppm.period_path.period.msy_id}, ${data.ppm.major_lecture_type.mlt_id}, ${data.ppm.ppm_id})"> <i class="bx bx-plus m-auto"></i> Tambah Komponen
                                    </button>
                                    </div>

                                </div>
                                <div id="PaymentRateInput">
                                </div>
                                `
                            },
                        },
                        schema: {
                            type: 'custom-field',
                            content: {
                                template: `<div class="mb-2">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12">
                                            <label class="form-label">Skema Cicilan</label>
                                            <select class="form-select select2" eazy-select2-active id="csId" name="cs_id[]" multiple="multiple">
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
                    formSubmitLabel: 'Simpan',
                    formSubmitNote: `
                    <small style="color:#163485">
                        *Pastikan Data Yang Anda Masukkan <strong>Lengkap</strong> dan <strong>Benar</strong>
                    </small>`,
                    callback: function() {
                        // ex: reload table
                        _ratesTable.reload()
                    },
                },
            });
            if (Object.keys(data.component).length > 0) {
                data.component.map(item => {
                    _ratesTableActions.PaymentRateInputField(item.cd_id, item.cd_fee, item.msc_id, null)
                })
            }
            // Skema
            var val = [];
            if (Object.keys(data.ppm.credit).length > 0) {
                data.ppm.credit.map(item => {
                    val.push(item.cs_id.toString());
                })
            }
            skema_cicilan = val;

            /**
             * Include non template Credit Schema on select option
             */
            let creditSchemaUnlisted = val;
            creditSchemaUnlisted.forEach(async (item) => {
                let d = await $.get(_baseURL + '/api/payment/settings/paymentrates/getschemabyid/' + data.ppm.ppm_id + '/' + item);
                const {credit_schema} = JSON.parse(d);
                $("#csId").append(`
                    <option value="` + credit_schema.cs_id + `">` + credit_schema.cs_name + `</option>
                `);
            });

            // console.log(skema_cicilan);
            $.get(_baseURL + '/api/payment/settings/paymentrates/schema', (d) => {
                JSON.parse(d).map(item => {
                    if (!creditSchemaUnlisted.includes(`${item['cs_id']}`)) {
                        $("#csId").append(`
                            <option value="` + item['cs_id'] + `">` + item['cs_name'] + `</option>
                        `);
                    }
                });

                $('#csId').val(val).change();
                selectRefresh()
            })
            if (Object.keys(data.ppm.credit).length > 0) {
                data.ppm.credit.map(item => {
                    _ratesTableActions.SchemaDeadlineField(item.cs_id, item.credit_schema.cs_name, item.credit_schema.credit_schema_detail)
                })
            }
            // schema = $("#csId").val();
            // diff = _ratesTableActions.difference(schema, val);
            // diff.map(item => {
            //     if (val.includes(item)) {
            //         val = val.filter(function(x) {
            //             if (x !== item) {
            //                 return x;
            //             }
            //         });
            //         // console.log("hapus "+item);
            //         $.get(_baseURL + '/api/payment/settings/paymentrates/removeschemabyid/' + data.ppm.ppm_id + '/' + item, (d) => {
            //             d = JSON.parse(d)
            //             _toastr.success(d.message, 'Success')
            //         })
            //         $("#schemaDeadlineTag" + item).remove();
            //     } else {
            //         val.push(item.toString());
            //         $.get(_baseURL + '/api/payment/settings/paymentrates/getschemabyid/' + data.ppm.ppm_id + '/' + item, (d) => {
            //             d = JSON.parse(d)
            //             _ratesTableActions.SchemaDeadlineField(item, d.credit_schema.cs_name, d.credit_schema.credit_schema_detail)
            //         })
            //         // console.log("tambah "+item);
            //     }
            // });
            $("#csId").change(function() {
                schema = $(this).val();
                diff = _ratesTableActions.difference(schema, val);
                diff.map(item => {
                    if (val.includes(item)) {
                        val = val.filter(function(x) {
                            if (x !== item) {
                                return x;
                            }
                        });
                        console.log("hapus "+item);
                        $.get(_baseURL + '/api/payment/settings/paymentrates/removeschemabyid/' + data.ppm.ppm_id + '/' + item, (d) => {
                            d = JSON.parse(d)
                            _toastr.success(d.message, 'Success')
                        })
                        $("#schemaDeadlineTag" + item).remove();
                    } else {
                        val.push(item.toString());
                        $.get(_baseURL + '/api/payment/settings/paymentrates/getschemabyid/' + data.ppm.ppm_id + '/' + item, (d) => {
                            d = JSON.parse(d)
                            _ratesTableActions.SchemaDeadlineField(item, d.credit_schema.cs_name, d.credit_schema.credit_schema_detail)
                        })
                        // console.log("tambah "+item);
                    }
                });
            })
        },
        copy: function(e) {
            dataCopy = _ratesTable.getRowData(e);
        },
        paste: function(mma_id,period_id,path_id,msy_id,mlt_id,ppm_id) {
            if (Object.keys(dataCopy.component).length > 0) {
                dataCopy.component.map(item => {
                    _ratesTableActions.PaymentRateInputField(0, item.cd_fee, item.msc_id, 1,
                    mma_id,
                    period_id,
                    path_id,
                    msy_id,
                    mlt_id,
                    ppm_id)
                })
            }
            skema_cicilan = [];
            if(Object.keys(dataCopy.ppm.credit).length > 0){
                dataCopy.ppm.credit.map(item => {
                    skema_cicilan.push(item.cs_id.toString());
                })
            }

            $('#csId').val(skema_cicilan).change();
            selectRefresh()

            if (Object.keys(dataCopy.ppm.credit).length > 0) {
                dataCopy.ppm.credit.map(item => {
                    var deadlines = $("#schemaDeadlineTag"+item.cs_id).find('input[name="cse_deadline[]"]');

                    for(var i = 0; i < item.credit_schema.credit_schema_detail.length; i++){
                        var valDeadline = "";
                        if(item.credit_schema.credit_schema_detail[i].credit_schema_deadline){
                            valDeadline = item.credit_schema.credit_schema_detail[i].credit_schema_deadline.cse_deadline
                        }
                        deadlines[i].value = valDeadline;
                    }
                })
            }
        },
        difference: function(a1, a2) {
            var a = [],
                diff = [];
            for (var i = 0; i < a1.length; i++) {
                a[a1[i]] = true;
            }
            for (var i = 0; i < a2.length; i++) {
                if (a[a2[i]]) {
                    delete a[a2[i]];
                } else {
                    a[a2[i]] = true;
                }
            }
            for (var k in a) {
                diff.push(k);
            }
            return diff;
        },
        SchemaDeadlineField: function(cs_id = 0, name = null, percentage = null) {
            let html = "";
            if (percentage != null) {
                percentage.map(item => {
                    let deadline = "";
                    if (item.credit_schema_deadline) {
                        deadline = item.credit_schema_deadline.cse_deadline;
                    }
                    html += `
                    <div class="d-flex flex-wrap align-items-center mb-1 SchemaDeadlineField" style="gap:10px"
                        id="comp-order-preview-0">
                        <div class="flex-fill">
                            <label class="form-label">Persentase Pembayaran</label>
                            <input type="text" class="form-control" name="" value="${item.csd_percentage}%"
                                placeholder="Persentase Pembayaran" readonly>
                        </div>
                        <div class="flex-fill">
                            <label class="form-label">Tenggat Pembayaran</label>
                            <input type="date" class="form-control" name="cse_deadline[]" value="${deadline}"
                                placeholder="Tenggat Pembayaran" required>
                            <input type="hidden" name="cse_cs_id[]" value="${cs_id}">
                            <input type="hidden" name="cse_csd_id[]" value="${item.csd_id}">
                        </div>
                    </div>
                    `
                })
            }
            $('#schemaDeadline').append(`
                <div id="schemaDeadlineTag${cs_id}">
                    <h5 class="fw-bolder mb-1 mt-2">Pengaturan Skema ${name}</h5>
                    ${html}
                </div>
            `);
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
                    $.post(_baseURL + '/api/payment/settings/paymentrates/deletecomponent/' + id, {
                        _method: 'DELETE'
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

    /**
     * OLD CODE
     */
    // function myImport() {
    //     var x = document.getElementById("myFiles");
    //     var txt = "";
    //     if ('files' in x) {
    //         if (x.files.length > 0) {
    //             console.log(x.files[0]);
    //             Swal.fire({
    //                 title: "Anda Yakin?",
    //                 text: "Ingin mengimport data dari file tersebut",
    //                 showDenyButton: true,
    //                 confirmButtonText: 'Import',
    //                 denyButtonText: "Cancel",
    //             }).then((result) => {
    //                 if (result.isConfirmed) {
    //                     var formData = new FormData();
    //                     formData.append("file", x.files[0]);
    //                     formData.append("_token", "{{ csrf_token() }}");
    //                     var xhr = new XMLHttpRequest();
    //                     xhr.onload = function() {
    //                         var response = JSON.parse(this.responseText);
    //                         console.log(response)
    //                         if (response.status) {
    //                             Swal.fire(response.message, '', 'success');
    //                         }
    //                     }
    //                     xhr.open("POST", _baseURL + '/api/payment/settings/paymentrates/import/{!! $id !!}', true);
    //                     xhr.send(formData);
    //                 }
    //             })
    //         }
    //     }
    // }

    /**
     * OLD CODE
     */
    // function importBtn() {
    //     $('#myFiles').click()
    // }

    function downloadTemplate() {
        window.location.href = `${_baseURL}/api/payment/settings`
            +`/paymentrates/download-file-for-import`
            +`?period_path_id=${periodPathId}`;
    }

    const _uploadFileForm = {
        clearInput: () => {
            $('#form-upload-file input[name="file"]').val('');
        },
        submit: () => {
            _toastr.info('Sedang memproses file, mungkin membutukan beberapa waktu.', 'Memproses');

            let formData = new FormData(document.getElementById('form-upload-file'));

            $.ajax({
                url: _baseURL+'/api/payment/settings/paymentrates/upload-file-for-import',
                type: 'POST',
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    _uploadFileForm.clearInput();
                    if(data.success){
                        const import_id = data.payload.import_id ?? 0;
                        setLocalStorageWithExpiry('eazy-academic-payment.settings.paymentrates.import_id', import_id, 24*60*60*1000);

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
                    url: _baseURL+'/api/payment/settings/paymentrates/dt-import-preview',
                    data: function(d) {
                        d.custom_payload = {
                            import_id: getLocalStorageWithExpiry('eazy-academic-payment.settings.paymentrates.import_id') ?? 0,
                        };
                    },
                    dataSrc: function (e){
                        const data = e.data;
                        let validCount = 0;
                        let invalidCount = 0;
                        data.forEach(item => {
                            if (item.statuses.includes("invalid")) {
                                invalidCount++;
                            } else if (item.statuses.includes("valid")) {
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
                        name: 'studyprogram',
                        data: 'studyprogram',
                        render: (data) => {
                            return this.template.defaultCell(data);
                        }
                    },
                    {
                        name: 'lecture_type',
                        data: 'lecture_type',
                        render: (data) => {
                            return this.template.defaultCell(data);
                        }
                    },
                    {
                        name: 'invoice_component',
                        data: 'invoice_component',
                        render: (data) => {
                            let html = '<ul class="custom-list">';
                            let array = JSON.parse(unescapeHtml(data));
                            html += array.map((item) => {
                                return `<li class="custom-list-item">${item.name} | ${Rupiah.format(parseInt(item.nominal))}</li>`;
                            }).join('');
                            html += '</ul>';
                            return html;
                        }
                    },
                    {
                        name: 'installment',
                        data: 'installment',
                        render: (data) => {
                            let html = '<div class="d-flex" style="gap: 2rem">';
                            let schemas = JSON.parse(unescapeHtml(data));

                            html += schemas.map((schema) => {
                                return `
                                    <div>
                                        <span><strong>${schema.schema_name}</strong></span>
                                        <ul class="custom-list">
                                            ${
                                                schema.schema_detail.map(item => {
                                                    return `<li class="custom-list-item">${item.percentage}% | ${item.due_date}</li>`;
                                                }).join('')
                                            }
                                        </ul>
                                    </div>
                                `;
                                return `<li class="custom-list-item">${item.percentage}% | ${item.due_date}</li>`;
                            }).join('');

                            html += '</div>';
                            return html;
                        }
                    },
                    {
                        name: 'statuses',
                        data: 'statuses',
                        render: (data) => {
                            const isValid = !data.includes('invalid');
                            if (isValid) {
                                return this.template.badgeCell('Valid', 'success');
                            } else {
                                return this.template.badgeCell('Tidak Valid', 'danger');
                            }
                        }
                    },
                    {
                        name: 'notes',
                        data: 'notes',
                        render: (data, _, row) => {
                            const isValid = !row.statuses.includes('invalid');
                            if (isValid) {
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

    var ImportInvoiceComponentModal = new bootstrap.Modal(document.getElementById('importInvoiceComponentModal'));

    function importSettingFee() {
        _toastr.info('Sedang mengimport data, mungkin membutukan beberapa waktu.', 'Mengimport');

        $.ajax({
            url: _baseURL+'/api/payment/settings/paymentrates/import',
            type: 'POST',
            data: {
                period_path_id: periodPathId,
                import_id: getLocalStorageWithExpiry('eazy-academic-payment.settings.paymentrates.import_id') ?? 0,
            },
            success: function(data) {
                if(data.success){
                    localStorage.removeItem('eazy-academic-payment.settings.paymentrates.import_id');

                    ImportInvoiceComponentModal.hide();
                    _toastr.success(data.message, 'Success');
                    _importPreviewTable.reload();
                    _ratesTable.reload();
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
