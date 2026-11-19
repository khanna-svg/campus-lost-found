@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'hint' => null,
    'required' => false,
    'readonly' => false,
])
@php
    $id = $attributes->get('id', $name);
    $hasError = $errors->has($name);
    $describedBy = trim(($hint ? "{$id}-hint " : '').($hasError ? "{$id}-error" : ''));
    $isSecret = $type === 'password';
@endphp

<div>
    <label for="{{ $id }}" class="mb-1.5 block text-sm font-medium text-zinc-800">
        {{ $label }}
        @if ($required)<span class="text-zinc-900 font-bold" aria-hidden="true">*</span>@endif
    </label>

    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @unless ($isSecret) value="{{ old($name, $value) }}" @endunless
        @required($required)
        @readonly($readonly)
        @if ($hasError) aria-invalid="true" @endif
        @if ($describedBy !== '') aria-describedby="{{ $describedBy }}" @endif
        {{ $attributes->except('id')->class([
            'block w-full rounded-lg border-0 px-3 py-2.5 text-sm text-zinc-900 shadow-sm ring-1 ring-inset placeholder:text-zinc-400 focus:ring-2 focus:ring-inset',
            'ring-zinc-300 focus:ring-black' => ! $hasError,
            'ring-red-400 focus:ring-red-500' => $hasError,
            'bg-zinc-50 text-zinc-500' => $readonly,
        ]) }}
    >

    @if ($hint)
        <p id="{{ $id }}-hint" class="mt-1.5 text-xs text-slate-500">{{ $hint }}</p>
    @endif
    @error($name)
        <p id="{{ $id }}-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
