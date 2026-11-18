@props(['variant' => 'primary', 'type' => 'submit'])
<button type="{{ $type }}"
    {{ $attributes->class([
        'inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-semibold shadow-sm transition-colors disabled:cursor-not-allowed disabled:opacity-60',
        'bg-brand-600 text-white hover:bg-brand-700' => $variant === 'primary',
        'bg-white text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50' => $variant === 'secondary',
        'bg-red-600 text-white hover:bg-red-700' => $variant === 'danger',
    ]) }}>
    {{ $slot }}
</button>
