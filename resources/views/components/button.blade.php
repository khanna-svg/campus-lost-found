@props(['variant' => 'primary', 'type' => 'submit'])
<button type="{{ $type }}"
    {{ $attributes->class([
        'inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold shadow-sm transition-all duration-150 disabled:cursor-not-allowed disabled:opacity-60',
        'bg-black text-white hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2' => $variant === 'primary',
        'bg-white text-zinc-800 ring-1 ring-inset ring-zinc-300 hover:bg-zinc-50' => $variant === 'secondary',
        'bg-zinc-900 text-white hover:bg-black' => $variant === 'danger',
    ]) }}>
    {{ $slot }}
</button>
