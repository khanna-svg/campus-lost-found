@props(['title' => null])
@php
    $nav = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'match' => 'admin.dashboard'],
        ['label' => 'Claims Management', 'route' => 'admin.claims.index', 'match' => 'admin.claims.*'],
        ['label' => 'Browse Directory', 'route' => 'items.index', 'match' => 'items.*'],
    ];
@endphp
<x-layouts.base :title="$title ? 'Admin · '.$title : 'Campus Lost & Found · Admin Console'">
    <div class="min-h-screen lg:flex bg-zinc-50">
        {{-- Sidebar (desktop) / top bar (mobile) in Monochrome Black & White --}}
        <aside class="bg-black text-zinc-300 lg:sticky lg:top-0 lg:h-screen lg:w-64 lg:shrink-0 border-r border-zinc-800">
            <div class="flex h-16 items-center justify-between px-4 lg:px-6">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 font-bold tracking-tight text-white">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-white text-black shadow-xs">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                        </svg>
                    </span>
                    <span>Admin Portal</span>
                </a>
                <button type="button" data-nav-toggle="admin-menu" aria-expanded="false" aria-controls="admin-menu"
                        class="rounded-lg p-2 text-zinc-300 hover:bg-zinc-800 lg:hidden">
                    <span class="sr-only">Toggle menu</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                </button>
            </div>

            <div id="admin-menu" class="hidden px-3 pb-4 lg:block">
                <nav class="flex flex-col gap-1 pt-2" aria-label="Admin Navigation">
                    @foreach ($nav as $item)
                        <a href="{{ route($item['route']) }}"
                           @class([
                               'rounded-xl px-3.5 py-2.5 text-xs font-semibold transition',
                               'bg-zinc-800 text-white font-bold' => request()->routeIs($item['match']),
                               'text-zinc-400 hover:bg-zinc-900 hover:text-white' => ! request()->routeIs($item['match']),
                           ])
                           @if (request()->routeIs($item['match'])) aria-current="page" @endif>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="mt-8 border-t border-zinc-800 pt-4 px-3 space-y-1">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">Administrator</p>
                    <p class="truncate text-xs font-bold text-white">{{ auth()->user()->name }}</p>
                    <p class="truncate text-[11px] text-zinc-400">{{ auth()->user()->email }}</p>

                    <div class="pt-3">
                        <a href="{{ route('profile.show') }}" class="block text-xs text-zinc-400 hover:text-white py-1">
                            Account Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="pt-1">
                            @csrf
                            <button type="submit" class="w-full text-left text-xs font-semibold text-rose-400 hover:text-rose-300 py-1">
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main Admin View Container --}}
        <main class="min-w-0 flex-1 px-4 py-8 sm:px-8">
            {{ $slot }}
        </main>
    </div>
</x-layouts.base>
