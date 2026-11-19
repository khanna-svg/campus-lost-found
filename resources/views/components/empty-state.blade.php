@props([
    'title',
    'description',
    'icon' => null,
])

<div class="rounded-2xl border-2 border-dashed border-zinc-200 bg-zinc-50/40 p-8 text-center sm:p-12">
    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-zinc-100 text-zinc-700">
        @if ($icon)
            {{ $icon }}
        @else
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
            </svg>
        @endif
    </div>
    <h3 class="mt-4 text-sm font-bold text-zinc-900">{{ $title }}</h3>
    <p class="mt-1 text-xs text-zinc-500 max-w-sm mx-auto">{{ $description }}</p>
    @if ($slot->isNotEmpty())
        <div class="mt-6 flex justify-center">
            {{ $slot }}
        </div>
    @endif
</div>
