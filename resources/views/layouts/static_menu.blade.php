<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow @yield('sidebar-size')" data-scroll-to-active="true">
    {{-- <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item me-auto"><a class="navbar-brand" href="">
                    <span class="brand-logo">
                        <img src="/images/logo-eazy-icon-small.png" />
                    </span>
                    <h2 class="brand-text"><img src="/images/logo-eazy-small.png" style="width:100px" /></h2>
                </a></li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pe-0" data-bs-toggle="collapse"><i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i><i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc" data-ticon="disc"></i></a></li>
        </ul>
    </div> --}}
    <div class="navbar-header mb-1">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item me-auto">
                <a class="navbar-brand" href="/dashboard">
                    <span class="brand-logo"></span>
                </a>
            </li>
            <li class="nav-item nav-toggle">
                <a class="nav-link modern-nav-toggle pe-0" data-bs-toggle="collapse">
                    <i class="d-block d-xl-none toggle-icon font-medium-4" data-feather="x"></i>
                    <i class="d-none d-xl-block collapse-toggle-icon font-medium-4" data-feather="disc" data-ticon="disc"></i>
                </a>
            </li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            {{-- @foreach(auth()->getAvailableModules() as $category)
                <li class="navigation-header"><span>{{ trans('modules.category.'.$category['name']) }}</span><i data-feather="more-horizontal"></i>
                @foreach($category['groups'] as $group)
                    <li class="nav-item">
                        <a class="align-items-center d-flex" href="#">
                            <i data-feather="{{ $group['icon'] }}"></i>
                            <span class="menu-title text-truncate">{{ trans('modules.group.'.$group['name']) }}</span>
                        </a>
                        <ul class="menu-content">
                            @foreach($group['modules'] as $module)
                                <li class="{{ request()->is($module['path']) || request()->is(substr($module['path'], 1)) ? 'active' : '' }}">
                                    <a href="{{ url($module['path']) }}" class="align-items-center d-flex">
                                        <i class="menu-item-icon" data-feather="circle" style="color:#163485"></i>
                                        <span class="menu-item">{{ trans('modules.modules.'.$module['name']) }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            @endforeach --}}

            <li class="nav-item mb-50">
                <a class="d-flex align-items-center fw-bold" href="/home">
                    <i data-feather="home"></i>
                    <span class="menu-title text-truncate">Home</span>
                </a>
            </li>

            <li class="nav-item mb-50">
                <a class="d-flex align-items-center fw-bold" href="#">
                    <i data-feather="settings"></i>
                    <span class="menu-title text-truncate">Setting</span>
                </a>
                <ul class="menu-content">
                    <li class="menu__item nav-item {{ 'payment.settings.component' == request()->route()->getName() ? 'active' : '' }}">
                        <a class="d-flex align-items-center fw-bold" href="{{ route('payment.settings.component') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-title text-truncate">Setting Komponen<br>Tagihan</span>
                        </a>
                    </li>
                    <li class="menu__item nav-item {{ 'payment.settings.credit-schema' == request()->route()->getName() ? 'active' : '' }}">
                        <a class="d-flex align-items-center fw-bold" href="{{ route('payment.settings.credit-schema') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-title text-truncate">Setting Template<br>Cicilan</span>
                        </a>
                    </li>
                    <li class="menu__item nav-item {{ 'payment.settings.payment-rates' == request()->route()->getName() ? 'active' : '' }}">
                        <a class="d-flex align-items-center fw-bold" href="{{ route('payment.settings.payment-rates') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-title text-truncate">Setting Tarif</span>
                        </a>
                    </li>
                    <li class="menu__item nav-item {{ 'payment.settings.subject-rates' == request()->route()->getName() ? 'active' : '' }}">
                        <a class="d-flex align-items-center fw-bold" href="{{ route('payment.settings.subject-rates') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-title text-truncate">Setting Tarif Per Mata Kuliah</span>
                        </a>
                    </li>
                    <li class="menu__item nav-item {{ 'setting/registration-form' == request()->path() ? 'active' : '' }}">
                        <a class="d-flex align-items-center fw-bold" href="{{ url('setting/registration-form') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-title text-truncate">Setting Formulir<br>Pendaftaran(PMB)</span>
                        </a>
                    </li>
                    <li class="menu__item nav-item {{ 'setting/academic-rules' == request()->path() ? 'active' : '' }}">
                        <a class="d-flex align-items-center fw-bold" href="{{ url('setting/academic-rules') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-title text-truncate">Setting Aturan<br>Akademik</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- <li class="nav-item mb-50">
                <a class="d-flex align-items-center fw-bold" href="/beasiswa">
                    <i data-feather="percent"></i>
                    <span class="menu-title text-truncate">Beasiswa<br>dan Potongan</span>
                </a>
            </li> -->

            <li class="nav-item mb-50">
                <a class="d-flex align-items-center fw-bold" href="#">
                    <i data-feather="mail"></i>
                    <span class="menu-title text-truncate">Generate</span>
                </a>
                <ul class="menu-content">
                    <li class="menu__item nav-item {{ 'generate/registrant-invoice' == request()->path() ? 'active' : '' }}">
                        <a class="d-flex align-items-center fw-bold" href="{{ url('generate/registrant-invoice') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-title text-truncate">Generate Tagihan<br>Pendaftar</span>
                        </a>
                    </li>
                    <li class="menu__item nav-item {{ 'payment.generate.student-invoice' == request()->path() ? 'active' : '' }}">
                        <a class="d-flex align-items-center fw-bold" href="{{ route('payment.generate.student-invoice') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-title text-truncate">Generate Tagihan<br>Mahasiswa Lama</span>
                        </a>
                    </li>
                    <li class="menu__item nav-item {{ 'payment.generate.new-student-invoice' == request()->route()->getName() ? 'active' : '' }}">
                        <a class="d-flex align-items-center fw-bold" href="{{ route('payment.generate.new-student-invoice') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-title text-truncate">Generate Tagihan<br>Mahasiswa Baru</span>
                        </a>
                    </li>
                    <li class="menu__item nav-item {{ 'generate/other-invoice' == request()->path() ? 'active' : '' }}">
                        <a class="d-flex align-items-center fw-bold" href="{{ url('generate/other-invoice') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-title text-truncate">Generate Tagihan<br>Lainnya</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item mb-50">
                <a class="d-flex align-items-center fw-bold" href="#">
                    <i data-feather="percent"></i>
                    <span class="menu-title text-truncate">Potongan</span>
                </a>
                <ul class="menu-content">
                    <li class="menu__item nav-item {{ 'payment.discount.index' == request()->route()->getName() ? 'active' : '' }}">
                        <a class="d-flex align-items-center fw-bold" href="{{ route('payment.discount.index') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-title text-truncate">Data Potongan</span>
                        </a>
                    </li>
                    <li class="menu__item nav-item {{ 'payment.discount.receiver' == request()->route()->getName() ? 'active' : '' }}">
                        <a class="d-flex align-items-center fw-bold" href="{{ route('payment.discount.receiver') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-title text-truncate">Penerima Potongan</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item mb-50">
                <a class="d-flex align-items-center fw-bold" href="#">
                    <i data-feather="layers"></i>
                    <span class="menu-title text-truncate">Beasiswa</span>
                </a>
                <ul class="menu-content">
                    <li class="menu__item nav-item {{ 'payment.scholarship.index' == request()->route()->getName() ? 'active' : '' }}">
                        <a class="d-flex align-items-center fw-bold" href="{{ route('payment.scholarship.index') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-title text-truncate">Data Beasiswa</span>
                        </a>
                    </li>
                    <li class="menu__item nav-item {{ 'payment.scholarship.receiver' == request()->route()->getName() ? 'active' : '' }}">
                        <a class="d-flex align-items-center fw-bold" href="{{ route('payment.scholarship.receiver') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-title text-truncate">Penerima Beasiswa</span>
                        </a>
                    </li>
                </ul>
            </li>
            {{-- <li class="nav-item mb-50">
                <a class="d-flex align-items-center fw-bold" href="/cicilan">
                    <i data-feather="file-text"></i>
                    <span class="menu-title text-truncate">Potongan</span>
                </a>
            </li> --}}

            <!-- <li class="nav-item mb-50">
                <a class="d-flex align-items-center fw-bold" href="/dispensasi">
                    <i data-feather="file-text"></i>
                    <span class="menu-title text-truncate">Dispensasi</span>
                </a>
            </li> -->

            <li class="nav-item mb-50">
                <a class="d-flex align-items-center fw-bold" href="#">
                    <i data-feather="file-text"></i>
                    <span class="menu-title text-truncate">Laporan</span>
                </a>
                <ul class="menu-content">
                    <li class="menu__item nav-item {{ 'report/old-student-invoice' == request()->path() ? 'active' : '' }}">
                        <a class="d-flex align-items-center fw-bold" href="{{ url('report/old-student-invoice') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-title text-truncate">Laporan Pembayaran<br>Tagihan Mahasiswa<br>Lama</span>
                        </a>
                    </li>
                    <li class="menu__item nav-item {{ 'report/new-student-invoice' == request()->path() ? 'active' : '' }}">
                        <a class="d-flex align-items-center fw-bold" href="{{ url('report/new-student-invoice') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-title text-truncate">Laporan Pembayaran<br>Tagihan Mahasiswa<br>Baru</span>
                        </a>
                    </li>
                    <li class="menu__item nav-item {{ 'report/registrant-invoice' == request()->path() ? 'active' : '' }}">
                        <a class="d-flex align-items-center fw-bold" href="{{ url('report/registrant-invoice') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-title text-truncate">Laporan Pembayaran<br>Tagihan Pendaftar</span>
                        </a>
                    </li>
                    <li class="menu__item nav-item {{ 'report/old-student-receivables' == request()->path() ? 'active' : '' }}">
                        <a class="d-flex align-items-center fw-bold" href="{{ url('report/old-student-receivables') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-title text-truncate">Laporan Piutang<br>Mahasiswa Lama</span>
                        </a>
                    </li>
                    <li class="menu__item nav-item {{ 'report/new-student-receivables' == request()->path() ? 'active' : '' }}">
                        <a class="d-flex align-items-center fw-bold" href="{{ url('report/new-student-receivables') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-title text-truncate">Laporan Piutang<br>Mahasiswa Baru</span>
                        </a>
                    </li>
                </ul>
            </li>
	    </ul>
    </div>
</div>

<script>
	window.onload = function(){
		$(".sidebar-content").find('.nav-link').each(function(item){
			if(window.location.href == $(this).attr('href')){
				$(this).addClass('active')
				$(this).parents('.nav-item-submenu').children()[0].click()
				return false;
			}
		})
	}
</script>
