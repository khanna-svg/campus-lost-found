<x-layouts.guest title="Sign in">
    <h2 class="text-lg font-semibold text-slate-900">Sign in</h2>
    <p class="mt-1 text-sm text-slate-500">Use your school account to continue.</p>

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5" novalidate>
        @csrf

        <x-form.input name="email" label="School email" type="email" autocomplete="username" required autofocus />
        <x-form.input name="password" label="Password" type="password" autocomplete="current-password" required />

        <x-button class="w-full">Sign in</x-button>
    </form>

    <x-slot:footer>
        @if (config('campus.auth_mode') === 'self_register')
            New here? <a href="{{ route('register') }}" class="font-medium text-brand-700 hover:underline">Create an account</a>
        @else
            Accounts are created by the school administrator.
        @endif
    </x-slot:footer>
</x-layouts.guest>
