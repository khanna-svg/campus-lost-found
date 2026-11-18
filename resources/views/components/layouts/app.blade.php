@props(['title' => null])
@php
    $nav = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'match' => 'dashboard'],
        ['label' => 'Profile', 'route' => 'profile.show', 'match' => 'profile.*'],
    ];
@endphp
<x-layouts.base :title="$title">
    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-semibold text-slate-900">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-600 text-white">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                </span>
                <span class="hidden sm:inline">{{ config('app.name') }}</span>
            </a>

            <nav class="hidden items-center gap-1 md:flex" aria-label="Main">
                @foreach ($nav as $item)
                    <a href="{{ route($item['route']) }}"
                       @class([
                           'rounded-lg px-3 py-2 text-sm font-medium',
                           'bg-brand-50 text-brand-700' => request()->routeIs($item['match']),
                           'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! request()->routeIs($item['match']),
                       ])
                       @if (request()->routeIs($item['match'])) aria-current="page" @endif>{{ $item['label'] }}</a>
                @endforeach
            </nav>

            <div class="flex items-center gap-3">
                <span class="hidden text-sm text-slate-600 sm:inline">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                    @csrf
                    <button type="submit" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900">Sign out</button>
                </form>
                <button type="button" data-nav-toggle="mobile-menu" aria-expanded="false" aria-controls="mobile-menu"
                        class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 md:hidden">
                    <span class="sr-only">Toggle menu</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                </button>
            </div>
        </div>

        <div id="mobile-menu" class="hidden border-t border-slate-200 bg-white md:hidden">
            <nav class="mx-auto flex max-w-6xl flex-col gap-1 px-4 py-3" aria-label="Mobile">
                @foreach ($nav as $item)
                    <a href="{{ route($item['route']) }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">{{ $item['label'] }}</a>
                @endforeach
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-slate-700 hover:bg-slate-100">Sign out</button>
                </form>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
        {{ $slot }}
    </main>
</x-layouts.base>
