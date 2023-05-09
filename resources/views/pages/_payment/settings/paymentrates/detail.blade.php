@extends('layouts.static_master')


@section('page_title', 'Setting Tagihan, Tarif, dan Pembayaran')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
    <style>
        .rates-filter {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            grid-gap: 1rem;
        }
    </style>
@endsection

@section('content')

@include('pages._payment.settings._shortcuts', ['active' => 'payment-rates'])
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
                <a href="{{ route('payment.settings.payment-rates')}}" class="btn btn-primary waves-effect waves-float waves-light w-100"><i class='bx bx-arrow-back m-auto'></i> Kembali</a>
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
@endsection


@section('js_section')
<script>
    $(function(){
        _ratesTable.init()

        select2Replace();
    })

    const _ratesTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#rates-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/payment/settings/paymentrates/detail/{!! $id !!}',
                },
                columns: [
                    {
                        name: 'action',
                        data: 'ppm.ppm_id',
                        orderable: false,
                        render: (data, _, row) => {
                            console.log(row)
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
                            if(Object.keys(row.component).length > 0){
                                row.component.map(item => {
                                    let component = "";
                                    if(item.component){
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
                            if(Object.keys(row.ppm.credit).length > 0){
                                row.ppm.credit.map(item => {
                                    let credit = "";
                                    if(item.credit_schema){
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
                            
                            <a onclick="_ratesTableActions.edit(this)" class="dropdown-item"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="_ratesTableActions.delete(this)" class="dropdown-item"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
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
            if(increment === 1){
                isId = count++;
            }else{
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
            $.get(_baseURL + '/api/payment/settings/paymentrates/component', (data) => {
                JSON.parse(data).map(item => {
                    $("#component"+isId).append(`
                        <option value="`+item['msc_id']+`">`+item['msc_name']+`</option>
                    `)
                })
                component ? $("#component"+isId).val(component) : ""
                $("#component"+isId).trigger('change')
                selectRefresh()
            })

        },
        courseRateDeleteField: function(e,id){
            if(id === 0){
                $(e).parents('.PaymentRateInputField').get(0).remove();
            }else{
                _ratesTableActions.deleteComponent(e,id);
            }
        },

        add: function() {
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
                                template: `<div class="mb-2">
                                    <div class="row mb-1">
                                        <div class="col-lg-6 col-md-6">
                                            <label class="form-label">Periode Masuk</label>
                                            <select class="form-select select2" eazy-select2-active id="periodId" name="f_period_id">
                                                <option value="">Pilih Periode</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <label class="form-label">Program Studi</label>
                                            <select class="form-select select2" eazy-select2-active id="programStudy" name="f_studyprogram_id">
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
                                            <select class="form-select select2" eazy-select2-active name="f_jenis_perkuliahan_id" id="jenisPerkuliahanId">
                                                <option value="1">Reguler</option>
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
            // Study Program
            _options.load({
                optionUrl: _baseURL + '/api/payment/settings/courserates/studyprogram',
                nameField: 'f_studyprogram_id',
                idData: 'studyprogram_id',
                nameData: 'studyprogram_name'
            });
            // Periode Masuk
            _options.load({
                optionUrl: _baseURL + '/api/payment/settings/paymentrates/period',
                nameField: 'f_period_id',
                idData: 'period_id',
                nameData: 'period_name'
            });
            // Jalur / Gelombang
            _options.load({
                optionUrl: _baseURL + '/api/payment/settings/paymentrates/path',
                nameField: 'f_path_id',
                idData: 'path_id',
                nameData: 'path_name'
            });
            // Skema
            $.get(_baseURL + '/api/payment/settings/paymentrates/schema', (data) => {
                JSON.parse(data).map(item => {
                    $("#csId").append(`
                        <option value="`+item['cs_id']+`">`+item['cs_name']+`</option>
                    `)
                })
                selectRefresh()
            })
        },
        edit: function(e) {
            let data = _ratesTable.getRowData(e);
            let semester = "";
            (data.period.schoolyear.msy_semester == 1) ? semester = 'Ganjil' : semester = 'Genap';
            let jenis = "";
            (data.f_jenis_perkuliahan_id == 1) ? jenis = 'Reguler' : jenis = 'Unknown';
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
            if(Object.keys(data.component).length > 0){
                data.component.map(item => {
                    _ratesTableActions.PaymentRateInputField(item.fc_id,item.fc_rate,item.msc_id, null)
                })
            }
        },
        deleteComponent: function(e,id) {
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
                    }, function(data){
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
                    }, function(data){
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
</script>
@endsection
