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
                <a class="d-flex align-items-center fw-bold" href="#">
                    <i data-feather="credit-card"></i>
                    <span class="menu-title text-truncate">Pembayaran</span>
                </a>
                <ul class="menu-content">
                    <li class="nav-item mb-50 {{ 'student.payment.index' == request()->route()->getName() ? 'active' : '' }}">
                        <a class="d-flex align-items-center fw-bold" href="{{ url('student/payment') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-title text-truncate">Tagihan</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item mb-50 {{ 'student.overpayment.index' == request()->route()->getName() ? 'active' : '' }}">
                <a class="d-flex align-items-center fw-bold" href="{{ url('student/overpayment') }}">
                    <i data-feather="circle"></i>
                    <span class="menu-title text-truncate">Kelebihan Bayar</span>
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
