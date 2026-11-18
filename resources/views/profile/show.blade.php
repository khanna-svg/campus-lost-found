<x-layouts.app title="My profile">
    <div class="mx-auto max-w-2xl space-y-8">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">My profile</h1>
            <p class="mt-1 text-sm text-slate-500">Manage your contact details and password.</p>
        </div>

        @if ($user->must_change_password)
            <div class="rounded-xl bg-amber-50 p-4 text-sm text-amber-900 ring-1 ring-amber-200">
                You are using a temporary password. Please choose a new one below to continue.
            </div>
        @endif

        {{-- Account details --}}
        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <h2 class="text-base font-semibold text-slate-900">Account details</h2>
            <form method="POST" action="{{ route('profile.update') }}" class="mt-5 space-y-5" novalidate>
                @csrf
                @method('PUT')

                <div class="grid gap-5 sm:grid-cols-2">
                    <x-form.input name="email" label="Institutional Email" :value="$user->email" readonly hint="Managed by the school administrator." />
                    <x-form.input name="role_label" label="Role" :value="$user->role->label()" readonly />
                </div>

                <x-form.input name="name" label="Full name" :value="$user->name" autocomplete="name" required />
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-form.input name="department" label="Department / course" :value="$user->department" />
                    <x-form.input name="phone" label="Phone" type="tel" :value="$user->phone" autocomplete="tel" />
                </div>

                <div class="flex justify-end">
                    <x-button>Save changes</x-button>
                </div>
            </form>
        </section>

        {{-- Password --}}
        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <h2 class="text-base font-semibold text-slate-900">Change password</h2>
            <form method="POST" action="{{ route('profile.password.update') }}" class="mt-5 space-y-5" novalidate>
                @csrf
                @method('PUT')

                <x-form.input name="current_password" label="Current password" type="password" autocomplete="current-password" required />
                <x-form.input name="password" label="New password" type="password" autocomplete="new-password" required
                              :hint="'At least '.config('campus.password_min_length').' characters, with letters and numbers.'" />
                <x-form.input name="password_confirmation" label="Confirm new password" type="password" autocomplete="new-password" required />

                <div class="flex justify-end">
                    <x-button>Update password</x-button>
                </div>
            </form>
        </section>
    </div>
</x-layouts.app>
