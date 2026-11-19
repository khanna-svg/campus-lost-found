<x-layouts.guest title="Sign in">
    <style>
        .floating-badge-group {
            position: relative;
            margin-top: 0.5rem;
        }
        .floating-badge-label {
            position: absolute;
            left: 0.85rem;
            top: 0.65rem;
            font-size: 0.8125rem;
            color: #71717a;
            pointer-events: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 10;
        }
        .floating-badge-input:focus ~ .floating-badge-label,
        .floating-badge-input:not(:placeholder-shown) ~ .floating-badge-label,
        .floating-badge-input:-webkit-autofill ~ .floating-badge-label {
            top: -1.2rem;
            left: 0.4rem;
            font-size: 0.7rem;
            font-weight: 700;
            color: #09090b;
        }
    </style>

    {{-- Brand Header --}}
    <div class="mb-5 text-center">
        <div class="flex items-center justify-center mb-2.5">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl bg-white p-2 shadow-sm border border-zinc-200/80 flex items-center justify-center">
                <x-brand-logo size="lg" :with-text="false" />
            </div>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-zinc-900 tracking-tight font-sans">
            CampusBeacon
        </h1>
        <p class="mt-0.5 text-xs text-zinc-500 font-medium tracking-wide">
            Campus Lost &amp; Found
        </p>
    </div>

    {{-- Flash Notifications --}}
    @if (session('status'))
        <div class="mb-4 rounded-xl border border-emerald-300 bg-emerald-50 p-2.5 text-xs text-emerald-800 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    {{-- Validation Error Banner --}}
    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-red-300 bg-red-50 p-2.5 text-xs text-red-800 flex items-start gap-2">
            <svg class="w-4 h-4 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    {{-- Sign In Form --}}
    <form method="POST" action="{{ route('login') }}" class="space-y-3.5" novalidate>
        @csrf

        {{-- Floating Email Input --}}
        <div class="floating-badge-group">
            <input 
                id="email" 
                name="email" 
                type="email" 
                value="{{ old('email') }}" 
                placeholder=" "
                required 
                autofocus 
                autocomplete="email"
                class="floating-badge-input block w-full rounded-xl border border-zinc-300 bg-zinc-50/70 px-3.5 py-2.5 text-xs sm:text-sm text-zinc-900 outline-none focus:bg-white focus:border-black focus:ring-1 focus:ring-black transition-all shadow-2xs">
            <label 
                for="email" 
                class="floating-badge-label">
                Email
            </label>
        </div>

        {{-- Floating Password Input with Toggle --}}
        <div class="floating-badge-group">
            <input 
                id="password" 
                name="password" 
                type="password" 
                placeholder=" " 
                required 
                autocomplete="current-password"
                class="floating-badge-input block w-full rounded-xl border border-zinc-300 bg-zinc-50/70 px-3.5 pr-10 py-2.5 text-xs sm:text-sm text-zinc-900 outline-none focus:bg-white focus:border-black focus:ring-1 focus:ring-black transition-all shadow-2xs">
            <label 
                for="password" 
                class="floating-badge-label">
                Password
            </label>
            <button 
                type="button" 
                onclick="togglePasswordVisibility()" 
                title="Show/Hide Password"
                class="absolute right-2.5 top-2 text-zinc-400 hover:text-black transition p-1 cursor-pointer z-10">
                <svg id="passwordEyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </button>
        </div>

        {{-- Submit Button --}}
        <button 
            type="submit" 
            class="w-full rounded-xl bg-black hover:bg-zinc-800 py-2.5 text-xs sm:text-sm font-semibold text-white transition shadow-sm flex items-center justify-center gap-2 cursor-pointer mt-1">
            <span>Sign in</span>
        </button>
    </form>

    @if (config('campus.auth_mode') === 'self_register')
        <x-slot:footer>
            <div class="flex items-center justify-between text-xs text-zinc-500 w-full max-w-[340px] mx-auto px-2">
                <span>New student?</span>
                <a href="{{ route('register') }}" class="font-semibold text-black hover:underline">
                    Create account
                </a>
            </div>
        </x-slot:footer>
    @endif

    <script>
        function togglePasswordVisibility() {
            const input = document.getElementById('password');
            const eyeIcon = document.getElementById('passwordEyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />`;
            } else {
                input.type = 'password';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />`;
            }
        }
    </script>
</x-layouts.guest>
