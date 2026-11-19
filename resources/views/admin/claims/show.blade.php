<x-layouts.admin :title="'Verify Claim #' . substr($claim->id, 0, 8)">
    <div class="space-y-6">
        {{-- Header & Breadcrumbs --}}
        <div>
            <nav class="flex items-center gap-2 text-xs font-medium text-zinc-400 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-800">Dashboard</a>
                <span>/</span>
                <a href="{{ route('admin.claims.index') }}" class="hover:text-zinc-800">Claims</a>
                <span>/</span>
                <span class="text-zinc-900 font-bold">Claim Verification</span>
            </nav>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-zinc-900">Claim Verification Dossier</h1>
                    <p class="mt-1 text-xs text-zinc-500">
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
                <div class="rounded-3xl bg-white p-6 border border-zinc-200 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-zinc-400">Physical Item Details</h2>
                        <span class="rounded-md bg-zinc-100 px-2 py-0.5 text-[10px] font-bold text-zinc-700">Official Record</span>
                    </div>

                    <div class="flex items-center gap-4">
                        @if ($claim->foundItem->photo_url)
                            <img src="{{ $claim->foundItem->photo_url }}" alt="" class="h-20 w-20 rounded-2xl object-cover border border-zinc-200 shrink-0">
                        @else
                            <div class="h-20 w-20 rounded-2xl bg-zinc-100 flex items-center justify-center text-zinc-400 font-bold text-lg shrink-0">
                                ?
                            </div>
                        @endif
                        <div class="space-y-1">
                            <h3 class="text-base font-bold text-zinc-900">{{ $claim->foundItem->item_name }}</h3>
                            <p class="text-xs text-zinc-500">Found on {{ $claim->foundItem->date_found->format('M d, Y') }} at {{ $claim->foundItem->location_found }}</p>
                            @if ($claim->foundItem->storage_location)
                                <p class="text-xs font-semibold text-zinc-900">Storage: {{ $claim->foundItem->storage_location }}</p>
                            @endif
                        </div>
                    </div>

                    <div>
                        <span class="text-[11px] font-bold uppercase text-zinc-400 block mb-1">Public Description</span>
                        <p class="text-xs text-zinc-700 bg-zinc-50 p-3.5 rounded-2xl border border-zinc-100 whitespace-pre-line">{{ $claim->foundItem->public_description }}</p>
                    </div>

                    {{-- Finder's Confidential Identifying Details --}}
                    <div class="rounded-2xl bg-zinc-50 p-4 border border-zinc-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-700 block">Finder's Confidential Details</span>
                            <span class="rounded bg-zinc-200 px-2 py-0.5 text-[9px] font-bold text-zinc-800">Compare Against Proof</span>
                        </div>
                        <p class="text-xs text-zinc-800 font-medium whitespace-pre-line leading-relaxed">{{ $claim->foundItem->private_details }}</p>
                        @if ($claim->foundItem->remarks)
                            <p class="text-[11px] text-zinc-500 pt-2 border-t border-zinc-200">
                                <strong>Remarks:</strong> {{ $claim->foundItem->remarks }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Column 2: Claimant's Submitted Evidence & Review Actions --}}
            <div class="space-y-4">
                <div class="rounded-3xl bg-white p-6 border border-zinc-200 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-zinc-400">Claimant's Proof Statement</h2>
                        <span class="rounded-md bg-zinc-100 px-2 py-0.5 text-[10px] font-bold text-zinc-700">Claimed by {{ $claim->claimant->name }}</span>
                    </div>

                    <div class="rounded-2xl bg-zinc-50 p-4 border border-zinc-100">
                        <span class="text-[11px] font-bold uppercase text-zinc-400 block mb-1">Ownership Statement</span>
                        <p class="text-xs text-zinc-800 font-medium whitespace-pre-line leading-relaxed">{{ $claim->proof_description ?: ($claim->claim_description ?: 'No statement provided by claimant.') }}</p>
                    </div>

                    @php $evidenceUrls = $claim->evidence_urls; @endphp
                    @if (! empty($evidenceUrls))
                        <div>
                            <span class="text-[11px] font-bold uppercase text-zinc-400 block mb-2">Attached Proof Images ({{ count($evidenceUrls) }})</span>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach ($evidenceUrls as $idx => $url)
                                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="group relative rounded-xl border border-zinc-200 overflow-hidden block aspect-square bg-zinc-100 hover:ring-2 hover:ring-black transition">
                                        <img src="{{ $url }}" alt="Proof file {{ $idx + 1 }}" class="h-full w-full object-cover">
                                        <span class="absolute inset-x-0 bottom-0 bg-black/75 py-1 text-center text-[10px] text-white backdrop-blur">
                                            Evidence #{{ $idx + 1 }} ↗
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @elseif ($claim->proof_document_path)
                        <div>
                            <span class="text-[11px] font-bold uppercase text-zinc-400 block mb-2">Attached Proof Image</span>
                            <img src="{{ $claim->proof_document_url }}" alt="Verification Proof" class="max-h-64 rounded-2xl border border-zinc-200 object-contain">
                        </div>
                    @endif

                    @if ($claim->admin_message)
                        <div class="rounded-2xl border border-zinc-200 bg-zinc-100 p-3.5 text-xs text-zinc-800">
                            <strong>Note sent to claimant:</strong> {{ $claim->admin_message }}
                        </div>
                    @endif
                </div>

                {{-- Action Panel --}}
                <div class="rounded-3xl bg-white p-6 border border-zinc-200 shadow-xs space-y-4">
                    <h3 class="text-sm font-bold text-zinc-900 border-b border-zinc-100 pb-3">Verification Decisions</h3>

                    {{-- 1. Approve Claim Form --}}
                    @if (in_array($claim->status->value, ['pending', 'under_review', 'additional_information_required']))
                        <form method="POST" action="{{ route('admin.claims.approve', $claim) }}" class="rounded-2xl bg-zinc-50 p-4 border border-zinc-200 space-y-3">
                            @csrf
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-900">Approve Claim &amp; Send Email Notification</h4>
                                <p class="text-[11px] text-zinc-500">Instructs the student that their claim is verified and ready for pickup at Administration Hall, Room 102.</p>
                            </div>
                            <div>
                                <label for="pickup_instructions" class="block text-xs font-bold text-zinc-800 mb-1">Pickup Instructions</label>
                                <textarea name="pickup_instructions"
                                          id="pickup_instructions"
                                          rows="2"
                                          class="w-full rounded-xl border border-zinc-200 p-2.5 text-xs text-zinc-900 focus:border-black focus:outline-none focus:ring-1 focus:ring-black">Your item is ready for pickup at the Campus Lost & Found Office (Administration Hall, Room 102). Please bring your School ID card.</textarea>
                            </div>
                            <button type="submit"
                                    onclick="return confirm('Approve this claim? This will automatically email {{ addslashes($claim->claimant->name) }} with pickup instructions.');"
                                    class="w-full rounded-xl bg-black px-4 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-zinc-800 transition">
                                ✓ Approve &amp; Email Student
                            </button>
                        </form>
                    @endif

                    {{-- 2. Record Physical Hand-Over Form --}}
                    @if ($claim->status->value === 'approved')
                        <form method="POST" action="{{ route('admin.claims.record-return', $claim) }}" class="rounded-2xl bg-black text-white p-5 space-y-3">
                            @csrf
                            <div>
                                <h4 class="text-sm font-bold text-white">Record Physical Return (Handover)</h4>
                                <p class="text-xs text-zinc-400">Complete this when the student presents their School ID in Room 102 to retrieve the item.</p>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <label class="block text-[10px] uppercase font-bold text-zinc-400 mb-1">ID Type</label>
                                    <input type="text" name="identification_type" value="Student ID" class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-2.5 py-1.5 text-xs text-white" required>
                                </div>
                                <div>
                                    <label class="block text-[10px] uppercase font-bold text-zinc-400 mb-1">Reference No.</label>
                                    <input type="text" name="id_reference_number" value="Verified in Office" class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-2.5 py-1.5 text-xs text-white" required>
                                </div>
                            </div>
                            <button type="submit"
                                    onclick="return confirm('Confirm item handover?');"
                                    class="w-full rounded-xl bg-white px-4 py-2 text-xs font-bold text-black hover:bg-zinc-100 transition">
                                Hand Over &amp; Complete Return
                            </button>
                        </form>
                    @endif

                    {{-- 3. Reject Claim Form --}}
                    @if (! in_array($claim->status->value, ['rejected', 'completed']))
                        <details class="pt-2 text-xs">
                            <summary class="cursor-pointer font-semibold text-rose-600 hover:text-rose-800">
                                Need to reject this claim?
                            </summary>
                            <form method="POST" action="{{ route('admin.claims.reject', $claim) }}" class="mt-3 space-y-2">
                                @csrf
                                <textarea name="rejection_reason" rows="2" placeholder="State reason for rejecting proof..." class="w-full rounded-xl border border-zinc-200 p-2 text-xs text-zinc-900 focus:border-black focus:outline-none" required></textarea>
                                <button type="submit" class="rounded-xl bg-rose-600 px-3.5 py-1.5 text-xs font-bold text-white hover:bg-rose-500 transition">
                                    Confirm Rejection
                                </button>
                            </form>
                        </details>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
