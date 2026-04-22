<aside class="left-sidebar" data-sidebarbg="skin6">
    <div class="scroll-sidebar" data-sidebarbg="skin6">
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                @foreach ($menuGroups as $group)
                    <li class="list-divider"></li>
                    <li class="nav-small-cap"><span class="hide-menu">{{ $group['title'] }}</span></li>

                    @foreach ($group['items'] as $item)
                        @php
                            $children = $item['children'] ?? [];
                            $hasChildren = count($children) > 0;
                            $isItemActive = isset($item['route']) && request()->routeIs($item['route']);
                            $isChildActive = collect($children)->contains(function ($child) {
                                return isset($child['route']) && request()->routeIs($child['route']);
                            });
                            $isOpen = $isItemActive || $isChildActive;
                        @endphp

                        <li class="sidebar-item {{ $isOpen ? 'selected' : '' }}">
                            <a class="sidebar-link {{ $hasChildren ? 'has-arrow' : '' }} {{ $isItemActive && !$hasChildren ? 'active' : '' }}"
                                href="{{ $hasChildren ? 'javascript:void(0)' : $item['url'] ?? 'javascript:void(0)' }}"
                                aria-expanded="{{ $isOpen ? 'true' : 'false' }}">
                                <i data-feather="{{ $item['icon'] }}" class="feather-icon"></i>
                                <span class="hide-menu">{{ $item['label'] }}</span>
                            </a>

                            @if ($hasChildren)
                                <ul aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                                    class="collapse first-level {{ $isOpen ? 'in' : '' }}">
                                    @foreach ($children as $child)
                                        @php
                                            $isSubActive =
                                                isset($child['route']) && request()->routeIs($child['route']);
                                        @endphp
                                        <li class="sidebar-item">
                                            <a href="{{ $child['url'] }}"
                                                class="sidebar-link {{ $isSubActive ? 'active' : '' }}">
                                                <span class="hide-menu">{{ $child['label'] }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
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
