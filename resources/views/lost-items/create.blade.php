<x-layouts.app title="Report Lost Item">
    <div class="mx-auto max-w-3xl space-y-6">
        <div>
            <nav class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700">Dashboard</a>
                <span>/</span>
                <span class="text-slate-900">Report Lost Item</span>
            </nav>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Report a Lost Item</h1>
            <p class="mt-1 text-sm text-slate-500">
                Provide accurate details about your misplaced belonging. Once approved, it will be published to the campus portal.
            </p>
        </div>

        <form method="POST" action="{{ route('lost-items.store') }}" enctype="multipart/form-data" class="space-y-6" novalidate>
            @csrf

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8 space-y-6">
                <h2 class="text-base font-semibold text-slate-900 border-b border-slate-100 pb-3">Item Details</h2>

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
                        <label for="category_id" class="mb-1.5 block text-sm font-medium text-slate-700">
                            Category <span class="text-red-600" aria-hidden="true">*</span>
                        </label>
                        <select
                            id="category_id"
                            name="category_id"
                            required
                            class="block w-full rounded-lg border-0 px-3 py-2.5 text-sm text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-500"
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
                    <label for="description" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Detailed Description <span class="text-red-600" aria-hidden="true">*</span>
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        required
                        placeholder="Describe the item's brand, color, model, stickers, condition, and any external marks..."
                        class="block w-full rounded-lg border-0 px-3 py-2.5 text-sm text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-brand-500"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Photograph & Private Verification Details --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8 space-y-6">
                <h2 class="text-base font-semibold text-slate-900 border-b border-slate-100 pb-3">Photograph & Private Details</h2>

                {{-- Photo Upload --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">
                        Item Photograph (Optional)
                    </label>
                    <div class="mt-1 flex justify-center rounded-2xl border-2 border-dashed border-slate-300 px-6 py-6 transition hover:border-slate-400">
                        <div class="text-center">
                            <svg class="mx-auto h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                            <div class="mt-3 flex text-sm text-slate-600 justify-center">
                                <label for="photo" class="relative cursor-pointer rounded-md font-semibold text-brand-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-brand-600 focus-within:ring-offset-2 hover:text-brand-500">
                                    <span>Upload a photo</span>
                                    <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" class="sr-only">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">PNG, JPG, or WebP up to 5MB</p>
                        </div>
                    </div>
                    @error('photo')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Private Identifying Details --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="identifying_details" class="block text-sm font-medium text-slate-700">
                            Private Identifying Details (Optional)
                        </label>
                        <span class="rounded bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-500">
                            Confidential · Only visible to you and admins
                        </span>
                    </div>
                    <textarea
                        id="identifying_details"
                        name="identifying_details"
                        rows="3"
                        placeholder="Provide details that only the true owner would know (e.g. serial numbers, specific items inside pockets, lock screen image, engraved initials)..."
                        class="block w-full rounded-lg border-0 px-3 py-2.5 text-sm text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-brand-500"
                    >{{ old('identifying_details') }}</textarea>
                    <p class="mt-1.5 text-xs text-slate-500">
                        This information is never shared publicly and helps campus administrators verify found items matching your report.
                    </p>
                    @error('identifying_details')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Submission Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('dashboard') }}" class="rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                    Cancel
                </a>
                <x-button class="px-6 py-2.5">
                    Submit Lost Item Report
                </x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
