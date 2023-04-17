@extends('layouts.master')


@section('page_title', 'Kelola Kurikulum')
@section('sidebar-size', 'collapsed')
@section('url_back', '')


@section('content')
<div class="card">
    <div class="table-responsive">
        <table class="table table-striped" id="curriculum-table">
            <thead>
            <tr>
                <th width="100px" class="text-center">Action</th>
                <th width="200px">Nama Kurikulum</th>
                <th width="130px">Status Aktif</th>
                <th width="180px">Program Studi / Fakultas</th>
                <th width="500px">Dokumen</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="curriculum-modal" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Kurikulum</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="curriculum-form" onsubmit="return _curriculumActions.save()">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="form-label">Nama Kurikulum</label>
                            <input type="text" class="form-control" name="name" placeholder="Tulis Nama Kurikulum">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label">Program Studi</label>
                            <select name="studyprogram_id" class="form-control">
                                <option value="">Pilih Program Studi</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label">Tanggal Mulai Berlaku</label>
                            <input type="text" class="form-control daterange-single" name="applied_date" autocomplete="off" placeholder="Pilih Tanggal">
                        </div>
                    </div>
                    <h4 class="text-danger mt-3 mb-2 fw-bolder">Upload Dokumen</h4>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="form-label">Dokumen Buku Kurikulum</label>
                            <input type="file" id="book_document" class="my-pond"
                                    accept="application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document"/>
                            <p class="update-data-section" style="margin: 0; margin-top: -10px; font-size: 10px;">
                                <a class="document-preview text-decoration-underline fw-bold" href="javascript:void(0)" target="_blank">Berkas</a>
                                <span>sudah di upload, silahkan upload kembali untuk memperbarui berkas</span>
                            </p>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">Dokumen SK</label>
                            <input type="file" id="sk_document" class="my-pond"
                                    accept="application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document"/>
                            <p class="update-data-section" style="margin: 0; margin-top: -10px; font-size: 10px;">
                                <a class="document-preview text-decoration-underline fw-bold" href="javascript:void(0)" target="_blank">Berkas</a>
                                <span>sudah di upload, silahkan upload kembali untuk memperbarui berkas</span>
                            </p>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-6 form-group">
                            <label class="form-label">Dokumen Laporan</label>
                            <input type="file" id="report_document" class="my-pond"
                                    accept="application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document"/>
                            <p class="update-data-section" style="margin: 0; margin-top: -10px; font-size: 10px;">
                                <a class="document-preview text-decoration-underline fw-bold" href="javascript:void(0)" target="_blank">Berkas</a>
                                <span>sudah di upload, silahkan upload kembali untuk memperbarui berkas</span>
                            </p>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">Dokumen BA</label>
                            <input type="file" id="ba_document" class="my-pond"
                                    accept="application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document"/>
                            <p class="update-data-section" style="margin: 0; margin-top: -10px; font-size: 10px;">
                                <a class="document-preview text-decoration-underline fw-bold" href="javascript:void(0)" target="_blank">Berkas</a>
                                <span>sudah di upload, silahkan upload kembali untuk memperbarui berkas</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer pb-2" style="justify-content: flex-start;">
                    <button type="submit" class="btn btn-info">
                        Simpan Kurikulum
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@section('js_section')
<!-- Only load library when its needed -->
<!-- datpicker -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<!-- Filepond sources -->
<link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
<script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
<script src="https://unpkg.com/jquery-filepond/filepond.jquery.js"></script>
<!-- Include Filepond class wrapper for make your life eazier -->
<script src="{{ url('/plugins/filepond.js') }}?version={{ config('version.js_config') }}"></script>

<script>
    /**
     * @var {Object} FormDataJson https://github.com/brainfoolong/form-data-json
     */

    $(function(){
        _curriculumTable.init()
        _curriculumForm.uploader.init()
        _curriculumForm.initStudyProgramSearch()
        _datepicker.init()
    })

    const _datepicker = {
        /**
         * Setup element as datepicker element
         */
        init: () => {
            $('.daterange-single').datepicker({
                format: "yyyy-mm-dd",
                todayHighlight: true,
                autoclose: true
            })
        }
    }

    const _curriculumTable = {
        ... _datatable,

        selected: null,

        init: function(){
            this.instance =  $('#curriculum-table').DataTable({
                ajax: {
                    url: _baseURL + '/api/curriculum?' + $.param({
                        withData: ['studyprogram']
                    })
                },
                columns: [
                    { render: (data, __, row) => {
                        return `
                                <div class="nav-item dropdown dt-action-group">
                                    <a href="#" class="btn btn-light rounded-pill btn-icon" aria-expanded="false" data-bs-toggle="dropdown">
                                        <i class="mdi mdi-dots-vertical ficon-dropdown"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-start">
                                        <a href="javascript:void(0)" onclick="_curriculumActions.edit(this)" class="dropdown-item">
                                            <i class="me-50" data-feather="edit"></i> Ubah Data
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a href="javascript:void(0)" onclick="_curriculumActions.delete(this)" class="dropdown-item">
                                            <i class="me-50" data-feather="x"></i> Hapus Data
                                        </a>
                                    </div>
                                </div>
                            `
                    }, data: null, orderable: false, searchable: false, className: 'text-center' },
                    { render: (data, __, row) => {
                        return `
                            <a class="fw-bolder" href="#">
                                ${row.name}
                            </a>
                        `
                    }, data: 'name', name: 'name' },
                    { render: (data, __, row) => {
                        return moment(row.applied_date);
                    }, data: 'applied_date', name: 'applied_date' },
                    { render: (data, __, row) => {
                        return `
                            <p class="m-0">${row.studyprogram?.name ?? '-'}</p>
                        `
                    }, data:null, orderable: false, searchable: false },
                    { render: (data, __, row) => {
                        const docuComp = (title, data) => {
                            return `
                                <div class="col-6">
                                    <p class="fw-bolder">${title}</p>
                                    ${ data == null ? `
                                        <span style="color: #828282;">Berkas belum diupload<span>
                                    ` : `
                                        <a href="${data}" target="_blank" class="text-info">
                                            <i data-feather="paperclip"></i>
                                            <span>Lihat Dokumen</span>
                                        </a>
                                    ` }
                                </div>
                            `
                        }

                        return `
                            <div class="row p-0">
                                ${ docuComp('Dokumen Buku Kurikulum', row.book_document) }
                                ${ docuComp('Dokumen SK', row.sk_document) }
                            </div>
                            <div class="row mt-1 p-0">
                                ${ docuComp('Dokumen Laporan', row.report_document) }
                                ${ docuComp('Dokumen BA', row.ba_document) }
                            </div>
                        `
                    }, data: null, searchable: false, orderable: false }
                ],
                "drawCallback": function(settings) {
                    feather.replace();
                }
            })
            $('.dtb').append(`
                <button class="btn btn-info" onclick="_curriculumActions.add()">
                    <i data-feather="plus-circle"></i>
                    <span class="d-none d-lg-inline-block ml-2">Tambah Kurikulum</span>
                </button>
            `);
            this.implementSearchDelay()
        }
    }

    const _curriculumActions = {
        /**
         * Show curriculum modal with no input value on form
         */
        add: function(){
            $("#curriculum-modal").modal('show')
            $("#curriculum-modal .create-data-section").show()
            $("#curriculum-modal .update-data-section").hide()

            _curriculumForm.clearForm()
            _curriculumForm.setTitle("Tambah Kurikulum")
            _curriculumTable.selected = null
        },
        /**
         * Show curriculum modal with input value on form
         */
        edit: function(e){
            $("#curriculum-modal").modal('show')
            $("#curriculum-modal .create-data-section").hide()
            $("#curriculum-modal .update-data-section").show()

            _curriculumForm.clearForm()
            _curriculumForm.setTitle("Update Kurikulum")
            _curriculumTable.selected = _curriculumTable.getRowData(e)

            _curriculumForm.setData(_curriculumTable.selected)
        },
        /**
         * Peform ajax request to add new resource or update existing resource
         */
        save: function(){
            // get submit data from form
            let formRequest = FormDataJson.toJson("#curriculum-form")
            // remove unused field
            delete formRequest['filepond']
            // decide request method and url
            let url = _baseURL + '/api/curriculum'
            if(_curriculumTable.selected != null){
                url = url + '/' + _curriculumTable.selected.id
                formRequest['_method'] = 'PUT'
            }
            // append and validate document data
            let uploader = _curriculumForm.uploader
            for(const item of uploader.getDocuments()){
                if(!item.instance.allFileUploaded()){
                    _toastr.error('Anda sedang mengupload dokumen', 'Failed')
                    return false
                }

                formRequest[`${item.name}`] = item.instance.getFileId()
            }
            // submit data
            $.post(url, formRequest, function(data){
                if(data.success){
                    $("#curriculum-modal").modal('hide')
                    _toastr.success('Berhasil menyimpan data', 'Success')
                    _curriculumTable.reload()
                } else {
                    _toastr.error('Gagal menyimpan data', 'Failed')
                }
            }).fail(function(jqXHR){
                _responseHandler.formFailResponse(jqXHR)
            })

            return false
        },
        /**
         * Show confirmation and then perform ajax request to delete resource
         */
        delete: async function(e){
            const data = _curriculumTable.getRowData(e)

            const confirmed = await _swalConfirmSync({
                title: 'Apakah anda yakin ?',
                text: 'Menghapus kurikulum ' + data.name
            })
            if(!confirmed)
                return

            $.post(_baseURL + '/api/curriculum/' + data.id, {
                _method: 'DELETE'
            }, function(data){
                _toastr.success('Berhasil menghapus kurikulum', 'Success')
                _curriculumTable.reload()
            }).fail((error) => {
                _responseHandler.generalFailResponse(error)
            })
        }
    }

    const _curriculumForm = {
        uploader: {
            book: {
                name: 'book_document',
                title: 'Dokumen Buku Kurikulum',
                instance: null
            },
            sk: {
                name: 'sk_document',
                title: 'Dokumen SK',
                instance: null
            },
            ba: {
                name: 'ba_document',
                title: 'Dokumen BA',
                instance: null
            },
            report: {
                name: 'report_document',
                title: 'Dokumen Laporan',
                instance: null
            },
            init: function(){
                FilePond.registerPlugin(FilePondPluginFileValidateType)

                this.book.instance = new Filepond("#book_document")
                this.sk.instance = new Filepond("#sk_document")
                this.ba.instance = new Filepond("#ba_document")
                this.report.instance = new Filepond("#report_document")
            },
            getDocuments: function(){
                let list = []
                for(const item of Object.keys(this)){
                    if(typeof(this[item]) == 'function')
                        continue;

                    list.push(this[item])
                }

                return list
            }
        },
        /**
         * Clear form inputs value
         */
        clearForm: function(){
            FormDataJson.clear('#curriculum-form')
            $("#curriculum-form .select2").val('').trigger('change')
            // clear document uploader
            let uploader = _curriculumForm.uploader
            for(const item of uploader.getDocuments()){
                item.instance.clearInput()
            }
        },
        /**
         * Set form inputs value
         */
        setData: function(data){
            console.log({data})
            FormDataJson.fromJson("#curriculum-form", data)
            // set studyprogram
            $("#curriculum-form [name=studyprogram_id]").append($(`
                <option selected>${data.studyprogram?.name ?? '-'}</option>
            `).val(data.studyprogram_id)).trigger('change')
            // set document link
            let uploader = _curriculumForm.uploader
            for(const item of uploader.getDocuments() ){
                let el = `#curriculum-form #${item.name}`
                if(data[item.name] == null){
                    $(el).next().hide()
                } else {
                    $(el).next().show()
                    $(el).next().find(".document-preview").attr({
                        href: data[item.name].replace('amp;', '')
                    })
                }
            }
        },
        /**
         * Set modal title
         */
        setTitle: function(title){
            $("#curriculum-modal .modal-title").html(title)
        },
        /**
         * Setup select2 element for 'Program Studi' field
         */
        initStudyProgramSearch: function(){
            $("#curriculum-form [name=studyprogram_id]").select2(
                _select2AjaxWithDTOptions({
                    url: _baseURL + "/api/studyprogram",
                    searchColumns: ['name'],
                    item: (item) => {
                        return {
                            id: item.id,
                            text: `${item.name}`
                        }
                    },
                    dropdownParent: $('#curriculum-modal')
                })
            )
        }
    }
</script>
@endsection
