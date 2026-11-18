@php
    $toasts = [];
    foreach (['success' => 'success', 'error' => 'error', 'warning' => 'warning', 'status' => 'success'] as $key => $type) {
        if (session()->has($key)) {
            $toasts[] = ['type' => $type, 'message' => session($key)];
        }
    }
    if ($errors->any()) {
        $toasts[] = ['type' => 'error', 'message' => 'Please check the highlighted fields and try again.'];
    }

    $styles = [
        'success' => 'bg-emerald-50 text-emerald-900 ring-emerald-200',
        'error' => 'bg-red-50 text-red-900 ring-red-200',
        'warning' => 'bg-amber-50 text-amber-900 ring-amber-200',
    ];
    $icons = [
        'success' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
        'error' => 'M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z',
        'warning' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z',
    ];
@endphp

<div class="pointer-events-none fixed inset-x-0 top-4 z-50 flex flex-col items-center gap-2 px-4 sm:items-end sm:px-6"
     role="status" aria-live="polite">
    @foreach ($toasts as $toast)
        <div data-toast class="pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-xl p-4 text-sm shadow-lg ring-1 {{ $styles[$toast['type']] }}">
            <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$toast['type']] }}" />
            </svg>
            <p class="flex-1">{{ $toast['message'] }}</p>
            <button type="button" data-toast-close class="-m-1 rounded p-1 opacity-60 hover:opacity-100" aria-label="Dismiss notification">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
            </button>
        </div>
    @endforeach
</div>
