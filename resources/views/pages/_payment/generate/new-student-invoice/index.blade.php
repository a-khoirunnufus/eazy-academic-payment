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
                <div class="d-flex flex-row mb-2" style="gap: 1rem">
                    <button onclick="TreeGenerate.checkAll()" class="btn btn-outline-primary btn-sm"><i data-feather="check-square"></i>&nbsp;&nbsp;Check Semua</button>
                    <button onclick="TreeGenerate.uncheckAll()" class="btn btn-outline-primary btn-sm"><i data-feather="square"></i>&nbsp;&nbsp;Uncheck Semua</button>
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
                    <button onclick="GenerateInvoiceAction.main()" class="btn btn-primary">Generate</button>
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
                            return this.template.rowAction(row.unit_type, row.unit_id, row.generated_status);
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
            rowAction: function(unit_type, unit_id, generated_status) {
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a onclick="_newStudentInvoiceTableActions.openDetail('${unit_type}', ${unit_id})" class="dropdown-item"><i data-feather="external-link"></i>&nbsp;&nbsp;Detail pada Unit ini</a>
                            <a onclick="GenerateInvoiceAction.generateOneScope(event)" class="dropdown-item ${generated_status == 'done' ? 'disabled' : ''}" href="javascript:void(0);"><i data-feather="mail"></i>&nbsp;&nbsp;Generate pada Unit ini</a>
                            <a onclick="GenerateInvoiceAction.deleteOneScope(event)" class="dropdown-item ${generated_status == 'done' ? '' : 'disabled'}" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete pada Unit ini</a>
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

            if ($(this.selector).jstree(true)) {
                $(this.selector).jstree(true).destroy();
            }

            $(this.selector).jstree({
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
        },
        checkAll: function() {
            $(this.selector).jstree(true).check_all();
            this.appendColumn(this.selector);
        },
        uncheckAll: function() {
            $(this.selector).jstree(true).uncheck_all();
            this.appendColumn(this.selector);
        },
    }

    // generate new id from this variable
    let idGlobal = 0;

    const GenerateInvoiceAction = {
        getPaths: () => {
            const treeApi = $(TreeGenerate.selector).jstree(true);
            // get selected nodes
            const selected = treeApi.get_selected();
            // foreach selected node, select only leaf node
            const leafs = selected.filter(nodeId => treeApi.is_leaf(nodeId));
            // foreach leaf, get path
            const paths = leafs.map(nodeId => {
                return {
                    id: ++idGlobal,
                    pathString: treeApi.get_path(nodeId, '/', true),
                };
            });
            return paths;
        },
        getOptimizedPaths: (paths) => {
            const treeApi = $(TreeGenerate.selector).jstree(true);

            let optimizedPaths = [...paths];

            // Get parents of each leaf at each path.
            // Ex: ['1', '2', '3']
            const leafsParents = [];
            paths.forEach(path => {
                const pathArr = path.pathString.split('/');
                const leafParentId = pathArr[pathArr.length-2];
                if (!leafsParents.includes(leafParentId)) {
                    leafsParents.push(leafParentId);
                }
            });

            /**
             * Add childrenCount and hasPaths property for each parent.
             * Ex: [
             *  {id: '1', childrenCount: 2, hasPaths: [...] },
             *  {id: '2', childrenCount: 1, hasPaths: [...] },
             *  {id: '3', childrenCount: 3, hasPaths: [...] },
             * ]
             */
            const leafsParentsExt = leafsParents.map(id => {
                const filteredPaths = paths.filter(path => {
                    const pathArr = path.pathString.split('/');
                    const leafParentId = pathArr[pathArr.length-2];
                    return leafParentId == id
                });
                const childrenCount = treeApi.get_node(id).children.length;
                return { id, childrenCount, hasPaths: filteredPaths };
            });

            /**
             * If parent childrenCount = hasPaths.length, then delete all path
             * belong this parent and replace with new path(one path).
             * Example: from ['1/2/3', '1/2/4', '1/2/5'] to ['1/2']
             */
            leafsParentsExt.forEach(parent => {
                if (parent.hasPaths.length == parent.childrenCount) {
                    parent.hasPaths.forEach(path => {
                        optimizedPaths = optimizedPaths.filter(optPath => optPath.id != path.id);
                    });
                    const newPathArr = parent.hasPaths[0].pathString.split('/');
                    newPathArr.pop();
                    const newPath = newPathArr.join('/');
                    optimizedPaths.push({
                        id: ++idGlobal,
                        pathString: newPath,
                    });
                }
            });

            return optimizedPaths;
        },
        getProcessScope: (optimizedPaths) => {
            const treeApi = $(TreeGenerate.selector).jstree(true);

            // Add scope, faculty_id and studyprogram_id property for each optimized path.
            return optimizedPaths.map(path => {
                const pathArr = path.pathString.split('/');
                let scope = 'n/a';
                if(pathArr.length == 1) scope = 'faculty';
                if(pathArr.length == 2) scope = 'studyprogram';
                return {
                    ...path,
                    scope,
                    faculty_id: treeApi.get_node(pathArr[0]).data.obj_id,
                    studyprogram_id: treeApi.get_node(pathArr[1]).data?.obj_id,
                };
            });
        },
        /**
         * Main method for generate invoice, used in generate invoice in modal.
         */
        main: async function() {
            // check if there are selected items
            const treeApi = $(TreeGenerate.selector).jstree(true);
            if (treeApi.get_selected().length == 0) {
                _toastr.error('Silahkan pilih item yang ingin digenerate.', 'Belum Memilih!');
                return;
            }

            const confirmed = await _swalConfirmSync({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin generate tagihan?',
            });
            if(!confirmed) return;

            const paths = this.getPaths();
            const optimizedPaths = this.getOptimizedPaths(paths);
            const processScope = this.getProcessScope(optimizedPaths);

            const generateData = processScope.map(item => {
                return {
                    scope: item.scope,
                    faculty_id: item.faculty_id,
                    studyprogram_id: item.studyprogram_id,
                }
            });

            $.ajax({
                url: _baseURL+'/api/payment/generate/new-student-invoice/generate-by-scopes',
                type: 'post',
                data: {
                    invoice_period_code: $('#select-invoice-period').val(),
                    generate_data: generateData,
                },
                success: (res) => {
                    GenerateInvoiceModal.hide();
                    _toastr.success(res.message, 'Success');
                    _newStudentInvoiceTable.reload();
                }
            });
        },
        /**
         * Method for generate invoice by clicking on action column.
         */
        generateOneScope: async (e) => {
            const data = _newStudentInvoiceTable.getRowData(e.currentTarget);

            const confirmed = await _swalConfirmSync({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin generate tagihan?',
            });
            if(!confirmed) return;

            let requestData = { invoice_period_code: $('#select-invoice-period').val() };

            if(data.unit_type == 'faculty') {
                requestData.scope = 'faculty';
                requestData.faculty_id = data.unit_id;
            } else if(data.unit_type == 'studyprogram') {
                requestData.scope = 'studyprogram';
                requestData.faculty_id = data.faculty_id;
                requestData.studyprogram_id = data.unit_id;
            }

            $.ajax({
                url: _baseURL+'/api/payment/generate/new-student-invoice/generate-by-scope',
                type: 'post',
                data: requestData,
                success: (res) => {
                    _toastr.success(res.message, 'Success');
                    _newStudentInvoiceTable.reload();
                }
            });
        },
        /**
         * Method for delete invoice by clicking on action column.
         */
        deleteOneScope: async (e) => {
            const data = _newStudentInvoiceTable.getRowData(e.currentTarget);

            const confirmed = await _swalConfirmSync({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin menghapus tagihan?',
            });
            if(!confirmed) return;

            let requestData = { invoice_period_code: $('#select-invoice-period').val() };

            if(data.unit_type == 'faculty') {
                requestData.scope = 'faculty';
                requestData.faculty_id = data.unit_id;
            } else if(data.unit_type == 'studyprogram') {
                requestData.scope = 'studyprogram';
                requestData.faculty_id = data.faculty_id;
                requestData.studyprogram_id = data.unit_id;
            }

            $.ajax({
                url: _baseURL+'/api/payment/generate/new-student-invoice/delete-by-scope',
                type: 'post',
                data: requestData,
                success: (res) => {
                    _toastr.success(res.message, 'Success');
                    _newStudentInvoiceTable.reload();
                }
            });
        }
    }

</script>
@endsection
