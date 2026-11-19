@props(['status'])

@php
    $label = is_object($status) && method_exists($status, 'label') ? $status->label() : ucwords(str_replace('_', ' ', (string) $status));
    $colorClass = is_object($status) && method_exists($status, 'colorClass') ? $status->colorClass() : 'bg-slate-100 text-slate-700 ring-slate-200';
@endphp

<span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $colorClass }}">
    <span class="h-1.5 w-1.5 rounded-full bg-current opacity-75"></span>
    {{ $label }}
</span>
