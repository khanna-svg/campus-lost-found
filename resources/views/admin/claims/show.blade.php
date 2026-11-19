<x-layouts.admin :title="'Verify Claim #' . substr($claim->id, 0, 8)">
    <div class="space-y-6">
        {{-- Header & Breadcrumbs --}}
        <div>
            <nav class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-700">Admin</a>
                <span>/</span>
                <a href="{{ route('admin.claims.index') }}" class="hover:text-slate-700">Claims Verification</a>
                <span>/</span>
                <span class="text-slate-900">Claim #{{ substr($claim->id, 0, 8) }}</span>
            </nav>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">Claim Verification Dossier</h1>
                    <p class="mt-1 text-xs text-slate-500">
                        Claimant: <strong>{{ $claim->claimant->name }}</strong> ({{ $claim->claimant->email }}) · Submitted {{ $claim->created_at->format('M d, Y h:i A') }}
                    </p>
                </div>
                <x-status-badge :status="$claim->status" />
            </div>
        </div>

        {{-- Verification Side-by-Side Grid --}}
        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Column 1: Found Item Physical Truth (Recorded by Finder) --}}
            <div class="space-y-4">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Physical Item Details</h2>
                        <span class="rounded bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600">Property Truth</span>
                    </div>

                    <div class="flex items-center gap-4">
                        @if ($claim->foundItem->photo_url)
                            <img src="{{ $claim->foundItem->photo_url }}" alt="" class="h-20 w-20 rounded-xl object-cover shrink-0">
                        @endif
                        <div class="space-y-1">
                            <h3 class="text-base font-bold text-slate-900">{{ $claim->foundItem->item_name }}</h3>
                            <p class="text-xs text-slate-500">Found on {{ $claim->foundItem->date_found->format('M d, Y') }} at {{ $claim->foundItem->location_found }}</p>
                            @if ($claim->foundItem->storage_location)
                                <p class="text-xs font-semibold text-brand-700">Kept at: {{ $claim->foundItem->storage_location }}</p>
                            @endif
                        </div>
                    </div>

                    <div>
                        <span class="text-[11px] font-bold uppercase text-slate-400 block mb-1">Public Description</span>
                        <p class="text-xs text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100 whitespace-pre-line">{{ $claim->foundItem->public_description }}</p>
                    </div>

                    {{-- PRIVATE DETAILS TO COMPARE AGAINST --}}
                    <div class="rounded-xl bg-amber-50 p-4 ring-1 ring-amber-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase text-amber-900 block">Finder's Confidential Details</span>
                            <span class="rounded bg-amber-200/80 px-2 py-0.5 text-[9px] font-bold text-amber-900">Compare Against Proof</span>
                        </div>
                        <p class="text-xs text-amber-950 font-medium whitespace-pre-line leading-relaxed">{{ $claim->foundItem->private_details }}</p>
                        @if ($claim->foundItem->remarks)
                            <p class="text-[11px] text-amber-800 pt-2 border-t border-amber-200/60">
                                <strong>Remarks:</strong> {{ $claim->foundItem->remarks }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Column 2: Claimant's Submitted Evidence --}}
            <div class="space-y-4">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Claimant Proof of Ownership</h2>
                        <span class="rounded bg-brand-50 px-2 py-0.5 text-[10px] font-bold text-brand-700">Submitted Proof</span>
                    </div>

                    <div>
                        <span class="text-[11px] font-bold uppercase text-slate-400 block mb-1">Claimant Distinguishing Description</span>
                        <p class="text-xs text-slate-900 font-medium bg-slate-50 p-3.5 rounded-xl border border-slate-100 whitespace-pre-line leading-relaxed">
                            {{ $claim->proof_description }}
                        </p>
                    </div>

                    @if ($claim->claimant_response)
                        <div class="rounded-xl bg-blue-50 p-3.5 ring-1 ring-blue-200 space-y-1">
                            <span class="text-[11px] font-bold uppercase text-blue-900 block">Claimant Clarification Response</span>
                            <p class="text-xs text-blue-950 whitespace-pre-line">{{ $claim->claimant_response }}</p>
                        </div>
                    @endif

                    {{-- Evidence Files --}}
                    @php $evidenceUrls = $claim->evidence_urls; @endphp
                    @if (! empty($evidenceUrls))
                        <div>
                            <span class="text-[11px] font-bold uppercase text-slate-400 block mb-1">Submitted Evidence Files ({{ count($evidenceUrls) }})</span>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach ($evidenceUrls as $idx => $url)
                                    <a href="{{ $url }}" target="_blank" class="rounded-lg border border-slate-200 overflow-hidden block aspect-square bg-slate-50 hover:ring-2 hover:ring-brand-500 transition">
                                        <img src="{{ $url }}" alt="" class="h-full w-full object-cover">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Verification Actions & Hand-Over Panel --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 space-y-6">
            <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">Administrator Verification Actions</h2>

            <div class="grid gap-6 md:grid-cols-2">
                {{-- State Transition Forms --}}
                <div class="space-y-4">
                    {{-- 1. Under Review --}}
                    @if ($claim->status->value === 'pending')
                        <form method="POST" action="{{ route('admin.claims.under-review', $claim) }}">
                            @csrf
                            <button type="submit" class="w-full rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-blue-500 transition">
                                🔍 Mark as Under Review
                            </button>
                        </form>
                    @endif

                    {{-- 2. Request Info --}}
                    @if (in_array($claim->status->value, ['pending', 'under_review']))
                        <form method="POST" action="{{ route('admin.claims.request-info', $claim) }}" class="space-y-2 pt-2 border-t border-slate-100">
                            @csrf
                            <label for="admin_message" class="block text-xs font-semibold text-amber-900">
                                Request Clarification from Claimant
                            </label>
                            <textarea name="admin_message" id="admin_message" rows="2" placeholder="e.g. Please describe the lock screen image or specify what brand of charger was in the pocket..." class="w-full rounded-xl border-0 p-2.5 text-xs text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-brand-600" required></textarea>
                            <button type="submit" class="rounded-xl bg-amber-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-amber-500 transition">
                                Send Information Request
                            </button>
                        </form>
                    @endif

                    {{-- 3. Reject Claim --}}
                    @if (! in_array($claim->status->value, ['rejected', 'completed']))
                        <form method="POST" action="{{ route('admin.claims.reject', $claim) }}" class="space-y-2 pt-2 border-t border-slate-100">
                            @csrf
                            <label for="rejection_reason" class="block text-xs font-semibold text-rose-700">
                                Reject Claim
                            </label>
                            <textarea name="rejection_reason" id="rejection_reason" rows="2" placeholder="State reason for rejecting proof..." class="w-full rounded-xl border-0 p-2.5 text-xs text-slate-900 ring-1 ring-inset ring-rose-300 focus:ring-2 focus:ring-rose-600" required></textarea>
                            <button type="submit" class="rounded-xl bg-rose-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-rose-500 transition">
                                Reject Claim
                            </button>
                        </form>
                    @endif
                </div>

                {{-- Approval & Hand-Over Forms --}}
                <div class="space-y-4">
                    {{-- 4. Approve Claim --}}
                    @if (in_array($claim->status->value, ['pending', 'under_review', 'additional_information_required']))
                        <form method="POST" action="{{ route('admin.claims.approve', $claim) }}" class="rounded-xl bg-emerald-50/60 p-4 ring-1 ring-emerald-200 space-y-3">
                            @csrf
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-900">Approve Ownership Claim</h3>
                                <p class="text-[11px] text-emerald-700">Marks found item as Claimed and instructs claimant to collect.</p>
                            </div>
                            <div>
                                <label for="pickup_instructions" class="block text-xs font-semibold text-slate-700">Special Pickup Instructions</label>
                                <textarea name="pickup_instructions" id="pickup_instructions" rows="2" placeholder="e.g. Please visit Administration Hall Room 102 with your School ID..." class="mt-1 w-full rounded-xl border-0 p-2 text-xs text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-brand-600"></textarea>
                            </div>
                            <button type="submit" class="w-full rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-500 transition">
                                ✓ Approve Claim
                            </button>
                        </form>
                    @endif

                    {{-- 5. Record Physical Hand-Over --}}
                    @if ($claim->status->value === 'approved')
                        <form method="POST" action="{{ route('admin.claims.record-return', $claim) }}" class="rounded-xl bg-slate-900 text-white p-5 space-y-3">
                            @csrf
                            <div>
                                <h3 class="text-sm font-bold text-white">Record Physical Return (Hand-Over)</h3>
                                <p class="text-xs text-slate-400">Complete this when the student/faculty receives their item in person.</p>
                            </div>
                            <div>
                                <label for="returned_to_name" class="block text-xs font-medium text-slate-300">Recipient Name *</label>
                                <input type="text" name="returned_to_name" id="returned_to_name" value="{{ old('returned_to_name', $claim->claimant->name) }}" class="mt-1 w-full rounded-xl border-0 p-2 text-xs text-slate-900" required>
                            </div>
                            <div>
                                <label for="id_presented" class="block text-xs font-medium text-slate-300">ID Presented *</label>
                                <input type="text" name="id_presented" id="id_presented" placeholder="e.g. Student ID #2024-11892" class="mt-1 w-full rounded-xl border-0 p-2 text-xs text-slate-900" required>
                            </div>
                            <div>
                                <label for="notes" class="block text-xs font-medium text-slate-300">Admin Hand-Over Notes</label>
                                <input type="text" name="notes" id="notes" placeholder="e.g. Inspected and confirmed working order" class="mt-1 w-full rounded-xl border-0 p-2 text-xs text-slate-900">
                            </div>
                            <button type="submit" class="w-full rounded-xl bg-emerald-500 px-4 py-2.5 text-xs font-bold text-slate-950 hover:bg-emerald-400 transition">
                                Confirm Physical Hand-Over & Complete Claim
                            </button>
                        </form>
                    @endif

                    @if ($claim->status->value === 'completed')
                        <div class="rounded-xl bg-slate-100 p-4 text-xs text-slate-700 space-y-1">
                            <span class="font-bold text-slate-900 block">✓ Physical Return Completed</span>
                            <p>Handed over to {{ $claim->itemReturn?->returned_to_name }} on {{ $claim->itemReturn?->returned_at->format('M d, Y h:i A') }}.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
