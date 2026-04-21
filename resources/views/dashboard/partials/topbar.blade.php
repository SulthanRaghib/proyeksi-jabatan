<header class="topbar" data-navbarbg="skin6">
    <nav class="navbar top-navbar navbar-expand-lg">
        <div class="navbar-header" data-logobg="skin6">
            <a class="nav-toggler waves-effect waves-light d-block d-lg-none" href="javascript:void(0)">
                <i class="ti-menu ti-close"></i>
            </a>

            <div class="navbar-brand">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('assets/images/freedashDark.svg') }}" alt="logo" class="img-fluid">
                </a>
            </div>

            <a class="topbartoggler d-block d-lg-none waves-effect waves-light" href="javascript:void(0)"
                data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="ti-more"></i>
            </a>
        </div>

        <div class="navbar-collapse collapse" id="navbarSupportedContent">
            <ul class="navbar-nav float-left me-auto ms-3 ps-1">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle pl-md-3 position-relative" href="javascript:void(0)"
                        id="bell" role="button" data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <span><i data-feather="bell" class="svg-icon"></i></span>
                        <span class="badge text-bg-primary notify-no rounded-circle">{{ count($notifications) }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-left mailbox animated bounceInDown">
                        <ul class="list-style-none">
                            <li>
                                <div class="message-center notifications position-relative">
                                    @foreach ($notifications as $notification)
                                        <a href="javascript:void(0)"
                                            class="message-item d-flex align-items-center border-bottom px-3 py-2">
                                            <div class="btn {{ $notification['iconClass'] }} rounded-circle btn-circle">
                                                <i data-feather="{{ $notification['icon'] }}" class="text-white"></i>
                                            </div>
                                            <div class="w-75 d-inline-block v-middle ps-2">
                                                <h6 class="message-title mb-0 mt-1">{{ $notification['title'] }}</h6>
                                                <span
                                                    class="font-12 text-nowrap d-block text-muted">{{ $notification['message'] }}</span>
                                                <span
                                                    class="font-12 text-nowrap d-block text-muted">{{ $notification['time'] }}</span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </li>
                            <li>
                                <a class="nav-link pt-3 text-center text-dark" href="javascript:void(0);">
                                    <strong>Check all notifications</strong>
                                    <i class="fa fa-angle-right"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item d-none d-md-block">
                    <a class="nav-link" href="javascript:void(0)">
                        <div class="customize-input">
                            <select class="custom-select form-control bg-white custom-radius custom-shadow border-0">
                                <option selected>EN</option>
                                <option value="1">ID</option>
                            </select>
                        </div>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav float-end">
                <li class="nav-item d-none d-md-block">
                    <a class="nav-link" href="javascript:void(0)">
                        <form>
                            <div class="customize-input">
                                <input class="form-control custom-shadow custom-radius border-0 bg-white" type="search"
                                    placeholder="Search" aria-label="Search">
                                <i class="form-control-icon" data-feather="search"></i>
                            </div>
                        </form>
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <img src="{{ asset('assets/images/users/profile-pic.jpg') }}" alt="user"
                            class="rounded-circle" width="40">
                        <span class="ms-2 d-none d-lg-inline-block">
                            <span>Hello,</span>
                            <span class="text-dark">Admin</span>
                            <i data-feather="chevron-down" class="svg-icon"></i>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-right user-dd animated flipInY">
                        <a class="dropdown-item" href="javascript:void(0)">
                            <i data-feather="user" class="svg-icon me-2 ms-1"></i> My Profile
                        </a>
                        <a class="dropdown-item" href="javascript:void(0)">
                            <i data-feather="settings" class="svg-icon me-2 ms-1"></i> Account Setting
                        </a>
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('logout') }}" method="POST" class="px-3 py-2">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                <i data-feather="power" class="svg-icon me-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>
