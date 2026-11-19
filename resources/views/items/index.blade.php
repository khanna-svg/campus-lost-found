<x-layouts.app title="Browse Campus Belongings · Directory">
    <div class="mx-auto max-w-7xl space-y-8">
        {{-- Hero & Discovery Overview --}}
        <div class="rounded-3xl bg-gradient-to-br from-brand-900 via-brand-800 to-slate-900 p-6 sm:p-10 text-white shadow-xl relative overflow-hidden">
            <div class="absolute -right-12 -top-12 h-64 w-64 rounded-full bg-brand-500/10 blur-3xl pointer-events-none"></div>
            <div class="absolute -left-12 -bottom-12 h-64 w-64 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-3xl space-y-4">
                <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-medium backdrop-blur">
                    <span class="flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Verified Campus Directory
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl text-white">
                    Search & Discover Campus Belongings
                </h1>
                <p class="text-sm sm:text-base text-brand-100/90 leading-relaxed">
                    Check turned-in items to recover what you've misplaced, or view missing reports to help fellow students and faculty reunite with their lost belongings.
                </p>

                {{-- Quick Metrics Row --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-4 sm:pt-6">
                    <div class="rounded-2xl bg-white/10 p-3 sm:p-4 backdrop-blur ring-1 ring-white/10">
                        <span class="block text-2xl font-black text-white">{{ number_format($counts['total_found']) }}</span>
                        <span class="text-xs text-brand-200">Found Items Waiting</span>
                    </div>
                    <div class="rounded-2xl bg-white/10 p-3 sm:p-4 backdrop-blur ring-1 ring-white/10">
                        <span class="block text-2xl font-black text-white">{{ number_format($counts['total_lost']) }}</span>
                        <span class="text-xs text-brand-200">Actively Missing Reports</span>
                    </div>
                    <div class="col-span-2 sm:col-span-1 rounded-2xl bg-white/10 p-3 sm:p-4 backdrop-blur ring-1 ring-white/10">
                        <span class="block text-2xl font-black text-emerald-400">{{ number_format($counts['total_resolved']) }}</span>
                        <span class="text-xs text-brand-200">Reunited with Owners</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search & Filters Panel --}}
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/80">
            <form method="GET" action="{{ route('items.index') }}" id="search-filter-form" class="space-y-6">
                {{-- Top Search Bar & Type Selector --}}
                <div class="flex flex-col lg:flex-row gap-4 items-stretch lg:items-center justify-between">
                    {{-- Search Input with Icon --}}
                    <div class="relative flex-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                        <input type="text"
                               name="q"
                               value="{{ $search }}"
                               placeholder="Search by keyword, item name, brand, or color..."
                               class="w-full rounded-2xl border-0 py-3.5 pl-11 pr-10 text-sm text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-brand-600">
                        @if ($search !== '')
                            <a href="{{ route('items.index', request()->except('q', 'page')) }}"
                               class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600"
                               title="Clear search query">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </a>
                        @endif
                    </div>

                    {{-- Type Segmented Pill Control --}}
                    <div class="inline-flex p-1 rounded-2xl bg-slate-100 ring-1 ring-slate-200 shrink-0 self-start lg:self-auto">
                        <button type="submit"
                                name="type"
                                value="all"
                                @class([
                                    'px-4 py-2 text-xs font-semibold rounded-xl transition',
                                    'bg-white text-slate-900 shadow-sm' => $type === 'all',
                                    'text-slate-600 hover:text-slate-900' => $type !== 'all',
                                ])>
                            All Belongings
                        </button>
                        <button type="submit"
                                name="type"
                                value="found"
                                @class([
                                    'px-4 py-2 text-xs font-semibold rounded-xl transition flex items-center gap-1.5',
                                    'bg-white text-emerald-700 shadow-sm' => $type === 'found',
                                    'text-slate-600 hover:text-slate-900' => $type !== 'found',
                                ])>
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            Found Items
                        </button>
                        <button type="submit"
                                name="type"
                                value="lost"
                                @class([
                                    'px-4 py-2 text-xs font-semibold rounded-xl transition flex items-center gap-1.5',
                                    'bg-white text-amber-700 shadow-sm' => $type === 'lost',
                                    'text-slate-600 hover:text-slate-900' => $type !== 'lost',
                                ])>
                            <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                            Lost Reports
                        </button>
                    </div>
                </div>

                {{-- Secondary Filters Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 pt-2 border-t border-slate-100">
                    {{-- Category Filter --}}
                    <div>
                        <label for="category" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
                            Category
                        </label>
                        <select name="category"
                                id="category"
                                onchange="this.form.submit()"
                                class="w-full rounded-xl border-0 py-2.5 pl-3 pr-8 text-xs text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-brand-600">
                            <option value="">All Categories</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected($categoryId === $cat->id)>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status Filter --}}
                    <div>
                        <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
                            Status
                        </label>
                        <select name="status"
                                id="status"
                                onchange="this.form.submit()"
                                class="w-full rounded-xl border-0 py-2.5 pl-3 pr-8 text-xs text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-brand-600">
                            <option value="">All Statuses</option>
                            <option value="approved" @selected($status === 'approved')>
                                Available / Active
                            </option>
                            @if ($type !== 'lost')
                                <option value="claimed" @selected($status === 'claimed')>
                                    Claimed (Pending Pickup)
                                </option>
                                <option value="returned" @selected($status === 'returned')>
                                    Returned to Owner
                                </option>
                            @endif
                            @if ($type !== 'found')
                                <option value="resolved" @selected($status === 'resolved')>
                                    Resolved / Recovered
                                </option>
                            @endif
                        </select>
                    </div>

                    {{-- Location Search --}}
                    <div>
                        <label for="location" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
                            Location
                        </label>
                        <input type="text"
                               name="location"
                               id="location"
                               value="{{ $location }}"
                               placeholder="e.g. Library, Gym, Cafeteria"
                               class="w-full rounded-xl border-0 py-2 pl-3 pr-3 text-xs text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-brand-600">
                    </div>

                    {{-- Date From / To --}}
                    <div class="sm:col-span-2 lg:col-span-1">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
                            Date Range
                        </label>
                        <div class="grid grid-cols-2 gap-1.5">
                            <input type="date"
                                   name="date_from"
                                   value="{{ $dateFrom }}"
                                   placeholder="From"
                                   title="From Date"
                                   class="w-full rounded-xl border-0 py-2 px-2 text-xs text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-brand-600">
                            <input type="date"
                                   name="date_to"
                                   value="{{ $dateTo }}"
                                   placeholder="To"
                                   title="To Date"
                                   class="w-full rounded-xl border-0 py-2 px-2 text-xs text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-brand-600">
                        </div>
                    </div>

                    {{-- Sort & Submit Buttons --}}
                    <div class="flex items-end gap-2">
                        <div class="flex-1">
                            <label for="sort" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
                                Sort
                            </label>
                            <select name="sort"
                                    id="sort"
                                    onchange="this.form.submit()"
                                    class="w-full rounded-xl border-0 py-2.5 pl-3 pr-8 text-xs text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-brand-600">
                                <option value="latest" @selected($sort === 'latest')>Newest First</option>
                                <option value="oldest" @selected($sort === 'oldest')>Oldest First</option>
                            </select>
                        </div>
                        <button type="submit"
                                class="rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-slate-800 transition">
                            Apply
                        </button>
                    </div>
                </div>

                {{-- Active Filter Tags Row --}}
                @php
                    $hasActiveFilters = $search !== '' || $type !== 'all' || $categoryId || $status || $location !== '' || $dateFrom || $dateTo || $sort !== 'latest';
                @endphp

                @if ($hasActiveFilters)
                    <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-slate-100 text-xs">
                        <span class="font-medium text-slate-400">Active Filters:</span>

                        @if ($type !== 'all')
                            <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1 text-slate-700">
                                Type: <strong class="capitalize">{{ $type }}</strong>
                                <a href="{{ route('items.index', request()->except('type', 'page')) }}" class="text-slate-400 hover:text-slate-600 ml-1">×</a>
                            </span>
                        @endif

                        @if ($search !== '')
                            <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1 text-slate-700">
                                Keyword: "<strong>{{ $search }}</strong>"
                                <a href="{{ route('items.index', request()->except('q', 'page')) }}" class="text-slate-400 hover:text-slate-600 ml-1">×</a>
                            </span>
                        @endif

                        @if ($categoryId && $categories->where('id', $categoryId)->first())
                            <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1 text-slate-700">
                                Category: <strong>{{ $categories->where('id', $categoryId)->first()->name }}</strong>
                                <a href="{{ route('items.index', request()->except('category', 'page')) }}" class="text-slate-400 hover:text-slate-600 ml-1">×</a>
                            </span>
                        @endif

                        @if ($status)
                            <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1 text-slate-700">
                                Status: <strong class="capitalize">{{ $status }}</strong>
                                <a href="{{ route('items.index', request()->except('status', 'page')) }}" class="text-slate-400 hover:text-slate-600 ml-1">×</a>
                            </span>
                        @endif

                        @if ($location !== '')
                            <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1 text-slate-700">
                                Location: "<strong>{{ $location }}</strong>"
                                <a href="{{ route('items.index', request()->except('location', 'page')) }}" class="text-slate-400 hover:text-slate-600 ml-1">×</a>
                            </span>
                        @endif

                        @if ($dateFrom || $dateTo)
                            <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1 text-slate-700">
                                Date: <strong>{{ $dateFrom ?: 'Any' }}</strong> to <strong>{{ $dateTo ?: 'Now' }}</strong>
                                <a href="{{ route('items.index', request()->except('date_from', 'date_to', 'page')) }}" class="text-slate-400 hover:text-slate-600 ml-1">×</a>
                            </span>
                        @endif

                        <a href="{{ route('items.index') }}"
                           class="text-xs font-semibold text-brand-600 hover:text-brand-800 underline ml-2">
                            Reset all filters
                        </a>
                    </div>
                @endif
            </form>
        </div>

        {{-- Results Counter Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900">
                    Directory Items
                </h2>
                <p class="text-xs text-slate-500">
                    @if ($items->total() > 0)
                        Showing {{ $items->firstItem() }} to {{ $items->lastItem() }} of {{ $items->total() }} recorded items
                    @else
                        No items found
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('lost-items.create') }}"
                   class="rounded-xl bg-amber-500/10 px-3.5 py-2 text-xs font-semibold text-amber-800 hover:bg-amber-500/20 transition">
                    + Report Lost Item
                </a>
                <a href="{{ route('found-items.create') }}"
                   class="rounded-xl bg-emerald-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-emerald-500 transition">
                    + Report Found Item
                </a>
            </div>
        </div>

        {{-- Items Grid or Zero-State --}}
        @if ($items->isNotEmpty())
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($items as $item)
                    <x-item-card :item="$item" />
                @endforeach
            </div>

            {{-- Pagination Links --}}
            <div class="pt-6">
                {{ $items->links() }}
            </div>
        @else
            <x-empty-state
                title="No items found"
                description="We couldn't find any belongings matching your current search parameters. Try expanding your search, changing category, or clearing filters."
                action-label="Clear All Filters"
                :action-url="route('items.index')" />
        @endif
    </div>
</x-layouts.app>
