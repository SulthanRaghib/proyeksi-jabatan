<aside class="left-sidebar" data-sidebarbg="skin6">
    <div class="scroll-sidebar" data-sidebarbg="skin6">
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                @foreach ($menuGroups as $group)
                    <li class="list-divider"></li>
                    <li class="nav-small-cap"><span class="hide-menu">{{ $group['title'] }}</span></li>

                    @foreach ($group['items'] as $item)
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="{{ $item['url'] }}" aria-expanded="false">
                                <i data-feather="{{ $item['icon'] }}" class="feather-icon"></i>
                                <span class="hide-menu">{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                @endforeach

                <li class="list-divider"></li>
                <li class="sidebar-item">
                    <form action="{{ route('logout') }}" method="POST" class="px-3 py-2">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                            <i data-feather="log-out" class="feather-icon me-2"></i>
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>
