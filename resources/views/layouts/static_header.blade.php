<nav
    class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow @if(View::hasSection('window-size')) {{ '' }}  @else {{ 'container-xxl' }} @endif">
    <div class="navbar-container d-flex content">
        <div class="bookmark-wrapper d-flex align-items-center">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a class="nav-link menu-toggle btn btn-sm btn-icon" href="#">
                        <i style="width:22px; height:22px" class="text-eazy" data-feather="grid"></i>
                    </a>
                    <!-- <a class="nav-link menu-toggle" href="#">
                        <i class="ficon" data-feather="menu"></i>
                    </a> -->
                </li>
            </ul>
            <ul class="nav navbar-nav bookmark-icons align-items-center">
                <li class="nav-item d-none d-lg-block">
                    <div class="ms-1">
                        <h4 class="h5 mb-0 fw-bolder text-eazy">Universitas Pembangunan Nasional Veteran Yogyakarta</h4>
                        <small class="mb-0 text-dark fw-bold">EAZY - Educational Smart System</small>
                    </div>
                </li>
            </ul>
        </div>
        <ul class="nav navbar-nav align-items-center ms-auto">

            <li class="nav-item dropdown dropdown-user ps-1"><a class="nav-link dropdown-toggle dropdown-user-link"
                    id="dropdown-user" href="#" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <span class="avatar me-1">
                        <img class="round" src="{{ url('images/defaultavatar.jpg') }}" style="height: 40px; width: 40px">
                        <span class="avatar-status-online"></span>
                    </span>
                    <div class="user-nav d-sm-flex d-none m-0">
                        <span class="user-name fw-bolder">{{ auth()->user()->user_fullname }}</span>
                        <span class="user-status">{{ auth()->user()->roles->first()?->role->name ?? '' }}</span>
                    </div>
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

    document.addEventListener("DOMContentLoaded", () => {
        $('.menu-toggle').click(function() {
            if ($('.main-menu').hasClass('expanded')) {
                $('.main-menu').removeClass('expanded');
                $('.main-menu .navbar-header').removeClass('expanded');
            }
        })
    });
</script>
