@extends('tpl.vuexy.master-payment')


@section('page_title', 'Setting')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('content')
<style>
    .nav.nav-custom {
        border-radius: 0 !important;
    }
    .nav.nav-custom .nav-link {
        text-align: left !important;
        justify-content: start !important;
    }

    .nav.nav-custom.nav-pills .nav-link {
        border-radius: 0 !important;
        white-space: nowrap;
        height: 4rem;
    }

    .nav.nav-custom.nav-pills .nav-link.active {
        border: unset;
        border-right: 4px solid #7367f0;
        border-radius: 0;
        box-shadow: unset;
        color: #7367f0 !important;
        background-color: unset !important;
    }

    .tab-content.tab-content-custom {
        width: -moz-available;
        width: -webkit-fill-available;
        width: fill-available;
    }
    .eazy-shortcut {
        display: flex;
        gap: 3rem;
    }
    .eazy-shortcut-item {
        display: flex;
        cursor: pointer;
    }
    .eazy-shortcut-item .eazy-shortcut-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 45px;
        height: 45px;
        padding: 0.5em 0;
        font-weight: 500;
        color: #565360;
        background-color: rgba(186, 191, 199, 0.12);
        border-radius: 0.35rem;
    }
    .eazy-shortcut-item.active .eazy-shortcut-icon {
        background-color: #356CFF;
    }
    .eazy-shortcut-item .eazy-shortcut-icon svg {
        width: 18px;
        height: 18px;
    }
    .eazy-shortcut-item.active .eazy-shortcut-icon svg {
        color: white;
    }
    .eazy-shortcut-item .eazy-shortcut-label {
        display: flex;
        align-items: center;
        margin-left: 1rem;
        color: #565360;
        font-weight: 500;
    }
    .eazy-shortcut-item.active .eazy-shortcut-label {
        color: #356CFF;
    }
</style>

<div class="eazy-shortcut mb-3">
    <div class="eazy-shortcut-item active">
        <div class="eazy-shortcut-icon">
            <i data-feather="settings"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Setting Komponen<br>Tagihan</span>
        </div>
    </div>
    <div class="eazy-shortcut-item">
        <div class="eazy-shortcut-icon">
            <i data-feather="settings"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Setting Template<br>Cicilan</span>
        </div>
    </div>
    <div class="eazy-shortcut-item">
        <div class="eazy-shortcut-icon">
            <i data-feather="settings"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Setting Tarif</span>
        </div>
    </div>
    <div class="eazy-shortcut-item">
        <div class="eazy-shortcut-icon">
            <i data-feather="settings"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Setting Formulir<br>Pendaftaran(PMB)</span>
        </div>
    </div>
    <div class="eazy-shortcut-item">
        <div class="eazy-shortcut-icon">
            <i data-feather="settings"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span class="eazy-stepper-label">Setting Aturan<br>Akademik</span>
        </div>
    </div>
</div>

<div class="card">
    <table id="invoice-component-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Kode Komponen</th>
                <th>Komponen Tagihan</th>
                <th class="text-center">Mahasiswa</th>
                <th class="text-center">Pendaftar</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- <div class="d-flex align-items-start bg-white shadow mb-2" style="border-radius: 6px">
    <div class="nav nav-custom flex-column nav-pills me-1 mb-0" id="v-pills-tab" role="tablist" aria-orientation="vertical"
        style="box-shadow: inset -2px 0px 0px 0px gainsboro;">
        <button class="nav-link active" role="tab" data-bs-toggle="tab"
            data-bs-target="#transaction-type-tabpanel">
            Jenis Transaksi
        </button>
        <button class="nav-link" role="tab" data-bs-toggle="tab"
            data-bs-target="#transaction-group-tabpanel">
            Kelompok Transaksi
        </button>
        <button class="nav-link" role="tab" data-bs-toggle="tab"
            data-bs-target="#pay-period-tabpanel">
            Periode Bayar
        </button>
        <button class="nav-link" role="tab" data-bs-toggle="tab"
            data-bs-target="#invoice-component-tabpanel">
            Komponen Tagihan
        </button>
        <button class="nav-link" role="tab" data-bs-toggle="tab"
            data-bs-target="#empty-tabpanel">
            Template Cicilan
        </button>
        <button class="nav-link" role="tab" data-bs-toggle="tab"
            data-bs-target="#empty-tabpanel">
            Tarif
        </button>
        <button class="nav-link" role="tab" data-bs-toggle="tab"
            data-bs-target="#empty-tabpanel">
            Tarif per Matakuliah
        </button>
        <button class="nav-link" role="tab" data-bs-toggle="tab"
            data-bs-target="#empty-tabpanel">
            Formulir Pendaftaran (PMB)
        </button>
        <button class="nav-link" role="tab" data-bs-toggle="tab"
            data-bs-target="#empty-tabpanel">
            Aturan Akademik
        </button>
    </div>
    <div class="tab-content tab-content-custom p-1" id="v-pills-tabContent">
        <div class="tab-pane fade show active" id="transaction-type-tabpanel" role="tabpanel" tabindex="0">
            <table id="transaction-type-table" class="table">
                <thead>
                    <tr>
                        <th class="text-center">Aksi</th>
                        <th>Kode Transaksi</th>
                        <th>Nama Transaksi</th>
                        <th>Format Transaksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="tab-pane fade" id="transaction-group-tabpanel" role="tabpanel" tabindex="0">
            <table id="transaction-group-table" class="table">
                <thead>
                    <tr>
                        <th>Kode Transaksi</th>
                        <th>Nama Transaksi</th>
                        <th>Jenis Transaksi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="tab-pane fade" id="pay-period-tabpanel" role="tabpanel" tabindex="0">
            <table id="pay-period-table" class="table">
                <thead>
                    <tr>
                        <th class="text-center">Aksi</th>
                        <th>Kode Transaksi</th>
                        <th>Nama Transaksi</th>
                        <th>Frekuensi Bayar(Hari)</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="tab-pane fade" id="invoice-component-tabpanel" role="tabpanel" tabindex="0">
            <table id="invoice-component-table" class="table">
                <thead>
                    <tr>
                        <th>Kode Komponen</th>
                        <th>Komponen Tagihan</th>
                        <th>Mahasiswa</th>
                        <th>Pendaftar</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="tab-pane fade" id="empty-tabpanel" role="tabpanel" tabindex="0">
            ...
        </div>
    </div>
</div> -->

<!-- STEPPER STYLE -->
<!-- <div class="bs-stepper wizard-numbered mb-2">
    <div class="bs-stepper-header" style="overflow-x: auto">
        <div class="step" data-target="#transaction-type-step-content">
            <button type="button" class="step-trigger">
                <span class="bs-stepper-circle">
                    <i data-feather="file-text"></i>
                </span>
                <span class="bs-stepper-label">
                    <span class="bs-stepper-title">Jenis Transaksi</span>
                    <span class="bs-stepper-subtitle">Atur Jenis dan Format Transaksi</span>
                </span>
            </button>
        </div>
        <div class="line">
            <i data-feather="chevron-right"></i>
        </div>
        <div class="step" data-target="#transaction-group-step-content">
            <button type="button" class="step-trigger">
                <span class="bs-stepper-circle">
                    <i data-feather="file-text"></i>
                </span>
                <span class="bs-stepper-label">
                    <span class="bs-stepper-title">Kelompok Komponen Tagihan</span>
                    <span class="bs-stepper-subtitle">Atur Jenis Per Kelompok Transaksi</span>
                </span>
            </button>
        </div>
        <div class="line">
            <i data-feather="chevron-right"></i>
        </div>
        <div class="step" data-target="#pay-period-step-content">
            <button type="button" class="step-trigger">
                <span class="bs-stepper-circle">
                    <i data-feather="file-text"></i>
                </span>
                <span class="bs-stepper-label">
                    <span class="bs-stepper-title">Periode Bayar Transaksi</span>
                    <span class="bs-stepper-subtitle">Atur Frekuensi Periode Bayar</span>
                </span>
            </button>
        </div>
        <div class="line">
            <i data-feather="chevron-right"></i>
        </div>
        <div class="step" data-target="#invoice-component-step-content">
            <button type="button" class="step-trigger">
                <span class="bs-stepper-circle">
                    <i data-feather="file-text"></i>
                </span>
                <span class="bs-stepper-label">
                    <span class="bs-stepper-title">Setting Komponen Tagihan</span>
                    <span class="bs-stepper-subtitle">Atur Jenis Tagihan dan Transaksi</span>
                </span>
            </button>
        </div>
        <div class="line">
            <i data-feather="chevron-right"></i>
        </div>
        <div class="step" data-target="#setting-biaya-transaksi">
            <button type="button" class="step-trigger">
                <span class="bs-stepper-circle">
                    <i data-feather="file-text"></i>
                </span>
                <span class="bs-stepper-label">
                    <span class="bs-stepper-title">Setting Biaya Transaksi</span>
                    <span class="bs-stepper-subtitle">Atur Tarif Tagihan</span>
                </span>
            </button>
        </div>
    </div>

    <div class="bs-stepper-content">
        <div id="transaction-type-step-content" class="content">

        </div>

        <div id="transaction-group-step-content" class="content">

        </div>

        <div id="pay-period-step-content" class="content">

        </div>

        <div id="invoice-component-step-content" class="content">

        </div>

        <div id="setting-biaya-transaksi" class="content">

        </div>
    </div>
</div> -->
@endsection


@section('js_section')
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
    // STEPPER STYLE SCIRPTS
    // const wizardNumbered = document.querySelector(".wizard-numbered");
    // if (typeof wizardNumbered !== undefined && wizardNumbered !== null) {
    //     const numberedStepper = new Stepper(wizardNumbered, {
    //         linear: false
    //     });
    // }
</script>

<script>
    $(function(){
        // TransactionTypeTable.init()
        // TransactionGroupTable.init()
        // PayPeriodTable.init()
        InvoiceComponentTable.init()
    })

    // const TransactionTypeTable = {
    //     state: {
    //         editId: null,
    //     },
    //     ..._datatable,
    //     init: function() {
    //         this.instance = $('#transaction-type-table').DataTable({
    //             serverSide: true,
    //             ajax: {
    //                 url: _baseURL+'/api/dt/transaction-type',
    //             },
    //             columns: [
    //                 {
    //                     name: 'action',
    //                     data: 'id',
    //                     orderable: false,
    //                     render: (data, _, row) => {
    //                         return this.template.rowAction(data)
    //                     }
    //                 },
    //                 {name: 'code', data: 'code'},
    //                 {
    //                     name: 'name',
    //                     data: 'name',
    //                     render: (data, _, row) => {
    //                         if(row.id == this.state.editId) {
    //                             return this.template.textInputCell('name', data)
    //                         } else {
    //                             return data
    //                         }
    //                     }
    //                 },
    //                 {
    //                     name: 'format',
    //                     data: 'format',
    //                     render: (data, _, row) => {
    //                         if(row.id == this.state.editId) {
    //                             return this.template.textInputCell('format', data)
    //                         } else {
    //                             return data
    //                         }
    //                     }
    //                 },
    //             ],
    //             drawCallback: function(settings) {
    //                 feather.replace();
    //             },
    //             dom:
    //                 '<"d-flex justify-content-between align-items-center header-actions mx-0 row"' +
    //                 '<"col-sm-12 col-lg-4 d-flex justify-content-center justify-content-lg-start" <"transaction-type-actions">>' +
    //                 '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
    //                 '>t' +
    //                 '<"d-flex justify-content-between mx-2 row"' +
    //                 '<"col-sm-12 col-md-6"i>' +
    //                 '<"col-sm-12 col-md-6"p>' +
    //                 '>',
    //             initComplete: function() {
    //                 $('.transaction-type-actions').html('<h5>Daftar Jenis Transaksi</h5>')
    //             },
    //         })
    //     },
    //     template: {
    //         rowAction: function(id) {
    //             return `
    //                 <div class="dropdown d-flex justify-content-center">
    //                     <button type="button" class="btn btn-outline-secondary btn-icon btn-sm round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
    //                         <i data-feather="more-vertical"></i>
    //                     </button>
    //                     <div class="dropdown-menu">
    //                         ${
    //                             TransactionTypeTable.state.editId != id ?
    //                                 '<a onclick="TransactionTypeTableActions.openEdit(event)" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>'
    //                                 : '<a onclick="TransactionTypeTableActions.closeEdit()" class="dropdown-item" href="javascript:void(0);"><i data-feather="x"></i>&nbsp;&nbsp;Close Edit</a>'
    //                         }
    //                         <a onclick="TransactionTypeTableActions.delete()" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
    //                     </div>
    //                 </div>
    //             `
    //         },
    //         textInputCell: function(name, value) {
    //             return `
    //                 <div class="input-group">
    //                     <input type="text" class="form-control" name="${name}" value="${value}">
    //                     <button onclick="TransactionTypeTableActions.update(event)" class="btn btn-primary" type="button">
    //                         <i data-feather="save"></i>
    //                     </button>
    //                 </div>
    //             `
    //         }
    //     }
    // }

    // const TransactionTypeTableActions = {
    //     tableRef: TransactionTypeTable,
    //     openEdit: function(e) {
    //         const row = this.tableRef.instance.row($(e.currentTarget).closest('tr'));
    //         const oldData = row.data()
    //         this.tableRef.state.editId = oldData.id
    //         this.tableRef.reload();
    //     },
    //     closeEdit: function() {
    //         this.tableRef.state.editId = null
    //         this.tableRef.reload()
    //     },
    //     update: function(e) {
    //         const key = $(e.currentTarget).prev('input')[0].name
    //         const value = $(e.currentTarget).prev('input')[0].value

    //         // $.ajax({
    //         //     url: _baseURL+'/transaction-type/'+this.tableRef.state.editId,
    //         //     type: 'put',
    //         //     data: {
    //         //         _token: _csrfToken,
    //         //         [key]: value
    //         //     }
    //         // })

    //         const request = new Promise((resolve, reject) => {
    //             setTimeout(() => {
    //                 resolve(value);
    //             }, 1000)
    //         });

    //         Swal.fire({
    //             title: 'Konfirmasi',
    //             text: "Anda yakin ingin mengupdate data?",
    //             icon: 'warning',
    //             showCancelButton: true,
    //             confirmButtonText: 'Update',
    //             cancelButtonText: 'Batal',
    //             confirmButtonClass: 'btn btn-warning',
    //             cancelButtonClass: 'btn btn-outline-secondary'
    //         }).then((result) => {
    //             if (result.isConfirmed) {
    //                 // ex: do ajax request
    //                 Swal.fire({
    //                     icon: 'success',
    //                     text: 'Berhasil mengupdate jenis transaksi',
    //                 }).then(() => {
    //                     console.log('update id: '+this.tableRef.state.editId+', key: '+key+', value: '+value)
    //                     this.tableRef.reload()
    //                 })
    //             }
    //         })
    //     },
    //     delete: function() {
    //         Swal.fire({
    //             title: 'Konfirmasi',
    //             text: 'Apakah anda yakin ingin menghapus jenis transaksi ini?',
    //             icon: 'warning',
    //             showCancelButton: true,
    //             confirmButtonColor: '#ea5455',
    //             cancelButtonColor: '#82868b',
    //             confirmButtonText: 'Hapus',
    //             cancelButtonText: 'Batal',
    //         }).then((result) => {
    //             if (result.isConfirmed) {
    //                 // ex: do ajax request
    //                 Swal.fire({
    //                     icon: 'success',
    //                     text: 'Berhasil menghapus jenis transaksi',
    //                 }).then(() => {
    //                     this.tableRef.reload()
    //                 })
    //             }
    //         })
    //     }
    // }

    // const TransactionGroupTable = {
    //     ..._datatable,
    //     init: function() {
    //         this.instance = $('#transaction-group-table').DataTable({
    //             serverSide: true,
    //             ajax: {
    //                 url: _baseURL+'/api/dt/transaction-group',
    //             },
    //             columns: [
    //                 {name: 'code', data: 'code'},
    //                 {name: 'name', data: 'name'},
    //                 {name: 'transaction_type', data: 'transaction_type_name'},
    //                 {
    //                     name: 'action',
    //                     data: 'id',
    //                     orderable: false,
    //                     render: (data, _, row) => {
    //                         return this.template.rowAction(data)
    //                     }
    //                 },
    //             ],
    //             drawCallback: function(settings) {
    //                 feather.replace();
    //             },
    //             dom:
    //                 '<"d-flex justify-content-between align-items-end header-actions mx-0 row mt-75"' +
    //                 '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"transaction-group-actions d-flex align-items-end">>' +
    //                 '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
    //                 '>t' +
    //                 '<"d-flex justify-content-between mx-2 row mb-1"' +
    //                 '<"col-sm-12 col-md-6"i>' +
    //                 '<"col-sm-12 col-md-6"p>' +
    //                 '>',
    //             initComplete: function() {
    //                 $('.transaction-group-actions').html(`
    //                     <div class="me-1" style="margin-bottom: 7px">
    //                         <label class="form-label">Jenis Transaksi</label>
    //                         <select class="form-select" style="width: 150px">
    //                             <option value="ALL">Semua</option>
    //                             <option value="DEP">Deposit</option>
    //                             <option value="INV">Tagihan</option>
    //                             <option value="PAY">Pembayaran</option>
    //                         </select>
    //                     </div>
    //                     <div style="margin-bottom: 7px">
    //                         <button onclick="TransactionGroupTableActions.add()" class="btn btn-primary">
    //                             <i data-feather="plus"></i>&nbsp;&nbsp;Tambah Jenis Transaksi
    //                         </button>
    //                     </div>
    //                 `)
    //                 feather.replace()
    //             }
    //         })
    //     },
    //     template: {
    //         rowAction: function(id) {
    //             return `
    //                 <div class="dropdown d-flex justify-content-center">
    //                     <button onclick="TransactionGroupTableActions.edit()" type="button" class="btn btn-warning btn-icon btn-sm round me-50">
    //                         <i data-feather="edit"></i>
    //                     </button>
    //                     <button onclick="TransactionGroupTableActions.delete()" type="button" class="btn btn-danger btn-icon btn-sm round">
    //                         <i data-feather="trash"></i>
    //                     </button>
    //                 </div>
    //             `
    //         }
    //     }
    // }

    // const TransactionGroupTableActions = {
    //     tableRef: TransactionGroupTable,
    //     add: function() {
    //         Modal.show({
    //             type: 'form',
    //             modalTitle: 'Tambah Kelompok Transaksi',
    //             config: {
    //                 formId: 'form-add-transaction-group',
    //                 formActionUrl: '#',
    //                 fields: {
    //                     transaction_type: {
    //                         title: 'Jenis Transaksi',
    //                         content: {
    //                             template: `
    //                                 <select name="transaction_type_code" class="form-select">
    //                                     <option selected disabled>Pilih Jenis Transaksi</option>
    //                                     <option value="DEP">Deposit</option>
    //                                     <option value="INV">Tagihan</option>
    //                                     <option value="PAY">Pembayaran</option>
    //                                 </select>
    //                             `
    //                         },
    //                     },
    //                     transaction_group_code: {
    //                         title: 'Kode Kelompok Transaksi',
    //                         content: {
    //                             template: '<input type="text" name="transaction_grup_code" class="form-control" placeholder="Tulis Kode">',
    //                         },
    //                     },
    //                     transaction_group_name: {
    //                         title: 'Nama Kelompok Transaksi',
    //                         content: {
    //                             template: '<input type="text" name="transaction_grup_name" class="form-control" placeholder="Nama Transaksi">',
    //                         }
    //                     },
    //                 },
    //                 formSubmitLabel: 'Tambah Data',
    //                 callback: function() {
    //                     // ex: reload table
    //                     Swal.fire({
    //                         icon: 'success',
    //                         text: 'Berhasil menambahkan kelompok transaksi',
    //                     }).then(() => {
    //                         this.tableRef.reload()
    //                     })
    //                 },
    //             },
    //         });
    //     },
    //     edit: function() {
    //         Modal.show({
    //             type: 'form',
    //             modalTitle: 'Edit Kelompok Transaksi',
    //             config: {
    //                 formId: 'form-edit-transaction-group',
    //                 formActionUrl: '#',
    //                 fields: {
    //                     transaction_type: {
    //                         title: 'Jenis Transaksi',
    //                         content: {
    //                             template: `
    //                                 <select name="transaction_type_code" class="form-select">
    //                                     <option disabled>Pilih Jenis Transaksi</option>
    //                                     <option value="DEP">Deposit</option>
    //                                     <option selected value="INV">Tagihan</option>
    //                                     <option value="PAY">Pembayaran</option>
    //                                 </select>
    //                             `
    //                         },
    //                     },
    //                     transaction_group_code: {
    //                         title: 'Kode Kelompok Transaksi',
    //                         content: {
    //                             template: '<input type="text" name="transaction_grup_code" class="form-control" value="INV-01">',
    //                         },
    //                     },
    //                     transaction_group_name: {
    //                         title: 'Nama Kelompok Transaksi',
    //                         content: {
    //                             template: '<input type="text" name="transaction_grup_name" class="form-control" value="Kelompok Tagihan 1">',
    //                         }
    //                     },
    //                 },
    //                 formSubmitLabel: 'Simpan Perubahan',
    //                 callback: function() {
    //                     // ex: reload table
    //                     Swal.fire({
    //                         icon: 'success',
    //                         text: 'Berhasil menghapus mengupdate kelompok transaksi',
    //                     }).then(() => {
    //                         this.tableRef.reload()
    //                     })
    //                 },
    //             },
    //         });
    //     },
    //     delete: function() {
    //         Swal.fire({
    //             title: 'Konfirmasi',
    //             text: 'Apakah anda yakin ingin menghapus kelompok transaksi ini?',
    //             icon: 'warning',
    //             showCancelButton: true,
    //             confirmButtonColor: '#ea5455',
    //             cancelButtonColor: '#82868b',
    //             confirmButtonText: 'Hapus',
    //             cancelButtonText: 'Batal',
    //         }).then((result) => {
    //             if (result.isConfirmed) {
    //                 // ex: do ajax request
    //                 Swal.fire({
    //                     icon: 'success',
    //                     text: 'Berhasil menghapus kelompok transaksi',
    //                 })
    //             }
    //         })
    //     }
    // }

    // const PayPeriodTable = {
    //     state: {
    //         editId: null,
    //     },
    //     ..._datatable,
    //     init: function() {
    //         this.instance = $('#pay-period-table').DataTable({
    //             serverSide: true,
    //             ajax: {
    //                 url: _baseURL+'/api/dt/pay-period',
    //             },
    //             columns: [
    //                 {
    //                     name: 'action',
    //                     data: 'id',
    //                     orderable: false,
    //                     render: (data, _, row) => {
    //                         return this.template.rowAction(data)
    //                     }
    //                 },
    //                 {name: 'code', data: 'code'},
    //                 {name: 'name', data: 'name'},
    //                 {
    //                     name: 'frequency',
    //                     data: 'frequency',
    //                     render: (data, _, row) => {
    //                         if(row.id == this.state.editId) {
    //                             return this.template.textInputCell('frequency', data)
    //                         } else {
    //                             return data
    //                         }
    //                     }
    //                 },
    //             ],
    //             drawCallback: function(settings) {
    //                 feather.replace();
    //             },
    //             dom:
    //                 '<"d-flex justify-content-between align-items-center header-actions mx-0 row mt-75"' +
    //                 '<"col-sm-12 col-lg-4 d-flex justify-content-center justify-content-lg-start" <"pay-period-actions">>' +
    //                 '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" flB> >' +
    //                 '>t' +
    //                 '<"d-flex justify-content-between mx-2 row mb-1"' +
    //                 '<"col-sm-12 col-md-6"i>' +
    //                 '<"col-sm-12 col-md-6"p>' +
    //                 '>',
    //             initComplete: function() {
    //                 $('.pay-period-actions').html('<h5>Daftar Jenis dan Frekuensi Bayar</h5>')
    //             },
    //         })
    //     },
    //     template: {
    //         rowAction: function(id) {
    //             return `
    //                 <div class="dropdown d-flex justify-content-center">
    //                     <button type="button" class="btn btn-outline-secondary btn-icon btn-sm round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
    //                         <i data-feather="more-vertical"></i>
    //                     </button>
    //                     <div class="dropdown-menu">
    //                         ${
    //                             PayPeriodTable.state.editId != id ?
    //                                 '<a onclick="PayPeriodTableActions.openEdit(event)" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>'
    //                                 : '<a onclick="PayPeriodTableActions.closeEdit()" class="dropdown-item" href="javascript:void(0);"><i data-feather="x"></i>&nbsp;&nbsp;Close Edit</a>'
    //                         }
    //                     </div>
    //                 </div>
    //             `
    //         },
    //         textInputCell: function(name, value) {
    //             return `
    //                 <div class="input-group">
    //                     <input type="number" class="form-control" name="${name}" value="${value}">
    //                     <button onclick="PayPeriodTableActions.update(event)" class="btn btn-primary" type="button">
    //                         <i data-feather="save"></i>
    //                     </button>
    //                 </div>
    //             `
    //         }
    //     }
    // }

    // const PayPeriodTableActions = {
    //     tableRef: PayPeriodTable,
    //     openEdit: function(e) {
    //         const row = this.tableRef.instance.row($(e.currentTarget).closest('tr'));
    //         const oldData = row.data()
    //         this.tableRef.state.editId = oldData.id
    //         this.tableRef.reload();
    //     },
    //     closeEdit: function() {
    //         this.tableRef.state.editId = null
    //         this.tableRef.reload()
    //     },
    //     update: function(e) {
    //         const key = $(e.currentTarget).prev('input')[0].name
    //         const value = $(e.currentTarget).prev('input')[0].value

    //         // $.ajax({
    //         //     url: _baseURL+'/transaction-type/'+this.tableRef.state.editId,
    //         //     type: 'put',
    //         //     data: {
    //         //         _token: _csrfToken,
    //         //         [key]: value
    //         //     }
    //         // })

    //         const request = new Promise((resolve, reject) => {
    //             setTimeout(() => {
    //                 resolve(value);
    //             }, 1000)
    //         });

    //         Swal.fire({
    //             title: 'Konfirmasi',
    //             text: "Anda yakin ingin mengupdate data?",
    //             icon: 'warning',
    //             showCancelButton: true,
    //             confirmButtonText: 'Update',
    //             cancelButtonText: 'Batal',
    //             confirmButtonClass: 'btn btn-warning',
    //             cancelButtonClass: 'btn btn-outline-secondary'
    //         }).then((result) => {
    //             if (result.isConfirmed) {
    //                 // ex: do ajax request
    //                 Swal.fire({
    //                     icon: 'success',
    //                     text: 'Berhasil mengupdate frekuensi bayar',
    //                 }).then(() => {
    //                     console.log('update id: '+this.tableRef.state.editId+', key: '+key+', value: '+value)
    //                     this.tableRef.reload()
    //                 })
    //             }
    //         })
    //     },
    // }

    const InvoiceComponentTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#invoice-component-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/dt/invoice-component',
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
                    {name: 'code', data: 'code'},
                    {name: 'name', data: 'name'},
                    {
                        name: 'student',
                        data: 'student',
                        render: (data, _, row) => {
                            var html = '<div class="d-flex justify-content-center">'
                            if(data) {
                                html += '<input class="form-check-input" type="checkbox" disabled checked>';
                            } else {
                                html += '<input class="form-check-input" type="checkbox" disabled>';
                            }
                            html += '</div>'
                            return html
                        }
                    },
                    {
                        name: 'registrant',
                        data: 'registrant',
                        render: (data, _, row) => {
                            var html = '<div class="d-flex justify-content-center">'
                            if(data) {
                                html += '<input class="form-check-input" type="checkbox" disabled checked>';
                            } else {
                                html += '<input class="form-check-input" type="checkbox" disabled>';
                            }
                            html += '</div>'
                            return html
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
                            <button onclick="InvoiceComponentTableActions.add()" class="btn btn-primary">
                                <i data-feather="plus"></i>&nbsp;&nbsp;Tambah Setting Komponen Tagihan
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
                            <a onclick="InvoiceComponentTableActions.edit()" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="InvoiceComponentTableActions.delete()" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const InvoiceComponentTableActions = {
        tableRef: InvoiceComponentTable,
        add: function() {
            Modal.show({
                type: 'form',
                modalTitle: 'Tambah Setting Komponen Tagihan',
                config: {
                    formId: 'form-add-invoice-component',
                    formActionUrl: '#',
                    fields: {
                        invoice_component: {
                            title: 'Komponen Tagihan',
                            content: {
                                template: `
                                    <select name="transaction_type_code" class="form-select">
                                        <option selected disabled>Pilih Komponen Tagihan</option>
                                        <option value="1">Komponen Tagihan 1</option>
                                        <option value="2">Komponen Tagihan 2</option>
                                        <option value="3">Komponen Tagihan 3</option>
                                    </select>
                                `
                            },
                        },
                        student: {
                            title: '',
                            content: {
                                template: `
                                    <input class="form-check-input" type="checkbox" value="">
                                    <label class="form-check-label">
                                        Mahasiswa
                                    </label>
                                `
                            },
                        },
                        registrant: {
                            title: '',
                            content: {
                                template: `
                                    <input class="form-check-input" type="checkbox" value="">
                                    <label class="form-check-label">
                                        Pendaftar
                                    </label>
                                `
                            },
                        },
                    },
                    formSubmitLabel: 'Tambah Data',
                    callback: function() {
                        // ex: reload table
                        Swal.fire({
                            icon: 'success',
                            text: 'Berhasil menambahkan setting komponen tagihan',
                        }).then(() => {
                            this.tableRef.reload()
                        })
                    },
                },
            });
        },
        edit: function() {
            Modal.show({
                type: 'form',
                modalTitle: 'Edit Setting Komponen Tagihan',
                config: {
                    formId: 'form-edit-transaction-group',
                    formActionUrl: '#',
                    fields: {
                        invoice_component: {
                            title: 'Komponen Tagihan',
                            content: {
                                template: `
                                    <select name="transaction_type_code" class="form-select">
                                        <option disabled>Pilih Komponen Tagihan</option>
                                        <option value="1" selected>CUTI</option>
                                        <option value="2">Komponen Tagihan 2</option>
                                        <option value="3">Komponen Tagihan 3</option>
                                    </select>
                                `
                            },
                        },
                        student: {
                            title: '',
                            content: {
                                template: `
                                    <input class="form-check-input" type="checkbox" value="" checked>
                                    <label class="form-check-label">
                                        Mahasiswa
                                    </label>
                                `
                            },
                        },
                        registrant: {
                            title: '',
                            content: {
                                template: `
                                    <input class="form-check-input" type="checkbox" value="">
                                    <label class="form-check-label">
                                        Pendaftar
                                    </label>
                                `
                            },
                        },
                    },
                    formSubmitLabel: 'Simpan Perubahan',
                    callback: function() {
                        // ex: reload table
                        Swal.fire({
                            icon: 'success',
                            text: 'Berhasil menghapus mengupdate setting komponen tagihan',
                        }).then(() => {
                            this.tableRef.reload()
                        })
                    },
                },
            });
        },
        delete: function() {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin menghapus setting komponen tagihan ini?',
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
                        text: 'Berhasil menghapus setting komponen tagihan',
                    })
                }
            })
        }
    }

</script>
@endsection
