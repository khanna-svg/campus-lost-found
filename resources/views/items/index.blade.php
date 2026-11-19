<x-layouts.app title="Campus Belongings · Directory">
    <div class="mx-auto max-w-7xl space-y-6">
        {{-- Top Filter & Search Bar (Matching Reference Screenshot) --}}
        <div class="bg-white rounded-2xl border border-zinc-200 p-4 shadow-xs">
            <form method="GET" action="{{ route('items.index') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
                {{-- Left: Filter Selectors (Type & Status) --}}
                <div class="flex flex-wrap sm:flex-nowrap items-center gap-2.5">
                    {{-- Type Filter Dropdown --}}
                    <div class="relative w-full sm:w-44">
                        <select name="type"
                                onchange="this.form.submit()"
                                class="w-full appearance-none rounded-xl border border-zinc-200 bg-white py-2.5 pl-3.5 pr-9 text-sm font-medium text-zinc-800 shadow-xs transition hover:border-zinc-300 focus:border-black focus:outline-none focus:ring-1 focus:ring-black">
                            <option value="all" @selected($type === 'all')>All Types</option>
                            <option value="lost" @selected($type === 'lost')>Lost Item</option>
                            <option value="found" @selected($type === 'found')>Found Item</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </div>

                    {{-- Status Filter Dropdown --}}
                    <div class="relative w-full sm:w-44">
                        <select name="status"
                                onchange="this.form.submit()"
                                class="w-full appearance-none rounded-xl border border-zinc-200 bg-white py-2.5 pl-3.5 pr-9 text-sm font-medium text-zinc-800 shadow-xs transition hover:border-zinc-300 focus:border-black focus:outline-none focus:ring-1 focus:ring-black">
                            <option value="all" @selected(empty($status) || $status === 'all')>All Statuses</option>
                            <option value="unresolved" @selected($status === 'unresolved')>Unresolved</option>
                            <option value="resolved" @selected($status === 'resolved')>Resolved</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Right: Search Input & Black Search Button --}}
                <div class="flex items-center gap-2 flex-1 lg:max-w-md">
                    <div class="relative flex-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-zinc-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                        <input type="text"
                               name="q"
                               value="{{ $search }}"
                               placeholder="Search items..."
                               class="w-full rounded-xl border border-zinc-200 bg-white py-2.5 pl-10 pr-9 text-sm text-zinc-900 placeholder:text-zinc-400 shadow-xs focus:border-black focus:outline-none focus:ring-1 focus:ring-black">
                        @if ($search !== '')
                            <a href="{{ route('items.index', request()->except('q', 'page')) }}"
                               class="absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400 hover:text-black"
                               title="Clear search">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </a>
                        @endif
                    </div>

                    {{-- Solid Black Search Button --}}
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-black px-5 py-2.5 text-sm font-semibold text-white shadow-xs transition hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-black">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        Search
                    </button>
                </div>
            </form>
        </div>

        {{-- Active Filters Strip (if any filter is selected) --}}
        @php
            $hasActiveFilters = ($search !== '') || ($type !== 'all') || (!empty($status) && $status !== 'all') || !empty($categoryId) || !empty($location);
        @endphp
        @if ($hasActiveFilters)
            <div class="flex flex-wrap items-center gap-2 text-xs text-zinc-500">
                <span>Active filters:</span>
                @if ($type !== 'all')
                    <span class="inline-flex items-center gap-1 rounded-full bg-zinc-100 px-3 py-1 font-medium text-zinc-800">
                        Type: <span class="capitalize font-semibold">{{ $type }}</span>
                        <a href="{{ route('items.index', request()->except('type', 'page')) }}" class="hover:text-black ml-0.5">×</a>
                    </span>
                @endif
                @if (!empty($status) && $status !== 'all')
                    <span class="inline-flex items-center gap-1 rounded-full bg-zinc-100 px-3 py-1 font-medium text-zinc-800">
                        Status: <span class="capitalize font-semibold">{{ $status }}</span>
                        <a href="{{ route('items.index', request()->except('status', 'page')) }}" class="hover:text-black ml-0.5">×</a>
                    </span>
                @endif
                @if ($search !== '')
                    <span class="inline-flex items-center gap-1 rounded-full bg-zinc-100 px-3 py-1 font-medium text-zinc-800">
                        "{{ $search }}"
                        <a href="{{ route('items.index', request()->except('q', 'page')) }}" class="hover:text-black ml-0.5">×</a>
                    </span>
                @endif
                <a href="{{ route('items.index') }}" class="font-semibold text-black hover:underline ml-1">Reset All</a>
            </div>
        @endif

        {{-- Items Grid matching reference 3-column layout --}}
        @if ($items->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
                description="We couldn't find any belongings matching your current search parameters."
                action-label="Clear All Filters"
                :action-url="route('items.index')" />
        @endif
    </div>
</x-layouts.app>
