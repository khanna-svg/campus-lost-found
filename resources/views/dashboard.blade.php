<x-layouts.app title="Student & Faculty Dashboard">
    <div class="space-y-8">
        {{-- Hero / Welcome Section --}}
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 sm:text-3xl">
                    Welcome back, {{ $user->name }} 👋
                </h1>
                <p class="mt-1 text-sm text-zinc-500">
                    {{ config('campus.school_name') }} · Lost & Found Central Portal
                </p>
            </div>

            {{-- Quick Actions --}}
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('lost-items.create') }}"
                   class="inline-flex items-center gap-1.5 rounded-xl bg-black px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Report Lost Item
                </a>

                <a href="{{ route('found-items.create') }}"
                   class="inline-flex items-center gap-1.5 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-zinc-800 shadow-sm ring-1 ring-inset ring-zinc-300 transition hover:bg-zinc-50 hover:text-black">
                    <svg class="h-4 w-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Report Found Item
                </a>

                <a href="{{ route('items.index') }}"
                   class="inline-flex items-center gap-1.5 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-zinc-800 shadow-sm ring-1 ring-inset ring-zinc-300 transition hover:bg-zinc-50 hover:text-black">
                    <svg class="h-4 w-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    Browse Directory
                </a>
            </div>
        </div>

        {{-- Statistics Overview Cards (3 Cards) --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 sm:gap-5">
            <x-stat-card
                title="Active Lost Reports"
                :value="$stats['active_lost']"
                description="Under review or missing"
            >
                <x-slot:icon>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card
                title="Found Items Logged"
                :value="$stats['found_reported']"
                description="Submitted by you"
            >
                <x-slot:icon>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card
                title="Reunited Belongings"
                :value="$stats['resolved']"
                description="Successfully returned"
            >
                <x-slot:icon>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>
        </div>

        {{-- Submissions & Activity Section with Interactive Tabs --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-zinc-200">
            {{-- Tab Headers --}}
            <div class="border-b border-zinc-200 px-6 pt-5">
                <div class="flex flex-wrap items-center justify-between gap-4 pb-4">
                    <div>
                        <h2 class="text-base font-bold text-zinc-900">My Reports</h2>
                        <p class="text-xs text-zinc-500">Track current status and updates for your items</p>
                    </div>

                    <div class="flex gap-1.5 rounded-xl bg-zinc-100 p-1" role="tablist">
                        <button type="button"
                                data-tab-target="tab-lost"
                                class="dashboard-tab rounded-lg px-3 py-1.5 text-xs font-semibold text-white bg-black shadow-sm transition"
                                role="tab"
                                aria-selected="true">
                            Lost Reports ({{ $recentLost->count() }})
                        </button>
                        <button type="button"
                                data-tab-target="tab-found"
                                class="dashboard-tab rounded-lg px-3 py-1.5 text-xs font-medium text-zinc-600 hover:text-black transition"
                                role="tab"
                                aria-selected="false">
                            Found Reports ({{ $recentFound->count() }})
                        </button>
                    </div>
                </div>
            </div>

            {{-- Tab 1: My Lost Reports --}}
            <div id="tab-lost" class="p-6">
                @if ($recentLost->isEmpty())
                    <x-empty-state
                        title="No lost items reported yet"
                        description="If you misplaced an item on campus, create a report so the school community and administration can assist in finding it."
                    >
                        <a href="{{ route('lost-items.create') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-black px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-zinc-800 transition">
                            + Report a Lost Item
                        </a>
                    </x-empty-state>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-zinc-600">
                            <thead class="border-b border-zinc-100 text-xs font-semibold uppercase text-zinc-400">
                                <tr>
                                    <th class="pb-3">Item Name</th>
                                    <th class="pb-3">Category</th>
                                    <th class="pb-3">Date Lost</th>
                                    <th class="pb-3">Last Seen Location</th>
                                    <th class="pb-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100">
                                @foreach ($recentLost as $item)
                                    <tr class="hover:bg-zinc-50/75 transition-colors">
                                        <td class="py-3.5 font-medium text-zinc-900">
                                            <a href="{{ route('lost-items.show', $item) }}" class="text-black font-semibold hover:underline">
                                                {{ $item->item_name }}
                                            </a>
                                            @if ($item->needs_correction)
                                                <span class="ml-2 inline-flex items-center rounded-md bg-zinc-900 text-white px-2 py-0.5 text-[10px] font-bold">
                                                    Action Required
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3.5">
                                            <span class="rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-800">
                                                {{ $item->category->name ?? 'General' }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 text-xs text-zinc-500">
                                            {{ $item->date_lost->format('M d, Y') }}
                                        </td>
                                        <td class="py-3.5 text-xs text-zinc-500">
                                            {{ $item->location_last_seen }}
                                        </td>
                                        <td class="py-3.5">
                                            <x-status-badge :status="$item->status" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Tab 2: My Found Reports --}}
            <div id="tab-found" class="hidden p-6">
                @if ($recentFound->isEmpty())
                    <x-empty-state
                        title="No found items reported yet"
                        description="Did you find someone's belongings on campus? Report it here so the rightful owner can verify and claim it."
                    >
                        <a href="{{ route('found-items.create') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-black px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-zinc-800 transition">
                            + Report a Found Item
                        </a>
                    </x-empty-state>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-zinc-600">
                            <thead class="border-b border-zinc-100 text-xs font-semibold uppercase text-zinc-400">
                                <tr>
                                    <th class="pb-3">Item Name</th>
                                    <th class="pb-3">Category</th>
                                    <th class="pb-3">Date Found</th>
                                    <th class="pb-3">Found Location</th>
                                    <th class="pb-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100">
                                @foreach ($recentFound as $item)
                                    <tr class="hover:bg-zinc-50/75 transition-colors">
                                        <td class="py-3.5 font-medium text-zinc-900">
                                            <a href="{{ route('found-items.show', $item) }}" class="text-black font-semibold hover:underline">
                                                {{ $item->item_name }}
                                            </a>
                                        </td>
                                        <td class="py-3.5">
                                            <span class="rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-800">
                                                {{ $item->category->name ?? 'General' }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 text-xs text-zinc-500">
                                            {{ $item->date_found->format('M d, Y') }}
                                        </td>
                                        <td class="py-3.5 text-xs text-zinc-500">
                                            {{ $item->location_found }}
                                        </td>
                                        <td class="py-3.5">
                                            <x-status-badge :status="$item->status" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Information & Retrieval Guidelines Banner --}}
        <div class="rounded-2xl bg-black p-6 text-white sm:p-8 border border-zinc-800">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl space-y-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-zinc-800 px-3 py-1 text-xs font-medium text-zinc-200 ring-1 ring-inset ring-zinc-700">
                        Campus Guidelines
                    </span>
                    <h3 class="text-lg font-bold tracking-tight text-white sm:text-xl">
                        How Verification & Retrieval Works
                    </h3>
                    <p class="text-xs leading-relaxed text-zinc-400 sm:text-sm">
                        To maintain fair and secure item recovery, sensitive identifying characteristics of found items are kept private. If you recognize a found belonging as yours, visit the Campus Lost & Found Office with your School ID to verify ownership and retrieve your property.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('items.index') }}"
                       class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2.5 text-xs font-bold text-black shadow-sm transition hover:bg-zinc-100">
                        Search Belongings
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
