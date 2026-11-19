@props(['status'])

@php
    $label = is_object($status) && method_exists($status, 'label') ? $status->label() : ucwords(str_replace('_', ' ', (string) $status));
    $raw = is_object($status) ? ($status->value ?? (string) $status) : (string) $status;

    // High-contrast modern monochrome badge styling
    $badgeStyle = match ($raw) {
        'approved', 'completed', 'returned' => 'bg-black text-white ring-black',
        'pending_review', 'pending', 'under_review' => 'bg-zinc-100 text-zinc-900 ring-zinc-300',
        'additional_information_required' => 'bg-zinc-900 text-white ring-zinc-900',
        'rejected' => 'bg-zinc-100 text-zinc-600 line-through ring-zinc-300',
        'claimed', 'resolved' => 'bg-zinc-800 text-white ring-zinc-800',
        default => 'bg-zinc-100 text-zinc-700 ring-zinc-200',
    };

    // Dot color accent (green for pending status as requested)
    $dotStyle = match ($raw) {
        'pending_review', 'pending', 'under_review' => 'bg-emerald-500',
        'approved', 'completed', 'returned' => 'bg-emerald-400',
        'additional_information_required' => 'bg-amber-400',
        'rejected' => 'bg-rose-500',
        default => 'bg-current opacity-75',
    };
@endphp

<span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $badgeStyle }}">
    <span class="h-1.5 w-1.5 rounded-full {{ $dotStyle }}"></span>
    {{ $label }}
</span>
