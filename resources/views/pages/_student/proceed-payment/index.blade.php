@extends('tpl.vuexy.master-payment')

@section('page_title', 'Proses Pembayaran')
@section('sidebar-size', 'collapsed')
@section('url_back', url('student/payment').'?email='.request()->query('email').'&type='.request()->query('type'))

@section('css_section')
    <style>
        .eazy-table-info {
            display: inline-block;
        }
        .eazy-table-info td {
            padding: 5px 10px 5px 0;
        }
        .eazy-table-info.lg td {
            padding: 10px 10px 10px 0;
        }
        .eazy-table-info td:first-child {
            padding-right: 1rem;
            font-weight: 500;
        }

        .nav-tabs.custom .nav-item {
            /* flex-grow: 1; */
        }
        .nav-tabs.custom .nav-link {
            /* width: -webkit-fill-available !important; */
            height: 50px !important;
        }
        .nav-tabs.custom .nav-link.active {
            background-color: #f2f2f2 !important;
        }

        .eazy-header {
            display: flex;
            flex-direction: row;
            gap: 4rem;
            flex-wrap: wrap;
        }
        .eazy-header .eazy-header__item {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 1rem;
        }
        .eazy-header .eazy-header__item .item__icon {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 65px;
            height: 65px;
            border-radius: 1.5rem;
            background-color: rgba(246, 246, 246, 1) !important;
        }
        .eazy-header .eazy-header__item .item__icon svg {
            width: 35px;
            height: 35px;
        }
        .eazy-header .eazy-header__item .item__text {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .eazy-header .eazy-header__item .item__text .text__subtitle {
            display: block;
        }
        .eazy-header .eazy-header__item .item__text .text__title {
            display: block;
            font-weight: 700;
            font-size: 16px;
        }
    </style>
@endsection

@section('content')

<div id="student-info" class="card mb-3">
    <div class="card-body" style="width: 100%">
        <div id="header-info-student">
            ...
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <nav>
            <div class="nav nav-tabs custom border-bottom" id="nav-tab" role="tablist">
                <button class="nav-link" id="nav-invoice-data-tab" data-bs-toggle="tab" data-bs-target="#nav-invoice-data" type="button" role="tab">Informasi Tagihan</button>
                <button class="nav-link" id="nav-payment-option-tab" data-bs-toggle="tab" data-bs-target="#nav-payment-option" type="button" role="tab">Opsi Pembayaran</button>
                <button class="nav-link" id="nav-pay-bill-tab" data-bs-toggle="tab" data-bs-target="#nav-pay-bill" type="button" role="tab">Bayar Tagihan</button>
            </div>
        </nav>
        <div class="tab-content pt-2 pb-3 px-3" id="nav-tabContent" style="min-height: 400px">
            <div class="tab-pane fade" id="nav-invoice-data" role="tabpanel">
                @include('pages._student.proceed-payment.tab-invoice-data')
            </div>

            <div class="tab-pane fade" id="nav-payment-option" role="tabpanel">
                @include('pages._student.proceed-payment.tab-payment-option')
            </div>

            <div class="tab-pane fade" id="nav-pay-bill" role="tabpanel">
                @include('pages._student.proceed-payment.tab-pay-bill')
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>

    const userMaster = JSON.parse(`{!! json_encode($user, true) !!}`);
    const prrId = parseInt("{{ $prr_id }}");

    /**
     * @var object invoiceDataTab
     * @var object paymentOptionTab
     * @var object payBillTab
     * @func getRequestCache()
     */

    $(function(){
        // render header student info
        renderHeaderInfo();

        // init bootstrap tab
        tabManager.initTabs(
            invoiceDataTab.showHandler,
            paymentOptionTab.showHandler,
            payBillTab.showHandler,
        );
    });

    async function renderHeaderInfo() {
        const studentType = userMaster.participant ? 'new_student' : 'student';
        const studentId = studentType == 'new_student' ? userMaster.participant.par_id : userMaster.student.student_id;
        const queryParam = `student_type=${studentType}&${studentType == 'new_student' ? 'par_id=' : 'student_id='}${studentId}`;

        const studentDetail = await getRequestCache(`${_baseURL}/api/student/detail?${queryParam}`);

        if (studentType == 'new_student') {
            $('#header-info-student').html(`
                <div class="eazy-header">
                    <div class="eazy-header__item">
                        <div class="item__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </div>
                        <div class="item__text">
                            <small class="text__subtitle">Nama Lengkap</small>
                            <span class="text__title">${studentDetail.par_fullname}</span>
                        </div>
                    </div>
                    <div class="eazy-header__item">
                        <div class="item__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-hash"><line x1="4" y1="9" x2="20" y2="9"></line><line x1="4" y1="15" x2="20" y2="15"></line><line x1="10" y1="3" x2="8" y2="21"></line><line x1="16" y1="3" x2="14" y2="21"></line></svg>
                        </div>
                        <div class="item__text">
                            <small class="text__subtitle">NIK</small>
                            <span class="text__title">${studentDetail.par_nik}</span>
                        </div>
                    </div>
                </div>
            `);
        } else if (studentType == 'student') {
            $('#header-info-student').html(`
                <div class="eazy-header">
                    <div class="eazy-header__item">
                        <div class="item__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </div>
                        <div class="item__text">
                            <small class="text__subtitle">Nama Lengkap dan NIM</small>
                            <span class="text__title">${studentDetail.fullname}</span>
                            <span class="d-block">${studentDetail.student_id}</span>
                        </div>
                    </div>
                    <div class="eazy-header__item">
                        <div class="item__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-book-open"><line x1="4" y1="9" x2="20" y2="9"></line><line x1="4" y1="15" x2="20" y2="15"></line><line x1="10" y1="3" x2="8" y2="21"></line><line x1="16" y1="3" x2="14" y2="21"></line></svg>
                        </div>
                        <div class="item__text">
                            <small class="text__subtitle">Fakultas</small>
                            <span class="text__title">${studentDetail.studyprogram.faculty.faculty_name}</span>
                        </div>
                    </div>
                    <div class="eazy-header__item">
                        <div class="item__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bookmark"><line x1="4" y1="9" x2="20" y2="9"></line><line x1="4" y1="15" x2="20" y2="15"></line><line x1="10" y1="3" x2="8" y2="21"></line><line x1="16" y1="3" x2="14" y2="21"></line></svg>
                        </div>
                        <div class="item__text">
                            <small class="text__subtitle">Program Studi</small>
                            <span class="text__title">${studentDetail.studyprogram.studyprogram_name}</span>
                        </div>
                    </div>
                </div>
            `);
        }
    }

    const tabManager = {
        initTabs: function (invoiceDataShowHandler, paymentOptionShowHandler, payBillShowHandler) {
            this.updateDisableState();

            // Enable tabbable tabs via JavaScript
            const triggerTabList = [].slice.call(document.querySelectorAll('#nav-tab button'))
            triggerTabList.forEach(function (triggerEl) {
                const tabTrigger = new bootstrap.Tab(triggerEl);
                triggerEl.addEventListener('click', function (event) {
                    event.preventDefault()
                    tabTrigger.show()
                });
            });

            // add event when tab open
            const tabElms = document.querySelectorAll('button[data-bs-toggle="tab"]');
            tabElms.forEach(tabEl => {
                tabEl.addEventListener('show.bs.tab', function (event) {
                    const target = $(event.target).attr('data-bs-target');
                    console.log('open tab', target);
                    if (target == '#nav-invoice-data') {
                        invoiceDataShowHandler();
                    } else if (target == '#nav-payment-option') {
                        paymentOptionShowHandler();
                    } else if (target == '#nav-pay-bill') {
                        payBillShowHandler();
                    }
                });
            });
        },
        openTab: function(target) {
            bootstrap.Tab.getInstance(document.querySelector('#nav-tab button[data-bs-target="#'+target+'"]')).show();
        },
        updateDisableState: async function() {
            // $('#nav-tab #nav-payment-option-tab').removeClass('disabled');
            $('#nav-tab #nav-pay-bill-tab').removeClass('disabled');

            await deleteRequestCache(`${_baseURL}/api/student/payment/${prrId}`);
            const payment = await getRequestCache(`${_baseURL}/api/student/payment/${prrId}`);

            let openTabId = 'nav-payment-option';

            // // check is payment method is not set
            // if (!payment.prr_method) {
            //     $('#nav-tab #nav-payment-option-tab').addClass('disabled');
            // } else {
            //     openTabId = 'nav-payment-option';
            // }

            // check is payment bill is not generated
            if (payment.payment_bill.length == 0) {
                $('#nav-tab #nav-pay-bill-tab').addClass('disabled');
            } else {
                openTabId = 'nav-pay-bill';
            }

            this.openTab(openTabId);
        }
    }

</script>
@endpush
