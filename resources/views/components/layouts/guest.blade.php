@props(['title' => null])
<x-layouts.base :title="$title">
    <div class="min-h-screen bg-white font-sans flex flex-col md:flex-row overflow-x-hidden">
        <!-- Left Section: School Campus Picture (Clean Straight Split) -->
        <div class="relative w-full md:w-1/2 lg:w-7/12 h-48 sm:h-64 md:h-auto md:min-h-screen overflow-hidden bg-slate-900 shrink-0 border-b md:border-b-0 md:border-r border-slate-200">
            <img 
                src="{{ asset('images/campus-hero.jpg') }}" 
                alt="Campus" 
                class="w-full h-full object-cover object-[center_20%] md:object-center filter contrast-[1.03] brightness-[0.98]"
            >
            <!-- Soft Tint Overlay: provides depth without washing out clarity -->
            <div class="absolute inset-0 bg-gradient-to-t md:bg-gradient-to-r from-black/35 via-slate-900/10 to-transparent"></div>
        </div>

        <!-- Right Section: White Background Login Credentials -->
        <div class="w-full md:w-1/2 lg:w-5/12 min-h-0 md:min-h-screen flex-1 flex flex-col justify-start md:justify-between items-center p-6 sm:p-10 lg:p-14 bg-white text-zinc-900 relative z-20">
            <!-- Top Spacer -->
            <div class="hidden md:block w-full h-2"></div>

            <!-- Main Form Container -->
            <main class="w-full max-w-[340px] my-4 sm:my-auto">
                {{ $slot }}
            </main>

            <!-- Footer -->
            @isset($footer)
                <footer class="w-full text-center text-xs text-zinc-400 py-4">
                    {{ $footer }}
                </footer>
            @endisset
        </div>
    </div>
</x-layouts.base>
