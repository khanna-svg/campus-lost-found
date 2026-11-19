<x-layouts.guest title="Sign in">
    <h2 class="text-lg font-semibold text-slate-900">Sign in</h2>

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5" novalidate>
        @csrf

        <x-form.input name="email" label="School email" type="email" autocomplete="username" required autofocus />
        <x-form.input name="password" label="Password" type="password" autocomplete="current-password" required />

        <x-button class="w-full">Sign in</x-button>
    </form>

    @if (config('campus.auth_mode') === 'self_register')
        <x-slot:footer>
            New here? <a href="{{ route('register') }}" class="font-medium text-brand-700 hover:underline">Create an account</a>
        </x-slot:footer>
    @endif
</x-layouts.guest>
