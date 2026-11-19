@props(['title' => null])
@php
    $nav = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'match' => 'admin.dashboard'],
        ['label' => 'Claims Management', 'route' => 'admin.claims.index', 'match' => 'admin.claims.*'],
        ['label' => 'Browse Directory', 'route' => 'items.index', 'match' => 'items.*'],
    ];
@endphp
<x-layouts.base :title="$title ? 'Admin · '.$title : 'Campus Lost & Found · Admin Portal'">
    {{-- Top Header Navbar (Identical layout style to student/user side) --}}
    <header class="sticky top-0 z-40 border-b border-zinc-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            {{-- Brand & Desktop Navigation --}}
            <div class="flex items-center gap-6">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 font-bold tracking-tight text-zinc-900">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-black text-white shadow-xs">
                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                        </svg>
                    </span>
                    <span class="text-base font-black tracking-tight text-black">Admin Portal</span>
                </a>

                {{-- Desktop Navigation Links in Header --}}
                <nav class="hidden items-center gap-1 sm:flex" aria-label="Admin Navigation">
                    @foreach ($nav as $item)
                        <a href="{{ route($item['route']) }}"
                           @class([
                               'rounded-xl px-3.5 py-2 text-xs font-semibold transition-colors',
                               'bg-zinc-100 text-black font-bold' => request()->routeIs($item['match']),
                               'text-zinc-600 hover:bg-zinc-50 hover:text-black' => ! request()->routeIs($item['match']),
                           ])
                           @if (request()->routeIs($item['match'])) aria-current="page" @endif>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>
            </div>

            {{-- Right Header Actions: User Profile Dropdown --}}
            <div class="flex items-center gap-3">
                @auth
                    {{-- Profile Dropdown --}}
                    <div class="relative hidden sm:block" id="admin-user-menu-container">
                        <button type="button"
                                id="admin-user-menu-button"
                                data-dropdown-toggle="admin-user-menu-dropdown"
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

                        <div id="admin-user-menu-dropdown"
                             class="hidden absolute right-0 mt-2 w-56 origin-top-right rounded-2xl bg-white p-2 shadow-lg ring-1 ring-zinc-900/10 focus:outline-none z-50"
                             role="menu"
                             aria-orientation="vertical"
                             aria-labelledby="admin-user-menu-button">
                            <div class="border-b border-zinc-100 px-3 py-2.5">
                                <p class="truncate text-sm font-semibold text-zinc-900">{{ auth()->user()->name }}</p>
                                <p class="truncate text-xs text-zinc-500 font-medium">{{ auth()->user()->email }}</p>
                            </div>

                            <div class="py-1">
                                <a href="{{ route('profile.show') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium text-zinc-700 hover:bg-zinc-50 hover:text-black" role="menuitem">
                                    <svg class="h-4 w-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>
                                    Account Profile
                                </a>
                            </div>

                            <div class="border-t border-zinc-100 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-xs font-semibold text-red-600 hover:bg-red-50" role="menuitem">
                                        <svg class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                                        </svg>
                                        Sign out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endauth

                {{-- Mobile Menu Hamburger --}}
                <button type="button" data-nav-toggle="admin-mobile-menu" aria-expanded="false" aria-controls="admin-mobile-menu"
                        class="rounded-lg p-2 text-zinc-600 hover:bg-zinc-100 sm:hidden">
                    <span class="sr-only">Toggle menu</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile dropdown menu --}}
        <div id="admin-mobile-menu" class="hidden border-t border-zinc-200 bg-white sm:hidden">
            @auth
                <div class="border-b border-zinc-100 px-4 py-3 bg-zinc-50">
                    <p class="text-sm font-semibold text-zinc-900">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-zinc-500">{{ auth()->user()->email }}</p>
                </div>
            @endauth
            <nav class="flex flex-col gap-1 px-4 py-3" aria-label="Mobile Admin Navigation">
                @foreach ($nav as $item)
                    <a href="{{ route($item['route']) }}" class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100">{{ $item['label'] }}</a>
                @endforeach
                @auth
                    <a href="{{ route('profile.show') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100">Account Profile</a>
                    <form method="POST" action="{{ route('logout') }}" class="pt-1 border-t border-zinc-100">
                        @csrf
                        <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-red-600 hover:bg-red-50">Sign out</button>
                    </form>
                @endauth
            </nav>
        </div>
    </header>

    {{-- Main Admin View Container (Full width, max-w-7xl centered) --}}
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        {{ $slot }}
    </main>
</x-layouts.base>
