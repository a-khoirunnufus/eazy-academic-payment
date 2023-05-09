@extends('layouts.static_master')


@section('page_title', 'Setting Tagihan, Tarif, dan Pembayaran')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"/>
    <style>
        ul#credit-schema-component-list {
            list-style: none;
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
        ul.installment-percentage-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        ul.installment-percentage-list li {
            padding: .5rem 0;
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
                <th>Frekuensi Pembayaran</th>
                <th>Persentase Cicilan</th>
                <th class="text-center">Status Validitas</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Credit Schema Detail Modal -->
<div class="modal fade" id="creditSchemaDetailModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="creditSchemaModalDetailLabel">Detail Template Cicilan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="credit-schema-detail" class="d-flex flex-column" style="gap: 1rem">
                    <div class="d-flex">
                        <div style="margin-right: 2rem">
                            <div class="form-label">Nama Skema Cicilan</div>
                            <h3 id="cs_name">...</h3>
                        </div>
                        <div>
                            <div class="form-label">Status Validitas</div>
                            <div id="cs_valid">...</div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Komponen Pembayaran</label>
                        <table id="schema-component" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Pembayaran</th>
                                    <th>Persen Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Credit Schema Form Modal -->
<div class="modal fade" id="creditSchemaFormModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="creditSchemaFormModalLabel">...</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="credit-schema-form">
                    <div class="row mb-1">
                        <div class="form-group">
                            <label class="form-label">Nama Skema Cicilan</label>
                            <input type="text" class="form-control" name="cs_name" placeholder="Masukkan nama skema cicilan" />
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="form-group">
                            <label class="form-label">Status Validitas</label>
                            <select name="cs_valid" class="form-select">
                                <option value="" disabled selected>Pilih status validitas</option>
                                <option value="no">Tidak Valid</option>
                                <option value="yes">Valid</option>
                            </select>
                        </div>
                    </div>
                    <div id="installment-component-group">
                        <label class="form-label">Komponen Cicilan</label>
                        <div class="border rounded p-1">
                            <div id="credit-schema-component" class="d-flex flex-column" style="gap: 1rem">
                                <div class="d-flex" style="gap: 1rem">
                                    <div class="fw-bold" style="width: 100px">Pembayaran</div>
                                    <div class="fw-bold flex-grow-1">Persen Pembayaran</div>
                                    <div class="fw-bold text-center" style="width: 50px">Aksi</div>
                                </div>
                                <ul id="credit-schema-component-list"></ul>
                            </div>
                            <div class="d-flex justify-content-end mt-2">
                                <a onclick="creditSchemaForm.addPaymentComponent()" id="btn-add-credit-schema-component" class="btn btn-sm btn-primary">
                                    <i data-feather="plus"></i>&nbsp;&nbsp;Tambah Komponen
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button role="button" data-bs-dismiss="modal" class="btn btn-outline-secondary me-1">Batal</button>
                <button onclick="creditSchemaActions.save()" id="btn-submit-credit-schema-form" class="btn btn-light">...</button>
            </div>
        </div>
    </div>
</div>

@endsection


@section('js_section')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script>
    $(function(){
        creditSchemaTable.init();
        datepicker.init();
    })

    const datepicker = {
        /**
         * Setup element as datepicker element
         */
        init: () => {
            // TODO: change to indonesian date format
            $('.daterange-single').datepicker({
                language: 'id',
                format: 'yyyy-mm-dd',
                todayHighlight: true,
                autoclose: true,
            });
        }
    }

    const creditSchemaTable = {
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
                            return this.template.defaultCell(row.credit_schema_detail.length, {postfix: ' Kali Pembayaran'});
                        }
                    },
                    {
                        name: 'percentage',
                        render: (data, _, row) => {
                            return (`
                                <ul class="installment-percentage-list">
                                    ${
                                        row.credit_schema_detail
                                            .map((item, i) => `<li>Cicilan ke-${i+1}: ${item.csd_percentage}%</li>`)
                                            .join('')
                                    }
                                </ul>
                            `);
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
                            <button onclick="creditSchemaActions.add()" class="btn btn-primary">
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
                            <a onclick="creditSchemaActions.detail(event)" class="dropdown-item"><i data-feather="eye"></i>&nbsp;&nbsp;Detail</a>
                            <a onclick="creditSchemaActions.edit(event)" class="dropdown-item"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="creditSchemaActions.delete(event)" class="dropdown-item"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const creditSchemaFormModal = new bootstrap.Modal(document.getElementById('creditSchemaFormModal'));
    const creditSchemaDetailModal = new bootstrap.Modal(document.getElementById('creditSchemaDetailModal'));

    const creditSchemaActions = {
        detail: (e) => {
            const data = creditSchemaTable.getRowData(e.currentTarget);

            $('#credit-schema-detail #cs_name').text(data.cs_name);

            $('#credit-schema-detail #cs_valid').html(
                data.cs_valid == 'yes' ?
                    `<div class="badge bg-success">Valid</div>`
                    : `<div class="badge bg-danger">Tidak Valid</div>`
            );

            $('#credit-schema-detail table#schema-component tbody').html(
                data.credit_schema_detail.map(item => {
                    return `
                        <tr>
                            <td>Ke-${item.csd_order}</td>
                            <td>${item.csd_percentage} %</td>
                        </tr>
                    `;
                }).join('')
            );

            creditSchemaDetailModal.show();
        },
        /**
         * Show credit schema modal with empty form
         */
        add: () => {
            creditSchemaFormModal.show();
            creditSchemaForm.clearForm();
            creditSchemaForm.setTitle('Tambah Skema Cicilan');
            creditSchemaForm.setActionBtn('add');
            creditSchemaTable.selected = null;
        },
        /**
         * Show credit schema modal with filled form
         */
        edit: (e) => {
            const data = creditSchemaTable.getRowData(e.currentTarget);
            // console.log(data); return;
            creditSchemaForm.clearForm();
            creditSchemaForm.setData(data);
            creditSchemaTable.selected = data;

            creditSchemaForm.setTitle("Edit Skema Cicilan");
            creditSchemaForm.setActionBtn('edit');
            creditSchemaFormModal.show();
        },
        /**
         * Peform ajax request to add new credit schema or update existing credit schema
         */
        save: () => {
            // get submit data from form
            let formData = FormDataJson.toJson("#credit-schema-form");

            // decide request method and url
            let url = _baseURL + '/api/payment/settings/credit-schema';
            if (creditSchemaTable.selected == null) {
                url = url + '/store';
            } else {
                url = url + '/update/' + creditSchemaTable.selected.cs_id;
                formData['_method'] = 'PUT';
            }

            // submit data
            $.post(
                url,
                formData,
                (data) => {
                    if(data.success){
                        creditSchemaFormModal.hide();
                        _toastr.success('Berhasil menyimpan data', 'Success');
                        creditSchemaTable.reload();
                    } else {
                        _toastr.error('Gagal menyimpan data', 'Failed')
                    }
                }
            )
            .fail((jqXHR) => {
                _responseHandler.formFailResponse(jqXHR)
            });
        },
        /**
         * Show confirmation and then perform ajax request to delete credit schema
         */
        delete: async (e) => {
            const data = creditSchemaTable.getRowData(e.currentTarget);

            const confirmed = await _swalConfirmSync({
                title: 'Apakah anda yakin ?',
                text: 'Menghapus template cicilan ' + data.cs_name
            });

            if(!confirmed) return;

            $.post(
                _baseURL + '/api/payment/settings/credit-schema/delete/' + data.cs_id,
                {_method: 'DELETE'},
                (data) => {
                    _toastr.success('Berhasil menghapus template cicilan', 'Success')
                    creditSchemaTable.reload()
                }
            ).fail((request) => {
                _responseHandler.generalFailResponse(request)
            });
        }
    }

    const creditSchemaForm = {
        /**
         * Clear form inputs value
         */
        clearForm: () => {
            $('#credit-schema-form input[name="cs_name"]').val('');
            $('#credit-schema-form select[name="cs_valid"]')
                .val($('#credit-schema-form select[name="cs_valid"] option:first').val())
                .trigger('change');
            $('#credit-schema-form #credit-schema-component-list')
                .html(creditSchemaTemplate.schemaComponentItem());
            feather.replace();
            datepicker.init();
        },
        setData: (data) => {
            $('#credit-schema-form input[name="cs_name"]').val(data.cs_name);
            $('#credit-schema-form select[name="cs_valid"]').val(data.cs_valid).trigger('change');
            $('#credit-schema-form #credit-schema-component-list').html(
                data.credit_schema_detail.map(item => {
                    return creditSchemaTemplate.schemaComponentItem(item.csd_percentage);
                }).join('')
            );
            feather.replace();
            datepicker.init();
        },
        /**
         * Set modal title
         */
        setTitle: (title) => {
            $("#creditSchemaFormModal .modal-title").html(title)
        },
        setActionBtn: (type) => {
            if (type == 'add') {
                $('#creditSchemaFormModal .modal-footer #btn-submit-credit-schema-form').attr('class', 'btn btn-success');
                $('#creditSchemaFormModal .modal-footer #btn-submit-credit-schema-form').text('Tambah');
            } else if (type == 'edit') {
                $('#creditSchemaFormModal .modal-footer #btn-submit-credit-schema-form').attr('class', 'btn btn-warning');
                $('#creditSchemaFormModal .modal-footer #btn-submit-credit-schema-form').text('Edit');
            } else {
                $('#creditSchemaFormModal .modal-footer #btn-submit-credit-schema-form').attr('class', 'btn btn-light');
                $('#creditSchemaFormModal .modal-footer #btn-submit-credit-schema-form').text('...');
            }
        },
        addPaymentComponent: () => {
            $('#credit-schema-component-list').append(creditSchemaTemplate.schemaComponentItem());
            feather.replace();
            datepicker.init();
        },
        removePaymentComponent: (e) => {
            $(e.currentTarget).parents('li').get(0).remove();
        },
    }

    const creditSchemaTemplate = {
        schemaComponentItem: (percentage = null) => `
            <li>
                <div class="d-flex align-items-start" style="gap: 1rem">
                    <div class="index" style="width: 100px"></div>
                    <div class="form-group flex-grow-1">
                        <div class="input-group">
                            <input type="number" name="csd_percentage[]" ${ percentage ? `value="${percentage}"` : '' } class="form-control" placeholder="Masukkan persentase" min="0" max="100" step="any" />
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="text-center" style="width: 50px">
                        <a onclick="creditSchemaForm.removePaymentComponent(event)" class="btn-delete-credit-schema-component btn btn-danger btn-icon"><i data-feather="trash"></i></a>
                    </div>
                </div>
            </li>
        `
    }
</script>
@endsection
