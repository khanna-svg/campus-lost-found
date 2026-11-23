<x-layouts.app title="Report an Item">
    <div class="mx-auto max-w-xl space-y-6">
        {{-- Header with Close Button --}}
        <div class="flex items-center justify-between pb-2 border-b border-zinc-200">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Report an item</h1>
            <a href="{{ route('items.index') }}"
               class="flex h-8 w-8 items-center justify-center rounded-lg bg-black text-white hover:bg-zinc-800 transition"
               title="Close">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </a>
        </div>

        {{-- Form Container --}}
        <form method="POST" action="{{ route('items.report.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- 1. Item Details Section --}}
            <div class="rounded-2xl bg-white p-6 shadow-xs border border-zinc-200 space-y-5">
                <div class="flex items-center gap-1.5">
                    <h2 class="text-base font-bold text-zinc-900">Item Details</h2>
                    <span class="text-zinc-400" title="Provide descriptive information">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                        </svg>
                    </span>
                </div>

                {{-- Item Type Radio Selector --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-zinc-800">Item Type</label>
                    <div class="flex items-center gap-6 pt-1">
                        <label class="flex items-center gap-2 text-sm font-semibold text-zinc-900 cursor-pointer">
                            <input type="radio"
                                   name="item_type"
                                   value="lost"
                                   @checked(($defaultType ?? 'lost') === 'lost')
                                   class="h-4 w-4 text-black focus:ring-black border-zinc-300">
                            Lost Item
                        </label>
                        <label class="flex items-center gap-2 text-sm font-semibold text-zinc-900 cursor-pointer">
                            <input type="radio"
                                   name="item_type"
                                   value="found"
                                   @checked(($defaultType ?? 'lost') === 'found')
                                   class="h-4 w-4 text-black focus:ring-black border-zinc-300">
                            Found Item
                        </label>
                    </div>
                </div>

                {{-- Item Possession (for Found items) --}}
                <div id="found-custody-section" class="space-y-2 pt-2 border-t border-zinc-100 {{ ($defaultType ?? 'lost') === 'found' ? '' : 'hidden' }}">
                    <label class="block text-xs font-bold text-zinc-800">
                        Item Possession / Custody <span class="text-zinc-900">*</span>
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-start gap-3 p-3 rounded-xl border border-zinc-200 hover:border-black cursor-pointer bg-white transition">
                            <input type="radio"
                                   name="is_surrendered"
                                   value="0"
                                   @checked(old('is_surrendered', '0') === '0')
                                   class="mt-0.5 h-4 w-4 text-black focus:ring-black border-zinc-300">
                            <div>
                                <span class="block text-xs font-bold text-zinc-900">In my possession (I still have it)</span>
                                <span class="block text-[11px] text-zinc-500">I have not surrendered the item to the Admin Office. The owner can claim it directly from me.</span>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-3 rounded-xl border border-zinc-200 hover:border-black cursor-pointer bg-white transition">
                            <input type="radio"
                                   name="is_surrendered"
                                   value="1"
                                   @checked(old('is_surrendered') === '1')
                                   class="mt-0.5 h-4 w-4 text-black focus:ring-black border-zinc-300">
                            <div>
                                <span class="block text-xs font-bold text-zinc-900">Surrendered to Admin Office</span>
                                <span class="block text-[11px] text-zinc-500">I turned the physical item over to the Admin Office. Claims will be verified and handed over at the Admin Office.</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Title --}}
                <div>
                    <label for="title" class="block text-xs font-bold text-zinc-800 mb-1">
                        Title <span class="text-zinc-900">*</span>
                    </label>
                    <input type="text"
                           id="title"
                           name="title"
                           value="{{ old('title') }}"
                           required
                           placeholder="What did you lose/find?"
                           class="w-full rounded-xl border border-zinc-200 px-3.5 py-2.5 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-black focus:outline-none focus:ring-1 focus:ring-black shadow-xs">
                    @error('title')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-xs font-bold text-zinc-800 mb-1">
                        Description <span class="text-zinc-900">*</span>
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="3"
                              required
                              placeholder="Provide details like color, brand, etc."
                              class="w-full rounded-xl border border-zinc-200 px-3.5 py-2.5 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-black focus:outline-none focus:ring-1 focus:ring-black shadow-xs">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Location & Date Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="location" class="block text-xs font-bold text-zinc-800 mb-1">
                            Location
                        </label>
                        <input type="text"
                               id="location"
                               name="location"
                               value="{{ old('location') }}"
                               placeholder="Where?"
                               class="w-full rounded-xl border border-zinc-200 px-3.5 py-2.5 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-black focus:outline-none focus:ring-1 focus:ring-black shadow-xs">
                        @error('location')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date" class="block text-xs font-bold text-zinc-800 mb-1">
                            Date <span class="text-zinc-900">*</span>
                        </label>
                        <input type="date"
                               id="date"
                               name="date"
                               value="{{ old('date', date('Y-m-d')) }}"
                               required
                               max="{{ date('Y-m-d') }}"
                               class="w-full rounded-xl border border-zinc-200 px-3.5 py-2.5 text-sm text-zinc-900 focus:border-black focus:outline-none focus:ring-1 focus:ring-black shadow-xs">
                        @error('date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- 2. Images Section --}}
            <div class="rounded-2xl bg-white p-6 shadow-xs border border-zinc-200 space-y-2">
                <h2 class="text-base font-bold text-zinc-900">Images</h2>
                <p class="text-xs text-zinc-500">Optional. Up to 4 images.</p>

                <div class="relative mt-2 rounded-xl border border-zinc-200 bg-white p-6 text-center hover:bg-zinc-50 transition cursor-pointer" id="dropzone-box">
                    <input type="file"
                           name="photo"
                           id="photo-input"
                           accept="image/png,image/jpeg,image/gif,image/webp"
                           onchange="handleFilePreview(this)"
                           class="absolute inset-0 h-full w-full opacity-0 cursor-pointer z-10">

                    <div id="dropzone-prompt" class="space-y-1">
                        <p class="text-xs text-zinc-700 font-medium">
                            <span class="font-bold text-black underline">Upload files</span> or drag and drop
                        </p>
                        <p class="text-[11px] text-zinc-400">
                            PNG, JPG, GIF up to 5MB each. Add clear images to help illustrate.
                        </p>
                    </div>

                    <div id="preview-area" class="hidden flex flex-col items-center justify-center pt-2">
                        <img id="preview-image" src="" alt="Selected photo" class="h-28 w-28 rounded-xl object-cover border border-zinc-200 shadow-xs">
                        <p id="preview-name" class="mt-2 text-xs font-semibold text-zinc-700 truncate max-w-xs"></p>
                        <button type="button"
                                onclick="clearFileSelection()"
                                class="mt-1.5 text-[11px] font-semibold text-red-600 hover:underline relative z-20">
                            Remove photo
                        </button>
                    </div>
                </div>
                @error('photo')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- 3. Contact Information Section --}}
            <div class="rounded-2xl bg-white p-6 shadow-xs border border-zinc-200 space-y-3">
                <h2 class="text-base font-bold text-zinc-900">Contact Information *</h2>
                <div>
                    <input type="text"
                           id="contact_info"
                           name="contact_info"
                           value="{{ old('contact_info', auth()->user()->phone ?: auth()->user()->email) }}"
                           required
                           placeholder="Email or Phone Number"
                           class="w-full rounded-xl border border-zinc-200 px-3.5 py-2.5 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-black focus:outline-none focus:ring-1 focus:ring-black shadow-xs">
                    @error('contact_info')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Footer Action Bar --}}
            <div class="pt-2 border-t border-zinc-200 flex justify-end">
                <button type="submit"
                        class="rounded-xl bg-black px-6 py-2.5 text-sm font-bold text-white shadow-xs transition hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-black">
                    Post item
                </button>
            </div>
        </form>
    </div>

    <script>
        function handleFilePreview(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-image').src = e.target.result;
                    document.getElementById('preview-name').textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
                    document.getElementById('dropzone-prompt').classList.add('hidden');
                    document.getElementById('preview-area').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        function clearFileSelection() {
            const input = document.getElementById('photo-input');
            input.value = '';
            document.getElementById('preview-area').classList.add('hidden');
            document.getElementById('dropzone-prompt').classList.remove('hidden');
        }

        document.querySelectorAll('input[name="item_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const custodySection = document.getElementById('found-custody-section');
                if (custodySection) {
                    if (this.value === 'found') {
                        custodySection.classList.remove('hidden');
                    } else {
                        custodySection.classList.add('hidden');
                    }
                }
            });
        });
    </script>
</x-layouts.app>
