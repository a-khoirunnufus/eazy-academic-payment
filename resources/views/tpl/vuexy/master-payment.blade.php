<!DOCTYPE html>
<html class="loading light-style" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="Bandung Techno Park">
    <title>@yield('page_title') | EAZY</title>
    <link rel="apple-touch-icon" href="{{ asset('images/logo-eazy-icon-small.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo-eazy-icon-small.png') }}">

    <!-- fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <!-- icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/7.0.96/css/materialdesignicons.min.css" integrity="sha512-fXnjLwoVZ01NUqS/7G5kAnhXNXat6v7e3M9PhoMHOTARUMCaf5qNO84r5x9AFf5HDzm3rEZD8sb/n6dZ19SzFA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- plugins -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/vuexy/vendors/css/forms/select/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/vuexy/vendors/css/tables/datatable/responsive.bootstrap5.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/vuexy/vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/vuexy/vendors/css/tables/datatable/rowGroup.bootstrap5.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/vuexy/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
    <!-- template -->
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/vuexy/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/vuexy/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/vuexy/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/vuexy/css/colors.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/vuexy/css/components.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/vuexy/css/themes/dark-layout.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/vuexy/css/themes/bordered-layout.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/vuexy/css/themes/semi-dark-layout.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/vuexy/css/core/menu/menu-types/vertical-menu.css') }}">
    <!-- plugins (must below template) -->
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/vuexy/vendors/css/extensions/toastr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/vuexy/css/plugins/extensions/ext-component-toastr.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ url('css/style.css') }}?version={{ config('version.css_style') }}">

    {{-- @vite(['resources/sass/custom.scss']) --}}
    <link rel="stylesheet" type="text/css" href="{{ url('css/custom.css') }}?version={{ config('version.css_style') }}">

    @yield('css_section')
    @stack('styles')
</head>

<body class="vertical-layout vertical-menu-modern  navbar-floating footer-static menu-@yield('sidebar-size')" data-open="click" data-menu="vertical-menu-modern" data-col="">
    <!-- Loading Animation -->
    <div id="overlay">
        <div class="d-flex h-100">
            <div class="m-auto bg-white rounded-circle p-1" style="box-shadow: 50px 50px 113px #defeec inset,-50px -50px 110px #defeec inset;">
                <div class="wheel-and-hamster bg-light rounded-circle" role="img" aria-label="Orange and tan hamster running in a metal wheel">
                    <div class="wheel"></div>
                    <div class="hamster">
                        <div class="hamster__body">
                            <div class="hamster__head">
                                <div class="hamster__ear"></div>
                                <div class="hamster__eye"></div>
                                <div class="hamster__nose"></div>
                            </div>
                            <div class="hamster__limb hamster__limb--fr"></div>
                            <div class="hamster__limb hamster__limb--fl"></div>
                            <div class="hamster__limb hamster__limb--br"></div>
                            <div class="hamster__limb hamster__limb--bl"></div>
                            <div class="hamster__tail"></div>
                        </div>
                    </div>
                    <div class="spoke"></div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.static_header')

    @if(auth()->user()->hasAssociateData('student'))
        @include('layouts.static_student_menu')
    @else
        @include('layouts.static_menu')
    @endif

    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper @if(View::hasSection('window-size')) {{ '' }}  @else {{ 'container-xxl p-0' }} @endif">
            <div class="content-header row mb-0">
                <div class="content-header-left col-6 mb-3 align-items-center">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <div class="content-header-title float-start mb-0 d-none d-md-block">
                                <a href="@yield('url_back')" class="fw-bold text-feeder bg-danger text-white avatar" style="width:28.1875px;height:28.1875px">
                                    <i class="m-auto" data-feather="chevron-left"></i>
                                </a>
                            </div>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><h4 class="fw-bold text-feeder page-title mb-0">@yield('page_title')</h4></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-end col-6">
                    <div class="mb-1 breadcrumb-right school-year-info">
                        <a href="javascript:void(0)" class="btn bg-label-info rounded-pill btn-icon small-info" title="-" data-bs-toggle="tooltip">
                            <i data-feather="calendar"></i>
                        </a>
                        <span class="large-info btn bg-label-info">
                            <i data-feather="calendar"></i>
                            <span class="ms-1">-</span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="content-body">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Main Modal -->
    <div class="modal fade" id="mainModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-white" style="padding: 2rem 3rem 3rem 3rem">
                    <h4 class="modal-title fw-bolder" id="mainModalLabel">...</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3 pt-0">
                    ...
                </div>
            </div>
        </div>
    </div>

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <footer class="footer footer-static footer-light">
      <p class="clearfix mb-0">
          <span class="float-md-start d-block d-md-inline-block mt-25">COPYRIGHT  © {{ date('Y') }} <a class="ms-25 fw-bold" href="" target="_blank">EAZY</a><span class="d-none d-sm-inline-block">, All rights Reserved</span></span>
          <span class="float-md-end d-none d-md-block">Hand-crafted &amp; Made from Bandung Techno Park, Telkom University</span>
        </p>
    </footer>

    <!-- TEMPLATE VENDOR -->
    <script src="{{ asset('themes/vuexy/vendors/js/vendors.min.js') }}"></script>

    <!-- EXTENSIONS -->
    <script src="{{ asset('themes/vuexy/vendors/js/extensions/toastr.min.js') }}"></script>
    <script src="{{ asset('themes/vuexy/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ asset('themes/vuexy/vendors/js/forms/validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('themes/vuexy/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/autonumeric/2.0.13/autoNumeric.min.js" integrity="sha512-IOt1IqHe4gQXHlRHQv9HWp/771RdI6dATdXaNq63pByU1zKa0tohtVg11/GWinzGJkbrZBkhtEsa8LqFmvI1Fw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/popper.js/dist/umd/popper.min.js"></script>
    <script src="https://unpkg.com/tooltip.js/dist/umd/tooltip.min.js"></script>
    <script src="https://unpkg.com/boxicons@2.1.1/dist/boxicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/form-data-json-convert/dist/form-data-json.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/id.min.js"></script>
    <!-- datatable stuff -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('themes/vuexy/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('themes/vuexy/vendors/js/tables/datatable/responsive.bootstrap5.js') }}"></script>
    <script src="{{ asset('themes/vuexy/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
    <script src="{{ asset('themes/vuexy/vendors/js/tables/datatable/jszip.min.js') }}"></script>
    <script src="{{ asset('themes/vuexy/vendors/js/tables/datatable/pdfmake.min.js') }}"></script>
    <script src="{{ asset('themes/vuexy/vendors/js/tables/datatable/vfs_fonts.js') }}"></script>
    <script src="{{ asset('themes/vuexy/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('themes/vuexy/vendors/js/tables/datatable/buttons.print.min.js') }}"></script>
    <script src="{{ asset('themes/vuexy/vendors/js/tables/datatable/dataTables.rowGroup.min.js') }}"></script>
    <!-- Extention applicators -->
    <script src="{{ asset('themes/vuexy/js/core/app-menu.js') }}"></script>
    <script src="{{ asset('themes/vuexy/js/core/app.js') }}"></script>
    <script src="{{ asset('themes/vuexy/js/core/jquery.uniform.js') }}"></script>
    <script src="{{ asset('themes/vuexy/js/scripts/forms/form-select2.js') }}"></script>
    <!-- Custom things -->
    <script>
        const _baseURL = "{{ url('/') }}"
        const _csrfToken = "{{ csrf_token() }}"
    </script>
    <script src="{{ url('js/app.js') }}?version={{ config('version.js_config') }}"></script>
    <script src="{{ url('js/setting.js') }}?version={{ config('version.js_config') }}"></script>
    <script src="{{ url('js/static-data.js') }}"></script>

    <script>
        $(document).ready(function () {
            @stack('elm_setup')
            _activeSchoolYear.load()
        });
    </script>

    @yield('js_section')
    @stack('scripts')
</body>

</html>
