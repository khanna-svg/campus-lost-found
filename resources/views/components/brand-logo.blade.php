@props([
    'size' => 'md',
    'withText' => true,
    'textClass' => 'text-base font-black tracking-tight text-zinc-900',
    'badgeText' => null,
])

@php
    $dimensions = match ($size) {
        'sm' => 'h-7 w-7',
        'md' => 'h-9 w-9',
        'lg' => 'h-11 w-11',
        default => 'h-9 w-9',
    };
    $iconSize = match ($size) {
        'sm' => 'h-4 w-4',
        'md' => 'h-5 w-5',
        'lg' => 'h-6 w-6',
        default => 'h-5 w-5',
    };
@endphp

<div class="flex items-center gap-2.5">
    <span class="flex {{ $dimensions }} items-center justify-center rounded-xl bg-black text-white shadow-xs shrink-0">
        <svg class="{{ $iconSize }}" viewBox="0 0 120 120" fill="none">
            <!-- Collegiate Shield Profile -->
            <path d="M60 14L92 26V58C92 78 78 96 60 106C42 96 28 78 28 58V26L60 14Z" stroke="currentColor" stroke-width="8" stroke-linejoin="round"/>
            
            <!-- Radar Search Waves / Pulse -->
            <circle cx="60" cy="54" r="8" fill="currentColor"/>
            <path d="M48 42C51.5 38.5 55.5 36.5 60 36.5C64.5 36.5 68.5 38.5 72 42" stroke="currentColor" stroke-width="6" stroke-linecap="round"/>
            <path d="M40 34C46 28 53 25 60 25C67 25 74 28 80 34" stroke="currentColor" stroke-width="6" stroke-linecap="round"/>
            
            <!-- Central Retrieval Checkmark -->
            <path d="M48 58L56 66L74 46" stroke="currentColor" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </span>
    @if ($withText)
        <div class="flex items-center gap-2">
            <span class="{{ $textClass }}">CampusBeacon</span>
            @if ($badgeText)
                <span class="rounded-md bg-zinc-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-zinc-600">
                    {{ $badgeText }}
                </span>
            @endif
        </div>
    @endif
</div>
