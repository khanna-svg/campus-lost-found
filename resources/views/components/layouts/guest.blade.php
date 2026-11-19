@props(['title' => null])
<x-layouts.base :title="$title">
    <main class="flex min-h-screen flex-col items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center flex flex-col items-center">
                <a href="{{ route('items.index') }}" class="inline-block">
                    <x-brand-logo size="lg" textClass="text-2xl font-black tracking-tight text-zinc-900" />
                </a>
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
