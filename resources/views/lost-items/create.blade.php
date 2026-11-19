<x-layouts.app title="Report Lost Item">
    <div class="mx-auto max-w-3xl space-y-6">
        <div>
            <nav class="flex items-center gap-2 text-xs font-medium text-zinc-500 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-black">Dashboard</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Report Lost Item</span>
            </nav>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 sm:text-3xl">Report a Lost Item</h1>
            <p class="mt-1 text-sm text-zinc-600">
                Provide accurate details about your misplaced belonging. Once approved, it will be published to the campus directory.
            </p>
        </div>

        <form method="POST" action="{{ route('lost-items.store') }}" enctype="multipart/form-data" class="space-y-6" novalidate>
            @csrf

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-zinc-200 sm:p-8 space-y-6">
                <h2 class="text-base font-semibold text-zinc-900 border-b border-zinc-100 pb-3">Item Details</h2>

                {{-- Item Name & Category --}}
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-form.input
                        name="item_name"
                        label="Item Name"
                        placeholder="e.g. Blue Hydro Flask, HP Spectre Laptop"
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

                {{-- Date & Time Lost --}}
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-form.input
                        name="date_lost"
                        label="Date Lost"
                        type="date"
                        max="{{ date('Y-m-d') }}"
                        required
                    />

                    <x-form.input
                        name="time_lost"
                        label="Approximate Time (Optional)"
                        type="time"
                        hint="Leave blank if unsure."
                    />
                </div>

                {{-- Location Last Seen --}}
                <x-form.input
                    name="location_last_seen"
                    label="Location Last Seen"
                    placeholder="e.g. Main Library 2nd Floor, Room 304, Gym Bleachers"
                    required
                />

                {{-- Detailed Public Description --}}
                <div>
                    <label for="description" class="mb-1.5 block text-sm font-medium text-zinc-700">
                        Detailed Description <span class="text-zinc-900 font-bold" aria-hidden="true">*</span>
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        required
                        placeholder="Describe the item's brand, color, model, stickers, condition, and any external marks..."
                        class="block w-full rounded-lg border-0 px-3 py-2.5 text-sm text-zinc-900 shadow-sm ring-1 ring-inset ring-zinc-300 placeholder:text-zinc-400 focus:ring-2 focus:ring-inset focus:ring-black"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Photograph & Private Verification Details --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-zinc-200 sm:p-8 space-y-6">
                <h2 class="text-base font-semibold text-zinc-900 border-b border-zinc-100 pb-3">Photograph & Private Details</h2>

                {{-- Photo Upload (Interactive Preview & Drag-Drop Uploader) --}}
                <x-form.image-upload
                    name="photo"
                    id="photo"
                    label="Item Photograph"
                    :required="false"
                    help="PNG, JPG, or WebP up to 5MB (Optional)"
                />

                {{-- Private Identifying Details --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="identifying_details" class="block text-sm font-medium text-zinc-700">
                            Private Identifying Details (Optional)
                        </label>
                        <span class="rounded bg-zinc-100 px-2 py-0.5 text-[11px] font-medium text-zinc-700 ring-1 ring-zinc-200">
                            Confidential · Only visible to you and admins
                        </span>
                    </div>
                    <textarea
                        id="identifying_details"
                        name="identifying_details"
                        rows="3"
                        placeholder="Provide details that only the true owner would know (e.g. serial numbers, specific items inside pockets, lock screen image, engraved initials)..."
                        class="block w-full rounded-lg border-0 px-3 py-2.5 text-sm text-zinc-900 shadow-sm ring-1 ring-inset ring-zinc-300 placeholder:text-zinc-400 focus:ring-2 focus:ring-inset focus:ring-black"
                    >{{ old('identifying_details') }}</textarea>
                    <p class="mt-1.5 text-xs text-zinc-500">
                        This information is never shared publicly and helps campus administrators verify found items matching your report.
                    </p>
                    @error('identifying_details')
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
                    Submit Lost Item Report
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
