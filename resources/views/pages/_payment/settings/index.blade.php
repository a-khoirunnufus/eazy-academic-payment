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
                        <span class="bs-stepper-title">Generate</span>
                        <span class="bs-stepper-subtitle">Rules & Options</span>
                    </span>
                </button>
            </div>
            <div class="step" data-target="#masterdata-vertical" role="tab" id="masterdata-vertical-trigger">
                <button type="button" class="step-trigger" aria-selected="true">
                    <span class="bs-stepper-box">2</span>
                    <span class="bs-stepper-label">
                        <span class="bs-stepper-title">Masterdata</span>
                        <span class="bs-stepper-subtitle">Rules & Options</span>
                    </span>
                </button>
            </div>
            {{-- <div class="step" data-target="#personal-info-vertical" role="tab" id="personal-info-vertical-trigger">
                <button type="button" class="step-trigger" aria-selected="false">
                    <span class="bs-stepper-box">2</span>
                    <span class="bs-stepper-label">
                        <span class="bs-stepper-title">Personal Info</span>
                        <span class="bs-stepper-subtitle">Add Personal Info</span>
                    </span>
                </button>
            </div> --}}
            {{-- <div class="step" data-target="#address-step-vertical" role="tab" id="address-step-vertical-trigger">
                <button type="button" class="step-trigger" aria-selected="false">
                    <span class="bs-stepper-box">3</span>
                    <span class="bs-stepper-label">
                        <span class="bs-stepper-title">Address</span>
                        <span class="bs-stepper-subtitle">Add Address</span>
                    </span>
                </button>
            </div>
            <div class="step" data-target="#social-links-vertical" role="tab" id="social-links-vertical-trigger">
                <button type="button" class="step-trigger" aria-selected="false">
                    <span class="bs-stepper-box">4</span>
                    <span class="bs-stepper-label">
                        <span class="bs-stepper-title">Social Links</span>
                        <span class="bs-stepper-subtitle">Add Social Links</span>
                    </span>
                </button>
            </div> --}}
        </div>
        <div class="bs-stepper-content">
            <div id="account-details-vertical" class="content" role="tabpanel" aria-labelledby="account-details-vertical-trigger">
                <form action="{{ route('payment.settings.update') }}" method="post">
                <input type="hidden" name="tabId" value=1>
                @csrf
                <div class="content-header">
                    <h5 class="mb-0">Regenerate</h5>
                    <small class="text-muted">Pengaturan batas tanggal akhir regenerate tagihan.</small>
                </div>
                <div class="row">
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-username">Batas Tanggal Akhir Regenerate Tagihan Mahasiswa Lama<br>
                        <small class="text-muted">Membatasi tanggal akhir untuk regenerate tagihan mahasiswa lama.</small></label>
                        <input type="date" id="vertical-username" name="payment_regenerate_lock_cache" class="form-control" value="{{$settings->where('name','payment_regenerate_lock_cache')->pluck('value')[0]}}" required>
                    </div>
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-username">Batas Tanggal Akhir Regenerate Tagihan Mahasiswa Baru<br>
                            <small class="text-muted">Membatasi tanggal akhir untuk regenerate tagihan mahasiswa baru.</small></label>
                        <input type="date" id="vertical-email" name="payment_regenerate_lock_new_cache" class="form-control" value="{{$settings->where('name','payment_regenerate_lock_new_cache')->pluck('value')[0]}}" required>
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
                        <input type="date" id="vertical-username" name="payment_delete_lock_cache" class="form-control" value="{{$settings->where('name','payment_delete_lock_cache')->pluck('value')[0]}}" required>
                    </div>
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-username">Batas Tanggal Akhir Hapus Tagihan Mahasiswa Baru<br>
                            <small class="text-muted">Membatasi tanggal akhir untuk hapus tagihan mahasiswa baru.</small></label>
                        <input type="date" id="vertical-email" name="payment_delete_lock_new_cache" class="form-control" value="{{$settings->where('name','payment_delete_lock_new_cache')->pluck('value')[0]}}" required>
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
                        <input type="date" id="vertical-username" name="payment_edit_lock_cache" class="form-control" value="{{$settings->where('name','payment_edit_lock_cache')->pluck('value')[0]}}" required>
                    </div>
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-username">Batas Tanggal Akhir Edit Tagihan Mahasiswa Baru<br>
                            <small class="text-muted">Membatasi tanggal akhir untuk edit tagihan mahasiswa baru.</small></label>
                        <input type="date" id="vertical-email" name="payment_edit_lock_new_cache" class="form-control" value="{{$settings->where('name','payment_edit_lock_new_cache')->pluck('value')[0]}}" required>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary waves-effect waves-float waves-light btn-submit-action">
                        <span class="align-middle d-sm-inline-block d-none"><i data-feather="save"></i> Save</span>
                    </button>
                </div>
                </form>
            </div>
            <div id="masterdata-vertical" class="content" role="tabpanel" aria-labelledby="masterdata-vertical-trigger">
                <form action="{{ route('payment.settings.update') }}" method="post">
                <input type="hidden" name="tabId" value=2>
                @csrf
                <div class="content-header">
                    <h5 class="mb-0">Jenis Tagihan</h5>
                    <small class="text-muted">Pengaturan default jenis tagihan.</small>
                </div>
                <div class="row">
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-username">Default Jenis Tagihan Mahasiswa Lama<br>
                        <small class="text-muted">Menentukan default jenis tagihan mahasiswa lama.</small></label>
                        <select class="form-select select2" eazy-select2-active name="payment_type_default_cache">
                            @foreach ($types as $key => $item)
                                <option value="{{ $item->msct_id }}" @if($item->msct_id == $settings->where('name','payment_type_default_cache')->pluck('value')[0]) selected @endif>{{ $item->msct_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-username">Default Jenis Tagihan Mahasiswa Baru<br>
                        <small class="text-muted">Menentukan default jenis tagihan mahasiswa baru.</small></label>
                        <select class="form-select select2" eazy-select2-active name="payment_type_default_new_cache">
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
                        <input type="text" class="form-control payment_sks_fee_cache" value="{{$settings->where('name','payment_sks_fee_cache')->pluck('value')[0]}}" required>
                    </div>
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-username">SKS Praktikum<br>
                            <small class="text-muted">Default harga untuk SKS Praktikum.</small></label>
                        <input type="text" class="form-control payment_sks_practicum_fee_cache" value="{{$settings->where('name','payment_sks_practicum_fee_cache')->pluck('value')[0]}}" required>
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
                        <input type="text" class="form-control payment_subject_fee_cache" value="{{$settings->where('name','payment_subject_fee_cache')->pluck('value')[0]}}" required>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary waves-effect waves-float waves-light btn-submit-action">
                        <span class="align-middle d-sm-inline-block d-none"><i data-feather="save"></i> Save</span>
                    </button>
                </div>
                </form>
            </div>
            <div id="personal-info-vertical" class="content dstepper-block" role="tabpanel" aria-labelledby="personal-info-vertical-trigger">
                <div class="content-header">
                    <h5 class="mb-0">Personal Info</h5>
                    <small>Enter Your Personal Info.</small>
                </div>
                <div class="row">
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-first-name">First Name</label>
                        <input type="text" id="vertical-first-name" class="form-control" placeholder="John">
                    </div>
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-last-name">Last Name</label>
                        <input type="text" id="vertical-last-name" class="form-control" placeholder="Doe">
                    </div>
                </div>
                <div class="row">
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-country">Country</label>
                        <div class="position-relative"><select class="select2 w-100 select2-hidden-accessible" id="vertical-country" data-select2-id="vertical-country" tabindex="-1" aria-hidden="true">
                            <option label=" " data-select2-id="5"></option>
                            <option>UK</option>
                            <option>USA</option>
                            <option>Spain</option>
                            <option>France</option>
                            <option>Italy</option>
                            <option>Australia</option>
                        </select><span class="select2 select2-container select2-container--default" dir="ltr" data-select2-id="4" style="width: auto;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-vertical-country-container"><span class="select2-selection__rendered" id="select2-vertical-country-container" role="textbox" aria-readonly="true"><span class="select2-selection__placeholder">Select value</span></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span></div>
                    </div>
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-language">Language</label>
                        <div class="position-relative"><select class="select2 w-100 select2-hidden-accessible" id="vertical-language" multiple="" data-select2-id="vertical-language" tabindex="-1" aria-hidden="true">
                            <option>English</option>
                            <option>French</option>
                            <option>Spanish</option>
                        </select><span class="select2 select2-container select2-container--default" dir="ltr" data-select2-id="6" style="width: auto;"><span class="selection"><span class="select2-selection select2-selection--multiple" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="-1" aria-disabled="false"><ul class="select2-selection__rendered"><li class="select2-search select2-search--inline"><input class="select2-search__field" type="search" tabindex="0" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" role="searchbox" aria-autocomplete="list" placeholder="Select value" style="width: 0px;"></li></ul></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span></div>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <button class="btn btn-primary btn-prev waves-effect waves-float waves-light">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left align-middle me-sm-25 me-0"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                        <span class="align-middle d-sm-inline-block d-none">Previous</span>
                    </button>
                    <button class="btn btn-primary btn-next waves-effect waves-float waves-light">
                        <span class="align-middle d-sm-inline-block d-none">Next</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right align-middle ms-sm-25 ms-0"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </button>
                </div>
            </div>
            <div id="address-step-vertical" class="content" role="tabpanel" aria-labelledby="address-step-vertical-trigger">
                <div class="content-header">
                    <h5 class="mb-0">Address</h5>
                    <small>Enter Your Address.</small>
                </div>
                <div class="row">
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-address">Address</label>
                        <input type="text" id="vertical-address" class="form-control" placeholder="98  Borough bridge Road, Birmingham">
                    </div>
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-landmark">Landmark</label>
                        <input type="text" id="vertical-landmark" class="form-control" placeholder="Borough bridge">
                    </div>
                </div>
                <div class="row">
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="pincode2">Pincode</label>
                        <input type="text" id="pincode2" class="form-control" placeholder="658921">
                    </div>
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="city2">City</label>
                        <input type="text" id="city2" class="form-control" placeholder="Birmingham">
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <button class="btn btn-primary btn-prev waves-effect waves-float waves-light">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left align-middle me-sm-25 me-0"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                        <span class="align-middle d-sm-inline-block d-none">Previous</span>
                    </button>
                    <button class="btn btn-primary btn-next waves-effect waves-float waves-light">
                        <span class="align-middle d-sm-inline-block d-none">Next</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right align-middle ms-sm-25 ms-0"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </button>
                </div>
            </div>
            <div id="social-links-vertical" class="content" role="tabpanel" aria-labelledby="social-links-vertical-trigger">
                <div class="content-header">
                    <h5 class="mb-0">Social Links</h5>
                    <small>Enter Your Social Links.</small>
                </div>
                <div class="row">
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-twitter">Twitter</label>
                        <input type="text" id="vertical-twitter" class="form-control" placeholder="https://twitter.com/abc">
                    </div>
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-facebook">Facebook</label>
                        <input type="text" id="vertical-facebook" class="form-control" placeholder="https://facebook.com/abc">
                    </div>
                </div>
                <div class="row">
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-google">Google+</label>
                        <input type="text" id="vertical-google" class="form-control" placeholder="https://plus.google.com/abc">
                    </div>
                    <div class="mb-1 col-md-6">
                        <label class="form-label" for="vertical-linkedin">Linkedin</label>
                        <input type="text" id="vertical-linkedin" class="form-control" placeholder="https://linkedin.com/abc">
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <button class="btn btn-primary btn-prev waves-effect waves-float waves-light">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left align-middle me-sm-25 me-0"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                        <span class="align-middle d-sm-inline-block d-none">Previous</span>
                    </button>
                    <button class="btn btn-success btn-submit waves-effect waves-float waves-light">Submit</button>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


@section('js_section')
<script src="{{ asset('themes/vuexy/vendors/js/forms/wizard/bs-stepper.min.js') }}"></script>
<script src="{{ asset('themes/vuexy/js/scripts/forms/form-wizard.js') }}"></script>
<script>
    $('.btn-submit-action').on('click',function(e){
        e.preventDefault();
        var form = $(this).parents('form');
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah anda yakin ingin menyimpan perubahan settings ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ea5455',
            cancelButtonColor: '#82868b',
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        })
    });
    _numberCurrencyFormat.load('payment_subject_fee_cache');
    _numberCurrencyFormat.load('payment_sks_practicum_fee_cache');
    _numberCurrencyFormat.load('payment_sks_fee_cache');
    $(document).ready(function () {
        var stepper = new Stepper($('.bs-stepper')[0]);
        @if (session('tabId'))
            stepper.to({{session("tabId")}});
        @endif
    })
</script>
@if (session('message'))
    <script>
        Swal.fire({
            icon: 'success',
            text: '{{session("message")}}',
        })
    </script>
@endif
@endsection
