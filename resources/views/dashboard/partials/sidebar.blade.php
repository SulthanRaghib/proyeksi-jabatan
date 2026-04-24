<aside class="left-sidebar" data-sidebarbg="skin6">
    <div class="scroll-sidebar" data-sidebarbg="skin6">
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                @php
                    $mainGroup = $menuGroups[0]['items'][0] ?? null;
                @endphp

                @if ($mainGroup)
                    @php
                        $isMainActive = isset($mainGroup['route']) && request()->routeIs($mainGroup['route']);
                    @endphp

                    <li class="sidebar-item {{ $isMainActive ? 'selected' : '' }}">
                        <a class="sidebar-link {{ $isMainActive ? 'active' : '' }}" href="{{ $mainGroup['url'] }}"
                            aria-expanded="false">
                            <i data-feather="{{ $mainGroup['icon'] }}" class="feather-icon"></i>
                            <span class="hide-menu">{{ $mainGroup['label'] }}</span>
                        </a>
                    </li>
                @endif

                @foreach ($menuGroups as $groupIndex => $group)
                    @continue($groupIndex === 0)

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
                            <a class="sidebar-link {{ $hasChildren ? 'has-arrow' : '' }} {{ $isOpen ? 'active' : '' }}"
                                href="{{ $hasChildren ? 'javascript:void(0)' : $item['url'] ?? 'javascript:void(0)' }}"
                                aria-expanded="{{ $isOpen ? 'true' : 'false' }}">
                                <i data-feather="{{ $item['icon'] }}" class="feather-icon"></i>
                                <span class="hide-menu">{{ $item['label'] }}</span>
                            </a>

                            @if ($hasChildren)
                                <ul aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                                    class="collapse first-level base-level-line {{ $isOpen ? 'in' : '' }}">
                                    @foreach ($children as $child)
                                        @php
                                            $isSubActive =
                                                isset($child['route']) && request()->routeIs($child['route']);
                                        @endphp
                                        <li class="sidebar-item {{ $isSubActive ? 'active' : '' }}">
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
                    <form action="{{ route('logout') }}" method="POST" class="px-3 py-2 mb-0">
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
