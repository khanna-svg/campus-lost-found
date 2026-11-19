<x-layouts.base title="Campus Lost & Found · Welcome">
    {{-- Top Navigation Header --}}
    <header class="sticky top-0 z-40 border-b border-zinc-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
            {{-- Brand Logo --}}
            <a href="{{ route('welcome') }}" class="flex items-center gap-2.5 font-bold tracking-tight text-zinc-900">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-black text-white shadow-xs">
                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </span>
                <span class="text-base font-black tracking-tight text-black">{{ config('app.name', 'Campus Lost & Found') }}</span>
            </a>

            {{-- Nav Links & Action Buttons --}}
            <nav class="flex items-center gap-3">
                <a href="{{ route('items.index') }}" class="text-xs font-semibold text-zinc-600 hover:text-black transition px-2 py-1">
                    Directory
                </a>

                @auth
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}"
                           class="inline-flex items-center gap-1.5 rounded-xl bg-black px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-zinc-800 transition">
                            Admin Portal →
                        </a>
                    @else
                        <a href="{{ route('items.index') }}"
                           class="inline-flex items-center gap-1.5 rounded-xl bg-black px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-zinc-800 transition">
                            Browse Items →
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-1.5 rounded-xl bg-black px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-zinc-800 transition">
                        Sign In
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    {{-- Main Landing Content --}}
    <main class="space-y-16 pb-20">
        {{-- Hero Section --}}
        <section class="pt-16 sm:pt-24 px-4 text-center max-w-4xl mx-auto space-y-6">
            {{-- Official Tag --}}
            <div class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-zinc-50 px-3.5 py-1 text-xs font-semibold text-zinc-700 shadow-2xs">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                Official Campus Property Recovery Portal
            </div>

            {{-- Main Title --}}
            <h1 class="text-4xl font-black tracking-tight text-zinc-900 sm:text-6xl sm:leading-[1.1]">
                Find what you’ve lost.<br class="hidden sm:inline">
                Return what you’ve found.
            </h1>

            {{-- Subtitle --}}
            <p class="text-base sm:text-lg text-zinc-500 max-w-2xl mx-auto leading-relaxed">
                A simple, secure, and centralized portal connecting students, faculty, and campus staff to help reunite missing belongings with their rightful owners.
            </p>

            {{-- Call To Action Buttons --}}
            <div class="flex flex-wrap items-center justify-center gap-3 pt-3">
                <a href="{{ route('items.index') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-2xl bg-black px-6 py-3.5 text-sm font-bold text-white shadow-sm hover:bg-zinc-800 transition-all hover:-translate-y-0.5">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    Browse Directory
                </a>
                <a href="{{ route('items.report.create') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-2xl border border-zinc-300 bg-white px-6 py-3.5 text-sm font-bold text-zinc-800 shadow-2xs hover:bg-zinc-50 hover:border-zinc-400 transition-all hover:-translate-y-0.5">
                    <svg class="h-4 w-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Report an Item
                </a>
            </div>
        </section>

        {{-- How It Works Section (3 Simple Cards) --}}
        <section class="max-w-5xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-10 space-y-1">
                <h2 class="text-2xl font-black tracking-tight text-zinc-900 sm:text-3xl">How It Works</h2>
                <p class="text-xs sm:text-sm text-zinc-500">Three easy steps to recover or return belongings.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Step 1 --}}
                <div class="rounded-3xl border border-zinc-200 bg-white p-7 shadow-xs space-y-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-zinc-100 font-black text-sm text-zinc-900">
                        01
                    </div>
                    <h3 class="text-base font-bold text-zinc-900">Report an Item</h3>
                    <p class="text-xs leading-relaxed text-zinc-500">
                        Lost something on campus or found an item left behind? Submit a quick report with description, date, location, and photos.
                    </p>
                </div>

                {{-- Step 2 --}}
                <div class="rounded-3xl border border-zinc-200 bg-white p-7 shadow-xs space-y-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-zinc-100 font-black text-sm text-zinc-900">
                        02
                    </div>
                    <h3 class="text-base font-bold text-zinc-900">Search &amp; Claim</h3>
                    <p class="text-xs leading-relaxed text-zinc-500">
                        Browse the real-time directory. If you spot your item, submit an ownership claim with details only the true owner would know.
                    </p>
                </div>

                {{-- Step 3 --}}
                <div class="rounded-3xl border border-zinc-200 bg-white p-7 shadow-xs space-y-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-zinc-100 font-black text-sm text-zinc-900">
                        03
                    </div>
                    <h3 class="text-base font-bold text-zinc-900">Office Pickup</h3>
                    <p class="text-xs leading-relaxed text-zinc-500">
                        Once administrators approve your claim, you receive an email confirmation. Collect your item securely at the campus office.
                    </p>
                </div>
            </div>
        </section>

        {{-- Office Location & Retrieval Details --}}
        <section class="max-w-4xl mx-auto px-4 sm:px-6">
            <div class="rounded-3xl border border-zinc-200 bg-zinc-50/60 p-8 sm:p-10 space-y-6 text-center sm:text-left">
                <div class="flex flex-col sm:flex-row items-center sm:items-start justify-between gap-6">
                    <div class="space-y-2 max-w-lg">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Campus Office</span>
                        <h3 class="text-xl font-black text-zinc-900">Physical Retrieval &amp; Turn-In Desk</h3>
                        <p class="text-xs text-zinc-500 leading-relaxed">
                            All found property turned in is stored securely in the central office until verified owners claim their items.
                        </p>
                    </div>

                    <a href="{{ route('items.index') }}"
                       class="inline-flex shrink-0 items-center gap-1.5 rounded-xl bg-black px-4 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-zinc-800 transition">
                        View Current Directory →
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-zinc-200/80 text-xs">
                    <div>
                        <span class="block font-bold text-zinc-900">Location</span>
                        <span class="text-zinc-500">Administration Hall, Room 102</span>
                    </div>
                    <div>
                        <span class="block font-bold text-zinc-900">Office Hours</span>
                        <span class="text-zinc-500">Monday – Friday, 8:00 AM – 5:00 PM</span>
                    </div>
                    <div>
                        <span class="block font-bold text-zinc-900">Requirements</span>
                        <span class="text-zinc-500">Valid Student or Faculty ID</span>
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- Minimal Footer --}}
    <footer class="border-t border-zinc-200 bg-white py-8">
        <div class="mx-auto flex max-w-6xl flex-col sm:flex-row items-center justify-between gap-4 px-4 sm:px-6 text-xs text-zinc-500">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Campus Lost & Found') }}. All rights reserved.</p>
            <div class="flex items-center gap-4">
                <a href="{{ route('items.index') }}" class="hover:text-black transition">Browse Directory</a>
                <span class="text-zinc-300">·</span>
                <a href="{{ route('items.report.create') }}" class="hover:text-black transition">Report Item</a>
                <span class="text-zinc-300">·</span>
                <a href="{{ route('login') }}" class="hover:text-black transition">Sign In</a>
            </div>
        </div>
    </footer>
</x-layouts.base>
