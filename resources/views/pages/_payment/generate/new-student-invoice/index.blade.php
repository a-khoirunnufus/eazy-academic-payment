@extends('layouts.static_master')

@section('page_title', 'Generate Tagihan')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
    <link rel="stylesheet" href="{{ url('css/jstree-custom-table.css') }}" />
@endsection

@section('content')

@include('pages._payment.generate._shortcuts', ['active' => 'new-student-invoice'])

<div class="form-group" style="width: 300px; margin-bottom: 2rem !important;">
    <label class="form-label">Pilih Periode Tagihan</label>
    <select id="select-invoice-period" class="form-control select2">
        @foreach($invoice_periods as $period)
            <option
                value="{{ $period->school_year_code }}"
                {{ $current_period_code == $period->school_year_code ? 'selected' : '' }}
            >
                {{ $period->school_year_year }} Semester {{ $period->school_year_semester }}
            </option>
        @endforeach
    </select>
</div>

<div class="card">
    <table id="new-student-invoice-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Program Studi / Fakultas</th>
                <th>Jumlah Mahasiswa</th>
                <th>Total Tagihan</th>
                <th>Status Generate</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Modal Generate Tagihan -->
<div class="modal fade" id="generateInvoiceModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-white" style="padding: 2rem 3rem">
                <h4 class="modal-title fw-bolder" id="generateInvoiceModalLabel">Generate Tagihan Mahasiswa Baru</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 pt-0">
                <div class="d-flex flex-column mb-2" style="gap: 5px">
                    <small class="d-block">Periode Tagihan</small>
                    <span class="fw-bolder" id="info-invoice-period">N/A</span>
                </div>
                <div class="jstree-table-wrapper">
                    <div style="width: 1185px; margin: 0 auto;">
                        <div id="tree-table-header" class="d-flex align-items-center bg-light border-top border-start border-end" style="height: 40px; width: 1185px;">
                            <div style="width: 80px"></div>
                            <div class="flex-grow-1 fw-bolder text-uppercase" style="width: 619px">Scope</div>
                            <div class="fw-bolder text-uppercase" style="width: 200px">Status Generate</div>
                            <div class="fw-bolder text-uppercase" style="width: 284px">Status Komponen Tagihan</div>
                        </div>
                        <div id="tree-generate-invoice"></div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Batal</button>
                    <button onclick="" class="btn btn-primary">Generate</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js_section')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>

<script>
    const defaultPeriodCode = "{{ $current_period_code }}";

    $(function(){
        _newStudentInvoiceTable.init();

        $('#select-invoice-period').change(function() {
            _newStudentInvoiceTable.reload();
        });
    })

    const _newStudentInvoiceTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#new-student-invoice-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/payment/generate/new-student-invoice/index',
                    data: function(d) {
                        d.invoice_period_code = $('#select-invoice-period').val();
                    }
                },
                stateSave: false,
                columns: [
                    {
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: (data, _, row) => {
                            return this.template.rowAction(row.unit_type, row.unit_id);
                        }
                    },
                    {
                        name: 'unit_name',
                        data: 'unit_name',
                        orderable: false,
                        render: (data, _, row) => {
                            return this.template.buttonLinkCell(
                                data,
                                {link: `${_baseURL}/payment/generate/new-student-invoice/detail?invoice_period_code=${$('#select-invoice-period').val()}&scope=${row.unit_type}&${row.unit_type}_id=${row.unit_id}`},
                                {additionalClass: row.unit_type == 'studyprogram' ? 'ps-2' : ''}
                            );
                        }
                    },
                    {
                        name: 'student_count',
                        data: 'student_count',
                        searchable: false,
                        render: (data) => {
                            return this.template.defaultCell(data);
                        }
                    },
                    {
                        name: 'invoice_total_amount',
                        data: 'invoice_total_amount',
                        searchable: false,
                        render: (data) => {
                            return this.template.currencyCell(data);
                        }
                    },
                    {
                        name: 'generated_msg',
                        data: 'generated_msg',
                        searchable: true,
                        render: (data, _, row) => {
                            let bsColor = 'secondary';
                            if (row.generated_status == 'not_yet') {
                                bsColor = 'danger';
                            } else if (row.generated_status == 'partial') {
                                bsColor = 'warning';
                            } else if (row.generated_status == 'done') {
                                bsColor = 'success';
                            }

                            return this.template.badgeCell(data, bsColor, {centered: false});
                        }
                    },
                    {
                        title: 'Total Tagihan',
                        name: 'invoice_total_amount',
                        data: 'invoice_total_amount',
                        visible: false,
                    },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom:
                    '<"d-flex justify-content-between align-items-end header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"new-student-invoice-actions d-flex align-items-end">>' +
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
                                    columns: [1,2,3,4]
                                }
                            },
                            {
                                extend: 'csv',
                                text: feather.icons['file-text'].toSvg({class: 'font-small-4 me-50'}) + 'Csv',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [1,2,5,4]
                                }
                            },
                            {
                                extend: 'excel',
                                text: feather.icons['file'].toSvg({class: 'font-small-4 me-50'}) + 'Excel',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [1,2,5,4]
                                }
                            },
                            {
                                extend: 'pdf',
                                text: feather.icons['clipboard'].toSvg({class: 'font-small-4 me-50'}) + 'Pdf',
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
                                    columns: [1,2,5,4]
                                }
                            }
                        ],
                    }
                ],
                initComplete: function() {
                    $('.new-student-invoice-actions').html(`
                        <div style="margin-bottom: 7px">
                            <button onclick="TreeGenerate.openModal()" class="btn btn-primary">
                                Generate Tagihan
                            </button>
                        </div>
                    `)
                    feather.replace()
                }
            });
            this.implementSearchDelay();
        },
        template: {
            rowAction: function(unit_type, unit_id) {
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a onclick="_newStudentInvoiceTableActions.openDetail('${unit_type}', ${unit_id})" class="dropdown-item"><i data-feather="external-link"></i>&nbsp;&nbsp;Detail pada Unit ini</a>
                            <a onclick="_newStudentInvoiceTableActions.generate()" class="dropdown-item disabled" href="javascript:void(0);"><i data-feather="mail"></i>&nbsp;&nbsp;Generate pada Unit ini</a>
                            <a onclick="_newStudentInvoiceTableActions.delete()" class="dropdown-item disabled" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete pada Unit ini</a>
                        </div>
                    </div>
                `
            },
            defaultCell: _datatableTemplates.defaultCell,
            buttonLinkCell: _datatableTemplates.buttonLinkCell,
            currencyCell: _datatableTemplates.currencyCell,
            badgeCell: _datatableTemplates.badgeCell,
        }
    }

    const _newStudentInvoiceTableActions = {
        tableRef: _newStudentInvoiceTable,
        openDetail: function(unit_type, unit_id) {
            window.location.href = `${_baseURL}/payment/generate/new-student-invoice/detail`
                +`?invoice_period_code=${$('#select-invoice-period').val()}`
                +`&scope=${unit_type}`
                +`&${unit_type}_id=${unit_id}`;
        },
        generate: function() {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin generate tagihan pada unit ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#356CFF',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Generate',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    // ex: do ajax request
                    Swal.fire({
                        icon: 'success',
                        text: 'Berhasil generate tagihan',
                    })
                }
            })
        },
        delete: function() {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin menghapus tagihan pada unit ini?',
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
                        text: 'Berhasil menghapus tagihan',
                    })
                }
            })
        },
    }

    const GenerateInvoiceModal = new bootstrap.Modal(document.getElementById('generateInvoiceModal'));

    const TreeGenerate = {
        selector: '#tree-generate-invoice',
        openModal: function() {
            $('#generateInvoiceModal #info-invoice-period').text($("#select-invoice-period option:selected" ).text());
            this.initTree();
            $(this.selector).on("loaded.jstree", () => { this.appendColumn(this.selector) });
            $(this.selector).on("before_open.jstree", () => { this.appendColumn(this.selector) });
            GenerateInvoiceModal.show();
        },
        initTree: async function() {
            const {data} = await $.ajax({
                async: true,
                url: _baseURL+'/api/payment/generate/new-student-invoice/get-tree-generate-all?invoice_period_code='+$('#select-invoice-period').val(),
                type: 'get',
            });

            return $(this.selector).jstree({
                'core' : {
                    'data' : data.tree,
                    "themes":{
                        "icons":false
                    }
                },
                "checkbox" : {
                    "keep_selected_style" : false
                },
                "plugins" : [ "checkbox", "wholerow" ],
            });
        },
        appendColumn: function(selector) {
            $(selector+' .jstree-anchor').each(function() {
                if ($(this).children().length <= 2) {
                    const nodeId = $(this).parents('li').attr('id');
                    const node = $(selector).jstree('get_node', nodeId);
                    const children = $(this).children();
                    $(this).empty();
                    $(this).append(children.get(0));
                    $(this).append(children.get(1));
                    $(this).append(`<div class="text"><span>${node.text}</span></div>`);
                    $(this).append(`<div style="display: flex; justify-content: flex-end;">
                        <div style="width: 200px">${node.data.status_generated.text}</div>
                        <div style="width: 280px">${node.data.status_invoice_component == 'defined' ? 'Komponen Tagihan Belum Diset!' : ''}</div>
                    </div>`);
                }
            });
        }
    }

</script>
@endsection
