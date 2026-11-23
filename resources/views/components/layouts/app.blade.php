@props(['title' => null])
@php
    $nav = [
        ['label' => 'Lost & Found', 'route' => 'items.index', 'match' => 'items.*'],
        ['label' => 'Report an item', 'route' => 'items.report.create', 'match' => 'items.report.*'],
    ];
    if (auth()->check()) {
        $nav[] = ['label' => 'Claims', 'route' => 'claims.index', 'match' => 'claims.*'];
    }
@endphp
<x-layouts.base :title="$title">
    <header class="sticky top-0 z-40 border-b border-zinc-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
            <a href="{{ route('items.index') }}" class="flex items-center">
                <x-brand-logo />
            </a>

            <nav class="hidden items-center gap-1 sm:flex" aria-label="Main">
                @foreach ($nav as $item)
                    <a href="{{ route($item['route']) }}"
                       @class([
                           'rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                           'bg-zinc-100 text-black font-semibold' => request()->routeIs($item['match']),
                           'text-zinc-600 hover:bg-zinc-50 hover:text-black' => ! request()->routeIs($item['match']),
                       ])
                       @if (request()->routeIs($item['match'])) aria-current="page" @endif>{{ $item['label'] }}</a>
                @endforeach

                @auth
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}"
                           class="rounded-lg px-3 py-2 text-sm font-semibold text-zinc-700 hover:bg-zinc-100 hover:text-black transition ml-1">
                            Admin Portal
                        </a>
                    @endif
                @endauth
            </nav>

            <div class="flex items-center gap-2">
                {{-- Notification Bell --}}
                @auth
                    @php
                        $navUnreadCount = auth()->user()->unreadNotifications()->count();
                    @endphp
                    <a href="{{ route('notifications.index') }}"
                       class="relative rounded-full p-2 text-zinc-600 hover:bg-zinc-100 hover:text-black transition focus:outline-none focus:ring-2 focus:ring-black"
                       title="Notification Center">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                        @if ($navUnreadCount > 0)
                            <span class="absolute top-1.5 right-1.5 flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-zinc-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-black"></span>
                            </span>
                        @endif
                    </a>
                @endauth

                @auth
                    {{-- User Profile Dropdown --}}
                    <div class="relative hidden sm:block" id="user-menu-container">
                        <button type="button"
                                id="user-menu-button"
                                data-dropdown-toggle="user-menu-dropdown"
                                aria-expanded="false"
                                aria-haspopup="true"
                                class="flex items-center gap-2.5 rounded-full p-1.5 text-zinc-700 transition hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-black">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-black font-semibold text-white">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <div class="text-left leading-tight hidden md:block">
                                <span class="block text-xs font-semibold text-zinc-900">{{ auth()->user()->name }}</span>
                                <span class="block text-[11px] text-zinc-500">{{ auth()->user()->email }}</span>
                            </div>
                            <svg class="h-4 w-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>

                        <div id="user-menu-dropdown"
                             class="hidden absolute right-0 mt-2 w-64 origin-top-right rounded-2xl bg-white p-2 shadow-lg ring-1 ring-zinc-900/10 focus:outline-none z-50"
                             role="menu"
                             aria-orientation="vertical"
                             aria-labelledby="user-menu-button">
                            <div class="border-b border-zinc-100 px-3 py-2.5">
                                <p class="text-[11px] font-medium text-zinc-400 uppercase tracking-wider">Institutional Account</p>
                                <p class="truncate text-sm font-semibold text-zinc-900">{{ auth()->user()->name }}</p>
                                <p class="truncate text-xs text-zinc-600 font-medium">{{ auth()->user()->email }}</p>
                            </div>

                            <div class="py-1">
                                <a href="{{ route('claims.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-50 hover:text-black" role="menuitem">
                                    <svg class="h-4 w-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                    Claims Management
                                </a>
                                <a href="{{ route('profile.show') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-50 hover:text-black" role="menuitem">
                                    <svg class="h-4 w-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                                    Account Profile
                                </a>
                            </div>

                            <div class="border-t border-zinc-100 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50" role="menuitem">
                                        <svg class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" /></svg>
                                        Sign out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="rounded-xl bg-black px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-zinc-800 transition">
                        Log in
                    </a>
                @endauth

                {{-- Mobile hamburger button --}}
                <button type="button" data-nav-toggle="mobile-menu" aria-expanded="false" aria-controls="mobile-menu"
                        class="rounded-lg p-2 text-zinc-600 hover:bg-zinc-100 sm:hidden">
                    <span class="sr-only">Toggle menu</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                </button>
            </div>
        </div>

        {{-- Mobile dropdown menu --}}
        <div id="mobile-menu" class="hidden border-t border-zinc-200 bg-white sm:hidden">
            @auth
                <div class="border-b border-zinc-100 px-4 py-3 bg-zinc-50">
                    <p class="text-sm font-semibold text-zinc-900">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-zinc-600 font-medium">{{ auth()->user()->email }}</p>
                </div>
            @endauth
            <nav class="flex flex-col gap-1 px-4 py-3" aria-label="Mobile">
                @foreach ($nav as $item)
                    <a href="{{ route($item['route']) }}" class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100">{{ $item['label'] }}</a>
                @endforeach
                @auth
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-black bg-zinc-100">
                            Admin Portal
                        </a>
                    @endif
                    <a href="{{ route('profile.show') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100">Account Profile</a>
                    <form method="POST" action="{{ route('logout') }}" class="pt-1 border-t border-zinc-100">
                        @csrf
                        <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-red-600 hover:bg-red-50">Sign out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-bold text-zinc-900 hover:bg-zinc-100">Log in</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
        {{ $slot }}
    </main>
</x-layouts.base>
