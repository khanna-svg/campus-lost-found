@props(['item'])

@php
    $isFound = $item->item_type === 'found';
    $detailRoute = $isFound ? route('found-items.show', $item->id) : route('lost-items.show', $item->id);
    $categoryName = $item->category ? $item->category->name : 'Uncategorized';
@endphp

<div class="group flex flex-col justify-between overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
    <div>
        {{-- Thumbnail / Photo Header --}}
        <div class="relative h-48 w-full overflow-hidden bg-slate-100">
            @if (! empty($item->photo_url))
                <img src="{{ $item->photo_url }}"
                     alt="{{ $item->item_name }}"
                     loading="lazy"
                     class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
            @else
                <div class="flex h-full w-full flex-col items-center justify-center bg-slate-50 text-slate-400">
                    <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    <span class="mt-1 text-[11px] font-medium text-slate-400">No photo provided</span>
                </div>
            @endif

            {{-- Floating Badges --}}
            <div class="absolute inset-x-3 top-3 flex items-center justify-between pointer-events-none">
                {{-- Type Badge --}}
                @if ($isFound)
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-600/90 px-2.5 py-0.5 text-[11px] font-semibold text-white shadow-sm backdrop-blur">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        Found
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-600/90 px-2.5 py-0.5 text-[11px] font-semibold text-white shadow-sm backdrop-blur">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                        Lost
                    </span>
                @endif

                {{-- Status Pill --}}
                @if ($item->status === 'approved')
                    @if ($isFound)
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-inset ring-emerald-600/20 shadow-sm backdrop-blur">
                            Available to Claim
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700 ring-1 ring-inset ring-amber-600/20 shadow-sm backdrop-blur">
                            Actively Missing
                        </span>
                    @endif
                @elseif ($item->status === 'claimed')
                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700 ring-1 ring-inset ring-blue-600/20 shadow-sm backdrop-blur">
                        Claimed
                    </span>
                @elseif ($item->status === 'returned')
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-700 ring-1 ring-inset ring-slate-600/20 shadow-sm backdrop-blur">
                        Returned to Owner
                    </span>
                @elseif ($item->status === 'resolved')
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-700 ring-1 ring-inset ring-slate-600/20 shadow-sm backdrop-blur">
                        Resolved
                    </span>
                @endif
            </div>
        </div>

        {{-- Card Content --}}
        <div class="p-5 space-y-3">
            <div class="flex items-center gap-2">
                <span class="inline-block rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-600">
                    {{ $categoryName }}
                </span>
                <span class="text-xs text-slate-400">·</span>
                <span class="text-xs text-slate-500">
                    {{ $isFound ? 'Found ' : 'Lost ' }} {{ $item->formatted_date }}
                </span>
            </div>

            <h3 class="text-base font-bold text-slate-900 group-hover:text-brand-600 transition-colors line-clamp-1">
                <a href="{{ $detailRoute }}">
                    {{ $item->item_name }}
                </a>
            </h3>

            <p class="text-xs leading-relaxed text-slate-600 line-clamp-2">
                {{ $item->description }}
            </p>

            <div class="flex items-center gap-1.5 text-xs text-slate-500 pt-1">
                <svg class="h-3.5 w-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
                <span class="truncate">{{ $item->location }}</span>
            </div>
        </div>
    </div>

    {{-- Card Footer --}}
    <div class="border-t border-slate-100 px-5 py-3.5 bg-slate-50/50 flex items-center justify-between gap-2">
        <a href="{{ $detailRoute }}"
           class="inline-flex items-center text-xs font-semibold text-brand-600 hover:text-brand-700 transition">
            View Details
            <svg class="ml-1 h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
            </svg>
        </a>

        @if ($isFound && $item->status === 'approved')
            <a href="{{ route('claims.create', $item->id) }}"
               class="rounded-lg bg-emerald-600 px-2.5 py-1 text-xs font-semibold text-white shadow-sm hover:bg-emerald-500 transition">
                Claim Item
            </a>
        @endif
    </div>
</div>
