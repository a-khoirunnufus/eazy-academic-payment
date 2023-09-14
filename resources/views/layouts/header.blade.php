<nav
    class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow @if(View::hasSection('window-size')) {{ '' }}  @else {{ 'container-xxl' }} @endif">
    <div class="navbar-container d-flex content">
        <div class="bookmark-wrapper d-flex align-items-center">
            <ul class="nav navbar-nav d-xl-none">
                <li class="nav-item"><a class="nav-link menu-toggle" href="#"><i class="ficon"
                            data-feather="menu"></i></a></li>
            </ul>
            <ul class="nav navbar-nav bookmark-icons align-items-center">
                <li class="nav-item d-none d-lg-block">
                    <i style="width:22px; height:22px" class="text-eazy" data-feather="grid"></i>
                </li>
                <li class="nav-item d-none d-lg-block">
                    <div class="ms-1">
                        <h4 class="h5 mb-0 fw-bolder text-eazy">Universitas Pembangunan Nasional Veteran Yogyakarta</h4>
                        <small class="mb-0 text-dark fw-bold">{{ config('ref.title_apps') }}</small>
                    </div>
                </li>
            </ul>
        </div>
        <ul class="nav navbar-nav align-items-center ms-auto">
            <li class="nav-item dropdown dropdown-notification me-25">
                <a class="nav-link" href="#" data-bs-toggle="dropdown">
                    <i data-feather="users" class="ficon"></i></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-media dropdown-menu-end">
                    <li class="dropdown-menu-header">
                        <div class="dropdown-header d-flex">
                            <h4 class="notification-title mb-0 me-auto">Switch User Group</h4>
                            <div class="badge rounded-pill badge-light-primary">{{ auth()->getAvailableRoles()->count() }} User Group</div>
                        </div>
                    </li>
                    <li class="scrollable-container media-list">
                        @foreach (auth()->getAvailableRoles() as $role)
                        <a class="d-flex" href="{{ url('switchrole' . '/' . $role->id) }}">
                            <div class="list-item d-flex align-items-start">
                                <div class="me-1">
                                    <div class="avatar bg-light-danger">
                                        <div class="avatar-content"><i class="avatar-icon" data-feather="user-check"></i></div>
                                    </div>
                                </div>
                                <div class="list-item-body flex-grow-1">
                                    <p class="media-heading"><span class="fw-bolder">{{ $role->name }}</span></p>
                                    <small class="notification-text">click to switch user group</small>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </li>
                </ul>
            </li>
            <li class="nav-item dropdown dropdown-notification me-25"><a class="nav-link" href="#"
                    data-bs-toggle="dropdown"><i class="ficon" data-feather="bell"></i><span
                        class="badge rounded-pill bg-danger badge-up">5</span></a>
                <ul class="dropdown-menu dropdown-menu-media dropdown-menu-end">
                    <li class="dropdown-menu-header">
                        <div class="dropdown-header d-flex">
                            <h4 class="notification-title mb-0 me-auto">Notifications</h4>
                            <div class="badge rounded-pill badge-light-primary">6 New</div>
                        </div>
                    </li>
                    <li class="scrollable-container media-list"><a class="d-flex" href="#">
                            <div class="list-item d-flex align-items-start">
                                <div class="me-1">
                                    <div class="avatar"><img
                                            src="{{ asset('images/defaultavatar.jpg" alt="avatar" width="32" height="32') }}">
                                    </div>
                                </div>
                                <div class="list-item-body flex-grow-1">
                                    <p class="media-heading"><span class="fw-bolder">Congratulation Sam
                                            🎉</span>winner!</p><small class="notification-text"> Won the monthly best
                                        seller badge.</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="dropdown-menu-footer"><a class="btn btn-info w-100" href="#">Read all notifications</a></li>
                </ul>
            </li>
            <li class="nav-item nav-search"><a class="nav-link nav-link-search"><i class="ficon"
                        data-feather="search"></i></a>
                <div class="search-input">
                    <div class="search-input-icon"><i data-feather="search"></i></div>
                    <input class="form-control input" type="text" placeholder="Explore Vuexy..." tabindex="-1"
                        data-search="search">
                    <div class="search-input-close"><i data-feather="x"></i></div>
                    <ul class="search-list search-list-main"></ul>
                </div>
            </li>
            <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-style"><i class="ficon" data-feather="moon"></i></a></li>


            <li class="nav-item dropdown dropdown-user"><a class="nav-link dropdown-toggle dropdown-user-link"
                    id="dropdown-user" href="#" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <div class="user-nav d-sm-flex d-none">
                        <span class="user-name fw-bolder">{{ auth()->user()->user_fullname }}</span>
                        <span class="user-status">{{ auth()->getActiveRole()->name }}</span>
                    </div>
                    <span class="avatar"><img class="round" src="{{ asset('images/defaultavatar.jpg') }}"
                            alt="avatar" height="40" width="40">
                        <span class="avatar-status-online"></span>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user"><a class="dropdown-item"
                        href="page-profile.html"><i class="me-50" data-feather="user"></i> Profile</a><a
                        class="dropdown-item" href="app-email.html"><i class="me-50" data-feather="mail"></i>
                        Inbox</a><a class="dropdown-item" href="app-todo.html"><i class="me-50"
                            data-feather="check-square"></i> Task</a><a class="dropdown-item" href="app-chat.html"><i
                            class="me-50" data-feather="message-square"></i> Chats</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="page-account-settings-account.html"><i class="me-50"
                            data-feather="settings"></i> Settings</a>
                    <a class="dropdown-item" href="page-pricing.html"><i class="me-50"
                            data-feather="credit-card"></i> Pricing</a>
                    <a class="dropdown-item" href="page-faq.html"><i class="me-50" data-feather="help-circle"></i>
                        FAQ</a>
                    <a class="dropdown-item" href="javascript:_logout()"><i class="me-50"
                            data-feather="power"></i> Logout</a>
                </div>
            </li>
        </ul>
    </div>
</nav>

<form action="{{ url('logout') }}" id="logout-form" method="post">
    @csrf
</form>
<script>
    function _logout(){
        document.getElementById("logout-form").submit();
    }
</script>
