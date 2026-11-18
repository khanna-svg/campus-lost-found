<x-layouts.guest title="Create account">
    <h2 class="text-lg font-semibold text-slate-900">Create your account</h2>
    <p class="mt-1 text-sm text-slate-500">Only school email addresses are accepted.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5" novalidate>
        @csrf

        <x-form.input name="name" label="Full name" autocomplete="name" required autofocus />
        <x-form.input name="school_id" label="Student / employee ID" hint="Optional" />
        <x-form.input name="email" label="School email" type="email" autocomplete="email" required />
        <x-form.input name="password" label="Password" type="password" autocomplete="new-password" required
                      :hint="'At least '.config('campus.password_min_length').' characters, with letters and numbers.'" />
        <x-form.input name="password_confirmation" label="Confirm password" type="password" autocomplete="new-password" required />

        <x-button class="w-full">Create account</x-button>
    </form>

    <x-slot:footer>
        Already have an account? <a href="{{ route('login') }}" class="font-medium text-brand-700 hover:underline">Sign in</a>
    </x-slot:footer>
</x-layouts.guest>
