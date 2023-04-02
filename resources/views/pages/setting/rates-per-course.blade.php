@extends('layouts.static_master')


@section('page_title', 'Setting Tarif Per Matakuliah')
@section('sidebar-size', 'collapsed')
@section('url_back', url('setting/rates'))

@section('css_section')
    <style>
        .rates-per-course-filter {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            grid-gap: 1rem;
        }
    </style>
@endsection

@section('content')

@include('pages.setting._shortcuts', ['active' => null])

<div class="card">
    <div class="card-body">
        <div class="d-flex flex-column" style="gap: 2rem">
            <div class="rates-per-course-filter" style="flex-grow: 1">
                <div>
                    <label class="form-label">Jenis</label>
                    <select class="form-select">
                        <option selected>Open this select menu</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">SKS</label>
                    <select class="form-select">
                        <option selected>Open this select menu</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Semester</label>
                    <select class="form-select">
                        <option selected>Open this select menu</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Wajib</label>
                    <select class="form-select">
                        <option selected>Open this select menu</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Paket</label>
                    <select class="form-select">
                        <option selected>Open this select menu</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>
            </div>
            <div>
                <button class="btn btn-primary">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="rates-per-course-table" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Matakuliah</th>
                <th>Jenis</th>
                <th>SKS</th>
                <th>Semester</th>
                <th>Wajib?</th>
                <th class="text-center">Paket?</th>
                <th>Nominal Tarif</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection


@section('js_section')
<script>
    $(function(){
        _ratesPerCourseTable.init()
    })

    const _ratesPerCourseTable = {
        ..._datatable,
        init: function() {
            this.instance = $('#rates-per-course-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL+'/api/dt/rates-per-course',
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
                    {name: 'number', data: 'id'},
                    {name: 'course_code', data: 'course_code'},
                    {name: 'course_name', data: 'course_name'},
                    {name: 'course_type', data: 'course_type'},
                    {name: 'sks', data: 'sks'},
                    {name: 'semester', data: 'semester'},
                    {name: 'mandatory', data: 'mandatory'},
                    {
                        name: 'is_package', 
                        data: 'is_package',
                        render: (data) => {
                            return `
                                <div class="d-flex justify-content-center">
                                    <input type="checkbox" class="form-check-input" ${data ? 'checked' : ''} disabled />
                                </div>
                            `
                        }
                    },
                    {name: 'rate', data: 'rate'},
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
                            <a onclick="_ratesPerCourseTableActions.edit()" class="dropdown-item" href="javascript:void(0);"><i data-feather="edit"></i>&nbsp;&nbsp;Edit</a>
                            <a onclick="_ratesPerCourseTableActions.delete()" class="dropdown-item" href="javascript:void(0);"><i data-feather="trash"></i>&nbsp;&nbsp;Delete</a>
                        </div>
                    </div>
                `
            }
        }
    }

    const _ratesPerCourseTableActions = {
        tableRef: _ratesPerCourseTable,
        edit: function() {
            Modal.show({
                type: 'form',
                modalTitle: 'Edit Tarif Matakuliah',
                config: {
                    formId: 'form-edit-rates-per-course',
                    formActionUrl: '#',
                    fields: {
                        course_code: {
                            title: 'Kode',
                            content: {
                                template: `<input type="text" class="form-control" value="BA081" disabled eazy-exclude-field />`
                            },
                        },
                        course_name: {
                            title: 'Nama Matakuliah',
                            content: {
                                template: `<input type="text" class="form-control" value="Matakuliah 1" disabled eazy-exclude-field />`
                            },
                        },
                        course_type: {
                            title: 'Jenis',
                            content: {
                                template: `<input type="text" class="form-control" value="Kuliah" disabled eazy-exclude-field />`
                            },
                        },
                        sks: {
                            title: 'SKS',
                            content: {
                                template: `<input type="number" class="form-control" value="2" disabled eazy-exclude-field />`
                            },
                        },
                        semester: {
                            title: 'Semester',
                            content: {
                                template: `<input type="number" class="form-control" value="1" disabled eazy-exclude-field />`
                            },
                        },
                        mandatory: {
                            title: 'Wajib?',
                            content: {
                                template: `<input type="text" class="form-control" value="W" disabled eazy-exclude-field />`
                            },
                        },
                        is_package: {
                            title: 'Paket?',
                            content: {
                                template: `<input type="text" class="form-control" value="Tidak" disabled eazy-exclude-field />`
                            },
                        },
                        rate: {
                            title: 'Nominal Tarif',
                            content: {
                                template: `<input name="rate" type="number" class="form-control" value="1000000" />`
                            },
                        },
                    },
                    formSubmitLabel: 'Edit Tarif',
                    callback: function() {
                        // ex: reload table
                        Swal.fire({
                            icon: 'success',
                            text: 'Berhasil mengupdate tarif',
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
    }
</script>
@endsection
