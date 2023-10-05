<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow @yield('sidebar-size')" data-scroll-to-active="true">
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
            <li class="nav-item mb-50 {{ 'payment.student-invoice.index' == request()->route()->getName() ? 'active' : '' }}">
                <a class="d-flex align-items-center fw-bold" href="{{ route('payment.student-invoice.index') }}">
                    <i data-feather="file-text"></i>
                    <span class="menu-title text-truncate">Tagihan</span>
                </a>
            </li>
            <li class="nav-item mb-50 {{ 'payment.student-balance.index' == request()->route()->getName() ? 'active' : '' }}">
                <a class="d-flex align-items-center fw-bold" href="{{ route('payment.student-balance.index') }}">
                    <i data-feather="file-text"></i>
                    <span class="menu-title text-truncate">Saldo Mahasiswa</span>
                </a>
            </li>
            <li class="nav-item mb-50 {{ 'payment.student-credit.index' == request()->route()->getName() ? 'active' : '' }}">
                <a class="d-flex align-items-center fw-bold" href="{{ route('payment.student-credit.index') }}">
                    <i data-feather="file-text"></i>
                    <span class="menu-title text-truncate">Pengajuan Cicilan</span>
                </a>
            </li>
            <li class="nav-item mb-50 {{ 'payment.student-dispensation.index' == request()->route()->getName() ? 'active' : '' }}">
                <a class="d-flex align-items-center fw-bold" href="{{ route('payment.student-dispensation.index') }}">
                    <i data-feather="file-text"></i>
                    <span class="menu-title text-truncate">Pengajuan Dispensasi</span>
                </a>
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
