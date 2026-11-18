@props(['title' => null])
<x-layouts.base :title="$title">
    <main class="flex min-h-screen flex-col items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-brand-600 text-white shadow-sm">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">{{ config('app.name') }}</h1>
                <p class="mt-1 text-sm text-slate-500">{{ config('campus.school_name') }}</p>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                {{ $slot }}
            </div>

            @isset($footer)
                <p class="mt-6 text-center text-sm text-slate-500">{{ $footer }}</p>
            @endisset
        </div>
    </main>
</x-layouts.base>
