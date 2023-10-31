@extends('tpl.vuexy.master-payment')

@section('page_title', 'Penarikan Saldo Mahasiswa')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
@endsection

@section('content')

@include('pages._payment.students-balance._shortcuts', ['active' => 'withdraw'])

<div class="card">
    <div class="card-body">
        <div class="datatable-filter one-row">
            <div>
                <label class="form-label">Jumlah Penarikan</label>
                <select id="balance-amount-filter" class="form-select"></select>
            </div>
            <div>
                <label class="form-label">Diproses Oleh</label>
                <select id="issuer-filter" class="form-select"></select>
            </div>
            <div class="d-flex align-items-end">
                <button onclick="_withdrawHistoryTable.reload()" class="btn btn-info text-nowrap">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="my-2">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-add-withdraw">
        <i data-feather="plus"></i> &nbsp; Tambah Penarikan Saldo
    </button>
</div>

<div class="card">
    <table id="table-withdraw-history" class="table responsive table-striped nowrap" width="100%">
        <thead>
            <tr>
                <!-- <th>Aksi</th> -->
                <th>Nama / NIM</th>
                <th>Jumlah Penarikan</th>
                <th>Diproses Oleh</th>
                <th>Waktu</th>
                <th>File Terkait</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<div class="modal fade" id="modal-add-withdraw" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Penarikan Saldo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-add-withdraw" enctype="multipart/form-data" onsubmit="_withdrawActions.add(event)">
                <input type="hidden" name="student_id" />
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Mahasiswa <span class="text-danger">*</span></label>
                        <select id="select-students-balance-list" class="form-select" required>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Total Saldo Mahasiswa</label>
                        <input id="selected-student-total-balance" type="number" class="form-control" disabled />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jumlah Penarikan <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="sbw_amount" required />
                    </div>
                    <div class="form-group">
                        <label class="form-label">File Terkait</label>
                        <input type="file" class="form-control mb-1" name="sbw_related_files[]" multiple />
                    </div>
                    <div class="mt-2">
                        <small>
                            <span class="text-danger">*</span> Wajib diisi
                        </small>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: flex-start;">
                    <button type="submit" class="btn btn-info m-0">
                        Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-detail-withdraw" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Penarikan Saldo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-3">
                            Nama / NIM
                        </div>
                        <div class="col-1">:</div>
                        <div class="col-8">
                            Ahmad Khoirunnufus<br>
                            1301180069
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-3">
                            Jumlah Penarikan
                        </div>
                        <div class="col-1">:</div>
                        <div class="col-8">
                            Rp10.000.000,00
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-3">
                            Diproses Oleh
                        </div>
                        <div class="col-1">:</div>
                        <div class="col-8">
                            Admin
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-3">
                            Waktu
                        </div>
                        <div class="col-1">:</div>
                        <div class="col-8">
                            01/01/2001 10:00
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-3">
                            File Terkait
                        </div>
                        <div class="col-1">:</div>
                        <div class="col-8">
                            <a href="#" class="btn btn-link p-0">
                                Download File 1
                            </a><br>
                            <a href="#" class="btn btn-link p-0">
                                Download File 2
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- <div class="modal fade dtr-bs-modal show" style="display: block;" aria-modal="true" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title">Details of undefined</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <table class="table">
               <tbody>
                  <tr data-dt-row="8" data-dt-column="2">
                     <td>Product:</td>
                     <td>
                        <div class="d-flex justify-content-start align-items-center customer-name">
                           <div class="avatar-wrapper">
                              <div class="avatar me-2 rounded-2 bg-label-secondary"><img src="../../assets/img/ecommerce-images/product-9.png" alt="Product-9" class="rounded-2"></div>
                           </div>
                           <div class="d-flex flex-column"><span class="fw-medium text-nowrap">Air Jordan</span><small class="text-muted">Air Jordan is a line of basketball shoes produced by Nike</small></div>
                        </div>
                     </td>
                  </tr>
                  <tr data-dt-row="8" data-dt-column="3">
                     <td>Reviewer:</td>
                     <td>
                        <div class="d-flex justify-content-start align-items-center customer-name">
                           <div class="avatar-wrapper">
                              <div class="avatar me-2"><img src="../../assets/img/avatars/5.png" alt="Avatar" class="rounded-circle"></div>
                           </div>
                           <div class="d-flex flex-column"><a href="app-ecommerce-customer-details-overview.html"><span class="fw-medium">Gisela Leppard</span></a><small class="text-muted text-nowrap">gleppard8@yandex.ru</small></div>
                        </div>
                     </td>
                  </tr>
                  <tr data-dt-row="8" data-dt-column="4">
                     <td>Review:</td>
                     <td>
                        <div>
                           <div class="read-only-ratings ps-0 mb-2 jq-ry-container" readonly="readonly" style="width: 112px;">
                              <div class="jq-ry-group-wrapper">
                                 <div class="jq-ry-normal-group jq-ry-group">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-star-filled" width="20px" height="20px" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="gray">
                                       <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                       <path d="M8.243 7.34l-6.38 .925l-.113 .023a1 1 0 0 0 -.44 1.684l4.622 4.499l-1.09 6.355l-.013 .11a1 1 0 0 0 1.464 .944l5.706 -3l5.693 3l.1 .046a1 1 0 0 0 1.352 -1.1l-1.091 -6.355l4.624 -4.5l.078 -.085a1 1 0 0 0 -.633 -1.62l-6.38 -.926l-2.852 -5.78a1 1 0 0 0 -1.794 0l-2.853 5.78z" stroke-width="0"></path>
                                    </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-star-filled" width="20px" height="20px" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="gray" style="margin-left: 3px;">
                                       <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                       <path d="M8.243 7.34l-6.38 .925l-.113 .023a1 1 0 0 0 -.44 1.684l4.622 4.499l-1.09 6.355l-.013 .11a1 1 0 0 0 1.464 .944l5.706 -3l5.693 3l.1 .046a1 1 0 0 0 1.352 -1.1l-1.091 -6.355l4.624 -4.5l.078 -.085a1 1 0 0 0 -.633 -1.62l-6.38 -.926l-2.852 -5.78a1 1 0 0 0 -1.794 0l-2.853 5.78z" stroke-width="0"></path>
                                    </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-star-filled" width="20px" height="20px" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="gray" style="margin-left: 3px;">
                                       <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                       <path d="M8.243 7.34l-6.38 .925l-.113 .023a1 1 0 0 0 -.44 1.684l4.622 4.499l-1.09 6.355l-.013 .11a1 1 0 0 0 1.464 .944l5.706 -3l5.693 3l.1 .046a1 1 0 0 0 1.352 -1.1l-1.091 -6.355l4.624 -4.5l.078 -.085a1 1 0 0 0 -.633 -1.62l-6.38 -.926l-2.852 -5.78a1 1 0 0 0 -1.794 0l-2.853 5.78z" stroke-width="0"></path>
                                    </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-star-filled" width="20px" height="20px" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="gray" style="margin-left: 3px;">
                                       <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                       <path d="M8.243 7.34l-6.38 .925l-.113 .023a1 1 0 0 0 -.44 1.684l4.622 4.499l-1.09 6.355l-.013 .11a1 1 0 0 0 1.464 .944l5.706 -3l5.693 3l.1 .046a1 1 0 0 0 1.352 -1.1l-1.091 -6.355l4.624 -4.5l.078 -.085a1 1 0 0 0 -.633 -1.62l-6.38 -.926l-2.852 -5.78a1 1 0 0 0 -1.794 0l-2.853 5.78z" stroke-width="0"></path>
                                    </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-star-filled" width="20px" height="20px" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="gray" style="margin-left: 3px;">
                                       <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                       <path d="M8.243 7.34l-6.38 .925l-.113 .023a1 1 0 0 0 -.44 1.684l4.622 4.499l-1.09 6.355l-.013 .11a1 1 0 0 0 1.464 .944l5.706 -3l5.693 3l.1 .046a1 1 0 0 0 1.352 -1.1l-1.091 -6.355l4.624 -4.5l.078 -.085a1 1 0 0 0 -.633 -1.62l-6.38 -.926l-2.852 -5.78a1 1 0 0 0 -1.794 0l-2.853 5.78z" stroke-width="0"></path>
                                    </svg>
                                 </div>
                                 <div class="jq-ry-rated-group jq-ry-group" style="width: 38.3929%;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-star-filled" width="20px" height="20px" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="#f39c12">
                                       <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                       <path d="M8.243 7.34l-6.38 .925l-.113 .023a1 1 0 0 0 -.44 1.684l4.622 4.499l-1.09 6.355l-.013 .11a1 1 0 0 0 1.464 .944l5.706 -3l5.693 3l.1 .046a1 1 0 0 0 1.352 -1.1l-1.091 -6.355l4.624 -4.5l.078 -.085a1 1 0 0 0 -.633 -1.62l-6.38 -.926l-2.852 -5.78a1 1 0 0 0 -1.794 0l-2.853 5.78z" stroke-width="0"></path>
                                    </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-star-filled" width="20px" height="20px" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="#f39c12" style="margin-left: 3px;">
                                       <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                       <path d="M8.243 7.34l-6.38 .925l-.113 .023a1 1 0 0 0 -.44 1.684l4.622 4.499l-1.09 6.355l-.013 .11a1 1 0 0 0 1.464 .944l5.706 -3l5.693 3l.1 .046a1 1 0 0 0 1.352 -1.1l-1.091 -6.355l4.624 -4.5l.078 -.085a1 1 0 0 0 -.633 -1.62l-6.38 -.926l-2.852 -5.78a1 1 0 0 0 -1.794 0l-2.853 5.78z" stroke-width="0"></path>
                                    </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-star-filled" width="20px" height="20px" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="#f39c12" style="margin-left: 3px;">
                                       <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                       <path d="M8.243 7.34l-6.38 .925l-.113 .023a1 1 0 0 0 -.44 1.684l4.622 4.499l-1.09 6.355l-.013 .11a1 1 0 0 0 1.464 .944l5.706 -3l5.693 3l.1 .046a1 1 0 0 0 1.352 -1.1l-1.091 -6.355l4.624 -4.5l.078 -.085a1 1 0 0 0 -.633 -1.62l-6.38 -.926l-2.852 -5.78a1 1 0 0 0 -1.794 0l-2.853 5.78z" stroke-width="0"></path>
                                    </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-star-filled" width="20px" height="20px" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="#f39c12" style="margin-left: 3px;">
                                       <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                       <path d="M8.243 7.34l-6.38 .925l-.113 .023a1 1 0 0 0 -.44 1.684l4.622 4.499l-1.09 6.355l-.013 .11a1 1 0 0 0 1.464 .944l5.706 -3l5.693 3l.1 .046a1 1 0 0 0 1.352 -1.1l-1.091 -6.355l4.624 -4.5l.078 -.085a1 1 0 0 0 -.633 -1.62l-6.38 -.926l-2.852 -5.78a1 1 0 0 0 -1.794 0l-2.853 5.78z" stroke-width="0"></path>
                                    </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-star-filled" width="20px" height="20px" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="#f39c12" style="margin-left: 3px;">
                                       <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                       <path d="M8.243 7.34l-6.38 .925l-.113 .023a1 1 0 0 0 -.44 1.684l4.622 4.499l-1.09 6.355l-.013 .11a1 1 0 0 0 1.464 .944l5.706 -3l5.693 3l.1 .046a1 1 0 0 0 1.352 -1.1l-1.091 -6.355l4.624 -4.5l.078 -.085a1 1 0 0 0 -.633 -1.62l-6.38 -.926l-2.852 -5.78a1 1 0 0 0 -1.794 0l-2.853 5.78z" stroke-width="0"></path>
                                    </svg>
                                 </div>
                              </div>
                           </div>
                           <p class="h6 mb-1 text-truncate">Ut mauris</p>
                           <small class="text-break pe-3">Fusce consequat. Nulla nisl. Nunc nisl.</small>
                        </div>
                     </td>
                  </tr>
                  <tr data-dt-row="8" data-dt-column="5">
                     <td>Date:</td>
                     <td><span class="text-nowrap">Apr 20, 2020</span></td>
                  </tr>
                  <tr data-dt-row="8" data-dt-column="6">
                     <td>Status:</td>
                     <td><span class="badge bg-label-success" text-capitalize="">Published</span></td>
                  </tr>
                  <tr data-dt-row="8" data-dt-column="7">
                     <td>Actions:</td>
                     <td>
                        <div class="text-xxl-center">
                           <div class="dropdown">
                              <a href="javascript:;" class="btn dropdown-toggle hide-arrow text-body p-0" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></a>
                              <div class="dropdown-menu dropdown-menu-end">
                                 <a href="javascript:;" class="dropdown-item">Download</a><a href="javascript:;" class="dropdown-item">Edit</a><a href="javascript:;" class="dropdown-item">Duplicate</a>
                                 <div class="dropdown-divider"></div>
                                 <a href="javascript:;" class="dropdown-item delete-record text-danger">Delete</a>
                              </div>
                           </div>
                        </div>
                     </td>
                  </tr>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div> -->

@endsection

@section('js_section')
    <script>
        $(function() {
            _withdrawHistoryTable.init();
        })

        const _withdrawHistoryTable = {
            ..._datatable,
            init: function() {
                this.instance = $('#table-withdraw-history').DataTable({
                    ajax: {
                        url: _baseURL + '/api/payment/students-balance/withdraw-datatable',
                        data: function(d) {
                            d.withData = ['student', 'issuer'];

                            let filters = [];

                            const balanceAmountFilter = assignFilter('#balance-amount-filter');
                            if (balanceAmountFilter) {
                                const addFilters = balanceAmountFilter.split('&')
                                    .map(item => item.split('.'))
                                    .map(item => ({
                                        column: 'sbw_amount',
                                        operator: item[0],
                                        value: item[1]
                                    }));

                                filters = [...filters, ...addFilters];
                            }

                            if (filters.length > 0) {
                                d.filters = filters;
                            }
                        }
                    },
                    stateSave: false,
                    order: [],
                    columns: [
                        // 0 fullname
                        {
                            orderable: false,
                            searchable: false,
                            render: (data, _, row) => {
                                return this.template.listCell([
                                    {text: row.student.fullname, bold: true, small: false, nowrap: true},
                                    {text: row.student.student_id, bold: false, small: true, nowrap: true},
                                ]);
                            }
                        },
                        // 1
                        {
                            data: 'sbw_amount',
                            searchable: false,
                            render: (data) => {
                                return this.template.currencyCell(data);
                            }
                        },
                        // 2
                        {
                            data: 'issuer.user_fullname'
                            searchable: false,
                            orderable: false,
                        },
                        // 3
                        {
                            data: 'sbw_issued_time',
                            searchable: false,
                            render: (data) => {
                                return this.template.dateTimeCell(data);
                            }
                        },
                        // 4
                        {
                            data: 'sbw_related_files',
                            searchable: false,
                            orderable: false,
                            render: (data) => {
                                if (!data || data?.length == 0) return '-';

                                return `<div class="d-flex flex-column" style="gap: .5rem">
                                    ${data.map(item => (`
                                        <a href="#" class="btn btn-link p-0 d-block text-start">
                                            <i data-feather="file"></i> &nbsp;Download
                                        </a>
                                    `)).join('')}
                                </div>`
                            }
                        },
                        //5
                        { data: ''}
                    ],
                    responsive: false,
                    scrollX: true,
                    language: {
                        search: '_INPUT_',
                        searchPlaceholder: "Cari Data",
                        lengthMenu: '_MENU_',
                        paginate: { 'first': 'First', 'last': 'Last', 'next': 'Next', 'previous': 'Prev' },
                        processing: "Loading...",
                        emptyTable: "Tidak ada data",
                        infoEmpty:  "Menampilkan 0",
                        lengthMenu: "_MENU_",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                        infoFiltered: "(difilter dari _MAX_ entri)",
                        zeroRecords: "Tidak ditemukan data yang cocok"
                    },
                    dom: '<"d-flex justify-content-between align-items-center header-actions mx-0 row"' +
                        '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"custom-actions">>' +
                        '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
                        '>' +
                        'tr' +
                        '<"d-flex justify-content-between mx-2 row"' +
                        '<"col-sm-12 col-md-6"i>' +
                        '<"col-sm-12 col-md-6"p>' +
                        '>',
                    // buttons: _datatableBtnExportTemplate({
                    //     btnTypes: ['excel', 'csv'],
                    //     exportColumns: [4,5,6,7,8,9,10,11]
                    // }),
                    drawCallback: (settings) => {
                        feather.replace();
                    },
                    initComplete: () => {
                        $('.custom-actions').html(`
                            <h5 class="mb-0">Riwayat Penarikan Saldo</h5>
                        `);
                        // feather.replace();
                    }
                });

                this.implementSearchDelay();
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
                                <a onclick="#" class="dropdown-item"><i data-feather="eye"></i>&nbsp;&nbsp;Detail</a>
                            </div>
                        </div>
                    `
                },
            }
        }

        function assignFilter(selector, prefix = null, postfix = null) {
            let value = $(selector).val();

            if (value === '#ALL')
                return null;

            if (value)
                value = `${prefix ?? ''}${value}${postfix ?? ''}`;

            return value;
        }

        const _withdrawActions = {
            add: async (e) => {
                e.preventDefault();

                const confirmed = await _swalConfirmSync({
                    title: 'Konfirmasi',
                    text: 'Apakah anda yakin ingin menambahkan data?',
                });

                if(!confirmed) return;

                const formData = new FormData($('#form-add-withdraw').get(0));

                $.ajax({
                    url: `${_baseURL}/api/payment/students-balance/withdraw`,
                    type: 'post',
                    data: formData,
                    contentType: false,
                    processData: false,
                })
                .done(data => {
                    // console.log(data);
                    _toastr.success(data.message, 'Sukses');
                    $('#modal-add-withdraw').modal('hide');
                    _withdrawHistoryTable.reload();
                })
                .fail(jqXHR => {
                    // console.log(jqXHR);
                    _toastr.error(jqXHR.responseJSON.message, 'Gagal');
                    $('#modal-add-withdraw').modal('hide');
                });
            }
        }
    </script>
@endsection

@push('laravel-component-setup')
    <script>
        $(function() {
            setupFilters.balanceAmount();
            setupFilters.studentsBalanceList();
        });

        const setupFilters = {
            balanceAmount: async function() {
                const formatted = [
                    {id: '>.0&<=.10000000', text: 'Rp1,00 sampai Rp10.000.000,00'},
                    {id: '>.10000000&<=.100000000', text: 'Rp10.000.000,00 sampai Rp100.000.000,00'},
                    {id: '>.100000000', text: 'Lebih dari Rp100.000.000,00'},
                ];

                $('#balance-amount-filter').select2({
                    data: [
                        {id: '#ALL', text: "Semua Jumlah Penarikan"},
                        ...formatted,
                    ],
                    minimumResultsForSearch: 6,
                });
            },
            studentsBalanceList: async function() {
                $('#select-students-balance-list').select2({
                    ajax: {
                        url: `${_baseURL}/api/payment/students-balance`,
                        data: function (params) {
                            var query = {
                                search: params.term,
                                page: params.page || 1
                            }

                            // Query parameters will be ?search=[term]&page=[page]
                            return query;
                        },
                        delay: 500,
                        processResults: function (data, params) {
                            params.page = params.page || 1;

                            const formattedData = data.data.map(item => ({
                                id: item.student_id,
                                text: `${item.student_id} - ${item.fullname}`,
                                'data-balance': item.current_balance,
                            }));

                            return {
                                results: formattedData,
                                pagination: {
                                    more: (params.page * data.per_page) < data.total
                                }
                            };
                        },
                    },
                    placeholder: 'Pilih Mahasiswa',
                    minimumResultsForSearch: 6,
                    dropdownParent: $("#modal-add-withdraw")
                })
                .on('select2:select', function (e) {
                    const studentId = e.currentTarget.value;
                    $('#form-add-withdraw input[name="student_id"]').val(studentId);

                    const data = e.params.data;
                    const balance = data["data-balance"];
                    $('#selected-student-total-balance').val(balance);

                    $('#form-add-withdraw input[name="withdraw_amount"').attr('max', balance);
                })
                .val(0).trigger('change');
            },
        }
    </script>
@endpush
