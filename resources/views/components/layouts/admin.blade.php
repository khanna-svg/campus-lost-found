@props(['title' => null])
@php
    $nav = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'match' => 'admin.dashboard'],
        ['label' => 'Report Review', 'route' => 'admin.reports.index', 'match' => 'admin.reports.*'],
        ['label' => 'Claim Verification', 'route' => 'admin.claims.index', 'match' => 'admin.claims.*'],
        ['label' => 'User Accounts', 'route' => 'admin.users.index', 'match' => 'admin.users.*'],
        ['label' => 'Item Categories', 'route' => 'admin.categories.index', 'match' => 'admin.categories.*'],
        ['label' => 'My Profile', 'route' => 'profile.show', 'match' => 'profile.*'],
    ];
@endphp
<x-layouts.base :title="$title ? 'Admin · '.$title : 'Admin'">
    <div class="min-h-screen lg:flex">
        {{-- Sidebar (desktop) / top bar (mobile) --}}
        <aside class="bg-slate-900 text-slate-300 lg:sticky lg:top-0 lg:h-screen lg:w-64 lg:shrink-0">
            <div class="flex h-16 items-center justify-between px-4 lg:px-6">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 font-semibold text-white">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>
                    </span>
                    Admin Console
                </a>
                <button type="button" data-nav-toggle="admin-menu" aria-expanded="false" aria-controls="admin-menu"
                        class="rounded-lg p-2 text-slate-300 hover:bg-slate-800 lg:hidden">
                    <span class="sr-only">Toggle menu</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                </button>
            </div>

            <div id="admin-menu" class="hidden px-3 pb-4 lg:block">
                <nav class="flex flex-col gap-1" aria-label="Admin">
                    @foreach ($nav as $item)
                        <a href="{{ route($item['route']) }}"
                           @class([
                               'rounded-lg px-3 py-2 text-sm font-medium',
                               'bg-slate-800 text-white' => request()->routeIs($item['match']),
                               'hover:bg-slate-800 hover:text-white' => ! request()->routeIs($item['match']),
                           ])
                           @if (request()->routeIs($item['match'])) aria-current="page" @endif>{{ $item['label'] }}</a>
                    @endforeach
                </nav>

                <div class="mt-6 border-t border-slate-800 pt-4">
                    <p class="px-3 text-[11px] font-medium uppercase tracking-wider text-slate-400">Institutional Account</p>
                    <p class="truncate px-3 text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                    <p class="truncate px-3 text-xs text-brand-300">{{ auth()->user()->email }}</p>
                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium hover:bg-slate-800 hover:text-white">Sign out</button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="min-w-0 flex-1 px-4 py-8 sm:px-8">
            {{ $slot }}
        </main>
    </div>
</x-layouts.base>
