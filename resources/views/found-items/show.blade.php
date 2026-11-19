<x-layouts.app :title="$foundItem->item_name . ' · Found Item Report'">
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <nav class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700">Dashboard</a>
                <span>/</span>
                <a href="{{ route('items.index') }}" class="hover:text-slate-700">Browse Items</a>
                <span>/</span>
                <span class="text-slate-900">Found Item Report</span>
            </nav>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $foundItem->item_name }}</h1>
                <x-status-badge :status="$foundItem->status" />
            </div>
            <p class="mt-1 text-xs text-slate-500">
                Reported by {{ $foundItem->user->name }} · {{ $foundItem->created_at->format('M d, Y h:i A') }}
            </p>
        </div>

        {{-- Correction Banner if applicable --}}
        @if ($foundItem->needs_correction)
            <div class="rounded-2xl bg-amber-50 p-5 ring-1 ring-amber-200">
                <div class="flex gap-3">
                    <svg class="h-5 w-5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900">Corrections Requested by Administrator</h3>
                        <p class="mt-1 text-sm text-amber-800">{{ $foundItem->correction_note }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Main Details Card --}}
        <div class="grid gap-6 md:grid-cols-3">
            {{-- Photo Card --}}
            <div class="md:col-span-1">
                <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                    <img src="{{ $foundItem->photo_url }}" alt="{{ $foundItem->item_name }}" class="h-64 w-full object-cover">
                    <div class="p-4 border-t border-slate-100 space-y-2">
                        <div>
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Category</span>
                            <p class="text-sm font-medium text-slate-900">{{ $foundItem->category->name }}</p>
                        </div>
                        <div>
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Date Found</span>
                            <p class="text-sm font-medium text-slate-900">{{ $foundItem->date_found->format('F d, Y') }}</p>
                        </div>
                        <div>
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Location Found</span>
                            <p class="text-sm font-medium text-slate-900">{{ $foundItem->location_found }}</p>
                        </div>
                        @if ($foundItem->storage_location && auth()->user()->isAdmin())
                            <div class="rounded-lg bg-slate-50 p-2.5">
                                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Storage Facility (Admin)</span>
                                <p class="text-xs font-medium text-slate-900">{{ $foundItem->storage_location }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Details & Confidential Notes --}}
            <div class="md:col-span-2 space-y-6">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8 space-y-4">
                    <h2 class="text-base font-semibold text-slate-900 border-b border-slate-100 pb-3">Public Description</h2>
                    <p class="text-sm leading-relaxed text-slate-700 whitespace-pre-line">{{ $foundItem->public_description }}</p>
                </div>

                @if ($isAuthorized)
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h2 class="text-base font-semibold text-slate-900">Private Identifying Characteristics</h2>
                            <span class="rounded bg-rose-50 px-2 py-0.5 text-xs font-semibold text-rose-700 ring-1 ring-rose-200">
                                Finder & Admin Only
                            </span>
                        </div>
                        <p class="text-sm leading-relaxed text-slate-700 whitespace-pre-line">{{ $foundItem->private_details }}</p>

                        @if ($foundItem->remarks)
                            <div class="border-t border-slate-100 pt-3">
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Finder Remarks</h3>
                                <p class="text-xs text-slate-600 whitespace-pre-line">{{ $foundItem->remarks }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Claim Call to Action for Other Users --}}
                @if ($foundItem->status->value === 'approved' && $foundItem->user_id !== auth()->id())
                    <div class="rounded-2xl bg-emerald-50 p-6 ring-1 ring-emerald-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <h3 class="text-sm font-bold text-emerald-900">Recognize this item as yours?</h3>
                            <p class="text-xs text-emerald-700">Submit a claim request with ownership proof to recover your item through campus security.</p>
                        </div>
                        <a href="{{ route('claims.index') }}"
                           class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-500 shrink-0 transition">
                            Submit Claim Request
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center justify-between pt-4">
            <a href="{{ route('items.index') }}" class="rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50">
                ← Back to Directory
            </a>
            <a href="{{ route('dashboard') }}" class="text-xs text-slate-500 hover:text-slate-800">
                Go to Dashboard
            </a>
        </div>
    </div>
</x-layouts.app>
