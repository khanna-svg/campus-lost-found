@props([
    'title',
    'value' => 0,
    'description' => null,
    'icon' => null,
    'color' => 'brand',
])

@php
    $colorMap = [
        'brand' => 'bg-brand-50 text-brand-600',
        'amber' => 'bg-amber-50 text-amber-600',
        'emerald' => 'bg-emerald-50 text-emerald-600',
        'indigo' => 'bg-indigo-50 text-indigo-600',
        'rose' => 'bg-rose-50 text-rose-600',
    ];
    $iconClass = $colorMap[$color] ?? $colorMap['brand'];
@endphp

<div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 transition hover:shadow-md">
    <div class="flex items-center justify-between">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $title }}</p>
        @if ($icon)
            <div class="flex h-9 w-9 items-center justify-center rounded-xl {{ $iconClass }}">
                {{ $icon }}
            </div>
        @endif
    </div>
    <div class="mt-2 flex items-baseline gap-2">
        <span class="text-3xl font-bold tracking-tight text-slate-900">{{ number_format($value) }}</span>
        @if ($description)
            <span class="text-xs text-slate-500">{{ $description }}</span>
        @endif
    </div>
</div>
