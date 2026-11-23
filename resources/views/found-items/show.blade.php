<x-layouts.app :title="$foundItem->item_name . ' · Item Details'">
    <div class="mx-auto max-w-2xl space-y-6">
        {{-- Header matching reference --}}
        <div class="flex items-center justify-between pb-3 border-b border-zinc-200">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Item Details</h1>
            <a href="{{ route('items.index') }}"
               class="flex h-8 w-8 items-center justify-center rounded-lg bg-black text-white hover:bg-zinc-800 transition"
               title="Back to Directory">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </a>
        </div>

        {{-- Main Details Card --}}
        <div class="rounded-3xl bg-white p-6 sm:p-8 border border-zinc-200 shadow-xs space-y-6">
            {{-- Image Box --}}
            @if ($foundItem->photo_url)
                <div class="relative overflow-hidden rounded-2xl bg-zinc-50 border border-zinc-200 flex items-center justify-center p-2 max-h-96">
                    <img src="{{ $foundItem->photo_url }}"
                         alt="{{ $foundItem->item_name }}"
                         onerror="this.onerror=null; this.parentElement.classList.add('hidden'); document.getElementById('found-no-image').classList.remove('hidden');"
                         class="max-h-80 w-full object-contain rounded-xl">
                </div>
            @endif

            <div id="found-no-image" class="{{ $foundItem->photo_url ? 'hidden' : 'flex' }} flex-col items-center justify-center rounded-3xl border-2 border-dashed border-zinc-200 bg-zinc-50/50 p-12 text-center min-h-[220px]">
                <svg class="h-12 w-12 text-zinc-300 stroke-[1.4]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                </svg>
                <span class="mt-2 text-xs font-semibold text-zinc-400">No images provided</span>
            </div>

            {{-- Badges Row --}}
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-emerald-600">
                    FOUND ITEM
                </span>
                @if ($foundItem->isSurrenderedToOffice())
                    <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-blue-700">
                        📍 At Admin Office
                    </span>
                @else
                    <span class="inline-flex items-center rounded-full bg-purple-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-purple-700">
                        👤 In Finder's Possession
                    </span>
                @endif
                @if ($foundItem->status->value === 'returned')
                    <span class="inline-flex items-center rounded-full bg-zinc-100 px-3 py-1 text-xs font-bold uppercase tracking-wider text-zinc-700">
                        RESOLVED
                    </span>
                @else
                    <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-amber-700">
                        UNRESOLVED
                    </span>
                @endif
            </div>

            {{-- Title & Posted Date --}}
            <div>
                <h2 class="text-2xl font-black text-zinc-900 tracking-tight">{{ $foundItem->item_name }}</h2>
                <div class="mt-1.5 flex items-center gap-1.5 text-xs text-zinc-500 font-medium">
                    <svg class="h-3.5 w-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.253 18.75m-18-13.5h22.5m-22.5 0v13.5A2.25 2.25 0 0 0 4.5 21h15a2.25 2.25 0 0 0 2.25-2.25V7.5H2.25Z" />
                    </svg>
                    <span>Posted on {{ $foundItem->created_at->format('F j, Y') }}</span>
                </div>
            </div>

            {{-- 2x2 Metadata Grid --}}
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
                            <span class="block text-sm font-black text-zinc-900 truncate">{{ $foundItem->location_found }}</span>
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
                            <span class="block text-sm font-black text-zinc-900 truncate">{{ $foundItem->user->name }}</span>
                        </div>
                    </div>

                    {{-- Date of Finding --}}
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white border border-zinc-200 text-zinc-700 shadow-2xs">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.253 18.75m-18-13.5h22.5m-22.5 0v13.5A2.25 2.25 0 0 0 4.5 21h15a2.25 2.25 0 0 0 2.25-2.25V7.5H2.25Z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400">DATE OF FINDING</span>
                            <span class="block text-sm font-black text-zinc-900">{{ $foundItem->date_found->format('F d, Y') }}</span>
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
                            <span class="block text-sm font-black text-zinc-900 truncate">{{ $foundItem->contact_info ?: ($foundItem->user->phone ?: $foundItem->user->email) }}</span>
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
                <div class="rounded-2xl border border-zinc-100 bg-zinc-50/70 p-5 text-sm leading-relaxed text-zinc-700 whitespace-pre-line">
                    {{ $foundItem->public_description }}
                </div>
            </div>

            {{-- Incoming Claims on this Item (Visible to Finder & Admin) --}}
            @if (($foundItem->user_id === auth()->id() || (auth()->check() && auth()->user()->isAdmin())) && $foundItem->claimRequests->isNotEmpty())
                <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-zinc-900">
                            Ownership Claims on this Item ({{ $foundItem->claimRequests->count() }})
                        </h3>
                        <span class="text-xs text-zinc-500">Review proof submitted by students</span>
                    </div>
                    <div class="space-y-2">
                        @foreach ($foundItem->claimRequests as $cr)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 rounded-xl bg-white border border-zinc-200">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-xs text-zinc-900">{{ $cr->claimant->name }}</span>
                                        <x-status-badge :status="$cr->status" />
                                    </div>
                                    <span class="text-[11px] text-zinc-500 block mt-0.5">Submitted {{ $cr->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                                <a href="{{ route('claims.show', $cr) }}" class="inline-flex items-center gap-1 self-start sm:self-auto rounded-lg bg-black px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-zinc-800 transition">
                                    Review Claim →
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Resolve Actions --}}
            @if ($isAuthorized)
                <div class="pt-4 border-t border-zinc-200 flex items-center justify-between">
                    <form method="POST" action="{{ route('found-items.toggle-resolve', $foundItem) }}">
                        @csrf
                        @method('PATCH')
                        @if ($foundItem->status->value === 'returned')
                            <button type="submit" class="rounded-xl border border-zinc-300 bg-white px-4 py-2 text-xs font-bold text-zinc-800 hover:bg-zinc-50 transition">
                                Mark as Unresolved
                            </button>
                        @else
                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-black px-4 py-2 text-xs font-bold text-white hover:bg-zinc-800 transition">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                Mark as Resolved
                            </button>
                        @endif
                    </form>
                    <a href="{{ route('items.index') }}" class="text-xs font-semibold text-zinc-500 hover:text-black">
                        Back to Directory
                    </a>
                </div>
            @elseif (auth()->check() && ! auth()->user()->isAdmin() && $foundItem->status->value !== 'returned')
                <div class="pt-4 border-t border-zinc-200 flex items-center justify-between">
                    <a href="{{ route('claims.create', $foundItem) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-black px-5 py-2.5 text-xs font-bold text-white hover:bg-zinc-800 transition shadow-xs">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        Claim this item
                    </a>
                    <a href="{{ route('items.index') }}" class="text-xs font-semibold text-zinc-500 hover:text-black">
                        Back to Directory
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
