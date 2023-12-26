@extends('tpl.vuexy.master-payment')


@section('page_title', 'Settings')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
<link rel="stylesheet" type="text/css" href="{{ asset('themes/vuexy/css/plugins/forms/form-wizard.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('themes/vuexy/vendors/css/forms/wizard/bs-stepper.min.css') }}">
    <style>
        .eazy-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }
    </style>
@endsection

@section('content')
<section class="vertical-wizard">
    <div class="bs-stepper vertical vertical-wizard-example">
        <div class="bs-stepper-header">
            <div class="step" data-target="#account-details-vertical" role="tab" id="account-details-vertical-trigger">
                <button type="button" class="step-trigger" aria-selected="true">
                    <span class="bs-stepper-box">1</span>
                    <span class="bs-stepper-label">
                        <span class="bs-stepper-title">Generate Finance</span>
                        <span class="bs-stepper-subtitle">Rules & Options</span>
                    </span>
                </button>
            </div>
            <div class="step" data-target="#masterdata-vertical" role="tab" id="masterdata-vertical-trigger">
                <button type="button" class="step-trigger" aria-selected="false">
                    <span class="bs-stepper-box">2</span>
                    <span class="bs-stepper-label">
                        <span class="bs-stepper-title">Masterdata Finance</span>
                        <span class="bs-stepper-subtitle">Rules & Options</span>
                    </span>
                </button>
            </div>
            <div class="step" data-target="#report-vertical" role="tab" id="report-vertical-trigger">
                <button type="button" class="step-trigger" aria-selected="false">
                    <span class="bs-stepper-box">3</span>
                    <span class="bs-stepper-label">
                        <span class="bs-stepper-title">Laporan Finance</span>
                        <span class="bs-stepper-subtitle">Options</span>
                    </span>
                </button>
            </div>
            <div class="step" data-target="#payment-vertical" role="tab" id="payment-vertical-trigger">
                <button type="button" class="step-trigger" aria-selected="false">
                    <span class="bs-stepper-box">4</span>
                    <span class="bs-stepper-label">
                        <span class="bs-stepper-title">Pembayaran Tagihan</span>
                        <span class="bs-stepper-subtitle">Options</span>
                    </span>
                </button>
            </div>
        </div>
        <div class="bs-stepper-content">
            <div id="account-details-vertical" class="content" role="tabpanel" aria-labelledby="account-details-vertical-trigger">
                <form action="" method="post">
                @csrf
                <div class="content-header">
                    <h5 class="mb-0">Regenerate</h5>
                    <small class="text-muted">Pengaturan batas tanggal akhir regenerate tagihan.</small>
                </div>
                <div class="row">
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-username">Batas Tanggal Akhir Regenerate Tagihan Mahasiswa Lama<br>
                        <small class="text-muted">Membatasi tanggal akhir untuk regenerate tagihan mahasiswa lama.</small></label>
                        <input type="date" id="payment_regenerate_lock_cache" name="payment_regenerate_lock_cache" data-id="payment_regenerate_lock_cache" class="form-control " value="{{$settings->where('name','payment_regenerate_lock_cache')->pluck('value')[0]}}" required>
                    </div>
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-username">Batas Tanggal Akhir Regenerate Tagihan Mahasiswa Baru<br>
                            <small class="text-muted">Membatasi tanggal akhir untuk regenerate tagihan mahasiswa baru.</small></label>
                        <input type="date" id="vertical-email" name="payment_regenerate_lock_new_cache" data-id="payment_regenerate_lock_new_cache" class="form-control " value="{{$settings->where('name','payment_regenerate_lock_new_cache')->pluck('value')[0]}}" required>
                    </div>
                </div>
                <hr>
                <div class="content-header">
                    <h5 class="mb-0">Hapus Tagihan</h5>
                    <small class="text-muted">Pengaturan batas tanggal akhir hapus tagihan.</small>
                </div>
                <div class="row">
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-username">Batas Tanggal Akhir Hapus Tagihan Mahasiswa Lama<br>
                        <small class="text-muted">Membatasi tanggal akhir untuk hapus tagihan mahasiswa lama.</small></label>
                        <input type="date" id="vertical-username" name="payment_delete_lock_cache" data-id="payment_delete_lock_cache" class="form-control " value="{{$settings->where('name','payment_delete_lock_cache')->pluck('value')[0]}}" required>
                    </div>
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-username">Batas Tanggal Akhir Hapus Tagihan Mahasiswa Baru<br>
                            <small class="text-muted">Membatasi tanggal akhir untuk hapus tagihan mahasiswa baru.</small></label>
                        <input type="date" id="vertical-email" name="payment_delete_lock_new_cache" data-id="payment_delete_lock_new_cache" class="form-control " value="{{$settings->where('name','payment_delete_lock_new_cache')->pluck('value')[0]}}" required>
                    </div>
                </div>
                <hr>
                <div class="content-header mt-1">
                    <h5 class="mb-0">Edit Tagihan</h5>
                    <small class="text-muted">Pengaturan batas tanggal akhir edit tagihan</small>
                </div>
                <div class="row">
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-username">Batas Tanggal Akhir Edit Tagihan Mahasiswa Lama<br>
                        <small class="text-muted">Membatasi tanggal akhir untuk edit tagihan mahasiswa lama.</small></label>
                        <input type="date" id="vertical-username" name="payment_edit_lock_cache" data-id="payment_edit_lock_cache" class="form-control " value="{{$settings->where('name','payment_edit_lock_cache')->pluck('value')[0]}}" required>
                    </div>
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-username">Batas Tanggal Akhir Edit Tagihan Mahasiswa Baru<br>
                            <small class="text-muted">Membatasi tanggal akhir untuk edit tagihan mahasiswa baru.</small></label>
                        <input type="date" id="vertical-email" name="payment_edit_lock_new_cache" data-id="payment_edit_lock_new_cache" class="form-control " value="{{$settings->where('name','payment_edit_lock_new_cache')->pluck('value')[0]}}" required>
                    </div>
                </div>
                </form>
            </div>

            <div id="masterdata-vertical" class="content" role="tabpanel" aria-labelledby="masterdata-vertical-trigger">
                <form action="" method="post">
                @csrf
                <div class="content-header">
                    <h5 class="mb-0">Jenis Tagihan</h5>
                    <small class="text-muted">Pengaturan default jenis tagihan.</small>
                </div>
                <div class="row">
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-username">Default Jenis Tagihan Mahasiswa Lama<br>
                        <small class="text-muted">Menentukan default jenis tagihan mahasiswa lama.</small></label>
                        <select class="form-select select2" eazy-select2-active name="payment_type_default_cache" data-id="payment_type_default_cache">
                            @foreach ($types as $key => $item)
                                <option value="{{ $item->msct_id }}" @if($item->msct_id == $settings->where('name','payment_type_default_cache')->pluck('value')[0]) selected @endif>{{ $item->msct_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-username">Default Jenis Tagihan Mahasiswa Baru<br>
                        <small class="text-muted">Menentukan default jenis tagihan mahasiswa baru.</small></label>
                        <select class="form-select select2" eazy-select2-active name="payment_type_default_new_cache" data-id="payment_type_default_new_cache">
                            @foreach ($types as $key => $item)
                                <option value="{{ $item->msct_id }}" @if($item->msct_id == $settings->where('name','payment_type_default_new_cache')->pluck('value')[0]) selected @endif>{{ $item->msct_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr>
                <div class="content-header">
                    <h5 class="mb-0">SKS</h5>
                    <small class="text-muted">Pengaturan masterdata SKS.</small>
                </div>
                <div class="row">
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-username">SKS Normal<br>
                        <small class="text-muted">Default harga untuk SKS normal.</small></label>
                        <input type="text" class="form-control payment_sks_fee_cache" data-id="payment_sks_fee_cache" value="{{$settings->where('name','payment_sks_fee_cache')->pluck('value')[0]}}" required>
                    </div>
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-username">SKS Praktikum<br>
                            <small class="text-muted">Default harga untuk SKS Praktikum.</small></label>
                        <input type="text" class="form-control payment_sks_practicum_fee_cache" data-id="payment_sks_practicum_fee_cache" value="{{$settings->where('name','payment_sks_practicum_fee_cache')->pluck('value')[0]}}" required>
                    </div>
                </div>
                <hr>
                <div class="content-header mt-1">
                    <h5 class="mb-0">Mata Kuliah</h5>
                    <small class="text-muted">Pengaturan harga default mata kuliah.</small>
                </div>
                <div class="row">
                    <div class="mb-1 col-md-12">
                        <label class="form-label" for="vertical-username">Harga Default Mata Kuliah<br>
                        <small class="text-muted">Default harga untuk mata kuliah yang belum disetting.</small></label>
                        <input type="text" class="form-control payment_subject_fee_cache" data-id="payment_subject_fee_cache" value="{{$settings->where('name','payment_subject_fee_cache')->pluck('value')[0]}}" required>
                    </div>
                </div>
                </form>
            </div>

            <div id="report-vertical" class="content" role="tabpanel" aria-labelledby="report-vertical-trigger">
                <form action="" method="post">
                    @csrf
                    <div class="content-header">
                        <h5 class="mb-0">Pembayaran Tagihan Mahasiswa Baru</h5>
                        <!-- <small class="text-muted">subtitle.</small> -->
                    </div>
                    <div class="row mb-1">
                        <div class="col-12">
                            <label class="form-label" for="vertical-username">Sumber Data</label>
                            <select class="form-select w-100 select2" name="payment_report_invoice_new_student_source" data-id="payment_report_invoice_new_student_source" value="{{ $settings->where('name','payment_report_invoice_new_student_source')->pluck('value')[0] }}">
                                <option value="finance" {{ $settings->where('name','payment_report_invoice_new_student_source')->pluck('value')[0] == 'finance' ? 'selected' : '' }}>Finance</option>
                                <option value="admission" {{ $settings->where('name','payment_report_invoice_new_student_source')->pluck('value')[0] == 'admission' ? 'selected' : '' }}>Admission</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div id="payment-vertical" class="content" role="tabpanel" aria-labelledby="payment-vertical-trigger">
                <form action="" method="post">
                    @csrf
                    <div class="content-header">
                        <h5 class="mb-0">Pembayaran Tagihan</h5>
                        <!-- <small class="text-muted">subtitle.</small> -->
                    </div>
                    <div class="row mb-1">
                        <div class="col-4">
                            <label class="form-label" for="vertical-username">Aktifkan Pembayaran Via Midtrans</label>
                            <select class="form-select w-100 select2" name="payment_with_midtrans_active" data-id="payment_with_midtrans_active" value="{{ $settings->where('name','payment_with_midtrans_active')->pluck('value')[0] }}">
                                <option value="true" {{ $settings->where('name','payment_with_midtrans_active')->pluck('value')[0] == 'true' ? 'selected' : '' }}>Ya</option>
                                <option value="false" {{ $settings->where('name','payment_with_midtrans_active')->pluck('value')[0] == 'false' ? 'selected' : '' }}>Tidak</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <label class="form-label" for="vertical-username">Aktifkan Pembayaran Via Finpay</label>
                            <select class="form-select w-100 select2" name="payment_with_finpay_active" data-id="payment_with_finpay_active" value="{{ $settings->where('name','payment_with_finpay_active')->pluck('value')[0] }}">
                                <option value="true" {{ $settings->where('name','payment_with_finpay_active')->pluck('value')[0] == 'true' ? 'selected' : '' }}>Ya</option>
                                <option value="false" {{ $settings->where('name','payment_with_finpay_active')->pluck('value')[0] == 'false' ? 'selected' : '' }}>Tidak</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <label class="form-label" for="vertical-username">Aktifkan Pembayaran Manual</label>
                            <select class="form-select w-100 select2" name="payment_with_manual_active" data-id="payment_with_manual_active" value="{{ $settings->where('name','payment_with_manual_active')->pluck('value')[0] }}">
                                <option value="true" {{ $settings->where('name','payment_with_manual_active')->pluck('value')[0] == 'true' ? 'selected' : '' }}>Ya</option>
                                <option value="false" {{ $settings->where('name','payment_with_manual_active')->pluck('value')[0] == 'false' ? 'selected' : '' }}>Tidak</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection


@section('js_section')
<script src="{{ asset('themes/vuexy/vendors/js/forms/wizard/bs-stepper.min.js') }}"></script>
<script src="{{ asset('themes/vuexy/js/scripts/forms/form-wizard.js') }}"></script>
<script>
    // Timeout
    function debounce(callback, wait) {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(function () { callback.apply(this, args); }, wait);
        };
    }

    // Auto save
    $(":input").on("keyup change", debounce( (event) => {
        let val = $(event.target).val();
        let name = $(event.target).attr('data-id');
        let removeDot = "";
        if(val){
            removeDot = val.replace(/\./g, "");
        }
        console.log(removeDot);

        if(removeDot.length > 0 && name.length > 0){
            console.log(removeDot);
            $.post(_baseURL + '/api/payment/settings/update/'+name, {
                _method: 'POST',
                name: name,
                val: removeDot
            }, function(data) {
                data = JSON.parse(data)
                Swal.fire({
                    icon: 'success',
                    text: data.message,
                });
            }).fail((error) => {
                Swal.fire({
                    icon: 'error',
                    text: data.text,
                });
                _responseHandler.generalFailResponse(error)
            })
        }
    }, 2000))

    // Currency Format
    _numberCurrencyFormat.load('payment_subject_fee_cache');
    _numberCurrencyFormat.load('payment_sks_practicum_fee_cache');
    _numberCurrencyFormat.load('payment_sks_fee_cache');

</script>
@endsection
