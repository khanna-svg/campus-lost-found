@props(['item'])

@php
    $isFound = $item->item_type === 'found';
    $detailRoute = $isFound ? route('found-items.show', $item->id) : route('lost-items.show', $item->id);
    $isResolved = $item->is_resolved ?? false;
@endphp

<div class="group relative flex flex-col justify-between overflow-hidden rounded-2xl bg-white border border-zinc-200 shadow-xs transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md hover:border-zinc-300 cursor-pointer"
     onclick="if (typeof openItemDetailsModal === 'function') { event.preventDefault(); openItemDetailsModal({{ json_encode($item) }}); }">
    <a href="{{ $detailRoute }}" class="block">
        {{-- Thumbnail / Photo Header --}}
        <div class="relative h-56 w-full overflow-hidden bg-zinc-50 border-b border-zinc-100 flex items-center justify-center">
            @if (! empty($item->photo_url))
                <img src="{{ $item->photo_url }}"
                     alt="{{ $item->item_name }}"
                     loading="lazy"
                     onerror="this.onerror=null; this.remove(); document.getElementById('card-placeholder-{{ $item->item_type }}-{{ $item->id }}').classList.remove('hidden');"
                     class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                {{-- Fallback SVG if image fails to load --}}
                <div id="card-placeholder-{{ $item->item_type }}-{{ $item->id }}" class="hidden flex items-center justify-center text-zinc-300">
                    <svg class="h-14 w-14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <circle cx="11" cy="11" r="7" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="m20 20-4-4" />
                    </svg>
                </div>
            @else
                {{-- Minimalist Magnifying Glass Placeholder (matching reference) --}}
                <div class="flex items-center justify-center text-zinc-300">
                    <svg class="h-14 w-14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <circle cx="11" cy="11" r="7" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="m20 20-4-4" />
                    </svg>
                </div>
            @endif

            {{-- Floating Badges in Top Right Corner (matching reference) --}}
            <div class="absolute top-3.5 right-3.5 flex flex-col items-end gap-1.5 pointer-events-none">
                {{-- Item Type Badge (Found Item vs Lost Item) --}}
                @if ($isFound)
                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-600 border border-emerald-100/60 shadow-xs">
                        Found Item
                    </span>
                @else
                    <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-semibold text-rose-500 border border-rose-100/60 shadow-xs">
                        Lost Item
                    </span>
                @endif

                {{-- Status Badge (Unresolved vs Resolved) --}}
                @if ($isResolved)
                    <span class="inline-flex items-center rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-semibold text-zinc-600 border border-zinc-200/60 shadow-xs">
                        Resolved
                    </span>
                @else
                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-600 border border-amber-100/60 shadow-xs">
                        Unresolved
                    </span>
                @endif
            </div>
        </div>

        {{-- Card Body --}}
        <div class="p-5 space-y-2">
            {{-- Title --}}
            <h3 class="text-base font-bold text-zinc-900 group-hover:text-black transition-colors line-clamp-1">
                {{ $item->item_name }}
            </h3>

            {{-- Description Snippet --}}
            <p class="text-xs leading-relaxed text-zinc-500 line-clamp-2 min-h-[2rem]">
                {{ $item->description }}
            </p>

            {{-- Horizontal Divider --}}
            <hr class="my-3.5 border-zinc-100">

            {{-- 3 Metadata Rows with Icons --}}
            <div class="space-y-2 text-xs text-zinc-500">
                {{-- Location --}}
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-zinc-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                    <span class="truncate">{{ $item->location }}</span>
                </div>

                {{-- Date --}}
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-zinc-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.253 18.75m-18-13.5h22.5m-22.5 0v13.5A2.25 2.25 0 0 0 4.5 21h15a2.25 2.25 0 0 0 2.25-2.25V7.5H2.25Z" />
                    </svg>
                    <span>{{ $item->formatted_date }}</span>
                </div>

                {{-- Poster Name --}}
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-zinc-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                    <span class="truncate">Posted by {{ $item->poster_name ?? 'Campus Member' }}</span>
                </div>
            </div>
        </div>
    </a>
</div>
