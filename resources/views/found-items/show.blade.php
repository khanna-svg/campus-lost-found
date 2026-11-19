<x-layouts.app :title="$foundItem->item_name . ' · Found Item Report'">
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <nav class="flex items-center gap-2 text-xs font-medium text-zinc-500 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-black">Dashboard</a>
                <span>/</span>
                <a href="{{ route('items.index') }}" class="hover:text-black">Browse Directory</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Found Item Report</span>
            </nav>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 sm:text-3xl">{{ $foundItem->item_name }}</h1>
                <div class="flex items-center gap-2">
                    @if ($foundItem->status->value === 'returned')
                        <span class="inline-flex items-center rounded-full bg-zinc-100 px-3 py-1 text-xs font-semibold text-zinc-700 border border-zinc-200">
                            Resolved (Reunited)
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 border border-amber-200">
                            Unresolved
                        </span>
                    @endif

                    @if ($isAuthorized && in_array($foundItem->status->value, ['approved', 'claimed', 'returned']))
                        <form method="POST" action="{{ route('found-items.toggle-resolve', $foundItem) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            @if ($foundItem->status->value === 'returned')
                                <button type="submit" class="rounded-lg bg-white px-3 py-1 text-xs font-semibold text-zinc-700 ring-1 ring-zinc-300 hover:bg-zinc-50 transition">
                                    Mark Unresolved
                                </button>
                            @else
                                <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-black px-3 py-1 text-xs font-semibold text-white hover:bg-zinc-800 transition">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                    Mark as Resolved
                                </button>
                            @endif
                        </form>
                    @endif
                </div>
            </div>
            <p class="mt-1 text-xs text-zinc-500">
                Reported by {{ $foundItem->user->name }} · {{ $foundItem->created_at->format('M d, Y h:i A') }}
            </p>
        </div>

        {{-- Correction Banner if applicable --}}
        @if ($foundItem->needs_correction)
            <div class="rounded-2xl border border-zinc-300 bg-zinc-100 p-5">
                <div class="flex gap-3">
                    <svg class="h-5 w-5 text-black shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-black">Corrections Requested by Administrator</h3>
                        <p class="mt-1 text-sm text-zinc-800">{{ $foundItem->correction_note }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Main Details Card --}}
        <div class="grid gap-6 md:grid-cols-3">
            {{-- Photo Card --}}
            <div class="md:col-span-1">
                <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-zinc-200">
                    <div class="relative h-64 w-full overflow-hidden bg-zinc-100 flex items-center justify-center">
                        @if ($foundItem->photo_url)
                            <img src="{{ $foundItem->photo_url }}"
                                 alt="{{ $foundItem->item_name }}"
                                 onerror="this.onerror=null; this.remove(); document.getElementById('found-photo-fallback').classList.remove('hidden');"
                                 class="h-full w-full object-cover">
                        @endif
                        <div id="found-photo-fallback" class="{{ $foundItem->photo_url ? 'hidden' : 'flex' }} flex-col items-center justify-center text-zinc-400 p-4 text-center">
                            <svg class="h-12 w-12 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                            <span class="mt-2 text-xs font-medium text-zinc-500">Photo unavailable</span>
                        </div>
                    </div>

                    <div class="p-4 border-t border-zinc-100 space-y-2.5">
                        <div>
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-zinc-400">Category</span>
                            <p class="text-sm font-semibold text-zinc-900">{{ $foundItem->category->name }}</p>
                        </div>
                        <div>
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-zinc-400">Date Found</span>
                            <p class="text-sm font-semibold text-zinc-900">{{ $foundItem->date_found->format('F d, Y') }}</p>
                        </div>
                        <div>
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-zinc-400">Location Found</span>
                            <p class="text-sm font-semibold text-zinc-900">{{ $foundItem->location_found }}</p>
                        </div>
                        @if ($foundItem->storage_location && auth()->user()->isAdmin())
                            <div class="rounded-lg bg-zinc-100 p-2.5">
                                <span class="text-[11px] font-semibold uppercase tracking-wider text-zinc-500">Storage Facility (Admin)</span>
                                <p class="text-xs font-semibold text-zinc-900">{{ $foundItem->storage_location }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Details & Confidential Notes --}}
            <div class="md:col-span-2 space-y-6">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-zinc-200 sm:p-8 space-y-4">
                    <h2 class="text-base font-bold text-zinc-900 border-b border-zinc-100 pb-3">Public Description</h2>
                    <p class="text-sm leading-relaxed text-zinc-700 whitespace-pre-line">{{ $foundItem->public_description }}</p>
                </div>

                @if ($isAuthorized)
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-zinc-200 sm:p-8 space-y-4">
                        <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                            <h2 class="text-base font-bold text-zinc-900">Private Identifying Characteristics</h2>
                            <span class="rounded bg-black px-2 py-0.5 text-xs font-semibold text-white">
                                Finder & Admin Only
                            </span>
                        </div>
                        <p class="text-sm leading-relaxed text-zinc-700 whitespace-pre-line">{{ $foundItem->private_details }}</p>

                        @if ($foundItem->remarks)
                            <div class="border-t border-zinc-100 pt-3">
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-500 mb-1">Finder Remarks</h3>
                                <p class="text-xs text-zinc-600 whitespace-pre-line">{{ $foundItem->remarks }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Physical Retrieval Notice for Other Users --}}
                @if ($foundItem->status->value === 'approved' && $foundItem->user_id !== auth()->id())
                    <div class="rounded-2xl bg-black p-6 text-white border border-zinc-800 space-y-1">
                        <h3 class="text-sm font-bold text-white">Recognize this item as yours?</h3>
                        <p class="text-xs text-zinc-400">Please visit the Campus Lost & Found Office (Administration Hall, Room 102) with your valid school ID to verify ownership and retrieve your belonging.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center justify-between pt-4">
            <a href="{{ route('items.index') }}" class="rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-zinc-700 shadow-sm ring-1 ring-inset ring-zinc-300 hover:bg-zinc-50 transition">
                ← Back to Directory
            </a>
            <a href="{{ route('dashboard') }}" class="text-xs text-zinc-500 hover:text-black">
                Go to Dashboard
            </a>
        </div>
    </div>
</x-layouts.app>
