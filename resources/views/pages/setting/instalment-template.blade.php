@extends('layouts.static_master')


@section('page_title', 'Setting Tagihan, Tarif, dan Pembayaran')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('content')

@include('pages.setting._shortcuts', ['active' => 'instalment-template'])

<div class="card">
    <table id="instalment-template-table" class="table table-striped">
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

<!-- Modal add installment scheme -->
<div class="modal fade" id="addInstallmentSchemeModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-white" style="padding: 2rem 3rem 3rem 3rem">
                <h4 class="modal-title fw-bolder" id="addInstallmentSchemeModalLabel">Tambah Skema Cicilan</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 pt-0">
                <form>
                    <div class="mb-2">
                        <label class="form-label-md">Nama Skema</label>
                        <input type="text" class="form-control" placeholder="Masukkan nama skema pembayaran" />
                    </div>
                    <div class="mb-2">
                        <div class="mb-2 d-flex justify-content-end">
                            <button class="btn btn-primary">
                                <i data-feather="plus"></i>&nbsp;&nbsp;Tambah Komponen
                            </button>
                        </div>
                        <div class="d-flex flex-column" style="gap: 1rem">
                            <div class="d-flex" style="gap: 1rem">
                                <div class="w-10 fw-bold">Bayar</div>
                                <div class="w-40 fw-bold">Persen Pembayaran</div>
                                <div class="w-40 fw-bold">Tenggat Pembayaran</div>
                                <div class="w-10 fw-bold text-center">Aksi</div>
                            </div>
                            <div class="d-flex align-items-center" style="gap: 1rem">
                                <div class="w-10">Ke-1</div>
                                <div class="w-40">
                                    <input type="number" class="form-control" />
                                </div>
                                <div class="w-40">
                                    <input type="text" class="form-control flatpickr-basic" />
                                </div>
                                <div class="w-10 text-center">
                                    <a class="btn btn-danger btn-icon"><i data-feather="trash"></i></a>
                                </div>
                            </div>
                            <div class="d-flex align-items-center" style="gap: 1rem">
                                <div class="w-10">Ke-2</div>
                                <div class="w-40">
                                    <input type="number" class="form-control" />
                                </div>
                                <div class="w-40">
                                    <input type="text" class="form-control flatpickr-basic" />
                                </div>
                                <div class="w-10 text-center">
                                    <a class="btn btn-danger btn-icon"><i data-feather="trash"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-success me-1">Tambah Skema</button>
                        <a data-bs-dismiss="modal" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal edit installment scheme -->
<div class="modal fade" id="editInstallmentSchemeModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-white" style="padding: 2rem 3rem 3rem 3rem">
                <h4 class="modal-title fw-bolder" id="editInstallmentSchemeModalLabel">Edit Skema Cicilan</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 pt-0">
                <form>
                    <div class="mb-2">
                        <label class="form-label-md">Nama Skema</label>
                        <input type="text" class="form-control" value="Cicilan 3X" placeholder="Masukkan nama skema pembayaran" />
                    </div>
                    <div class="mb-2">
                        <div class="mb-2 d-flex justify-content-end">
                            <button class="btn btn-primary">
                                <i data-feather="plus"></i>&nbsp;&nbsp;Tambah Komponen
                            </button>
                        </div>
                        <div class="d-flex flex-column" style="gap: 1rem">
                            <div class="d-flex" style="gap: 1rem">
                                <div class="w-10 fw-bold">Bayar</div>
                                <div class="w-40 fw-bold">Persen Pembayaran</div>
                                <div class="w-40 fw-bold">Tenggat Pembayaran</div>
                                <div class="w-10 fw-bold text-center">Aksi</div>
                            </div>
                            <div class="d-flex align-items-center" style="gap: 1rem">
                                <div class="w-10">Ke-1</div>
                                <div class="w-40">
                                    <input type="number" class="form-control" value="33" />
                                </div>
                                <div class="w-40">
                                    <input type="text" class="form-control flatpickr-basic" value="2023-04-10" />
                                </div>
                                <div class="w-10 text-center">
                                    <a class="btn btn-danger btn-icon"><i data-feather="trash"></i></a>
                                </div>
                            </div>
                            <div class="d-flex align-items-center" style="gap: 1rem">
                                <div class="w-10">Ke-2</div>
                                <div class="w-40">
                                    <input type="number" class="form-control" value="33" />
                                </div>
                                <div class="w-40">
                                    <input type="text" class="form-control flatpickr-basic" value="2023-04-10" />
                                </div>
                                <div class="w-10 text-center">
                                    <a class="btn btn-danger btn-icon"><i data-feather="trash"></i></a>
                                </div>
                            </div>
                            <div class="d-flex align-items-center" style="gap: 1rem">
                                <div class="w-10">Ke-3</div>
                                <div class="w-40">
                                    <input type="number" class="form-control" value="33" />
                                </div>
                                <div class="w-40">
                                    <input type="text" class="form-control flatpickr-basic" value="2023-04-10" />
                                </div>
                                <div class="w-10 text-center">
                                    <a class="btn btn-danger btn-icon"><i data-feather="trash"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-warning me-1">Edit Skema</button>
                        <a data-bs-dismiss="modal" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


@section('js_section')
<script>
    $(function(){
        _instalmentTemplateTable.init();
        $('.flatpickr-basic').flatpickr();
    })

    const _instalmentTemplateTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#instalment-template-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/dt/instalment-template',
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
                    {name: 'schema_name', data: 'schema_name'},
                    {
                        name: 'n_payment', 
                        data: 'n_payment',
                        render: (data) => {
                            return `${data} Pembayaran`
                        }
                    },
                    {
                        name: 'validity', 
                        data: 'validity',
                        render: (data) => {
                            var html = '<div class="d-flex justify-content-center">'
                            if (data.toLowerCase() == 'valid') {
                                html += '<div class="badge bg-success" style="font-size: inherit">Valid</div>'
                            } else {
                                html += '<div class="badge bg-danger" style="font-size: inherit">Tidak Valid</div>'
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
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addInstallmentSchemeModal">
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
            rowAction: function(id) {
                return `
                    <div class="dropdown d-flex justify-content-center">
                        <button type="button" class="btn btn-light btn-icon round dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical" style="width: 18px; height: 18px"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editInstallmentSchemeModal"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="_instalmentTemplateTableActions.delete()" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const addInstallmentSchemeModal = new bootstrap.Modal(document.getElementById('addInstallmentSchemeModal'));
    const editInstallmentSchemeModal = new bootstrap.Modal(document.getElementById('addInstallmentSchemeModal'));

    const _instalmentTemplateTableActions = {
        tableRef: _instalmentTemplateTable,
        add: function() {
            addInstallmentSchemeModal.show();
        },
        edit: function() {
            editInstallmentSchemeModal.show();
        },
        delete: function() {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin menghapus skema cician ini?',
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
                        text: 'Berhasil menghapus skema cician',
                    })
                }
            })
        }
    }
</script>
@endsection
