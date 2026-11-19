<x-layouts.app title="Student & Faculty Dashboard">
    <div class="space-y-8">
        {{-- Hero / Welcome Section --}}
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Welcome back, {{ $user->name }} 👋
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ config('campus.school_name') }} · Lost & Found Central Portal
                </p>
            </div>

            {{-- Quick Actions --}}
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('lost-items.create') }}"
                   class="inline-flex items-center gap-1.5 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Report Lost Item
                </a>

                <a href="{{ route('found-items.create') }}"
                   class="inline-flex items-center gap-1.5 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 transition hover:bg-slate-50">
                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Report Found Item
                </a>

                <a href="{{ route('items.index') }}"
                   class="inline-flex items-center gap-1.5 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 transition hover:bg-slate-50">
                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    Browse Listings
                </a>
            </div>
        </div>

        {{-- Statistics Overview Cards --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-4 sm:gap-5">
            <x-stat-card
                title="Active Lost Reports"
                :value="$stats['active_lost']"
                description="Under review or published"
                color="amber"
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
                color="indigo"
            >
                <x-slot:icon>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card
                title="Active Claims"
                :value="$stats['pending_claims']"
                description="Awaiting return or review"
                color="brand"
            >
                <x-slot:icon>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card
                title="Reunited Belongings"
                :value="$stats['resolved']"
                description="Successfully returned"
                color="emerald"
            >
                <x-slot:icon>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>
        </div>

        {{-- Submissions & Activity Section with Interactive Tabs --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            {{-- Tab Headers --}}
            <div class="border-b border-slate-200 px-6 pt-5">
                <div class="flex flex-wrap items-center justify-between gap-4 pb-4">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">My Reports & Claims</h2>
                        <p class="text-xs text-slate-500">Track current status and updates for your items</p>
                    </div>

                    <div class="flex gap-1.5 rounded-xl bg-slate-100 p-1" role="tablist">
                        <button type="button"
                                data-tab-target="tab-lost"
                                class="dashboard-tab rounded-lg px-3 py-1.5 text-xs font-semibold text-brand-700 bg-white shadow-sm transition"
                                role="tab"
                                aria-selected="true">
                            Lost Reports ({{ $recentLost->count() }})
                        </button>
                        <button type="button"
                                data-tab-target="tab-found"
                                class="dashboard-tab rounded-lg px-3 py-1.5 text-xs font-medium text-slate-600 hover:text-slate-900 transition"
                                role="tab"
                                aria-selected="false">
                            Found Reports ({{ $recentFound->count() }})
                        </button>
                        <button type="button"
                                data-tab-target="tab-claims"
                                class="dashboard-tab rounded-lg px-3 py-1.5 text-xs font-medium text-slate-600 hover:text-slate-900 transition"
                                role="tab"
                                aria-selected="false">
                            My Claims ({{ $recentClaims->count() }})
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
                        <a href="{{ route('lost-items.create') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-brand-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-brand-700">
                            + Report a Lost Item
                        </a>
                    </x-empty-state>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead class="border-b border-slate-100 text-xs font-semibold uppercase text-slate-400">
                                <tr>
                                    <th class="pb-3">Item Name</th>
                                    <th class="pb-3">Category</th>
                                    <th class="pb-3">Date Lost</th>
                                    <th class="pb-3">Last Seen Location</th>
                                    <th class="pb-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($recentLost as $item)
                                    <tr class="hover:bg-slate-50/75 transition-colors">
                                        <td class="py-3.5 font-medium text-slate-900">
                                            <a href="{{ route('lost-items.show', $item) }}" class="text-brand-600 hover:text-brand-800 hover:underline">
                                                {{ $item->item_name }}
                                            </a>
                                            @if ($item->needs_correction)
                                                <span class="ml-2 inline-flex items-center rounded-md bg-amber-100 px-1.5 py-0.5 text-[10px] font-semibold text-amber-800">
                                                    Action Required
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3.5">
                                            <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">
                                                {{ $item->category->name ?? 'General' }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 text-xs text-slate-500">
                                            {{ $item->date_lost->format('M d, Y') }}
                                        </td>
                                        <td class="py-3.5 text-xs text-slate-500">
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
                        <a href="{{ route('found-items.create') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-white px-4 py-2 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50">
                            + Report a Found Item
                        </a>
                    </x-empty-state>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead class="border-b border-slate-100 text-xs font-semibold uppercase text-slate-400">
                                <tr>
                                    <th class="pb-3">Item Name</th>
                                    <th class="pb-3">Category</th>
                                    <th class="pb-3">Date Found</th>
                                    <th class="pb-3">Found Location</th>
                                    <th class="pb-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($recentFound as $item)
                                    <tr class="hover:bg-slate-50/75 transition-colors">
                                        <td class="py-3.5 font-medium text-slate-900">
                                            <a href="{{ route('found-items.show', $item) }}" class="text-brand-600 hover:text-brand-800 hover:underline">
                                                {{ $item->item_name }}
                                            </a>
                                        </td>
                                        <td class="py-3.5">
                                            <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">
                                                {{ $item->category->name ?? 'General' }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 text-xs text-slate-500">
                                            {{ $item->date_found->format('M d, Y') }}
                                        </td>
                                        <td class="py-3.5 text-xs text-slate-500">
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

            {{-- Tab 3: My Claims --}}
            <div id="tab-claims" class="hidden p-6">
                @if ($recentClaims->isEmpty())
                    <x-empty-state
                        title="No claim requests submitted"
                        description="If you see an item listed that matches your lost belongings, click 'Claim Item' on its details page to submit proof of ownership."
                    >
                        <a href="{{ route('items.index') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-brand-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-brand-700">
                            Browse Listings
                        </a>
                    </x-empty-state>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead class="border-b border-slate-100 text-xs font-semibold uppercase text-slate-400">
                                <tr>
                                    <th class="pb-3">Claimed Item</th>
                                    <th class="pb-3">Category</th>
                                    <th class="pb-3">Date Submitted</th>
                                    <th class="pb-3">Status</th>
                                    <th class="pb-3">Instructions / Notice</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($recentClaims as $claim)
                                    <tr class="hover:bg-slate-50/75 transition-colors">
                                        <td class="py-3.5 font-medium text-slate-900">
                                            {{ $claim->foundItem->item_name ?? 'Item' }}
                                        </td>
                                        <td class="py-3.5">
                                            <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">
                                                {{ $claim->foundItem->category->name ?? 'General' }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 text-xs text-slate-500">
                                            {{ $claim->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="py-3.5">
                                            <x-status-badge :status="$claim->status" />
                                        </td>
                                        <td class="py-3.5 text-xs">
                                            @if ($claim->admin_message)
                                                <span class="text-brand-700 font-medium">{{ $claim->admin_message }}</span>
                                            @elseif ($claim->pickup_instructions)
                                                <span class="text-emerald-700 font-medium">{{ $claim->pickup_instructions }}</span>
                                            @else
                                                <span class="text-slate-400">Under administrator review</span>
                                            @endif
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
        <div class="rounded-2xl bg-slate-900 p-6 text-white sm:p-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl space-y-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-500/20 px-3 py-1 text-xs font-medium text-brand-300 ring-1 ring-inset ring-brand-500/30">
                        Campus Guidelines
                    </span>
                    <h3 class="text-lg font-semibold tracking-tight text-white sm:text-xl">
                        How Verification & Retrieval Works
                    </h3>
                    <p class="text-xs leading-relaxed text-slate-300 sm:text-sm">
                        To maintain fair and secure item recovery, sensitive identifying characteristics of found items are kept private. When claiming an item, provide specific identifying marks, serial numbers, or unique details that only the true owner would know.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('items.index') }}"
                       class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2.5 text-xs font-semibold text-slate-900 shadow-sm transition hover:bg-slate-100">
                        Search Found Belongings
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
