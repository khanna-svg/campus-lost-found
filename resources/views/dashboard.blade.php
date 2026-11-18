<x-layouts.app title="Dashboard">
    <div class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Hello, {{ $user->name }} 👋</h1>
        <p class="mt-2 text-sm text-slate-500">
            You are signed in as <span class="font-medium text-slate-700">{{ $user->role->label() }}</span>.
            Your dashboard, item reporting and search tools arrive in the next phases.
        </p>
    </div>
</x-layouts.app>
