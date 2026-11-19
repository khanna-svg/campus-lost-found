<x-layouts.app title="Report Found Item">
    <div class="mx-auto max-w-3xl space-y-6">
        <div>
            <nav class="flex items-center gap-2 text-xs font-medium text-zinc-500 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-black">Dashboard</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Report Found Item</span>
            </nav>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 sm:text-3xl">Report a Found Item</h1>
            <p class="mt-1 text-sm text-zinc-600">
                Help return a found item to its rightful owner. Please surrender the physical item to the Campus Lost & Found office.
            </p>
        </div>

        {{-- Crucial Security Notice Banner (Monochrome High-Contrast) --}}
        <div class="rounded-2xl border border-zinc-300 bg-zinc-100/75 p-5 text-zinc-900 shadow-sm">
            <div class="flex gap-3.5">
                <div class="shrink-0 text-zinc-900">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
                <div class="space-y-1">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-black">Fraud Prevention Requirement</h3>
                    <p class="text-xs leading-relaxed text-zinc-700">
                        To protect the rightful owner from fraudulent claims, <strong>do not put secret or unique details in the public description</strong>. Keep identifying features (such as contents of pockets, unique keychains, or serial numbers) strictly in the <strong>Private Identifying Details</strong> field below.
                    </p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('found-items.store') }}" enctype="multipart/form-data" class="space-y-6" novalidate>
            @csrf

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-zinc-200 sm:p-8 space-y-6">
                <h2 class="text-base font-semibold text-zinc-900 border-b border-zinc-100 pb-3">Public Item Information</h2>

                {{-- Item Name & Category --}}
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-form.input
                        name="item_name"
                        label="Item Name"
                        placeholder="e.g. Black Wallet, Scientific Calculator, Water Bottle"
                        required
                        autofocus
                    />

                    <div>
                        <label for="category_id" class="mb-1.5 block text-sm font-medium text-zinc-700">
                            Category <span class="text-zinc-900 font-bold" aria-hidden="true">*</span>
                        </label>
                        <select
                            id="category_id"
                            name="category_id"
                            required
                            class="block w-full rounded-lg border-0 px-3 py-2.5 text-sm text-zinc-900 shadow-sm ring-1 ring-inset ring-zinc-300 focus:ring-2 focus:ring-inset focus:ring-black"
                        >
                            <option value="">Select a category</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id') === $cat->id)>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Date Found & Location Found --}}
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-form.input
                        name="date_found"
                        label="Date Found"
                        type="date"
                        max="{{ date('Y-m-d') }}"
                        required
                    />

                    <x-form.input
                        name="location_found"
                        label="Location Where Found"
                        placeholder="e.g. Science Building Hallway, Table 4 Library"
                        required
                    />
                </div>

                {{-- General Public Description --}}
                <div>
                    <label for="public_description" class="mb-1.5 block text-sm font-medium text-zinc-700">
                        General Description (Public) <span class="text-zinc-900 font-bold" aria-hidden="true">*</span>
                    </label>
                    <textarea
                        id="public_description"
                        name="public_description"
                        rows="3"
                        required
                        placeholder="Provide only general visual characteristics visible from a distance (color, basic shape, brand if obvious)..."
                        class="block w-full rounded-lg border-0 px-3 py-2.5 text-sm text-zinc-900 shadow-sm ring-1 ring-inset ring-zinc-300 placeholder:text-zinc-400 focus:ring-2 focus:ring-inset focus:ring-black"
                    >{{ old('public_description') }}</textarea>
                    <p class="mt-1.5 text-xs text-zinc-500">
                        This text will be published publicly so claimants can identify their general item.
                    </p>
                    @error('public_description')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Item Photograph (Interactive Preview & Drag-Drop Uploader) --}}
                <x-form.image-upload
                    name="photo"
                    id="photo"
                    label="Item Photograph"
                    :required="true"
                    help="PNG, JPG, or WebP up to 5MB (Required)"
                />
            </div>

            {{-- Private Verification Section --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-zinc-200 sm:p-8 space-y-6">
                <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                    <h2 class="text-base font-semibold text-zinc-900">Verification & Confidential Details</h2>
                    <span class="rounded bg-black px-2.5 py-0.5 text-xs font-semibold text-white tracking-wide">
                        Strictly Confidential
                    </span>
                </div>

                {{-- Private Identifying Details --}}
                <div>
                    <label for="private_details" class="mb-1.5 block text-sm font-medium text-zinc-700">
                        Private Identifying Characteristics <span class="text-zinc-900 font-bold" aria-hidden="true">*</span>
                    </label>
                    <textarea
                        id="private_details"
                        name="private_details"
                        rows="3"
                        required
                        placeholder="State distinct characteristics only the legitimate owner can prove: e.g. names on cards inside, engraving, specific keychains, cash amount, sticker details, phone lock screen..."
                        class="block w-full rounded-lg border-0 px-3 py-2.5 text-sm text-zinc-900 shadow-sm ring-1 ring-inset ring-zinc-300 placeholder:text-zinc-400 focus:ring-2 focus:ring-inset focus:ring-black"
                    >{{ old('private_details') }}</textarea>
                    <p class="mt-1.5 text-xs text-zinc-500">
                        Administrators will compare this private information against proof submitted by claimants to ensure the item is released to the true owner.
                    </p>
                    @error('private_details')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Additional Remarks --}}
                <div>
                    <label for="remarks" class="mb-1.5 block text-sm font-medium text-zinc-700">
                        Additional Remarks (Optional)
                    </label>
                    <textarea
                        id="remarks"
                        name="remarks"
                        rows="2"
                        placeholder="e.g. Turned over to Security Guard John at Gate 2; finder contact preferences..."
                        class="block w-full rounded-lg border-0 px-3 py-2.5 text-sm text-zinc-900 shadow-sm ring-1 ring-inset ring-zinc-300 placeholder:text-zinc-400 focus:ring-2 focus:ring-inset focus:ring-black"
                    >{{ old('remarks') }}</textarea>
                    @error('remarks')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Submission Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('dashboard') }}" class="rounded-xl px-4 py-2.5 text-sm font-semibold text-zinc-700 hover:bg-zinc-100 transition">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-black px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2">
                    Submit Found Item Report
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
