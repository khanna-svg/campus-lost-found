<x-layouts.app :title="'Claim #' . substr($claim->id, 0, 8) . ' · ' . $claim->foundItem->item_name">
    <div class="mx-auto max-w-4xl space-y-8">
        {{-- Breadcrumb & Title --}}
        <div>
            <nav class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700">Dashboard</a>
                <span>/</span>
                <a href="{{ route('claims.index') }}" class="hover:text-slate-700">My Claims</a>
                <span>/</span>
                <span class="text-slate-900">Claim #{{ substr($claim->id, 0, 8) }}</span>
            </nav>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                        Claim Verification Details
                    </h1>
                    <p class="mt-1 text-xs text-slate-500">
                        Claim ID: <span class="font-mono">{{ $claim->id }}</span> · Submitted on {{ $claim->created_at->format('M d, Y h:i A') }}
                    </p>
                </div>
                <x-status-badge :status="$claim->status" />
            </div>
        </div>

        {{-- Status Alert Banners --}}
        @if ($claim->status->value === 'pending')
            <div class="rounded-2xl bg-amber-50 p-5 ring-1 ring-amber-200">
                <div class="flex gap-3">
                    <svg class="h-5 w-5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-amber-900">Pending Review Queue</h2>
                        <p class="mt-1 text-sm text-amber-800">
                            Your claim has been securely received. An authorized campus administrator will review your proof against physical records shortly.
                        </p>
                    </div>
                </div>
            </div>
        @elseif ($claim->status->value === 'under_review')
            <div class="rounded-2xl bg-blue-50 p-5 ring-1 ring-blue-200">
                <div class="flex gap-3">
                    <svg class="h-5 w-5 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-blue-900">Under Active Examination</h2>
                        <p class="mt-1 text-sm text-blue-800">
                            A security administrator is currently reviewing your submitted evidence and verifying property characteristics.
                        </p>
                    </div>
                </div>
            </div>
        @elseif ($claim->status->value === 'additional_information_required')
            <div class="rounded-2xl bg-amber-50 p-6 ring-1 ring-amber-300 space-y-4">
                <div class="flex gap-3">
                    <svg class="h-6 w-6 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <div>
                        <h2 class="text-sm font-bold text-amber-950">Administrator Request for Information</h2>
                        <p class="mt-1 text-sm text-amber-900 leading-relaxed font-medium">
                            "{{ $claim->admin_message }}"
                        </p>
                    </div>
                </div>

                {{-- Claimant Response Form --}}
                <form method="POST" action="{{ route('claims.respond', $claim) }}" class="pt-2 border-t border-amber-200 space-y-3">
                    @csrf
                    <label for="claimant_response" class="block text-xs font-bold uppercase tracking-wider text-amber-900">
                        Your Clarification / Response <span class="text-red-500">*</span>
                    </label>
                    <textarea name="claimant_response"
                              id="claimant_response"
                              rows="3"
                              required
                              placeholder="Provide the specific details requested above..."
                              class="w-full rounded-xl border-amber-300 p-3 text-xs text-slate-900 ring-1 ring-inset ring-amber-300 focus:ring-2 focus:ring-brand-600">{{ old('claimant_response') }}</textarea>
                    @error('claimant_response')
                        <p class="text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                    <div class="flex justify-end">
                        <button type="submit" class="rounded-xl bg-amber-700 px-4 py-2 text-xs font-semibold text-white hover:bg-amber-800 transition">
                            Submit Response
                        </button>
                    </div>
                </form>
            </div>
        @elseif ($claim->status->value === 'approved')
            <div class="rounded-2xl bg-emerald-50 p-6 ring-1 ring-emerald-300 space-y-4">
                <div class="flex gap-3">
                    <svg class="h-6 w-6 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <div>
                        <h2 class="text-base font-bold text-emerald-950">Claim Approved — Item Ready for Hand-Over!</h2>
                        <p class="mt-1 text-xs text-emerald-800 leading-relaxed">
                            Your ownership was verified by {{ $claim->approver?->name ?? 'Campus Security' }} on {{ $claim->approved_at?->format('F d, Y h:i A') }}.
                        </p>
                    </div>
                </div>

                {{-- Pickup Instructions Card --}}
                <div class="rounded-xl bg-white p-4 ring-1 ring-emerald-200 text-xs text-slate-700 space-y-2">
                    <h3 class="font-bold text-slate-900 uppercase tracking-wider text-[11px]">How to Retrieve Your Item</h3>
                    <div class="grid sm:grid-cols-2 gap-3 pt-1">
                        <div>
                            <span class="text-slate-400 block font-medium">Location</span>
                            <span class="font-semibold text-slate-900">Campus Security & Lost & Found Office</span>
                            <span class="text-slate-500 block">Administration Hall, Ground Floor Room 102</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Office Hours</span>
                            <span class="font-semibold text-slate-900">Mon – Fri, 8:00 AM – 5:00 PM</span>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-slate-400 block font-medium">What to Bring</span>
                            <span class="font-semibold text-slate-900">Valid Institutional ID Card or Government-Issued ID</span>
                        </div>
                        @if ($claim->pickup_instructions)
                            <div class="sm:col-span-2 border-t border-slate-100 pt-2">
                                <span class="text-slate-400 block font-medium">Admin Remarks</span>
                                <p class="text-slate-800 font-medium whitespace-pre-line">{{ $claim->pickup_instructions }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @elseif ($claim->status->value === 'rejected')
            <div class="rounded-2xl bg-rose-50 p-5 ring-1 ring-rose-200">
                <div class="flex gap-3">
                    <svg class="h-5 w-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-rose-900">Claim Rejected</h2>
                        <p class="mt-1 text-sm text-rose-800">
                            Reason: {{ $claim->rejection_reason }}
                        </p>
                    </div>
                </div>
            </div>
        @elseif ($claim->status->value === 'completed')
            <div class="rounded-2xl bg-slate-900 text-white p-6 shadow-md space-y-3">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                    </span>
                    <div>
                        <h2 class="text-base font-bold">Item Successfully Returned</h2>
                        <p class="text-xs text-slate-400">This property was physically released and signed for at campus security.</p>
                    </div>
                </div>
                @if ($claim->itemReturn)
                    <div class="grid sm:grid-cols-3 gap-2 pt-2 border-t border-slate-800 text-xs">
                        <div>
                            <span class="text-slate-400 block text-[11px]">Received By</span>
                            <span class="font-semibold">{{ $claim->itemReturn->returned_to_name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">ID Verified</span>
                            <span class="font-semibold">{{ $claim->itemReturn->id_presented ?? 'Institutional ID' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Date & Time</span>
                            <span class="font-semibold">{{ $claim->itemReturn->returned_at->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Found Item Reference Card --}}
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 space-y-4">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Item Claimed</h2>
            <div class="flex items-center gap-4">
                @if ($claim->foundItem->photo_url)
                    <img src="{{ $claim->foundItem->photo_url }}" alt="{{ $claim->foundItem->item_name }}" class="h-24 w-24 rounded-2xl object-cover shrink-0">
                @endif
                <div class="space-y-1">
                    <span class="inline-block rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">
                        {{ $claim->foundItem->category->name }}
                    </span>
                    <h3 class="text-lg font-bold text-slate-900">{{ $claim->foundItem->item_name }}</h3>
                    <p class="text-xs text-slate-500">
                        Found at <strong>{{ $claim->foundItem->location_found }}</strong> on {{ $claim->foundItem->date_found->format('F d, Y') }}
                    </p>
                    <a href="{{ route('found-items.show', $claim->foundItem) }}" class="inline-block text-xs font-semibold text-brand-600 hover:underline pt-1">
                        View Public Found Item Report →
                    </a>
                </div>
            </div>
        </div>

        {{-- Submitted Proof Card --}}
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900">Your Submitted Proof of Ownership</h2>
                <span class="rounded bg-slate-100 px-2.5 py-0.5 text-[11px] font-medium text-slate-600">
                    Confidential
                </span>
            </div>

            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Description of Distinguishing Characteristics</span>
                <p class="mt-2 text-sm leading-relaxed text-slate-800 whitespace-pre-line bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    {{ $claim->proof_description }}
                </p>
            </div>

            @if ($claim->claimant_response)
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Your Additional Clarification</span>
                    <p class="mt-2 text-sm leading-relaxed text-slate-800 whitespace-pre-line bg-amber-50/60 p-4 rounded-2xl border border-amber-100">
                        {{ $claim->claimant_response }}
                    </p>
                </div>
            @endif

            {{-- Evidence Files --}}
            @php $evidenceUrls = $claim->evidence_urls; @endphp
            @if (! empty($evidenceUrls))
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Attached Proof Files ({{ count($evidenceUrls) }})</span>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-2">
                        @foreach ($evidenceUrls as $idx => $url)
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="group relative rounded-xl border border-slate-200 overflow-hidden block aspect-square bg-slate-100 hover:ring-2 hover:ring-brand-500 transition">
                                <img src="{{ $url }}" alt="Proof file {{ $idx + 1 }}" class="h-full w-full object-cover">
                                <span class="absolute inset-x-0 bottom-0 bg-slate-900/70 py-1 text-center text-[10px] text-white backdrop-blur">
                                    Evidence #{{ $idx + 1 }} ↗
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Back Navigation --}}
        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('claims.index') }}" class="rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 transition">
                ← Back to My Claims
            </a>
            <a href="{{ route('dashboard') }}" class="text-xs text-slate-500 hover:text-slate-800">
                Dashboard
            </a>
        </div>
    </div>
</x-layouts.app>
