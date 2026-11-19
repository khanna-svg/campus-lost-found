<x-layouts.app title="Campus Belongings · Directory">
    <div class="mx-auto max-w-7xl space-y-6">
        {{-- Header Section (Matching Reference Image) --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-2">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-zinc-900 sm:text-4xl">Lost &amp; Found</h1>
                <p class="mt-1 text-sm text-zinc-500">
                    Report something you lost or found on campus, and help return items to their owners.
                </p>
            </div>
            <a href="{{ route('items.report.create') }}"
               onclick="event.preventDefault(); openReportModal();"
               class="inline-flex items-center justify-center rounded-xl bg-black px-5 py-2.5 text-sm font-bold text-white shadow-xs transition hover:bg-zinc-800 shrink-0 self-start sm:self-auto">
                Report an item
            </a>
        </div>

        {{-- Filter & Search Row (Matching Reference Image) --}}
        <form method="GET" action="{{ route('items.index') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
            {{-- Left: Filter Selectors (Type & Status) --}}
            <div class="flex flex-wrap sm:flex-nowrap items-center gap-2.5">
                {{-- Type Filter Dropdown --}}
                <div class="relative w-full sm:w-40">
                    <select name="type"
                            onchange="this.form.submit()"
                            class="w-full appearance-none rounded-xl border border-zinc-200 bg-white py-2.5 pl-3.5 pr-8 text-sm font-medium text-zinc-800 shadow-xs transition hover:border-zinc-300 focus:border-black focus:outline-none focus:ring-1 focus:ring-black">
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
                <div class="relative w-full sm:w-40">
                    <select name="status"
                            onchange="this.form.submit()"
                            class="w-full appearance-none rounded-xl border border-zinc-200 bg-white py-2.5 pl-3.5 pr-8 text-sm font-medium text-zinc-800 shadow-xs transition hover:border-zinc-300 focus:border-black focus:outline-none focus:ring-1 focus:ring-black">
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
                        class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-black px-5 py-2.5 text-sm font-semibold text-white shadow-xs transition hover:bg-zinc-800">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    Search
                </button>
            </div>
        </form>

        {{-- Active Filters Strip --}}
        @php
            $hasActiveFilters = ($search !== '') || ($type !== 'all') || (!empty($status) && $status !== 'all');
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

        {{-- Items Grid (3 Columns) --}}
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
            @if ($hasActiveFilters)
                <x-empty-state
                    title="No items found"
                    description="We couldn't find any belongings matching your current search parameters."
                    action-label="Clear All Filters"
                    :action-url="route('items.index')" />
            @else
                <div class="rounded-3xl border-2 border-dashed border-zinc-200 bg-white p-12 text-center max-w-lg mx-auto space-y-4 my-8">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-zinc-100 text-zinc-400">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <circle cx="11" cy="11" r="7" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="m20 20-4-4" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-base font-bold text-zinc-900">No items listed yet</h3>
                        <p class="text-xs text-zinc-500">
                            The directory is currently empty. Be the first to report something lost or found on campus!
                        </p>
                    </div>
                    <button type="button"
                            onclick="openReportModal()"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-black px-5 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-zinc-800 transition">
                        Report an item
                    </button>
                </div>
            @endif
        @endif
    </div>

    {{-- MODAL 1: ITEM DETAILS (Matching Reference Image 1 & 2) --}}
    <div id="item-details-modal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto" onclick="if(event.target === this) closeItemModal();">
        <div class="relative w-full max-w-xl rounded-3xl bg-white p-6 sm:p-8 shadow-2xl border border-zinc-200 my-8 max-h-[90vh] overflow-y-auto space-y-6">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between pb-3 border-b border-zinc-200">
                <h3 class="text-xl font-bold tracking-tight text-zinc-900">Item Details</h3>
                <button type="button"
                        onclick="closeItemModal()"
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-black text-white hover:bg-zinc-800 transition"
                        title="Close">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Image Display Area --}}
            <div id="modal-image-container" class="relative overflow-hidden rounded-2xl bg-zinc-50 border border-zinc-200 flex items-center justify-center p-2 max-h-80 hidden">
                <img id="modal-item-photo" src="" alt="" class="max-h-72 w-full object-contain rounded-xl">
            </div>

            {{-- No Image Fallback Box (Dashed box matching reference) --}}
            <div id="modal-no-image-box" class="flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-zinc-200 bg-zinc-50/50 p-12 text-center min-h-[220px]">
                <svg class="h-12 w-12 text-zinc-300 stroke-[1.4]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                </svg>
                <span class="mt-2 text-xs font-semibold text-zinc-400">No images provided</span>
            </div>

            {{-- Badges Row --}}
            <div class="flex flex-wrap items-center gap-2">
                <span id="modal-badge-type" class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wider"></span>
                <span id="modal-badge-status" class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wider"></span>
            </div>

            {{-- Title & Posted Date --}}
            <div>
                <h4 id="modal-item-title" class="text-2xl font-black text-zinc-900 tracking-tight"></h4>
                <div class="mt-1 flex items-center gap-1.5 text-xs text-zinc-500 font-medium">
                    <svg class="h-3.5 w-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.253 18.75m-18-13.5h22.5m-22.5 0v13.5A2.25 2.25 0 0 0 4.5 21h15a2.25 2.25 0 0 0 2.25-2.25V7.5H2.25Z" />
                    </svg>
                    <span id="modal-posted-date"></span>
                </div>
            </div>

            {{-- 2x2 Meta Grid (Matching Reference Image 2) --}}
            <div class="rounded-2xl border border-zinc-100 bg-zinc-50/70 p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Location --}}
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white border border-zinc-200 text-zinc-700 shadow-2xs">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400">LOCATION</span>
                            <span id="modal-item-location" class="block text-sm font-black text-zinc-900 truncate"></span>
                        </div>
                    </div>

                    {{-- Posted By --}}
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white border border-zinc-200 text-zinc-700 shadow-2xs">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400">POSTED BY</span>
                            <span id="modal-poster-name" class="block text-sm font-black text-zinc-900 truncate"></span>
                        </div>
                    </div>

                    {{-- Date Found / Lost --}}
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white border border-zinc-200 text-zinc-700 shadow-2xs">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.253 18.75m-18-13.5h22.5m-22.5 0v13.5A2.25 2.25 0 0 0 4.5 21h15a2.25 2.25 0 0 0 2.25-2.25V7.5H2.25Z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <span id="modal-date-label" class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400">DATE OF FINDING</span>
                            <span id="modal-item-date" class="block text-sm font-black text-zinc-900"></span>
                        </div>
                    </div>

                    {{-- Contact Info --}}
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white border border-zinc-200 text-zinc-700 shadow-2xs">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400">CONTACT INFO</span>
                            <span id="modal-contact-info" class="block text-sm font-black text-zinc-900 truncate"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Description Section --}}
            <div class="space-y-2">
                <div class="flex items-center gap-2 text-base font-bold text-zinc-900">
                    <svg class="h-4 w-4 text-zinc-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    <span>Description</span>
                </div>
                <div id="modal-item-description" class="rounded-2xl border border-zinc-100 bg-zinc-50/70 p-5 text-sm leading-relaxed text-zinc-700 whitespace-pre-line"></div>
            </div>

            {{-- Resolve Button (Authorized Poster or Admin) --}}
            <div id="modal-resolve-container" class="hidden pt-3 border-t border-zinc-200 flex items-center justify-between">
                <form id="modal-resolve-form" method="POST" action="">
                    @csrf
                    @method('PATCH')
                    <button type="submit" id="modal-resolve-btn" class="rounded-xl px-4 py-2 text-xs font-bold shadow-xs transition"></button>
                </form>
                <button type="button" onclick="closeItemModal()" class="text-xs font-semibold text-zinc-500 hover:text-black">
                    Close
                </button>
            </div>

            {{-- Claim Button (Found Items that are unresolved and not posted by the viewer) --}}
            <div id="modal-claim-container" class="hidden pt-3 border-t border-zinc-200 flex items-center justify-between">
                <a id="modal-claim-btn" href="" class="inline-flex items-center gap-1.5 rounded-xl bg-black px-5 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-zinc-800 transition">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    Claim this item
                </a>
                <button type="button" onclick="closeItemModal()" class="text-xs font-semibold text-zinc-500 hover:text-black">
                    Close
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL 2: REPORT AN ITEM (Matching Reference Image 3, 4, 5) --}}
    <div id="report-item-modal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto" onclick="if(event.target === this) closeReportModal();">
        <div class="relative w-full max-w-xl rounded-3xl bg-white p-6 sm:p-8 shadow-2xl border border-zinc-200 my-8 max-h-[90vh] overflow-y-auto space-y-6">
            {{-- Header with Close Button --}}
            <div class="flex items-center justify-between pb-3 border-b border-zinc-200">
                <h3 class="text-xl font-bold tracking-tight text-zinc-900">Report an item</h3>
                <button type="button"
                        onclick="closeReportModal()"
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-black text-white hover:bg-zinc-800 transition"
                        title="Close">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('items.report.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- 1. Item Details --}}
                <div class="rounded-2xl bg-white p-5 border border-zinc-200 space-y-4">
                    <div class="flex items-center gap-1.5">
                        <h4 class="text-sm font-bold text-zinc-900">Item Details</h4>
                        <span class="text-zinc-400">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                            </svg>
                        </span>
                    </div>

                    {{-- Item Type Radio --}}
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-zinc-700">Item Type</label>
                        <div class="flex items-center gap-6 pt-0.5">
                            <label class="flex items-center gap-2 text-sm font-semibold text-zinc-900 cursor-pointer">
                                <input type="radio" name="item_type" value="lost" checked class="h-4 w-4 text-black focus:ring-black border-zinc-300">
                                Lost Item
                            </label>
                            <label class="flex items-center gap-2 text-sm font-semibold text-zinc-900 cursor-pointer">
                                <input type="radio" name="item_type" value="found" class="h-4 w-4 text-black focus:ring-black border-zinc-300">
                                Found Item
                            </label>
                        </div>
                    </div>

                    {{-- Title --}}
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1">
                            Title <span class="text-zinc-900">*</span>
                        </label>
                        <input type="text"
                               name="title"
                               required
                               placeholder="What did you lose/find?"
                               class="w-full rounded-xl border border-zinc-200 px-3.5 py-2 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-black focus:outline-none focus:ring-1 focus:ring-black">
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1">
                            Description <span class="text-zinc-900">*</span>
                        </label>
                        <textarea name="description"
                                  rows="3"
                                  required
                                  placeholder="Provide details like color, brand, etc."
                                  class="w-full rounded-xl border border-zinc-200 px-3.5 py-2 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-black focus:outline-none focus:ring-1 focus:ring-black"></textarea>
                    </div>

                    {{-- Location & Date --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Location</label>
                            <input type="text"
                                   name="location"
                                   placeholder="Where?"
                                   class="w-full rounded-xl border border-zinc-200 px-3.5 py-2 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-black focus:outline-none focus:ring-1 focus:ring-black">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Date <span class="text-zinc-900">*</span></label>
                            <input type="date"
                                   name="date"
                                   required
                                   value="{{ date('Y-m-d') }}"
                                   max="{{ date('Y-m-d') }}"
                                   class="w-full rounded-xl border border-zinc-200 px-3.5 py-2 text-sm text-zinc-900 focus:border-black focus:outline-none focus:ring-1 focus:ring-black">
                        </div>
                    </div>
                </div>

                {{-- 2. Images --}}
                <div class="rounded-2xl bg-white p-5 border border-zinc-200 space-y-2">
                    <h4 class="text-sm font-bold text-zinc-900">Images</h4>
                    <p class="text-xs text-zinc-500">Optional. Up to 4 images.</p>

                    <div class="relative mt-2 rounded-xl border border-zinc-200 bg-white p-6 text-center hover:bg-zinc-50 transition cursor-pointer">
                        <input type="file"
                               name="photo"
                               id="modal-photo-input"
                               accept="image/png,image/jpeg,image/gif,image/webp"
                               onchange="handleModalFilePreview(this)"
                               class="absolute inset-0 h-full w-full opacity-0 cursor-pointer z-10">

                        <div id="modal-dropzone-prompt" class="space-y-1">
                            <p class="text-xs text-zinc-700 font-medium">
                                <span class="font-bold text-black underline">Upload files</span> or drag and drop
                            </p>
                            <p class="text-[11px] text-zinc-400">
                                PNG, JPG, GIF up to 5MB each. Add clear images to help illustrate.
                            </p>
                        </div>

                        <div id="modal-preview-area" class="hidden flex flex-col items-center justify-center pt-2">
                            <img id="modal-preview-image" src="" alt="Selected photo" class="h-28 w-28 rounded-xl object-cover border border-zinc-200 shadow-xs">
                            <p id="modal-preview-name" class="mt-2 text-xs font-semibold text-zinc-700 truncate max-w-xs"></p>
                            <button type="button"
                                    onclick="clearModalFileSelection()"
                                    class="mt-1.5 text-[11px] font-semibold text-red-600 hover:underline relative z-20">
                                Remove photo
                            </button>
                        </div>
                    </div>
                </div>

                {{-- 3. Contact Information --}}
                <div class="rounded-2xl bg-white p-5 border border-zinc-200 space-y-2">
                    <h4 class="text-sm font-bold text-zinc-900">Contact Information *</h4>
                    <input type="text"
                           name="contact_info"
                           required
                           value="{{ auth()->check() ? (auth()->user()->phone ?: auth()->user()->email) : '' }}"
                           placeholder="Email or Phone Number"
                           class="w-full rounded-xl border border-zinc-200 px-3.5 py-2 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-black focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                {{-- Submit Button --}}
                <div class="pt-2 border-t border-zinc-200 flex justify-end">
                    <button type="submit"
                            class="rounded-xl bg-black px-6 py-2.5 text-sm font-bold text-white shadow-xs transition hover:bg-zinc-800">
                        Post item
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Interactive Modal Scripts --}}
    <script>
        function openItemDetailsModal(item) {
            // Set text & attributes
            document.getElementById('modal-item-title').textContent = item.item_name;
            document.getElementById('modal-item-description').textContent = item.description;
            document.getElementById('modal-item-location').textContent = item.location;
            document.getElementById('modal-poster-name').textContent = item.poster_name;
            document.getElementById('modal-item-date').textContent = item.formatted_date;
            document.getElementById('modal-contact-info').textContent = item.contact_info;
            document.getElementById('modal-posted-date').textContent = 'Posted on ' + item.posted_date;

            // Date label
            document.getElementById('modal-date-label').textContent = item.item_type === 'found' ? 'DATE OF FINDING' : 'DATE LOST';

            // Badges
            const typeBadge = document.getElementById('modal-badge-type');
            if (item.item_type === 'found') {
                typeBadge.textContent = 'FOUND ITEM';
                typeBadge.className = 'inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-emerald-600';
            } else {
                typeBadge.textContent = 'LOST ITEM';
                typeBadge.className = 'inline-flex items-center rounded-full bg-rose-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-rose-500';
            }

            const statusBadge = document.getElementById('modal-badge-status');
            if (item.is_resolved) {
                statusBadge.textContent = 'RESOLVED';
                statusBadge.className = 'inline-flex items-center rounded-full bg-zinc-100 px-3 py-1 text-xs font-bold uppercase tracking-wider text-zinc-700';
            } else {
                statusBadge.textContent = 'UNRESOLVED';
                statusBadge.className = 'inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-amber-700';
            }

            // Image vs No-Image
            const imgContainer = document.getElementById('modal-image-container');
            const noImgBox = document.getElementById('modal-no-image-box');
            const photoEl = document.getElementById('modal-item-photo');

            if (item.photo_url) {
                photoEl.src = item.photo_url;
                imgContainer.classList.remove('hidden');
                noImgBox.classList.add('hidden');
            } else {
                imgContainer.classList.add('hidden');
                noImgBox.classList.remove('hidden');
            }

            // Resolve button
            const resolveContainer = document.getElementById('modal-resolve-container');
            if (item.can_resolve) {
                const form = document.getElementById('modal-resolve-form');
                form.action = item.toggle_resolve_route;
                const btn = document.getElementById('modal-resolve-btn');

                if (item.is_resolved) {
                    btn.textContent = 'Mark as Unresolved';
                    btn.className = 'rounded-xl border border-zinc-300 bg-white px-4 py-2 text-xs font-bold text-zinc-800 hover:bg-zinc-50 transition';
                } else {
                    btn.textContent = 'Mark as Resolved';
                    btn.className = 'rounded-xl bg-black px-4 py-2 text-xs font-bold text-white hover:bg-zinc-800 transition';
                }
                resolveContainer.classList.remove('hidden');
            } else {
                resolveContainer.classList.add('hidden');
            }

            // Claim button
            const claimContainer = document.getElementById('modal-claim-container');
            const claimBtn = document.getElementById('modal-claim-btn');
            if (item.can_claim && item.claim_route) {
                claimBtn.href = item.claim_route;
                claimContainer.classList.remove('hidden');
            } else {
                claimContainer.classList.add('hidden');
            }

            // Display modal
            document.getElementById('item-details-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeItemModal() {
            document.getElementById('item-details-modal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function openReportModal() {
            document.getElementById('report-item-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeReportModal() {
            document.getElementById('report-item-modal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function handleModalFilePreview(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('modal-preview-image').src = e.target.result;
                    document.getElementById('modal-preview-name').textContent = file.name;
                    document.getElementById('modal-dropzone-prompt').classList.add('hidden');
                    document.getElementById('modal-preview-area').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        function clearModalFileSelection() {
            const input = document.getElementById('modal-photo-input');
            input.value = '';
            document.getElementById('modal-preview-area').classList.add('hidden');
            document.getElementById('modal-dropzone-prompt').classList.remove('hidden');
        }

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeItemModal();
                closeReportModal();
            }
        });
    </script>
</x-layouts.app>
