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

@include('pages.setting._shortcuts', ['active' => 'rates'])

<div class="card">
    <div class="card-body">
        <div class="d-flex flex-column" style="gap: 2rem">
            <div class="rates-filter" style="flex-grow: 1">
                <div>
                    <label class="form-label">Periode Masuk</label>
                    <select class="form-select" eazy-select2-active>
                        <option value="all" selected>Semua Periode Masuk</option>
                        @foreach($static_school_years as $school_year)
                            <option value="{{ $school_year }}">{{ $school_year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Gelombang</label>
                    <select class="form-select" eazy-select2-active>
                        <option value="all" selected>Semua Gelombang</option>
                        @foreach($static_registration_periods as $registration_period)
                            <option value="{{ $registration_period }}">{{ $registration_period }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Jalur Pendaftaran</label>
                    <select class="form-select" eazy-select2-active>
                        <option value="all" selected>Semua Jalur Pendaftaran</option>
                        @foreach($static_registration_paths as $registration_path)
                            <option value="{{ $registration_path }}">{{ $registration_path }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Sistem Kuliah</label>
                    <select class="form-select" eazy-select2-active>
                        <option value="all" selected>Semua Sistem Kuliah</option>
                        @foreach($static_study_systems as $study_system)
                            <option value="{{ $study_system }}">{{ $study_system }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Fakultas</label>
                    <select class="form-select" eazy-select2-active>
                        <option value="all" selected>Semua Fakultas</option>
                        @foreach($static_faculties as $faculty)
                            <option value="{{ $faculty }}">{{ $faculty }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Program Studi</label>
                    <select class="form-select" eazy-select2-active>
                        <option value="all" selected>Semua Program Studi</option>
                        @foreach($static_study_programs as $study_program)
                            <option value="{{ $study_program }}">{{ $study_program }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Komponen Tagihan</label>
                    <select class="form-select" eazy-select2-active>
                        <option value="all" selected>Semua Komponen Tagihan</option>
                        @foreach($static_invoice_components as $invoice_component)
                            <option value="{{ $invoice_component }}">{{ $invoice_component }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-flex align-items-end">
                    <button class="btn btn-primary">
                        <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="rates-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Periode Masuk</th>
                <th>Program Studi / Fakultas</th>
                <th>Jalur / Gelombang</th>
                <th>Jenis Perkuliahan</th>
                {{-- <th>Komponen Tagihan</th>
                <th>Nominal Tarif</th> --}}
                <th>Cicilan</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<div class="modal fade" id="frmbox-comp" tabindex="-1" aria-labelledby="frmbox-title" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg justify-content-center">
        <div class="modal-content" style="">
            <div class="modal-header bg-transparent">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-sm-5 mx-50 pb-3">
                <h1 class=" fw-bolder h3 mb-3" id="frmbox-title">Form Data Komponen Tagihan</h1>

                <div>
                    <div class="d-flex flex-wrap justify-content-between align-items-center" style="gap:10px">
                        <h1 class="h4 fw-bolder mb-0">Lengkapi Data Di Bawah!</h1>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <h6>Tahun</h6>
                            <h1 class="h6 fw-bolder" id="tahun-name">2023/2024 - Ganjil</h1>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <h6>Periode</h6>
                            <h1 class="h6 fw-bolder" id="period-name">Periode Maret Gelombang 1 Tahun Ajaran 2023/2024 Ganjil</h1>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <h6>Jalur</h6>
                            <h1 class="h6 fw-bolder" id="path-name">Prestasi</h1>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <h6>Program Studi</h6>
                            <h1 class="h6 fw-bolder" id="prodi-name">S1 - Teknik Infrastruktur Lingkungan - Reguler</h1>
                        </div>
                    </div>

                    <hr>
                </div>

                <!-- form -->
                <form id="frm-comp">
                    <input type="hidden" name="_token" value="pMBK0kMPXiiXpXKIBiPrFhi144CmMMclRsC4G84E">                        <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap:10px">
                        <h4 class="fw-bolder mb-0">Tambah Komponen Baru</h4>
                        <button type="button" class="btn btn-primary text-white btn-sm edit-component waves-effect waves-float waves-light" onclick="addNewComp()"> <i class="bx bx-plus m-auto"></i>
                        </button>
                    </div>

                    <hr>

                    <div id="comp-container-input">
            <div class="d-flex flex-wrap align-items-center mb-1" style="gap:10px" id="comp-order-preview-0">
                <input type="hidden" name="cd_id[]" value="107">
                <div class="flex-fill" style="width:40%">
                    <label class="form-label">Nama Komponen Biaya</label>
                    <select class="select2-comp form-select select2-hidden-accessible" name="comp_id[]" data-select2-id="26" tabindex="-1" aria-hidden="true">
                        <option selected="" disabled="">Pilih Nama Komponen Biaya</option>
                        <option value="41">SDP2</option>,<option value="42">UP3</option>,<option value="11">Biaya Daftar Ulang</option>,<option value="43" selected="" data-select2-id="28">BPP</option>,<option value="40">biaya daftar ulang 2</option>,<option value="44">Asrama</option>
                    </select><span class="select2 select2-container select2-container--default" dir="ltr" data-select2-id="27" style="width: auto;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-comp_id-ci-container"><span class="select2-selection__rendered" id="select2-comp_id-ci-container" role="textbox" aria-readonly="true" title="BPP">BPP</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                </div>
                <div class="flex-fill">
                    <label class="form-label">Harga Komponen Biaya</label>
                    <input type="text" class="form-control comp_price" name="comp_price[]" value="1,000" placeholder="Tulis Harga Komponen Biaya">
                </div>
                <div>
                    <label class="form-label" style="opacity: 0">#</label>
                    <button class="btn btn-danger text-white btn-sm d-flex" style="height: 36px" onclick="del_comp('0')"> <i class="bx bx-trash m-auto"></i> </button>
                </div>
            </div>
            
            <div class="d-flex flex-wrap align-items-center mb-1" style="gap:10px" id="comp-order-preview-1">
                <input type="hidden" name="cd_id[]" value="108">
                <div class="flex-fill" style="width:40%">
                    <label class="form-label">Nama Komponen Biaya</label>
                    <select class="select2-comp form-select select2-hidden-accessible" name="comp_id[]" data-select2-id="29" tabindex="-1" aria-hidden="true">
                        <option selected="" disabled="">Pilih Nama Komponen Biaya</option>
                        <option value="41">SDP2</option>,<option value="42" selected="" data-select2-id="31">UP3</option>,<option value="11">Biaya Daftar Ulang</option>,<option value="43">BPP</option>,<option value="40">biaya daftar ulang 2</option>,<option value="44">Asrama</option>
                    </select><span class="select2 select2-container select2-container--default" dir="ltr" data-select2-id="30" style="width: auto;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-comp_id-gl-container"><span class="select2-selection__rendered" id="select2-comp_id-gl-container" role="textbox" aria-readonly="true" title="UP3">UP3</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                </div>
                <div class="flex-fill">
                    <label class="form-label">Harga Komponen Biaya</label>
                    <input type="text" class="form-control comp_price" name="comp_price[]" value="2,000" placeholder="Tulis Harga Komponen Biaya">
                </div>
                <div>
                    <label class="form-label" style="opacity: 0">#</label>
                    <button class="btn btn-danger text-white btn-sm d-flex" style="height: 36px" onclick="del_comp('1')"> <i class="bx bx-trash m-auto"></i> </button>
                </div>
            </div>
            </div>
                    <hr>

                    <label class="form-label">Skema Cicilan</label>
                    <div class="position-relative">
                        <select class="select2 form-select select2-hidden-accessible" id="skemacicilan" name="skemacicilan[]" multiple="" data-select2-id="skemacicilan" tabindex="-1" aria-hidden="true">
                            <option disabled="">Pilih Skema Cicilan</option>
                            <option value="2">Tes 3</option>
                            <option value="3" data-select2-id="22">Full 100%</option>
                            <option value="1" data-select2-id="23">Pembayaran Cicilan 3x</option>
                            <option value="34">Pembayaran Cicilan 2x</option>
                            <option value="67">Cicilan 5x</option>
                            <option value="100">Cicilan 2x</option>
                            <option value="101">Cicilan 6x</option>
                            <option value="102">Cicilan 10x</option>
                        </select>
                    </div>
                </form>


                <div class="d-flex align-items-center flex-wrap justify-content-between mt-2" style="gap:10px">
                    <small style="color:#163485">
                        *Pastikan Data Yang Anda Masukkan <strong>Lengkap</strong> dan <strong>Benar</strong>
                    </small>
                    <button class="btn btn-primary edit-component waves-effect waves-float waves-light" type="button" onclick="save_comp()">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
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
                    url: _baseURL+'/api/dt/rates',
                },
                columns: [
                    {
                        name: 'action',
                        data: 'id',
                        orderable: false,
                        render: (data, _, row) => {
                            return this.template.rowAction(data)
                        }
                    },
                    {
                        name: 'entry_period', 
                        data: 'entry_period',
                        render: (data) => {
                            return `<span class="fw-bold">${data}</span>`;
                        }
                    },
                    {
                        name: 'study_program_n_faculty',
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <span class="fw-bold">${row.study_program}</span><br>
                                    <small class="text-secondary">${row.faculty}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        name: 'registration_path_n_wave',
                        render: (data, _, row) => {
                            return `
                                <div>
                                    <span class="fw-bold">${row.registration_path}</span><br>
                                    <small class="text-secondary">${row.wave}</small>
                                </div>
                            `;
                        }
                    },
                    {name: 'study_system', data: 'study_system'},
                    // {
                    //     name: 'invoice_component', 
                    //     data: 'invoice_component',
                    //     render: (data) => {
                    //         return `<span class="fw-bold">${data}</span>`;
                    //     }
                    // },
                    // {
                    //     name: 'rate', 
                    //     data: 'rate',
                    //     render: (data) => {
                    //         return Rupiah.format(data);
                    //     }
                    // },
                    {
                        name: 'instalment', 
                        data: 'instalment',
                        render: (data) => {
                            return `<span class="fw-bold">${data}</span>`;
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
                    $('.invoice-component-actions').html(`
                        <div style="margin-bottom: 7px">
                            <button onclick="_ratesTableActions.add()" class="btn btn-primary me-1">
                                <span style="vertical-align: middle">
                                    <i data-feather="plus" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Tambah Tarif
                                </span>
                            </button>
                            <button onclick="_ratesTableActions.copy()" class="btn btn-warning">
                                <span style="vertical-align: middle">
                                    <i data-feather="copy" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Salin
                                </span>
                            </button>
                        </div>
                    `)
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
                            
                            <a onclick="_ratesTableActions.edit()" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="_ratesTableActions.delete()" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
                        </div>
                    </div>
                `
            }
        }
    }
    // <a href="${_baseURL+'/setting/rates-per-course'}" class="dropdown-item" href="javascript:void(0);"><i data-feather="settings"></i>&nbsp;&nbsp;Setting Per-Matakuliah</a>
    const _ratesTableActions = {
        tableRef: _ratesTable,
        add: function() {
            Modal.show({
                type: 'form',
                modalTitle: 'Tambah Tarif',
                modalSize: 'lg',
                config: {
                    formId: 'form-add-rates',
                    formActionUrl: '#',
                    isTwoColumn: true,
                    fields: {
                        entry_period: {
                            title: 'Periode Masuk',
                            content: {
                                template: `
                                    <select class="form-select" eazy-select2-active>
                                        <option disabled selected>Pilih Periode Masuk</option>
                                        @foreach($static_school_years as $school_year)
                                            <option value="{{ $school_year }}">{{ $school_year }}</option>
                                        @endforeach
                                    </select>
                                `,
                            },
                        },
                        wave: {
                            title: 'Gelombang',
                            content: {
                                template: `
                                    <select class="form-select" eazy-select2-active>
                                        <option disabled selected>Pilih Gelombang</option>
                                        @foreach($static_registration_periods as $registration_period)
                                            <option value="{{ $registration_period }}">{{ $registration_period }}</option>
                                        @endforeach
                                    </select>    
                                `,
                            },
                        },
                        registration_path: {
                            title: 'Jalur Masuk',
                            content: {
                                template: `
                                    <select class="form-select" eazy-select2-active>
                                        <option disabled selected>Pilih Jalur Masuk</option>
                                        @foreach($static_registration_paths as $registration_path)
                                            <option value="{{ $registration_path }}">{{ $registration_path }}</option>
                                        @endforeach
                                    </select>  
                                `,
                            },
                        },
                        study_system: {
                            title: 'Sistem Kuliah',
                            content: {
                                template: `
                                    <select class="form-select" eazy-select2-active>
                                        <option disabled selected>Pilih Sistem Kuliah</option>
                                        @foreach($static_study_systems as $study_system)
                                            <option value="{{ $study_system }}">{{ $study_system }}</option>
                                        @endforeach
                                    </select>  
                                `,
                            },
                        },
                        faculty: {
                            title: 'Fakultas',
                            content: {
                                template: `
                                    <select class="form-select" eazy-select2-active>
                                        <option disabled selected>Pilih Fakultas</option>
                                        @foreach($static_faculties as $faculty)
                                            <option value="{{ $faculty }}">{{ $faculty }}</option>
                                        @endforeach
                                    </select>
                                `,
                            },
                        },
                        study_program: {
                            title: 'Program Studi',
                            content: {
                                template: `
                                    <select class="form-select" eazy-select2-active>
                                        <option disabled selected>Pilih Program Studi</option>
                                        @foreach($static_study_programs as $study_program)
                                            <option value="{{ $study_program }}">{{ $study_program }}</option>
                                        @endforeach
                                    </select>
                                `,
                            },
                        },
                        invoice_component: {
                            title: 'Komponen Tagihan',
                            content: {
                                template: `
                                    <select class="form-select" eazy-select2-active>
                                        <option disabled selected>Pilih Komponen Tagihan</option>
                                        @foreach($static_invoice_components as $invoice_component)
                                            <option value="{{ $invoice_component }}">{{ $invoice_component }}</option>
                                        @endforeach
                                    </select>
                                `,
                            },
                        },
                        rate: {
                            title: 'Nominal Tarif',
                            content: {
                                template: `<input type="number" name="rate" class="form-control" placeholder="Masukkan nominal" />`,
                            },
                        },
                        instalment: {
                            title: 'Cicilan',
                            content: {
                                template: `
                                    <select class="form-select" eazy-select2-active>
                                        <option disabled selected>Pilih Skema Cicilan</option>
                                        @foreach($static_installments as $installment)
                                            <option value="{{ $installment }}">{{ $installment }}</option>
                                        @endforeach
                                    </select>
                                `,
                            },
                        },
                    },
                    formSubmitLabel: 'Tambah Tarif',
                    callback: function() {
                        // ex: reload table
                        Swal.fire({
                            icon: 'success',
                            text: 'Berhasil menambahkan tarif',
                        }).then(() => {
                            this.tableRef.reload()
                        })
                    },
                },
            });
        },
        edit: function() {
            // ajax request
            const {
                period,
                wave,
                path,
                study_system,
                faculty,
                study_program,
                invoice_component,
                rate,
                installment
            } = {
                period: '2022/2023',
                wave: 'Periode Februari',
                path: 'Jalur Mandiri',
                study_system: 'Onsite',
                faculty: 'Fakultas Informatika',
                study_program: 'S1 Informatika',
                invoice_component: 'Biaya Perkuliahan',
                rate: '2000000',
                installment: 'Full 100% Pembayaran',
            }

            $("#frmbox-comp").modal('show');


            // Modal.show({
            //     type: 'form',
            //     modalTitle: 'Edit Tarif',
            //     modalSize: 'lg',
            //     config: {
            //         formId: 'form-edit-rates',
            //         formActionUrl: '#',
            //         formType: 'edit',
            //         isTwoColumn: true,
            //         fields: {
            //             entry_period: {
            //                 title: 'Periode Masuk',
            //                 content: {
            //                     template: `
            //                         <select class="form-select" value=":selected" eazy-select2-active>
            //                             <option disabled>Pilih Periode Masuk</option>
            //                             @foreach($static_school_years as $school_year)
            //                                 <option value="{{ $school_year }}">{{ $school_year }}</option>
            //                             @endforeach
            //                         </select>
            //                     `,
            //                     selected: period,
            //                 },
            //             },
            //             wave: {
            //                 title: 'Gelombang',
            //                 content: {
            //                     template: `
            //                         <select class="form-select" value=":selected" eazy-select2-active>
            //                             <option disabled>Pilih Gelombang</option>
            //                             @foreach($static_registration_periods as $registration_period)
            //                                 <option value="{{ $registration_period }}">{{ $registration_period }}</option>
            //                             @endforeach
            //                         </select>
            //                     `,
            //                     selected: wave,
            //                 },
            //             },
            //             registration_path: {
            //                 title: 'Jalur Masuk',
            //                 content: {
            //                     template: `
            //                         <select class="form-select" value=":selected" eazy-select2-active>
            //                             <option disabled>Pilih Jalur Masuk</option>
            //                             @foreach($static_registration_paths as $registration_path)
            //                                 <option value="{{ $registration_path }}">{{ $registration_path }}</option>
            //                             @endforeach
            //                         </select>
            //                     `,
            //                     selected: path,
            //                 },
            //             },
            //             study_system: {
            //                 title: 'Sistem Kuliah',
            //                 content: {
            //                     template: `
            //                         <select class="form-select" value=":selected" eazy-select2-active>
            //                             <option disabled>Pilih Sistem Kuliah</option>
            //                             @foreach($static_study_systems as $study_system)
            //                                 <option value="{{ $study_system }}">{{ $study_system }}</option>
            //                             @endforeach
            //                         </select>
            //                     `,
            //                     selected: study_system,
            //                 },
            //             },
            //             faculty: {
            //                 title: 'Fakultas',
            //                 content: {
            //                     template: `
            //                         <select class="form-select" value=":selected" eazy-select2-active>
            //                             <option disabled>Pilih Fakultas</option>
            //                             @foreach($static_faculties as $faculty)
            //                                 <option value="{{ $faculty }}">{{ $faculty }}</option>
            //                             @endforeach
            //                         </select>
            //                     `,
            //                     selected: faculty,
            //                 },
            //             },
            //             study_program: {
            //                 title: 'Program Studi',
            //                 content: {
            //                     template: `
            //                         <select class="form-select" value=":selected" eazy-select2-active>
            //                             <option disabled>Pilih Program Studi</option>
            //                             @foreach($static_study_programs as $study_program)
            //                                 <option value="{{ $study_program }}">{{ $study_program }}</option>
            //                             @endforeach
            //                         </select>
            //                     `,
            //                     selected: study_program,
            //                 },
            //             },
            //             invoice_component: {
            //                 title: 'Komponen Tagihan',
            //                 content: {
            //                     template: `
            //                         <select class="form-select" value=":selected" eazy-select2-active>
            //                             <option disabled>Pilih Komponen Tagihan</option>
            //                             @foreach($static_invoice_components as $invoice_component)
            //                                 <option value="{{ $invoice_component }}">{{ $invoice_component }}</option>
            //                             @endforeach
            //                         </select>
            //                     `,
            //                     selected: invoice_component,
            //                 },
            //             },
            //             rate: {
            //                 title: 'Nominal Tarif',
            //                 content: {
            //                     template: `<input type="number" name="rate" class="form-control" value=":value" />`,
            //                     value: rate,
            //                 },
            //             },
            //             instalment: {
            //                 title: 'Cicilan',
            //                 content: {
            //                     template: `
            //                         <select class="form-select" value=":selected" eazy-select2-active>
            //                             <option disabled>Pilih Skema Cicilan</option>
            //                             @foreach($static_installments as $installment)
            //                                 <option value="{{ $installment }}">{{ $installment }}</option>
            //                             @endforeach
            //                         </select>
            //                     `,
            //                     selected: installment,
            //                 },
            //             },
            //         },
            //         formSubmitLabel: 'Edit Tarif',
            //         callback: function() {
            //             // ex: reload table
            //             Swal.fire({
            //                 icon: 'success',
            //                 text: 'Berhasil mengupdate tarif',
            //             }).then(() => {
            //                 this.tableRef.reload()
            //             })
            //         },
            //     },
            // });
        },
        delete: function() {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin menghapus tarif ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    // ex: do ajax request
                    Swal.fire({
                        icon: 'success',
                        text: 'Berhasil menghapus tarif',
                    })
                }
            })
        },
        copy: function() {}
    }
</script>
@endsection
