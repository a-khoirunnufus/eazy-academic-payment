@extends('layouts.static_master')


@section('page_title', 'Setting Tagihan, Tarif, dan Pembayaran')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('content')

@include('pages._payment.settings._shortcuts', ['active' => 'payment-rates'])

<div class="card">
    <div class="card-body">
        <div class="datatable-filter one-row">
            <div>
                <label class="form-label">Tahun Ajaran dan Semester</label>
                <select name="filter-school-year" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Tahun Ajaran dan Semester</option>
                    @foreach($school_years as $school_year)
                        <option value="{{ $school_year->id }}">{{ $school_year->text }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Periode Masuk</label>
                <select name="filter-period" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Periode Masuk</option>
                    @foreach($periods as $period)
                        <option value="{{ $period->period_id }}">{{ $period->period_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Jalur dan Gelombang</label>
                <select name="filter-path" class="form-select" eazy-select2-active>
                    <option value="#ALL" selected>Semua Jalur dan Gelombang</option>
                    @foreach($paths as $path)
                        <option value="{{ $path->path_id }}">{{ $path->path_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex align-items-end">
                <button onclick="_ratesTable.reload()" class="btn btn-primary text-nowrap">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="rates-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Tahun Ajar</th>
                <th>Periode Masuk</th>
                <th>Jalur / Gelombang</th>
                <th width="50%">Program Studi - Jenis Perkuliahan</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection


@section('js_section')
<script>
    var dataCopy;
    var credit;
    var periode;
    var path;
    var programStudy;
    var lecture_type

    $(function() {
        _ratesTable.init()

        select2Replace();
    })

    const _ratesTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#rates-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL + '/api/payment/settings/paymentrates/index',
                    data: function(d) {
                        d.custom_filters = {
                            'school_year_id': $('select[name="filter-school-year"]').val(),
                            'period_id': $('select[name="filter-period"]').val(),
                            'path_id': $('select[name="filter-path"]').val(),
                        };
                    }
                },
                columns: [{
                        name: 'action',
                        data: 'ppd_id',
                        orderable: false,
                        render: (data, _, row) => {
                            // console.log(row)
                            return this.template.rowAction(data)
                        }
                    },
                    {
                        name: 'path.path_name',
                        render: (data, _, row) => {
                            var year = "Unknown";
                            var semester = "Unknown";
                            if (row.period.schoolyear) {
                                year = row.period.schoolyear.msy_year;
                                if (row.period.schoolyear.msy_semester == 1) {
                                    semester = "Ganjil";
                                } else {
                                    semester = "Genap";
                                }
                            }
                            return `
                                <div>
                                    <span class="fw-bold">${year} - ${semester}</span><br>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'period.period_name',
                        data: 'period.period_name',
                        render: (data) => {
                            return `<span class="fw-bold">${data}</span>`;
                        }
                    },
                    {
                        name: 'path.path_name',
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <span class="fw-bold">${row.path.path_name}</span><br>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'major.ppm_id',
                        render: (data, _, row) => {
                            let html = '<div class="d-flex flex-wrap" style="gap:10px">';
                            if (Object.keys(row.major).length > 0) {
                                row.major.map(item => {
                                    var study_program = "Unknown";
                                    var study_program_type = "";
                                    var lecture_type = "Unknown";
                                    if (item.major_lecture_type) {
                                        if (item.major_lecture_type.study_program) {
                                            study_program_type = item.major_lecture_type.study_program.studyprogram_type
                                            study_program = item.major_lecture_type.study_program.studyprogram_name
                                        }
                                        if (item.major_lecture_type.lecture_type) {
                                            lecture_type = item.major_lecture_type.lecture_type.mlt_name
                                        }
                                    }
                                    html += `<span class="badge bg-primary">${study_program_type} ${study_program} - ${lecture_type}</span>`;
                                })
                            }
                            html += '</div>';
                            return html;
                        }
                    }
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
                    // $('.invoice-component-actions').html(`
                    //     <div style="margin-bottom: 7px">
                    //         <button onclick="_ratesTableActions.add()" class="btn btn-primary me-1">
                    //             <span style="vertical-align: middle">
                    //                 <i data-feather="plus" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                    //                 Tambah Komponen Baru
                    //             </span>
                    //         </button>
                    //     </div>
                    // `)
                    feather.replace()
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
                            
                            <a href="${_baseURL}/payment/settings/payment-rates/detail/${id}" class="dropdown-item"><i data-feather="dollar-sign"></i>&nbsp;&nbsp;Edit Komponen Biaya</a>
                            <!-- <a href="#" class="dropdown-item" onclick="salinData(${id})"><i data-feather="clipboard"></i>&nbsp;&nbsp;Salin Data</a> -->
                        </div>
                    </div>
                `
            }
        }
    }

    var count = -9999;
    const _ratesTableActions = {
        tableRef: _ratesTable,
        PaymentRateInputField: function(id = 0, rate = 0, component = null, increment = 0) {
            let isId = 0;
            if (increment === 1) {
                isId = count++;
            } else {
                isId = id;
            }
            $('#PaymentRateInput').append(`
                <div class="d-flex flex-wrap align-items-center mb-1 PaymentRateInputField" style="gap:10px"
                    id="comp-order-preview-0">
                    <input type="hidden" name="fc_id[]" value="${id}">
                    <div class="flex-fill">
                        <label class="form-label">Nama Komponen</label>
                        <select class="form-select select2" eazy-select2-active name="msc_id[]" id="component${isId}" value="">
                        </select>
                    </div>
                    <div class="flex-fill">
                        <label class="form-label">Harga Komponen Biaya</label>
                        <input type="text" class="form-control comp_price" name="fc_rate[]" value="${rate}"
                            placeholder="Tarif Mata Kuliah">
                    </div>
                    <div class="d-flex align-content-end">
                        <div class="">
                            <label class="form-label" style="opacity: 0">#</label>
                            <a class="btn btn-danger text-white btn-sm d-flex" style="height: 36px"
                            onclick="_ratesTableActions.courseRateDeleteField(this,${id})"> <i class="bx bx-trash m-auto"></i> </a>
                        </div>
                    </div>
                </div>
            `);
            getApi(_baseURL + '/api/payment/settings/paymentrates/component', "", (data) => {
                data.map(item => {
                    var count_elm = $(".PaymentRateInputField").length;
                    $(
                        $(
                            $(
                                $(".PaymentRateInputField")[count_elm - 1]
                            ).children(".flex-fill")[0]
                        ).children("select")[0]
                    ).append(`
                        <option value="` + item['msc_id'] + `">` + item['msc_name'] + `</option>
                    `)
                })
                component ? $("#component" + isId).val(component) : ""
                $("#component" + isId).trigger('change')
                selectRefresh()
            })

        },
        courseRateDeleteField: function(e, id) {
            if (id === 0) {
                $(e).parents('.PaymentRateInputField').get(0).remove();
            } else {
                _ratesTableActions.deleteComponent(e, id);
            }
        },

        add: function() {
            var btnPaste = "";
            if (dataCopy != null) {
                btnPaste = '<div><button class="btn bg-primary text-white" type="button" onclick="paste()">Paste</button></div>'
            }
            Modal.show({
                type: 'form',
                modalTitle: 'Tambah Tarif Matakuliah',
                modalSize: 'lg',
                config: {
                    formId: 'paymentRateForm',
                    formActionUrl: _baseURL + '/api/payment/settings/paymentrates/store',
                    formType: 'add',
                    data: $("#paymentRateForm").serialize(),
                    isTwoColumn: false,
                    fields: {
                        selections: {
                            type: 'custom-field',
                            content: {
                                template: `${btnPaste}<div class="mb-2">
                                    <div class="row mb-1">
                                        <div class="col-lg-6 col-md-6">
                                            <label class="form-label">Periode Masuk</label>
                                            <select class="form-select select2" eazy-select2-active id="periodId" name="f_period_id">
                                                <option value="">Pilih Periode</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <label class="form-label">Program Studi</label>
                                            <select class="form-select select2" eazy-select2-active id="programStudy" name="f_studyprogram_id[]" multiple>
                                                <option value="">Pilih Program Studi</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6">
                                            <label class="form-label">Jalur / Gelombang</label>
                                            <select class="form-select select2" eazy-select2-active id="pathId" name="f_path_id">
                                                <option value="">Pilih Jalur / Gelombang</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <label class="form-label">Jenis Perkuliahan</label>
                                            <select class="form-select select2" eazy-select2-active name="f_jenis_perkuliahan_id[]" id="jenisPerkuliahanId" multiple>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12">
                                            <label class="form-label">Skema Cicilan</label>
                                            <select class="form-select select2" eazy-select2-active id="csId" name="cs_id[]" multiple="multiple">
                                                <option value="">Pilih Skema</option>
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
                                    <h4 class="fw-bolder mb-0">Tambah Komponen Baru</h4>
                                    <button type="button"
                                        class="btn btn-primary text-white edit-component waves-effect waves-float waves-light"
                                        onclick="_ratesTableActions.PaymentRateInputField(0,0,null,1)"> <i class="bx bx-plus m-auto"></i> Tambah Komponen
                                    </button>
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
                        _ratesTable.reload()
                    },
                },
            });
            $('#PaymentRateInput').empty();

            getApi(_baseURL + '/api/payment/settings/paymentrates/period', "", function(response) {
                periode = response;
                periode.map(item => {
                    $("#periodId").append(`
                        <option value="` + item['period_id'] + `">` + item['period_name'] + `</option>
                    `)
                })
                // selectRefresh()
            });

            getApi(_baseURL + '/api/payment/settings/paymentrates/path', "", (response) => {
                path = response
                path.map(item => {
                    $("#pathId").append(`
                        <option value="` + item['path_id'] + `">` + item['path_name'] + `</option>
                    `)
                })
                // selectRefresh()
            });

            getApi(_baseURL + '/api/payment/settings/paymentrates/studyprogram', "", (response) => {
                programStudy = response;
                programStudy.map(item => {
                    $("#programStudy").append(`
                        <option value="` + item['studyprogram_id'] + `">` + item['studyprogram_name'] + `</option>
                    `)
                })
                // selectRefresh()
            });

            getApi(_baseURL + '/api/payment/settings/paymentrates/lecture-type', "", (response) => {
                lecture_type = response;
                lecture_type.map(item => {
                    $("#jenisPerkuliahanId").append(`
                        <option value="` + item['mlt_id'] + `">` + item['mlt_name'] + `</option>
                    `)
                })
                // selectRefresh()
            });

            getApi(_baseURL + '/api/payment/settings/paymentrates/credit-schema', "", (response) => {
                credit = response;
                credit.map(item => {
                    $("#csId").append(`
                        <option value="` + item['cs_id'] + `">` + item['cs_name'] + `</option>
                    `)
                })
                // selectRefresh()
            });
            console.log("credit : ");
            console.log(credit);
            // Study Program
            // _options.load({
            //     optionUrl: _baseURL + '/api/payment/settings/courserates/studyprogram',
            //     nameField: 'f_studyprogram_id',
            //     idData: 'studyprogram_id',
            //     nameData: 'studyprogram_name'
            // });
            // Periode Masuk
            // _options.load({
            //     optionUrl: _baseURL + '/api/payment/settings/paymentrates/period',
            //     nameField: 'f_period_id',
            //     idData: 'period_id',
            //     nameData: 'period_name'
            // });
            // Jalur / Gelombang
            // _options.load({
            //     optionUrl: _baseURL + '/api/payment/settings/paymentrates/path',
            //     nameField: 'f_path_id',
            //     idData: 'path_id',
            //     nameData: 'path_name'
            // });
            // Skema
            // $.get(_baseURL + '/api/payment/settings/paymentrates/schema', (data) => {
            //     JSON.parse(data).map(item => {
            //         $("#csId").append(`
            //             <option value="`+item['cs_id']+`">`+item['cs_name']+`</option>
            //         `)
            //     })
            //     selectRefresh()
            // })
            // selectRefresh()
        },
        edit: function(e) {
            let data = _ratesTable.getRowData(e);
            let semester = "";
            (data.period.schoolyear.msy_semester == 1) ? semester = 'Ganjil': semester = 'Genap';
            let jenis = "";
            (data.f_jenis_perkuliahan_id == 1) ? jenis = 'Reguler': jenis = 'Unknown';
            console.log(data)
            Modal.show({
                type: 'form',
                modalTitle: 'Tambah Tarif Matakuliah',
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
                                            <h1 class="h6 fw-bolder" id="tahun-name">${data.period.schoolyear.msy_year} - ${semester}</h1>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <h6>Periode</h6>
                                            <h1 class="h6 fw-bolder" id="period-name">${data.period.period_name}</h1>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <h6>Jalur</h6>
                                            <h1 class="h6 fw-bolder" id="path-name">${data.path.path_name}</h1>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <h6>Program Studi</h6>
                                            <h1 class="h6 fw-bolder" id="prodi-name">${data.study_program.studyprogram_type} - ${data.study_program.studyprogram_name} - ${jenis}</h1>
                                        </div>
                                        <input type="hidden" name="f_id" value="${data.f_id}">
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
                                    <h4 class="fw-bolder mb-0">Tambah Komponen Baru</h4>
                                    <button type="button"
                                        class="btn btn-primary text-white edit-component waves-effect waves-float waves-light"
                                        onclick="_ratesTableActions.PaymentRateInputField(0,0,null,1)"> <i class="bx bx-plus m-auto"></i> Tambah Komponen
                                    </button>
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
                        _ratesTable.reload()
                    },
                },
            });
            if (Object.keys(data.component).length > 0) {
                data.component.map(item => {
                    _ratesTableActions.PaymentRateInputField(item.fc_id, item.fc_rate, item.msc_id, null)
                })
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
        delete: function(e) {
            let data = _ratesTable.getRowData(e);
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin menghapus tarif dan pembayaran ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(_baseURL + '/api/payment/settings/paymentrates/delete/' + data.f_id, {
                        _method: 'DELETE'
                    }, function(data) {
                        data = JSON.parse(data)
                        Swal.fire({
                            icon: 'success',
                            text: data.message,
                        }).then(() => {
                            _ratesTable.reload()
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
        }
    }

    function getApi(url, query = "", callback) {
        var xhr = new XMLHttpRequest();
        xhr.onload = function() {
            var response = this.responseText;
            var data = JSON.parse(response);
            callback(data);
        }
        xhr.onerror = function() {
            return getApi(url, query);
        }
        xhr.open("GET", url + query, false);
        xhr.send();
    }

    function salinData(id) {
        getApi(_baseURL + '/api/payment/settings/paymentrates/detail', "/" + id, function(response) {
            dataCopy = response;
        });
        console.log("copy data :");
        console.log(dataCopy);
    }

    function paste() {
        console.log("copy data :");
        console.log(dataCopy);

        var prodi = [];
        var prodiList = "";
        var options = $("#programStudy").children("option");
        for (var i = 0; i < dataCopy.data.length; i++) {
            prodi[i] = dataCopy.data[i].ppm.major_lecture_type.mma_id
            for (var j = 0; j < options.length; j++) {
                if (prodi[i] == options[j].getAttribute("value")) {
                    prodiList += `<li class="select2-selection__choice" title="` + options[j].innerHTML + `" data-select2-id="` + options[j].getAttribute("data-select2-id") + `"><span class="select2-selection__choice__remove" role="presentation">×</span>` + options[j].innerHTML + `</li>`
                }
            }
        }
        prodiList += `<li class="select2-search select2-search--inline"><input class="select2-search__field" type="search" tabindex="0" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" role="searchbox" aria-autocomplete="list" placeholder="" style="width: 0.75em;"></li>`

        var jenisPerkuliahan = [];
        var options = $("#jenisPerkuliahanId").children("option");
        var jenisList = "";
        for (var i = 0; i < dataCopy.data.length; i++) {
            if (jenisPerkuliahan.indexOf(dataCopy.data[i].ppm.major_lecture_type.mlt_id) < 0) {
                jenisPerkuliahan.push(dataCopy.data[i].ppm.major_lecture_type.mlt_id);
            }
        }
        for (var j = 0; j < options.length; j++) {
            for (var k = 0; k < jenisPerkuliahan.length; k++) {
                if (jenisPerkuliahan[k] == options[j].getAttribute("value")) {
                    jenisList += `<li class="select2-selection__choice" title="` + options[j].innerHTML + `" data-select2-id="` + options[j].getAttribute("data-select2-id") + `"><span class="select2-selection__choice__remove" role="presentation">×</span>` + options[j].innerHTML + `</li>`
                }
            }
        }
        jenisList += `<li class="select2-search select2-search--inline"><input class="select2-search__field" type="search" tabindex="0" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" role="searchbox" aria-autocomplete="list" placeholder="" style="width: 0.75em;"></li>`

        var pathTitle = "";
        var i = 0;
        var isFound = false;
        while (i < path.length && !isFound) {
            if (path[i].path_id == dataCopy.data[0].ppm.period_path.path_id) {
                isFound = true;
                pathTitle = path[i].path_name;
            }
            i++;
        }

        var typeCicilan = [];
        var options = $("#csId").children("option");
        var cicilanList = "";
        for (var i = 0; i < dataCopy.data.length; i++) {
            for (var j = 0; j < dataCopy.data[i].ppm.credit.length; j++) {
                if (typeCicilan.indexOf(dataCopy.data[i].ppm.credit[j].cs_id) < 0) {
                    typeCicilan.push(dataCopy.data[i].ppm.credit[j].cs_id)
                }
            }
        }
        for (var j = 0; j < options.length; j++) {
            for (var k = 0; k < typeCicilan.length; k++) {
                if (typeCicilan[k] == options[j].getAttribute("value")) {
                    cicilanList += `<li class="select2-selection__choice" title="` + options[j].innerHTML + `" data-select2-id="` + options[j].getAttribute("data-select2-id") + `"><span class="select2-selection__choice__remove" role="presentation">×</span>` + options[j].innerHTML + `</li>`
                }
            }
        }
        cicilanList += `<li class="select2-search select2-search--inline"><input class="select2-search__field" type="search" tabindex="0" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" role="searchbox" aria-autocomplete="list" placeholder="" style="width: 0.75em;"></li>`

        $("#periodId").val(dataCopy.data[0].ppm.period_path.period_id);
        $("#select2-periodId-container").attr("title", dataCopy.data[0].ppm.period_path.period.period_name);
        $("#select2-periodId-container").html(dataCopy.data[0].ppm.period_path.period.period_name)

        $("#programStudy").val(prodi);
        $(".select2-selection__rendered")[1].innerHTML = prodiList

        $("#pathId").val(dataCopy.data[0].ppm.period_path.path_id);
        $("#select2-pathId-container").attr("title", pathTitle);
        $("#select2-pathId-container").html(pathTitle);

        $("#jenisPerkuliahanId").val(jenisPerkuliahan);
        $(".select2-selection__rendered")[3].innerHTML = jenisList;

        $("#csId").val(typeCicilan);
        $(".select2-selection__rendered")[4].innerHTML = cicilanList;
        console.log($(".select2-selection__rendered"));

        var komponen = {
            component: [],
            data: []
        };
        for (var i = 0; i < dataCopy.data.length; i++) {
            for (var j = 0; j < dataCopy.data[i].component.length; j++) {
                if (komponen.component.indexOf(dataCopy.data[i].component[j].msc_id) < 0) {
                    komponen.component.push(dataCopy.data[i].component[j].msc_id);
                    komponen.data.push([]);
                } else {
                    var onindex = komponen.component.indexOf(dataCopy.data[i].component[j].msc_id);
                    if (
                        komponen.data[onindex].length == 0 ||
                        komponen.data[onindex].indexOf(dataCopy.data[i].component[j].cd_fee) < 0
                    ) {
                        komponen.data[onindex].push(dataCopy.data[i].component[j].cd_fee);
                    }
                }
            }
        }

        console.log($($(".PaymentRateInputField")[0]).children("[role=textbox]"));
        var count_data = 0;
        for (var i = 0; i < komponen.component.length; i++) {
            for (var j = 0; j < komponen.data[i].length; j++) {
                _ratesTableActions.PaymentRateInputField(0, komponen.data[i][j], null, 0);

                $(
                    $(
                        $(
                            $(".PaymentRateInputField")[count_data]
                        ).children(".flex-fill")[0]
                    ).children("select")[0]
                ).val(komponen.component[i]);

                count_data++;
            }
        }
        
    }
</script>
@endsection