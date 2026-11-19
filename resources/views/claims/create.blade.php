<x-layouts.app :title="'Claim ' . $foundItem->item_name">
    <div class="mx-auto max-w-3xl space-y-8">
        {{-- Breadcrumb --}}
        <div>
            <nav class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700">Dashboard</a>
                <span>/</span>
                <a href="{{ route('items.index') }}" class="hover:text-slate-700">Browse Items</a>
                <span>/</span>
                <a href="{{ route('found-items.show', $foundItem) }}" class="hover:text-slate-700">{{ $foundItem->item_name }}</a>
                <span>/</span>
                <span class="text-slate-900">Submit Ownership Claim</span>
            </nav>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Claim Belonging</h1>
            <p class="mt-1 text-sm text-slate-500">
                Provide proof of ownership so campus security can verify and reunite you with your item.
            </p>
        </div>

        {{-- Item Being Claimed Summary --}}
        <div class="flex items-center gap-4 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            @if ($foundItem->photo_url)
                <img src="{{ $foundItem->photo_url }}" alt="{{ $foundItem->item_name }}" class="h-20 w-20 rounded-xl object-cover shrink-0">
            @else
                <div class="flex h-20 w-20 items-center justify-center rounded-xl bg-slate-100 text-slate-400 shrink-0">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                </div>
            @endif
            <div class="space-y-1 min-w-0">
                <span class="inline-block rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-200">
                    {{ $foundItem->category->name }}
                </span>
                <h2 class="text-base font-bold text-slate-900 truncate">{{ $foundItem->item_name }}</h2>
                <p class="text-xs text-slate-500">
                    Found on {{ $foundItem->date_found->format('M d, Y') }} at {{ $foundItem->location_found }}
                </p>
            </div>
        </div>

        {{-- Verification Guidelines Callout --}}
        <div class="rounded-2xl bg-brand-50/70 p-5 ring-1 ring-brand-100">
            <div class="flex gap-3">
                <svg class="h-5 w-5 text-brand-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                </svg>
                <div class="space-y-1 text-xs leading-relaxed text-brand-900">
                    <strong class="font-semibold block text-brand-950">How Verification Works</strong>
                    To safeguard student belongings against false claims, campus administrators match your description against confidential private characteristics recorded by the finder. Describe unique aspects that only the true owner would know.
                </div>
            </div>
        </div>

        {{-- Claim Submission Form --}}
        <form method="POST" action="{{ route('claims.store') }}" enctype="multipart/form-data" class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8 space-y-6">
            @csrf
            <input type="hidden" name="found_item_id" value="{{ $foundItem->id }}">

            @if ($errors->has('found_item_id'))
                <div class="rounded-xl bg-red-50 p-4 text-xs font-medium text-red-700 ring-1 ring-red-200">
                    {{ $errors->first('found_item_id') }}
                </div>
            @endif

            {{-- Optional Link to Existing Lost Item Report --}}
            @if ($myLostItems->isNotEmpty())
                <div>
                    <label for="lost_item_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                        Link to your Lost Item Report <span class="text-slate-400 font-normal lowercase">(optional)</span>
                    </label>
                    <select name="lost_item_id" id="lost_item_id" class="mt-2 block w-full rounded-xl border-0 py-2.5 px-3 text-sm text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-brand-600">
                        <option value="">-- No linked lost report --</option>
                        @foreach ($myLostItems as $lost)
                            <option value="{{ $lost->id }}" @selected(old('lost_item_id') === $lost->id)>
                                {{ $lost->item_name }} (Reported {{ $lost->date_lost->format('M d, Y') }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-[11px] text-slate-500">
                        Linking your prior report helps administrators cross-reference your timeline and description.
                    </p>
                </div>
            @endif

            {{-- Proof Description --}}
            <div>
                <div class="flex items-center justify-between">
                    <label for="proof_description" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                        Detailed Proof of Ownership <span class="text-red-500">*</span>
                    </label>
                    <span class="text-[11px] text-slate-400">Min 20 characters</span>
                </div>
                <textarea name="proof_description"
                          id="proof_description"
                          rows="5"
                          required
                          placeholder="Provide verifiable ownership details: lock screen photo/wallpaper, serial number, internal contents of pockets/compartments, unique engravings, stickers, scratches, or PIN hints..."
                          class="mt-2 block w-full rounded-2xl border-0 p-3.5 text-sm text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-brand-600 @error('proof_description') ring-red-500 @enderror">{{ old('proof_description') }}</textarea>
                @error('proof_description')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-[11px] text-slate-500">
                    This confidential information is strictly restricted to reviewing administrators.
                </p>
            </div>

            {{-- Optional Supporting Evidence Upload (Max 3 files) --}}
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                    Supporting Documents or Photos <span class="text-slate-400 font-normal lowercase">(optional, up to 3 files)</span>
                </label>
                <input type="file"
                       name="evidence[]"
                       id="evidence"
                       multiple
                       accept="image/jpeg,image/png,image/webp,application/pdf"
                       class="mt-2 block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer">
                <p class="mt-1.5 text-[11px] text-slate-500">
                    Accepted: Original purchase receipt, warranty slip, photo of you with the item, or box barcode (JPG, PNG, WEBP, PDF up to 5MB each). Stored securely in private school archives.
                </p>
                @error('evidence')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
                @error('evidence.*')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('found-items.show', $foundItem) }}" class="rounded-xl bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 transition">
                    Cancel
                </a>
                <button type="submit" class="rounded-xl bg-brand-600 px-5 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-brand-500 transition">
                    Submit Claim Request
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
