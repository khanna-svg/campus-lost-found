@props([
    'size' => 'md',
    'withText' => true,
    'textClass' => 'text-base font-black tracking-tight text-zinc-900',
    'badgeText' => null,
])

@php
    $dimensions = match ($size) {
        'sm' => 'h-6 w-6',
        'md' => 'h-8 w-8',
        'lg' => 'h-11 w-11',
        'xl' => 'h-14 w-14 sm:h-16 sm:w-16',
        default => 'h-8 w-8',
    };
@endphp

<div class="flex items-center gap-2.5">
    <svg class="{{ $dimensions }} text-zinc-900 shrink-0" viewBox="0 0 100 100" fill="none" aria-hidden="true">
        <!-- Collegiate Shield Profile with Map Pin Anchor -->
        <path d="M50 8C68 12 84 15 84 38V52C84 66 66 84 50 92C34 84 16 66 16 52V38C16 15 32 12 50 8Z" stroke="currentColor" stroke-width="7.5" stroke-linejoin="round"/>
        
        <!-- Solid Map Pin Base Accent -->
        <path d="M50 90C41 84 30 76 25 66C32 72 42 75 50 75C58 75 68 72 75 66C70 76 59 84 50 90Z" fill="currentColor"/>
        
        <!-- Discovery Magnifying Glass Lens -->
        <circle cx="50" cy="44" r="17" stroke="currentColor" stroke-width="6.5"/>
        <path d="M38 56L26 68" stroke="currentColor" stroke-width="6.5" stroke-linecap="round"/>
        
        <!-- Central Verified Retrieval Checkmark -->
        <path d="M42 44L48 50L64 32" stroke="currentColor" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
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
