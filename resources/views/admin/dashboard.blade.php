<x-layouts.admin title="Dashboard">
    <div class="space-y-8">
        {{-- Header & Direct Actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-zinc-900 sm:text-3xl">Administrative Dashboard</h1>
                <p class="mt-1 text-xs sm:text-sm text-zinc-500">
                    Executive overview of campus lost and found operations and student claim approvals.
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('items.index') }}"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 rounded-xl bg-white px-4 py-2.5 text-xs font-bold text-zinc-800 border border-zinc-200 shadow-xs hover:bg-zinc-50 transition">
                    <svg class="h-4 w-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    View Student Directory ↗
                </a>
            </div>
        </div>

        {{-- 4 Core Metric Cards (Requested by User) --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Card 1: Total Lost Items --}}
            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Total Lost Items</span>
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4">
                    <span class="text-3xl font-black text-zinc-900">{{ number_format($stats['total_lost']) }}</span>
                    <p class="mt-1 text-xs text-zinc-400">Total missing reports submitted</p>
                </div>
            </div>

            {{-- Card 2: Total Found Items --}}
            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Total Found Items</span>
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4">
                    <span class="text-3xl font-black text-zinc-900">{{ number_format($stats['total_found']) }}</span>
                    <p class="mt-1 text-xs text-zinc-400">Belongings logged by finders</p>
                </div>
            </div>

            {{-- Card 3: Pending Claims --}}
            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Pending Claims</span>
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4">
                    <span class="text-3xl font-black text-zinc-900">{{ number_format($stats['pending_claims']) }}</span>
                    <p class="mt-1 text-xs text-zinc-400">Claims awaiting verification</p>
                </div>
            </div>

            {{-- Card 4: Successfully Returned Items --}}
            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Returned Items</span>
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-zinc-100 text-zinc-800">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4">
                    <span class="text-3xl font-black text-zinc-900">{{ number_format($stats['returned_items']) }}</span>
                    <p class="mt-1 text-xs text-zinc-400">Safely reunited with owners</p>
                </div>
            </div>
        </div>

        {{-- Section 1: Pending Claims Awaiting Approval (With Email Notification Trigger) --}}
        <div class="rounded-3xl border border-zinc-200 bg-white p-6 sm:p-8 shadow-xs space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-zinc-100 pb-4">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-black tracking-tight text-zinc-900">Pending Claim Requests</h2>
                        @if ($pendingClaims->isNotEmpty())
                            <span class="rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-bold text-amber-700">
                                {{ $pendingClaims->count() }} action required
                            </span>
                        @endif
                    </div>
                    <p class="mt-1 text-xs text-zinc-500">
                        Review student ownership claims. Approving sends an automated email notification letting the student know their item is ready for pickup at the Admin Office.
                    </p>
                </div>
            </div>

            @if ($pendingClaims->isNotEmpty())
                <div class="space-y-4">
                    @foreach ($pendingClaims as $claim)
                        <div class="rounded-2xl border border-zinc-200 bg-zinc-50/50 p-5 space-y-4 hover:border-zinc-300 transition">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-200/60 pb-3">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Claimant</span>
                                    <h3 class="text-sm font-bold text-zinc-900">{{ $claim->claimant->name }}</h3>
                                    <p class="text-xs text-zinc-500">{{ $claim->claimant->email }} {{ $claim->claimant->phone ? '· ' . $claim->claimant->phone : '' }}</p>
                                </div>
                                <div class="text-left sm:text-right">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Date Submitted</span>
                                    <p class="text-xs font-semibold text-zinc-700">{{ $claim->created_at->format('M d, Y h:i A') }}</p>
                                    <div class="mt-1">
                                        <x-status-badge :status="$claim->status" />
                                    </div>
                                </div>
                            </div>

                            {{-- Item Info & Ownership Proof --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Left: Found Item Claimed --}}
                                <div class="space-y-1.5">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Claimed Belonging</span>
                                    <div class="flex items-center gap-3">
                                        @if ($claim->foundItem->photo_url)
                                            <img src="{{ $claim->foundItem->photo_url }}" alt="" class="h-12 w-12 rounded-xl object-cover border border-zinc-200 shrink-0">
                                        @else
                                            <div class="h-12 w-12 rounded-xl bg-zinc-200 flex items-center justify-center text-zinc-400 text-xs shrink-0 font-bold">
                                                ?
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-bold text-zinc-900">{{ $claim->foundItem->item_name }}</p>
                                            <p class="text-xs text-zinc-500">Found: {{ $claim->foundItem->location_found }} · {{ $claim->foundItem->date_found->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Right: Claimant Owner Description --}}
                                <div class="space-y-1">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Owner Description</span>
                                    <div class="rounded-xl bg-white p-3 border border-zinc-200 text-xs text-zinc-700 leading-relaxed">
                                        {{ $claim->proof_description ?: ($claim->claim_description ?: 'No statement provided.') }}
                                    </div>
                                </div>
                            </div>

                            {{-- Approval Actions with Office Pickup Message --}}
                            <div class="pt-2 border-t border-zinc-200/60 flex flex-wrap items-center justify-between gap-3">
                                <div class="text-xs text-zinc-500">
                                    Office: <strong>Admin Office</strong> (Mon-Fri, 8:00 AM – 5:00 PM)
                                </div>

                                <div class="flex items-center gap-2">
                                    {{-- Approve Claim Form (Sends Email) --}}
                                    <form method="POST" action="{{ route('admin.claims.approve', $claim) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="pickup_instructions" value="Your item is ready for pickup at the Admin Office. Please bring your School ID card.">
                                        <button type="submit"
                                                onclick="return confirm('Approve claim for {{ addslashes($claim->claimant->name) }}?');"
                                                class="inline-flex items-center gap-1.5 rounded-xl bg-black px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-zinc-800 transition">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                            </svg>
                                            Approve
                                        </button>
                                    </form>

                                    {{-- Reject Claim Button --}}
                                    <a href="{{ route('admin.claims.show', $claim) }}"
                                       class="rounded-xl border border-zinc-300 bg-white px-3 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 transition">
                                        Review Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border-2 border-dashed border-zinc-200 p-8 text-center text-zinc-400">
                    <svg class="mx-auto h-8 w-8 text-zinc-300 stroke-[1.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <p class="mt-2 text-xs font-semibold text-zinc-600">No pending claims awaiting review</p>
                    <p class="text-[11px] text-zinc-400">All submitted claims have been reviewed and verified.</p>
                </div>
            @endif
        </div>

        {{-- Section 2: Approved Claims Ready for Handover at Office --}}
        <div class="rounded-3xl border border-zinc-200 bg-white p-6 sm:p-8 shadow-xs space-y-5">
            <div class="flex items-center justify-between border-b border-zinc-100 pb-4">
                <div>
                    <h2 class="text-lg font-black tracking-tight text-zinc-900">Approved for Office Pickup</h2>
                    <p class="mt-1 text-xs text-zinc-500">
                        Students have been notified to retrieve these items at the Admin Office. Record return once ID is verified.
                    </p>
                </div>
            </div>

            @if ($approvedClaims->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="border-b border-zinc-200 text-[10px] font-bold uppercase tracking-wider text-zinc-400">
                            <tr>
                                <th class="pb-3">Item</th>
                                <th class="pb-3">Claimant</th>
                                <th class="pb-3">Approved Date</th>
                                <th class="pb-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100">
                            @foreach ($approvedClaims as $approved)
                                <tr class="hover:bg-zinc-50/50">
                                    <td class="py-3 font-bold text-zinc-900">
                                        {{ $approved->foundItem->item_name }}
                                    </td>
                                    <td class="py-3 text-zinc-700">
                                        <span class="font-semibold">{{ $approved->claimant->name }}</span>
                                        <span class="block text-[11px] text-zinc-400">{{ $approved->claimant->email }}</span>
                                    </td>
                                    <td class="py-3 text-zinc-500">
                                        {{ $approved->approved_at ? $approved->approved_at->format('M d, Y h:i A') : 'Recently' }}
                                    </td>
                                    <td class="py-3 text-right">
                                        <a href="{{ route('admin.claims.show', $approved) }}"
                                           class="inline-flex items-center gap-1.5 rounded-lg bg-black px-3 py-1.5 text-xs font-bold text-white hover:bg-zinc-800 transition">
                                            Hand over
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-xs text-zinc-400 italic py-2">No approved items currently waiting for pickup.</p>
            @endif
        </div>

        {{-- Section 3: Recent Reunited Items History --}}
        @if ($recentReturns->isNotEmpty())
            <div class="rounded-3xl border border-zinc-200 bg-white p-6 sm:p-8 shadow-xs space-y-4">
                <h2 class="text-lg font-black tracking-tight text-zinc-900">Successfully Returned Belongings</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="border-b border-zinc-200 text-[10px] font-bold uppercase tracking-wider text-zinc-400">
                            <tr>
                                <th class="pb-3">Item</th>
                                <th class="pb-3">Owner Returned To</th>
                                <th class="pb-3">Handed Over Date</th>
                                <th class="pb-3">Recorded By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100">
                            @foreach ($recentReturns as $ret)
                                <tr>
                                    <td class="py-3 font-bold text-zinc-900">{{ $ret->foundItem->item_name }}</td>
                                    <td class="py-3 text-zinc-700">{{ $ret->claimRequest->claimant->name ?? 'Verified Student' }}</td>
                                    <td class="py-3 text-zinc-500">{{ $ret->returned_at->format('M d, Y h:i A') }}</td>
                                    <td class="py-3 text-zinc-400">{{ $ret->recordedBy->name ?? 'Administrator' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-layouts.admin>
