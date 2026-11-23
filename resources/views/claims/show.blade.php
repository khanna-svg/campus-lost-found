<x-layouts.app :title="'Claim #' . substr($claim->id, 0, 8) . ' · ' . $claim->foundItem->item_name">
    <div class="mx-auto max-w-4xl space-y-8">
        {{-- Breadcrumb & Title --}}
        <div>
            <nav class="flex items-center gap-2 text-xs font-medium text-zinc-500 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-black">Dashboard</a>
                <span>/</span>
                <a href="{{ route('claims.index') }}" class="hover:text-black">My Claims</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Claim #{{ substr($claim->id, 0, 8) }}</span>
            </nav>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 sm:text-3xl">
                        Claim Verification Details
                    </h1>
                    <p class="mt-1 text-xs text-zinc-500">
                        Claim ID: <span class="font-mono text-zinc-800">{{ $claim->id }}</span> · Submitted on {{ $claim->created_at->format('M d, Y h:i A') }}
                    </p>
                </div>
                <x-status-badge :status="$claim->status" />
            </div>
        </div>

        {{-- Status Alert Banners --}}
        @if ($claim->status->value === 'pending')
            <div class="rounded-2xl border border-zinc-300 bg-zinc-100 p-5">
                <div class="flex gap-3">
                    <svg class="h-5 w-5 text-black shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-black">Pending Verification</h2>
                        <p class="mt-1 text-sm text-zinc-800">
                            @if ($claim->foundItem->isKeptByFinder())
                                @if (!empty($isFinder) && $isFinder)
                                    A student has submitted an ownership claim for this item in your possession. Review their proof below to approve or ask for more details.
                                @else
                                    Your claim was submitted directly to the finder (<strong>{{ $claim->foundItem->user->name }}</strong>). The finder is currently reviewing your proof.
                                @endif
                            @else
                                Your claim has been securely received. An authorized campus administrator will review your proof against physical records shortly.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @elseif ($claim->status->value === 'under_review')
            <div class="rounded-2xl border border-zinc-400 bg-zinc-100 p-5">
                <div class="flex gap-3">
                    <svg class="h-5 w-5 text-black shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-black">Under Active Review</h2>
                        <p class="mt-1 text-sm text-zinc-800">
                            @if ($claim->foundItem->isKeptByFinder())
                                @if (!empty($isFinder) && $isFinder)
                                    You are currently examining this claim. You can approve it, request further clarification, or reject it below.
                                @else
                                    The finder is currently reviewing your submitted evidence and property characteristics.
                                @endif
                            @else
                                A security administrator is currently reviewing your submitted evidence and verifying property characteristics.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @elseif ($claim->status->value === 'additional_information_required')
            <div class="rounded-2xl border border-zinc-900 bg-zinc-100 p-6 space-y-4">
                <div class="flex gap-3">
                    <svg class="h-6 w-6 text-black shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <div>
                        <h2 class="text-sm font-bold text-black">
                            {{ $claim->foundItem->isKeptByFinder() ? 'Finder Request for Information' : 'Administrator Request for Information' }}
                        </h2>
                        <p class="mt-1 text-sm text-zinc-900 leading-relaxed font-medium">
                            "{{ $claim->admin_message }}"
                        </p>
                    </div>
                </div>

                {{-- Claimant Response Form (Visible only to Claimant) --}}
                @if (!empty($isClaimant) && $isClaimant)
                    <form method="POST" action="{{ route('claims.respond', $claim) }}" class="pt-3 border-t border-zinc-300 space-y-3">
                        @csrf
                        <label for="claimant_response" class="block text-xs font-bold uppercase tracking-wider text-black">
                            Your Clarification / Response <span class="text-black font-bold">*</span>
                        </label>
                        <textarea name="claimant_response"
                                  id="claimant_response"
                                  rows="3"
                                  required
                                  placeholder="Provide the specific details requested above..."
                                  class="w-full rounded-xl border-zinc-300 p-3 text-xs text-zinc-900 ring-1 ring-inset ring-zinc-300 focus:ring-2 focus:ring-black">{{ old('claimant_response') }}</textarea>
                        @error('claimant_response')
                            <p class="text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                        <div class="flex justify-end">
                            <button type="submit" class="rounded-xl bg-black px-4 py-2 text-xs font-semibold text-white hover:bg-zinc-800 transition">
                                Submit Response
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        @elseif ($claim->status->value === 'approved')
            <div class="rounded-2xl border border-zinc-800 bg-black text-white p-6 space-y-4 shadow-sm">
                <div class="flex gap-3">
                    <svg class="h-6 w-6 text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <div>
                        <h2 class="text-base font-bold text-white">Claim Approved — Item Ready for Hand-Over!</h2>
                        <p class="mt-1 text-xs text-zinc-400 leading-relaxed">
                            @if ($claim->foundItem->isKeptByFinder())
                                Verified by finder <strong>{{ $claim->approver?->name ?? $claim->foundItem->user->name }}</strong> on {{ $claim->approved_at?->format('F d, Y h:i A') }}.
                            @else
                                Your ownership was verified by {{ $claim->approver?->name ?? 'Campus Administration' }} on {{ $claim->approved_at?->format('F d, Y h:i A') }}.
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Retrieval / Meetup Instructions Card --}}
                <div class="rounded-xl bg-zinc-900 p-4 border border-zinc-800 text-xs text-zinc-300 space-y-2">
                    <h3 class="font-bold text-white uppercase tracking-wider text-[11px]">
                        {{ $claim->foundItem->isKeptByFinder() ? 'Meetup & Handover Details' : 'How to Retrieve Your Item' }}
                    </h3>
                    <div class="grid sm:grid-cols-2 gap-3 pt-1">
                        @if ($claim->foundItem->isKeptByFinder())
                            <div>
                                <span class="text-zinc-500 block font-medium">Finder</span>
                                <span class="font-semibold text-white">{{ $claim->foundItem->user->name }}</span>
                            </div>
                            <div>
                                <span class="text-zinc-500 block font-medium">Finder Contact</span>
                                <span class="font-semibold text-white">{{ $claim->foundItem->contact_info ?: ($claim->foundItem->user->phone ?: $claim->foundItem->user->email) }}</span>
                            </div>
                            @if ($claim->pickup_instructions)
                                <div class="sm:col-span-2 border-t border-zinc-800 pt-2">
                                    <span class="text-zinc-500 block font-medium">Meetup Instructions from Finder</span>
                                    <p class="text-zinc-200 font-medium whitespace-pre-line">{{ $claim->pickup_instructions }}</p>
                                </div>
                            @endif
                        @else
                            <div>
                                <span class="text-zinc-500 block font-medium">Location</span>
                                <span class="font-semibold text-white">Admin Office</span>
                            </div>
                            <div>
                                <span class="text-zinc-500 block font-medium">Office Hours</span>
                                <span class="font-semibold text-white">Mon – Fri, 8:00 AM – 5:00 PM</span>
                            </div>
                            <div class="sm:col-span-2">
                                <span class="text-zinc-500 block font-medium">What to Bring</span>
                                <span class="font-semibold text-white">Valid Institutional ID Card or Government-Issued ID</span>
                            </div>
                            @if ($claim->pickup_instructions)
                                <div class="sm:col-span-2 border-t border-zinc-800 pt-2">
                                    <span class="text-zinc-500 block font-medium">Admin Remarks</span>
                                    <p class="text-zinc-200 font-medium whitespace-pre-line">{{ $claim->pickup_instructions }}</p>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @elseif ($claim->status->value === 'rejected')
            <div class="rounded-2xl border border-zinc-300 bg-zinc-100 p-5">
                <div class="flex gap-3">
                    <svg class="h-5 w-5 text-black shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-black">Claim Rejected</h2>
                        <p class="mt-1 text-sm text-zinc-800">
                            Reason: {{ $claim->rejection_reason }}
                        </p>
                    </div>
                </div>
            </div>
        @elseif ($claim->status->value === 'completed')
            <div class="rounded-2xl bg-black text-white p-6 shadow-md space-y-3 border border-zinc-800">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-zinc-800 text-white">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                    </span>
                    <div>
                        <h2 class="text-base font-bold text-white">Item Successfully Returned</h2>
                        <p class="text-xs text-zinc-400">
                            @if ($claim->foundItem->isKeptByFinder())
                                This item has been safely returned to its rightful owner.
                            @else
                                This property was physically released and signed for at campus security.
                            @endif
                        </p>
                    </div>
                </div>
                @if ($claim->itemReturn)
                    <div class="grid sm:grid-cols-4 gap-2 pt-2 border-t border-zinc-800 text-xs">
                        <div>
                            <span class="text-zinc-400 block text-[11px]">Received By</span>
                            <span class="font-semibold text-white">{{ $claim->itemReturn->returned_to_name }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-400 block text-[11px]">LRN</span>
                            <span class="font-semibold text-white">{{ $claim->itemReturn->lrn ?? ($claim->itemReturn->id_presented ?? 'N/A') }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-400 block text-[11px]">Course &amp; Year</span>
                            <span class="font-semibold text-white">{{ $claim->itemReturn->course_year ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-400 block text-[11px]">Date &amp; Time</span>
                            <span class="font-semibold text-white">{{ $claim->itemReturn->returned_at->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Found Item Reference Card --}}
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-zinc-200 space-y-4">
            <h2 class="text-base font-bold text-zinc-900 border-b border-zinc-100 pb-3">Item Details</h2>
            <div class="flex items-center gap-4">
                @if ($claim->foundItem->photo_url)
                    <img src="{{ $claim->foundItem->photo_url }}" alt="{{ $claim->foundItem->item_name }}"
                         onerror="this.onerror=null; this.remove();"
                         class="h-24 w-24 rounded-2xl object-cover shrink-0">
                @endif
                <div class="space-y-1">
                    <span class="inline-block rounded-md bg-zinc-100 px-2 py-0.5 text-[10px] font-bold text-zinc-800">
                        {{ $claim->foundItem->category->name }}
                    </span>
                    <h3 class="text-lg font-bold text-zinc-900">{{ $claim->foundItem->item_name }}</h3>
                    <p class="text-xs text-zinc-500">
                        Found at <strong>{{ $claim->foundItem->location_found }}</strong> on {{ $claim->foundItem->date_found->format('F d, Y') }}
                    </p>
                    <a href="{{ route('found-items.show', $claim->foundItem) }}" class="inline-block text-xs font-semibold text-black hover:underline pt-1">
                        View Public Found Item Report →
                    </a>
                </div>
            </div>
        </div>

        {{-- Submitted Proof Card --}}
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-zinc-200 space-y-5">
            <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                <h2 class="text-base font-bold text-zinc-900">
                    {{ !empty($isFinder) && $isFinder ? "Claimant's Proof of Ownership" : "Your Submitted Proof of Ownership" }}
                </h2>
                <span class="rounded bg-black px-2.5 py-0.5 text-[11px] font-semibold text-white">
                    Confidential
                </span>
            </div>

            @if (!empty($isFinder) && $isFinder)
                <div class="rounded-2xl border border-zinc-100 bg-zinc-50 p-3.5 text-xs text-zinc-700 flex items-center justify-between gap-3">
                    <div>
                        <span class="text-zinc-400 block text-[10px] font-bold uppercase">Claimant</span>
                        <span class="font-bold text-zinc-900">{{ $claim->claimant->name }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-400 block text-[10px] font-bold uppercase">Contact Email</span>
                        <span class="font-medium text-zinc-800">{{ $claim->claimant->email }}</span>
                    </div>
                    @if ($claim->claimant->phone)
                        <div>
                            <span class="text-zinc-400 block text-[10px] font-bold uppercase">Phone</span>
                            <span class="font-medium text-zinc-800">{{ $claim->claimant->phone }}</span>
                        </div>
                    @endif
                </div>
            @endif

            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Description of Distinguishing Characteristics</span>
                <p class="mt-2 text-sm leading-relaxed text-zinc-900 whitespace-pre-line bg-zinc-50 p-4 rounded-2xl border border-zinc-200">
                    {{ $claim->proof_description }}
                </p>
            </div>

            @if ($claim->claimant_response)
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Claimant's Additional Clarification</span>
                    <p class="mt-2 text-sm leading-relaxed text-zinc-900 whitespace-pre-line bg-zinc-100 p-4 rounded-2xl border border-zinc-300">
                        {{ $claim->claimant_response }}
                    </p>
                </div>
            @endif

            {{-- Evidence Files --}}
            @php $evidenceUrls = $claim->evidence_urls; @endphp
            @if (! empty($evidenceUrls))
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Attached Proof Files ({{ count($evidenceUrls) }})</span>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-2">
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
            @endif
        </div>

        {{-- Finder Verification & Handover Action Panel --}}
        @if (!empty($isFinder) && $isFinder && $claim->foundItem->isKeptByFinder())
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-zinc-200 space-y-5">
                <div class="border-b border-zinc-100 pb-3">
                    <h2 class="text-base font-bold text-zinc-900">Finder Verification Decisions</h2>
                    <p class="text-xs text-zinc-500">Since you are keeping this item, you verify the proof and coordinate the physical handover directly with the owner.</p>
                </div>

                {{-- State 1: Pending or Under Review or Info Required --}}
                @if (in_array($claim->status->value, ['pending', 'under_review', 'additional_information_required']))
                    {{-- Form A: Approve Claim & Set Meetup Instructions --}}
                    <form method="POST" action="{{ route('claims.finder-approve', $claim) }}" class="rounded-2xl bg-zinc-50 p-4 border border-zinc-200 space-y-3">
                        @csrf
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-900">Approve Claim &amp; Arrange Meetup</h4>
                            <p class="text-xs text-zinc-500">Provide meetup details on campus (where &amp; when to meet) and your preferred contact method.</p>
                        </div>
                        <div>
                            <label for="pickup_instructions" class="block text-xs font-bold text-zinc-800 mb-1">Meetup &amp; Handover Instructions <span class="text-black font-bold">*</span></label>
                            <textarea name="pickup_instructions"
                                      id="pickup_instructions"
                                      rows="2"
                                      required
                                      placeholder="e.g. Meet me at Library 2nd floor lobby at 3:00 PM. Call or text me at {{ auth()->user()->phone ?: 'my email' }}."
                                      class="w-full rounded-xl border border-zinc-200 p-2.5 text-xs text-zinc-900 focus:border-black focus:outline-none focus:ring-1 focus:ring-black">{{ old('pickup_instructions') }}</textarea>
                            @error('pickup_instructions')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit"
                                onclick="return confirm('Approve this claim? The claimant will be notified with your meetup instructions.');"
                                class="w-full rounded-xl bg-black px-4 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-zinc-800 transition">
                            Approve Claim &amp; Notify Owner
                        </button>
                    </form>

                    {{-- Form B: Request Additional Clarification --}}
                    <form method="POST" action="{{ route('claims.finder-request-info', $claim) }}" class="rounded-2xl bg-zinc-50 p-4 border border-zinc-200 space-y-2">
                        @csrf
                        <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-900">Need More Verification Details?</h4>
                        <input type="text"
                               name="message"
                               placeholder="e.g. What color is the keychain? What stickers are on it?"
                               required
                               class="w-full rounded-xl border border-zinc-200 px-3 py-2 text-xs text-zinc-900 focus:border-black focus:outline-none">
                        <button type="submit" class="rounded-xl bg-zinc-800 px-3.5 py-1.5 text-xs font-bold text-white hover:bg-black transition">
                            Send Question to Claimant
                        </button>
                    </form>

                    {{-- Form C: Reject Claim --}}
                    <details class="text-xs pt-1">
                        <summary class="cursor-pointer font-semibold text-rose-600 hover:text-rose-800">
                            Reject this claim?
                        </summary>
                        <form method="POST" action="{{ route('claims.finder-reject', $claim) }}" class="mt-2.5 space-y-2">
                            @csrf
                            <textarea name="rejection_reason" rows="2" placeholder="State reason why proof does not match..." class="w-full rounded-xl border border-zinc-200 p-2 text-xs text-zinc-900 focus:border-black focus:outline-none" required></textarea>
                            <button type="submit" onclick="return confirm('Reject this claim?');" class="rounded-xl bg-rose-600 px-3.5 py-1.5 text-xs font-bold text-white hover:bg-rose-500 transition">
                                Confirm Rejection
                            </button>
                        </form>
                    </details>
                @endif

                {{-- State 2: Approved -> Record Physical Handover --}}
                @if ($claim->status->value === 'approved')
                    <form method="POST" action="{{ route('claims.finder-return', $claim) }}" class="rounded-2xl bg-black text-white p-5 space-y-3">
                        @csrf
                        <div>
                            <h4 class="text-sm font-bold text-white">Record Physical Return (Handover)</h4>
                            <p class="text-xs text-zinc-400">Complete this when you meet with the student to hand over the item.</p>
                        </div>
                        <div class="space-y-2.5 text-xs">
                            <div>
                                <label for="returned_to_name" class="block text-[10px] uppercase font-bold text-zinc-400 mb-1">Student's Name</label>
                                <input type="text"
                                       name="returned_to_name"
                                       id="returned_to_name"
                                       value="{{ old('returned_to_name', $claim->claimant->name) }}"
                                       placeholder="Student's Name"
                                       class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-3 py-2 text-xs text-white focus:outline-none focus:ring-1 focus:ring-white"
                                       required>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label for="lrn" class="block text-[10px] uppercase font-bold text-zinc-400 mb-1">LRN / School ID</label>
                                    <input type="text"
                                           name="lrn"
                                           id="lrn"
                                           value="{{ old('lrn', $claim->claimant->school_id) }}"
                                           placeholder="LRN / Student ID"
                                           class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-3 py-2 text-xs text-white focus:outline-none focus:ring-1 focus:ring-white"
                                           required>
                                </div>
                                <div>
                                    <label for="course_year" class="block text-[10px] uppercase font-bold text-zinc-400 mb-1">Course &amp; Year</label>
                                    <input type="text"
                                           name="course_year"
                                           id="course_year"
                                           value="{{ old('course_year') }}"
                                           placeholder="e.g. BSIT 3A"
                                           class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-3 py-2 text-xs text-white focus:outline-none focus:ring-1 focus:ring-white"
                                           required>
                                </div>
                            </div>
                        </div>
                        <button type="submit"
                                onclick="return confirm('Confirm item handover? This will mark the item as resolved and returned to its owner.');"
                                class="w-full rounded-xl bg-white px-4 py-2.5 text-xs font-bold text-black hover:bg-zinc-100 transition">
                            Hand over &amp; Complete Return
                        </button>
                    </form>
                @endif

                {{-- State 3: Completed --}}
                @if ($claim->status->value === 'completed')
                    <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 space-y-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-800 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Item Returned to Owner
                        </span>
                        @if ($claim->itemReturn)
                            <div class="grid grid-cols-2 gap-2 text-xs text-zinc-700 pt-1">
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-zinc-400 block">Received By</span>
                                    <span class="font-semibold text-zinc-900">{{ $claim->itemReturn->returned_to_name }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-zinc-400 block">LRN</span>
                                    <span class="font-semibold text-zinc-900">{{ $claim->itemReturn->lrn ?? ($claim->itemReturn->id_presented ?? 'N/A') }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-zinc-400 block">Course &amp; Year</span>
                                    <span class="font-semibold text-zinc-900">{{ $claim->itemReturn->course_year ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-zinc-400 block">Handover Date</span>
                                    <span class="font-semibold text-zinc-900">{{ $claim->itemReturn->returned_at->format('M d, Y h:i A') }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- State 4: Rejected --}}
                @if ($claim->status->value === 'rejected')
                    <div class="rounded-2xl bg-rose-50 border border-rose-200 p-4 space-y-1.5 text-xs">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-rose-700 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Claim Rejected
                        </span>
                        @if ($claim->rejection_reason)
                            <p class="text-zinc-800"><strong>Reason:</strong> {{ $claim->rejection_reason }}</p>
                        @endif
                    </div>
                @endif
            </div>
        @endif

        {{-- Back Navigation --}}
        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('claims.index') }}" class="rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-zinc-700 shadow-sm ring-1 ring-inset ring-zinc-300 hover:bg-zinc-50 transition">
                ← Back to My Claims
            </a>
            <a href="{{ route('dashboard') }}" class="text-xs text-zinc-500 hover:text-black">
                Dashboard
            </a>
        </div>
    </div>
</x-layouts.app>
