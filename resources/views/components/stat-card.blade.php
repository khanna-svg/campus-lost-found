@props([
    'title',
    'value' => 0,
    'description' => null,
    'icon' => null,
    'color' => 'neutral',
])

<div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-zinc-200 transition hover:shadow-md hover:ring-zinc-900">
    <div class="flex items-center justify-between">
        <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">{{ $title }}</p>
        @if ($icon)
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-zinc-100 text-zinc-900">
                {{ $icon }}
            </div>
        @endif
    </div>
    <div class="mt-2 flex items-baseline gap-2">
        <span class="text-3xl font-bold tracking-tight text-zinc-900">{{ number_format($value) }}</span>
        @if ($description)
            <span class="text-xs text-zinc-500">{{ $description }}</span>
        @endif
    </div>
</div>
