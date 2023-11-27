@extends('tpl.vuexy.master-payment')


@section('page_title', 'Mahasiswa Penerima Potongan')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
<style>
    .form-control.w-200,
    .form-select.w-200 {
        width: 200px !important;
    }
    .form-control.w-150,
    .form-select.w-150 {
        width: 150px !important;
    }

    table.dtr-details-custom td {
        padding: 10px 0;
    }
    table.dtr-details-custom td > * {
        padding-left: 0;
        padding-right: 0;
    }
    .dtr-bs-modal .modal-dialog {
        max-width: max-content;
    }
</style>
@endsection

@section('content')

@include('pages._payment.discount._shortcuts', ['active' => 'receiver'])

<div class="card">
    <div class="card-body">
        <div class="datatable-filter one-row">
            <x-select-option
                title="Periode"
                select-id="period-filter"
                resource-url="/api/payment/resource/school-year"
                value="msy_id"
                label-template=":msy_year :msy_semester"
                :label-template-items="['msy_year', [
                    'key' => 'msy_semester',
                    'mapping' => [
                        '1' => 'Ganjil',
                        '2' => 'Genap',
                        '3' => 'Antara',
                    ],
                ]]"
            />
            <x-select-option
                title="Potongan"
                select-id="discount-filter"
                resource-url="/api/payment/resource/discount"
                value="md_id"
                label-template=":md_name"
                :label-template-items="['md_name']"
            />
            <x-select-option
                title="Fakultas"
                select-id="faculty-filter"
                resource-url="/api/payment/resource/faculty"
                value="faculty_id"
                label-template=":faculty_name"
                :label-template-items="['faculty_name']"
            />
            <div>
                <label class="form-label">Program Studi</label>
                <select id="studyprogram-filter" class="form-select">
                </select>
            </div>

            <div class="d-flex align-items-end">
                <button onclick="filterDatatable()" class="btn btn-info text-nowrap">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="nav-tabs-shadow nav-align-top">
        <ul class="nav nav-tabs custom border-bottom mb-0" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-student" aria-controls="navs-student" aria-selected="true">Mahasiswa Lama</button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-new-student" aria-controls="navs-new-student" aria-selected="false">Mahasiswa baru</button>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="navs-student" role="tabpanel">
                @include('pages._payment.discount.tab-student')
            </div>
            <div class="tab-pane fade" id="navs-new-student" role="tabpanel">
                @include('pages._payment.discount.tab-new-student')
            </div>
        </div>
    </div>
</div>

<div class="modal fade dtr-bs-modal" id="modal-discount-detail" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Potongan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="custom-body"></div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let activeTab = 'student';

    $(function() {
        // enabling multiple modal open
        $(document).on('show.bs.modal', '.modal', function() {
            const zIndex = 1040 + 10 * $('.modal:visible').length;
            $(this).css('z-index', zIndex);
            setTimeout(() => $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack'));
        });

        // track tab switching
        const tabEl = document.querySelectorAll('button[data-bs-toggle="tab"]')
        for (i = 0; i < tabEl.length; i++) {
            tabEl[i].addEventListener('shown.bs.tab', function(event) {
                const activated_pane = document.querySelector(event.target.getAttribute('data-bs-target'))
                const deactivated_pane = document.querySelector(event.relatedTarget.getAttribute('data-bs-target'))

                console.log('active tab', activated_pane.id)
                if(activated_pane.id == 'navs-student') {
                    activeTab = 'student';
                }
                else if (activated_pane.id == 'navs-new-student') {
                    activeTab = 'new-student';
                }
            })
        }

    })

    function filterDatatable() {
        if (activeTab == 'student') {
            _discountReceiverStudentTable.reload();
        }

        if (activeTab == 'new-student') {
            _discountReceiverNewStudentTable.reload();
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
</script>
@endpush

@push('laravel-component-setup')
    <script>
        $(function() {
            setupFilters.studyprogram();
        });

        const setupFilters = {
            studyprogram: async function() {
                const data = await $.get({
                    async: true,
                    url: `${_baseURL}/api/payment/resource/studyprogram`,
                });

                const formatted = data.map(item => {
                    return {
                        id: item.studyprogram_id,
                        text: item.studyprogram_type.toUpperCase() + ' ' + item.studyprogram_name,
                    };
                });

                $('#studyprogram-filter').select2({
                    data: [
                        {id: '#ALL', text: "Semua Program Studi"},
                        ...formatted,
                    ],
                    minimumResultsForSearch: 6,
                });

                $('#faculty-filter').change(async function() {
                    const facultyId = this.value;
                    const studyprograms = await $.get({
                        async: true,
                        url: `${_baseURL}/api/payment/resource/studyprogram`,
                        data: {
                            faculty: facultyId != '#ALL' ? facultyId : null,
                        },
                        processData: true,
                    });
                    const options = [
                        new Option('Semua Program Studi', '#ALL', false, false),
                        ...studyprograms.map(item => {
                            return new Option(
                                item.studyprogram_type.toUpperCase() + ' ' + item.studyprogram_name,
                                item.studyprogram_id,
                                false,
                                false,
                            );
                        })
                    ];
                    $('#studyprogram-filter').empty().append(options).trigger('change');
                });
            }
        }
    </script>
@endpush
