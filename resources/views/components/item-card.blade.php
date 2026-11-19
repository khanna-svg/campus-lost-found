@props(['item'])

@php
    $isFound = $item->item_type === 'found';
    $detailRoute = $isFound ? route('found-items.show', $item->id) : route('lost-items.show', $item->id);
    $categoryName = $item->category ? $item->category->name : 'Uncategorized';
@endphp

<div class="group flex flex-col justify-between overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-zinc-200 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md hover:ring-zinc-900">
    <div>
        {{-- Thumbnail / Photo Header --}}
        <div class="relative h-48 w-full overflow-hidden bg-zinc-100">
            @if (! empty($item->photo_url))
                <img src="{{ $item->photo_url }}"
                     alt="{{ $item->item_name }}"
                     loading="lazy"
                     onerror="this.onerror=null; this.parentElement.classList.add('flex', 'items-center', 'justify-center'); this.remove();"
                     class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                {{-- Fallback SVG if image is missing --}}
                <div class="hidden absolute inset-0 flex flex-col items-center justify-center bg-zinc-50 text-zinc-400">
                    <svg class="h-10 w-10 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    <span class="mt-1 text-[11px] font-medium text-zinc-400">Photo unavailable</span>
                </div>
            @else
                <div class="flex h-full w-full flex-col items-center justify-center bg-zinc-50 text-zinc-400">
                    <svg class="h-10 w-10 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    <span class="mt-1 text-[11px] font-medium text-zinc-400">No photo provided</span>
                </div>
            @endif

            {{-- Floating Badges --}}
            <div class="absolute inset-x-3 top-3 flex items-center justify-between pointer-events-none">
                {{-- Type Badge (Black / Dark Zinc) --}}
                @if ($isFound)
                    <span class="inline-flex items-center gap-1 rounded-full bg-black/90 px-2.5 py-0.5 text-[11px] font-semibold text-white shadow-sm backdrop-blur">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        Found
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 rounded-full bg-zinc-800/90 px-2.5 py-0.5 text-[11px] font-semibold text-white shadow-sm backdrop-blur">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                        Lost
                    </span>
                @endif

                {{-- Status Pill --}}
                @if ($item->status === 'approved')
                    @if ($isFound)
                        <span class="inline-flex items-center rounded-full bg-white/95 px-2.5 py-0.5 text-[10px] font-bold text-black ring-1 ring-inset ring-zinc-300 shadow-sm backdrop-blur">
                            Available to Claim
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-white/95 px-2.5 py-0.5 text-[10px] font-bold text-zinc-800 ring-1 ring-inset ring-zinc-300 shadow-sm backdrop-blur">
                            Actively Missing
                        </span>
                    @endif
                @elseif ($item->status === 'claimed')
                    <span class="inline-flex items-center rounded-full bg-zinc-900 px-2.5 py-0.5 text-[10px] font-bold text-white shadow-sm">
                        Claimed
                    </span>
                @elseif ($item->status === 'returned')
                    <span class="inline-flex items-center rounded-full bg-zinc-100 px-2.5 py-0.5 text-[10px] font-bold text-zinc-900 ring-1 ring-inset ring-zinc-300 shadow-sm">
                        Returned to Owner
                    </span>
                @elseif ($item->status === 'resolved')
                    <span class="inline-flex items-center rounded-full bg-zinc-100 px-2.5 py-0.5 text-[10px] font-bold text-zinc-900 ring-1 ring-inset ring-zinc-300 shadow-sm">
                        Resolved
                    </span>
                @endif
            </div>
        </div>

        {{-- Card Content --}}
        <div class="p-5 space-y-3">
            <div class="flex items-center gap-2">
                <span class="inline-block rounded-md bg-zinc-100 px-2 py-0.5 text-[11px] font-medium text-zinc-800">
                    {{ $categoryName }}
                </span>
                <span class="text-xs text-zinc-400">·</span>
                <span class="text-xs text-zinc-500">
                    {{ $isFound ? 'Found ' : 'Lost ' }} {{ $item->formatted_date }}
                </span>
            </div>

            <h3 class="text-base font-bold text-zinc-900 group-hover:text-black transition-colors line-clamp-1">
                <a href="{{ $detailRoute }}" class="hover:underline">
                    {{ $item->item_name }}
                </a>
            </h3>

            <p class="text-xs leading-relaxed text-zinc-600 line-clamp-2">
                {{ $item->description }}
            </p>

            <div class="flex items-center gap-1.5 text-xs text-zinc-500 pt-1">
                <svg class="h-3.5 w-3.5 text-zinc-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
                <span class="truncate">{{ $item->location }}</span>
            </div>
        </div>
    </div>

    {{-- Card Footer --}}
    <div class="border-t border-zinc-100 px-5 py-3.5 bg-zinc-50/50 flex items-center justify-between gap-2">
        <a href="{{ $detailRoute }}"
           class="inline-flex items-center text-xs font-semibold text-zinc-900 hover:text-black hover:underline transition">
            View Details
            <svg class="ml-1 h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
            </svg>
        </a>
    </div>
</div>
