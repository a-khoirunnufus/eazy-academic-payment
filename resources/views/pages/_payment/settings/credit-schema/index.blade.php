@extends('layouts.static_master')


@section('page_title', 'Setting Tagihan, Tarif, dan Pembayaran')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
    <style>
        ul#credit-schema-component-list {
            counter-reset: li-count;
            padding: 0;
            margin: 0;
        }
        ul#credit-schema-component-list li {
            counter-increment: li-count;
        }
        ul#credit-schema-component-list li:not(:last-child) {
            margin-bottom: 1rem;
        }
        ul#credit-schema-component-list li .index:before {
            content: 'Ke-' counter( li-count);
        }
    </style>
@endsection

@section('content')

@include('pages._payment.settings._shortcuts', ['active' => 'credit-schema'])

<div class="card">
    <table id="credit-schema-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Nama Skema</th>
                <th>X Kali Pembayaran</th>
                <th class="text-center">Status Validitas</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection


@section('js_section')
<script>
    /**
     * Modal Builder & Form Builder is on development
     */
    class ModalBuilder {
        // #modalId;
        #bsInstance;
        #modalBodyElm;

        constructor(modalId, modalTitle, modalSize) {
            // this.#modalId = modalId;
            this.#createModalElement(modalId, modalTitle, modalSize);
            this.#bsInstance = new bootstrap.Modal(document.getElementById(modalId));
            this.#modalBodyElm = document.querySelector(`#${modalId} .modal-body`);
        }

        #createModalElement(id, title, size) {
            const html = `
                <div class="modal fade" id="${id}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog modal-${size} -modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-white" style="padding: 2rem 3rem 3rem 3rem">
                                <h4 class="modal-title fw-bolder" id="mainModalLabel">${title}</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-3 pt-0"></div>
                        </div>
                    </div>
                </div>
            `;
            const elm = document.createElement('div');
            elm.innerHTML = html;
            document.body.appendChild(elm);
        }

        generateDetailHtml(fieldConfig) {
            let html = '<div class="d-flex flex-column" style="gap: 1.5rem">';

            for (const field of fieldConfig) {
                switch (field.type) {
                    case 'custom-field':
                        html += field.content;
                        break;

                    default:
                        const title = field.title;
                        let {template, ...embedValues} = field.content;

                        for(var x in embedValues) {
                            template = template.replace(':'+x, embedValues[x].escape());
                        }

                        if(field.isHidden) {
                            html += '';
                        } else {
                            html += `
                                <div>
                                    <div class="fw-bold" style="margin-bottom: .5rem">${title}</div>
                                    <div>${template}</div>
                                </div>
                            `;
                        }
                        break;
                }
            }

            html += '</div>';
            return html;
        }

        /**
         *
         * @param {string} content html string of modal body
         */
        renderBody({html, callbacks = []}) {
            this.#modalBodyElm.innerHTML = html;

            for (const call of callbacks) {
                call();
            }
        }

        show() {
            this.#bsInstance.show();
        }

        close(callback = null) {
            callback && callback();
            this.#bsInstance.hide();
        }
    }
    class FormBuilder {

        id;
        type;
        fieldConfig;
        isTwoColumn;
        actionUrl;
        submitLabel;
        afterSubmit;
        callbacks;

        constructor({id, type, fieldConfig, isTwoColumn = false, actionUrl, submitLabel, afterSubmit}) {
            this.id = id;
            this.type = type;
            this.fieldConfig = fieldConfig;
            this.isTwoColumn = isTwoColumn;
            this.actionUrl = actionUrl;
            this.submitLabel = submitLabel;
            this.afterSubmit = afterSubmit;
            this.callbacks = [];
        }

        /**
         * Generate Form Html
         *
         * TODO: make template for different field types
         *
         * @returns {string} html string of generated form
         */
        generateHtml() {
            const formTemplate = (content) => {
                return `<form id="${this.id}">
                    ${content}
                </form>`;
            }

            let formContentHtml = '';

            for (const field of this.fieldConfig) {
                switch (field.type) {
                    case 'custom-field':
                        formContentHtml += field.content;
                        if (this.callbacks.length == 0) {
                            this.callbacks = [field.callback];
                        } else {
                            this.callbacks = [...this.callbacks, field.callback];
                        }
                        break;

                    default:
                        const title = field.title ?? '';
                        let {template, ...embedValues} = field.content;

                        for(const embedKey in embedValues) {
                            template = template.replace(':'+embedKey, embedValues[embedKey].escape());
                        }

                        if(field.isHidden) {
                            formContentHtml += template;
                        } else {
                            formContentHtml += `
                                <div class="form-group">
                                    <label class="form-label-md">${title}</label>
                                    ${template}
                                </div>
                            `;
                        }
                        break;
                }
            }

            if (this.isTwoColumn) {
                formContentHtml = `
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem">
                        ${formContentHtml}
                    </div>
                `;
            } else {
                formContentHtml = `
                    <div style="display: flex; flex-direction: column; gap: 1.5rem">
                        ${formContentHtml}
                    </div>
                `;
            }

            const formActionHtml = `
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn ${this.type == 'add' ? 'btn-success' : 'btn-warning'} me-1">${this.submitLabel}</button>
                    <a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-outline-secondary">Batal</a>
                </div>
            `;

            return formTemplate(formContentHtml + formActionHtml);
        }

        /**
         * Generate Form Submit Handler
         *
         * @returns {Function} handler function
         */
        generateHandler(formId, formActionUrl, afterSubmit) {
            return () => {
                $('#'+formId).on('submit', async (e) => {
                    e.preventDefault();

                    try {
                        const formData = FormDataJson.toJson('#'+formId);

                        const data = await $.ajax({
                            async: true,
                            url: formActionUrl,
                            type:'POST',
                            data: formData,
                            dataType:'json',
                        });

                        if(data.success) {
                            _toastr.success(data.message, 'Success');
                            afterSubmit();
                        } else {
                            _toastr.error(data.message, 'Failed');
                        }

                    } catch (error) {
                        _toastr.error(error, 'Failed');
                    }

                });
            }
        }

        makeForm() {
            return {
                html: this.generateHtml(),
                callbacks: [...this.callbacks, this.generateHandler(this.id, this.actionUrl, this.afterSubmit)],
            };
        }
    }

    const CreditSchemaModal = new ModalBuilder('creditSchemaModal', 'Skema Cicilan', 'lg');
    let CreditSchemaFormAdd = undefined;

    $(function(){
        _creditSchemaTable.init();
    })

    const _creditSchemaTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#credit-schema-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/payment/settings/credit-schema/index',
                },
                columns: [
                    {
                        name: 'action',
                        data: 'cs_id',
                        orderable: false,
                        searchable: false,
                        render: (data) => {
                            return this.template.rowAction(data)
                        }
                    },
                    {
                        name: 'cs_name',
                        data: 'cs_name',
                        render: (data) => {
                            return this.template.defaultCell(data);
                        }
                    },
                    {
                        name: 'payment_count',
                        render: (data, _, row) => {
                            return this.template.defaultCell(row.credit_schema_detail.length, {postfix: ' Pembayaran'});
                        }
                    },
                    {
                        name: 'cs_valid',
                        data: 'cs_valid',
                        render: (data) => {
                            return this.template.badgeCell(
                                data == 'yes' ? 'Valid' :  'Tidak Valid',
                                data == 'yes' ? 'success' : 'danger',
                            );
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
                                    columns: [1, 2, 3]
                                }
                            },
                            {
                                extend: 'csv',
                                text: feather.icons['file-text'].toSvg({class: 'font-small-4 me-50'}) + 'Csv',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [1, 2, 3]
                                }
                            },
                            {
                                extend: 'excel',
                                text: feather.icons['file'].toSvg({class: 'font-small-4 me-50'}) + 'Excel',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [1, 2, 3]
                                }
                            },
                            {
                                extend: 'pdf',
                                text: feather.icons['clipboard'].toSvg({class: 'font-small-4 me-50'}) + 'Pdf',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [1, 2, 3]
                                }
                            },
                            {
                                extend: 'copy',
                                text: feather.icons['copy'].toSvg({class: 'font-small-4 me-50'}) + 'Copy',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [1, 2, 3]
                                }
                            }
                        ],
                    }
                ],
                initComplete: function() {
                    $('.invoice-component-actions').html(`
                        <div style="margin-bottom: 7px">
                            <button onclick="_creditSchemaTableActions.add()" class="btn btn-primary">
                                <span style="vertical-align: middle">
                                    <i data-feather="plus" style="width: 18px; height: 18px;"></i>&nbsp;&nbsp;
                                    Tambah Skema
                                </span>
                            </button>
                        </div>
                    `)
                    feather.replace()
                }
            })
        },
        template: {
            ..._datatableTemplates,
            rowAction: function(id) {
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a onclick="_creditSchemaTableActions.detail(event)" class="dropdown-item"><i data-feather="eye"></i>&nbsp;&nbsp;Detail</a>
                            <a onclick="_creditSchemaTableActions.edit(${id})" class="dropdown-item"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="_creditSchemaTableActions.delete(event)" class="dropdown-item"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const _creditSchemaTableActions = {
        tableRef: _creditSchemaTable,
        detail: function(event) {
            const data = this.tableRef.getRowData(event.currentTarget);

            const fieldConfig = [
                {
                    title: 'Nama Skema Cicilan',
                    content: {
                        template: ":value",
                        value: data.cs_name,
                    },
                },
                {
                    type: 'custom-field',
                    content: `
                        <table class="table table-bordered table-striped">
                            <thead>
                                <th>Bayar</th>
                                <th>Persen Pembayaran</th>
                                <th>Tenggat Pembayaran</th>
                            </thead>
                            <tbody>
                                ${
                                    data.credit_schema_detail.map(item => {
                                        return `
                                            <tr>
                                                <td>Ke-${item.csd_order}</td>
                                                <td>${item.csd_percentage} %</td>
                                                <td>${moment(item.csd_date).format('DD/MM/YYYY')}</td>
                                            </tr>
                                        `
                                    }).join('')
                                }
                            </tbody>
                        </table>
                    `,
                },
                {
                    title: 'Status Validitas',
                    content: {
                        template: `
                            <div class="badge bg-:bsColor" style="font-size: 1rem">:label</div>
                        `,
                        bsColor: data.cs_valid == 'yes' ? 'success' : 'danger',
                        label: data.cs_valid == 'yes' ? 'Valid' : 'Tidak Valid',
                    },
                },
            ];

            const renderable = {
                html: CreditSchemaModal.generateDetailHtml(fieldConfig),
            };
            CreditSchemaModal.renderBody(renderable);
            CreditSchemaModal.show();
        },
        add: function() {
            // Form not initialize yet
            if(!CreditSchemaFormAdd) {
                const formOptions = {
                    id: 'form-add-credit-schema',
                    type: 'add',
                    actionUrl: _baseURL+'/api/payment/settings/credit-schema/store',
                    fieldConfig: [
                        {
                            title: 'Nama Skema Cicilan',
                            content: {
                                template:
                                    `<input
                                        type="text"
                                        name="cs_name"
                                        class="form-control"
                                        placeholder="Masukkan nama skema cicilan"
                                    />`,
                            },
                        },
                        {
                            type: 'custom-field',
                            content: `
                                <div class="mb-2">
                                    <div class="mb-2 d-flex justify-content-end">
                                        <a id="btn-add-credit-schema-component" class="btn btn-primary">
                                            <i data-feather="plus"></i>&nbsp;&nbsp;Tambah Komponen
                                        </a>
                                    </div>
                                    <div id="credit-schema-component" class="d-flex flex-column" style="gap: 1rem">
                                        <div class="d-flex" style="gap: 1rem">
                                            <div class="w-10 fw-bold">Bayar</div>
                                            <div class="w-40 fw-bold">Persen Pembayaran</div>
                                            <div class="w-40 fw-bold">Tenggat Pembayaran</div>
                                            <div class="w-10 fw-bold text-center">Aksi</div>
                                        </div>
                                        <ul id="credit-schema-component-list">
                                            <li class="d-flex align-items-center" style="gap: 1rem">
                                                <div class="w-10 index"></div>
                                                <div class="input-group w-40">
                                                    <input type="number" name="csd_percentage[]" class="form-control" placeholder="Masukkan persentase" />
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                <div class="w-40">
                                                    <input type="text" name="csd_date[]" class="form-control flatpickr-basic" placeholder="Pilih tanggal" />
                                                </div>
                                                <div class="w-10 text-center">
                                                    <a class="btn-delete-credit-schema-component btn btn-danger btn-icon"><i data-feather="trash"></i></a>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            `,
                            callback: () => {
                                $('#form-add-credit-schema .btn-delete-credit-schema-component').click((e) => {
                                    $(e.currentTarget).parents('li').get(0).remove();
                                });

                                $('#form-add-credit-schema #btn-add-credit-schema-component').click(() => {
                                    $('#credit-schema-component-list').append(`
                                        <li class="d-flex align-items-center" style="gap: 1rem">
                                            <div class="w-10 index"></div>
                                            <div class="input-group w-40">
                                                <input type="number" name="csd_percentage[]" class="form-control" placeholder="Masukkan persentase" />
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <div class="w-40">
                                                <input type="text" name="csd_date[]" class="form-control flatpickr-basic" placeholder="Pilih tanggal" />
                                            </div>
                                            <div class="w-10 text-center">
                                                <a class="btn-delete-credit-schema-component btn btn-danger btn-icon"><i data-feather="trash"></i></a>
                                            </div>
                                        </li>
                                    `);

                                    $('#form-add-credit-schema .btn-delete-credit-schema-component').click((e) => {
                                        $(e.currentTarget).parents('li').get(0).remove();
                                    });

                                    feather.replace();
                                    $('.flatpickr-basic').flatpickr();
                                    select2Replace();
                                });

                                feather.replace();
                                $('.flatpickr-basic').flatpickr();
                                select2Replace();
                            }
                        },
                        {
                            title: 'Status Validitas',
                            content: {
                                template:`
                                    <select name="cs_valid" value="no" eazy-select2-active>
                                        <option value="no" selected>Tidak Valid</option>
                                        <option value="yes">Valid</option>
                                    </select>
                                `,
                            },
                        },
                    ],
                    submitLabel: 'Tambah Skema',
                    afterSubmit: () => {
                        this.tableRef.reload();
                        CreditSchemaModal.close();
                    }
                };
                CreditSchemaFormAdd = new FormBuilder(formOptions);
            }

            // Form already initialize
            CreditSchemaModal.renderBody(CreditSchemaFormAdd.makeForm());
            CreditSchemaModal.show();
        },
        edit: async function(id) {
            const data = await $.ajax({
                async: true,
                url: _baseURL+'/api/payment/settings/credit-schema/show/'+id,
                type: 'GET',
                dataType: 'json',
            });

            const formOptions = {
                id: 'form-edit-credit-schema',
                type: 'edit',
                actionUrl: _baseURL+'/api/payment/settings/credit-schema/update/'+id,
                fieldConfig: [
                    {
                        title: 'Nama Skema Cicilan',
                        content: {
                            template:
                                `<input
                                    type="text"
                                    name="cs_name"
                                    value=":value"
                                    class="form-control"
                                    placeholder="Masukkan nama skema cicilan"
                                />`,
                            value: data.cs_name

                        },
                    },
                    {
                        type: 'custom-field',
                        content: `
                            <div class="mb-2">
                                <div class="mb-2 d-flex justify-content-end">
                                    <a id="btn-add-credit-schema-component" class="btn btn-primary">
                                        <i data-feather="plus"></i>&nbsp;&nbsp;Tambah Komponen
                                    </a>
                                </div>
                                <div id="credit-schema-component" class="d-flex flex-column" style="gap: 1rem">
                                    <div class="d-flex" style="gap: 1rem">
                                        <div class="w-10 fw-bold">Bayar</div>
                                        <div class="w-40 fw-bold">Persen Pembayaran</div>
                                        <div class="w-40 fw-bold">Tenggat Pembayaran</div>
                                        <div class="w-10 fw-bold text-center">Aksi</div>
                                    </div>
                                    <ul id="credit-schema-component-list">
                                        ${
                                            data.credit_schema_detail.map(item => {
                                                return `
                                                    <li class="d-flex align-items-center" style="gap: 1rem">
                                                        <div class="w-10 index"></div>
                                                        <div class="input-group w-40">
                                                            <input type="number" name="csd_percentage[]" value="${item.csd_percentage}" class="form-control" placeholder="Masukkan persentase" />
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                        <div class="w-40">
                                                            <input type="text" name="csd_date[]" value="${item.csd_date}" class="form-control flatpickr-basic" placeholder="Pilih tanggal" />
                                                        </div>
                                                        <div class="w-10 text-center">
                                                            <a class="btn-delete-credit-schema-component btn btn-danger btn-icon"><i data-feather="trash"></i></a>
                                                        </div>
                                                    </li>
                                                `
                                            }).join('')
                                        }
                                    </ul>
                                </div>
                            </div>
                        `,
                        callback: () => {
                            $('#form-edit-credit-schema .btn-delete-credit-schema-component').click((e) => {
                                $(e.currentTarget).parents('li').get(0).remove();
                            });

                            $('#form-edit-credit-schema #btn-add-credit-schema-component').click(() => {
                                $('#credit-schema-component-list').append(`
                                    <li class="d-flex align-items-center" style="gap: 1rem">
                                        <div class="w-10 index"></div>
                                        <div class="input-group w-40">
                                            <input type="number" name="csd_percentage[]" class="form-control" placeholder="Masukkan persentase" />
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <div class="w-40">
                                            <input type="text" name="csd_date[]" class="form-control flatpickr-basic" placeholder="Pilih tanggal" />
                                        </div>
                                        <div class="w-10 text-center">
                                            <a class="btn-delete-credit-schema-component btn btn-danger btn-icon"><i data-feather="trash"></i></a>
                                        </div>
                                    </li>
                                `);

                                $('#form-edit-credit-schema .btn-delete-credit-schema-component').click((e) => {
                                    $(e.currentTarget).parents('li').get(0).remove();
                                });

                                feather.replace();
                                $('.flatpickr-basic').flatpickr();
                                select2Replace();
                            });

                            feather.replace();
                            $('.flatpickr-basic').flatpickr();
                            select2Replace();
                        }
                    },
                    {
                        title: 'Status Validitas',
                        content: {
                            template:`
                                <select name="cs_valid" selected="${data.cs_valid}" eazy-select2-active>
                                    <option value="no" ${ 'no' == data.cs_valid ? 'selected' : ''}>Tidak Valid</option>
                                    <option value="yes" ${ 'yes' == data.cs_valid ? 'selected' : ''}>Valid</option>
                                </select>
                            `,
                        },
                    },
                ],
                submitLabel: 'Edit Skema',
                afterSubmit: () => {
                    this.tableRef.reload();
                    CreditSchemaModal.close();
                }
            };
            const CreditSchemaFormEdit = new FormBuilder(formOptions);

            CreditSchemaModal.renderBody(CreditSchemaFormEdit.makeForm());
            CreditSchemaModal.show();
        },
        delete: async function(event) {
            const data = this.tableRef.getRowData(event.currentTarget)

            const confirmed = await _swalConfirmSync({
                title: 'Apakah anda yakin ?',
                text: 'Menghapus skema cicilan ' + data.cs_name
            })

            if(!confirmed)
                return

            $.post(
                _baseURL+'/api/payment/settings/credit-schema/delete/'+data.cs_id,
                {_method: 'DELETE'},
                (data) => {
                    _toastr.success('Berhasil menghapus skema cicilan', 'Success')
                    this.tableRef.reload()
                }
            ).fail((error) => {
                _responseHandler.generalFailResponse(error)
            });

        }
    }
</script>
@endsection
