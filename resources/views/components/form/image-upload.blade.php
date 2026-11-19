@props([
    'name' => 'photo',
    'id' => 'photo',
    'label' => 'Item Photograph',
    'required' => false,
    'help' => 'PNG, JPG, or WebP up to 5MB',
    'currentUrl' => null,
])

<div class="space-y-2" x-data="{}" id="{{ $id }}-container">
    <div class="flex items-center justify-between">
        <label for="{{ $id }}" class="block text-sm font-semibold text-zinc-900">
            {{ $label }}
            @if ($required)
                <span class="text-zinc-900 font-bold" aria-hidden="true">*</span>
            @else
                <span class="text-xs font-normal text-zinc-500">(Optional)</span>
            @endif
        </label>
        <span id="{{ $id }}-size-indicator" class="text-xs font-mono text-zinc-500 hidden"></span>
    </div>

    {{-- Dropzone & Preview Container --}}
    <div
        id="{{ $id }}-dropzone"
        class="relative flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-zinc-300 bg-zinc-50/50 p-6 transition-all duration-200 hover:border-zinc-900 hover:bg-zinc-100/50 cursor-pointer text-center"
        tabindex="0"
        role="button"
        aria-label="Upload photo"
    >
        {{-- Hidden File Input --}}
        <input
            type="file"
            id="{{ $id }}"
            name="{{ $name }}"
            accept="image/jpeg,image/png,image/webp"
            @if ($required && ! $currentUrl) required @endif
            class="sr-only"
        >

        {{-- Empty / Initial State --}}
        <div id="{{ $id }}-empty-state" class="{{ $currentUrl ? 'hidden' : 'flex' }} flex-col items-center justify-center py-4 space-y-2 pointer-events-none">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white ring-1 ring-zinc-200 shadow-sm text-zinc-700">
                <svg class="h-6 w-6 text-zinc-800" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
            </div>
            <div class="text-sm">
                <span class="font-semibold text-zinc-900 underline decoration-zinc-400 underline-offset-2">Click to select photo</span>
                <span class="text-zinc-500"> or drag and drop</span>
            </div>
            <p class="text-xs text-zinc-500">{{ $help }}</p>
        </div>

        {{-- Preview State (Populated when user selects a photo or when currentUrl exists) --}}
        <div id="{{ $id }}-preview-state" class="{{ $currentUrl ? 'flex' : 'hidden' }} flex-col items-center justify-center w-full space-y-3">
            <div class="relative max-h-72 w-full max-w-md overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm">
                <img
                    id="{{ $id }}-preview-img"
                    src="{{ $currentUrl ?? '' }}"
                    alt="Photo preview"
                    class="h-56 w-full object-contain bg-zinc-900/5"
                >
                <div class="absolute top-2 right-2 flex items-center gap-1.5">
                    <button
                        type="button"
                        id="{{ $id }}-remove-btn"
                        class="flex h-7 w-7 items-center justify-center rounded-full bg-black/75 text-white hover:bg-black transition shadow-sm backdrop-blur focus:outline-none"
                        title="Remove photo"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span id="{{ $id }}-filename" class="text-xs font-medium text-zinc-700 truncate max-w-xs">
                    {{ $currentUrl ? 'Current photograph' : '' }}
                </span>
                <button
                    type="button"
                    id="{{ $id }}-change-btn"
                    class="rounded-lg bg-zinc-100 px-2.5 py-1 text-xs font-semibold text-zinc-800 hover:bg-zinc-200 transition"
                >
                    Change
                </button>
            </div>
        </div>
    </div>

    {{-- Error message banner --}}
    <div id="{{ $id }}-client-error" class="hidden rounded-lg bg-red-50 p-2.5 text-xs font-medium text-red-700 ring-1 ring-red-200"></div>

    @error($name)
        <p class="text-xs font-medium text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>

<script>
(function() {
    const input = document.getElementById('{{ $id }}');
    const dropzone = document.getElementById('{{ $id }}-dropzone');
    const emptyState = document.getElementById('{{ $id }}-empty-state');
    const previewState = document.getElementById('{{ $id }}-preview-state');
    const previewImg = document.getElementById('{{ $id }}-preview-img');
    const filenameEl = document.getElementById('{{ $id }}-filename');
    const removeBtn = document.getElementById('{{ $id }}-remove-btn');
    const changeBtn = document.getElementById('{{ $id }}-change-btn');
    const errorEl = document.getElementById('{{ $id }}-client-error');
    const sizeIndicator = document.getElementById('{{ $id }}-size-indicator');

    if (!input || !dropzone) return;

    function formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }

    function showError(msg) {
        if (!errorEl) return;
        errorEl.textContent = msg;
        errorEl.classList.remove('hidden');
    }

    function clearError() {
        if (!errorEl) return;
        errorEl.textContent = '';
        errorEl.classList.add('hidden');
    }

    function handleFile(file) {
        clearError();
        if (!file) return;

        // Validation: Image type
        const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            showError('Invalid file type. Please select a JPG, PNG, or WebP image.');
            input.value = '';
            return;
        }

        // Validation: Max 5MB (5,242,880 bytes)
        if (file.size > 5242880) {
            showError('File is too large (' + formatBytes(file.size) + '). Maximum allowed photo size is 5MB.');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            filenameEl.textContent = file.name + ' (' + formatBytes(file.size) + ')';
            if (sizeIndicator) {
                sizeIndicator.textContent = formatBytes(file.size);
                sizeIndicator.classList.remove('hidden');
            }
            emptyState.classList.add('hidden');
            previewState.classList.remove('hidden');
            previewState.classList.add('flex');
            dropzone.classList.remove('border-dashed');
            dropzone.classList.add('border-solid', 'border-zinc-400');
        };
        reader.readAsDataURL(file);
    }

    // Click on dropzone opens file dialog (unless clicking remove or change)
    dropzone.addEventListener('click', function(e) {
        if (e.target.closest('#{{ $id }}-remove-btn')) return;
        input.click();
    });

    // Keyboard navigation (Enter / Space)
    dropzone.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            input.click();
        }
    });

    input.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            handleFile(this.files[0]);
        }
    });

    if (removeBtn) {
        removeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            input.value = '';
            previewImg.src = '';
            filenameEl.textContent = '';
            if (sizeIndicator) {
                sizeIndicator.textContent = '';
                sizeIndicator.classList.add('hidden');
            }
            previewState.classList.add('hidden');
            previewState.classList.remove('flex');
            emptyState.classList.remove('hidden');
            emptyState.classList.add('flex');
            dropzone.classList.remove('border-solid', 'border-zinc-400');
            dropzone.classList.add('border-dashed');
            clearError();
        });
    }

    if (changeBtn) {
        changeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            input.click();
        });
    }

    // Drag and drop support
    ['dragenter', 'dragover'].forEach(function(eventName) {
        dropzone.addEventListener(eventName, function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.add('border-zinc-900', 'bg-zinc-100');
        });
    });

    ['dragleave', 'drop'].forEach(function(eventName) {
        dropzone.addEventListener(eventName, function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.remove('border-zinc-900', 'bg-zinc-100');
        });
    });

    dropzone.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        if (dt && dt.files && dt.files.length > 0) {
            input.files = dt.files;
            handleFile(dt.files[0]);
        }
    });

    // Paste support (Ctrl+V)
    window.addEventListener('paste', function(e) {
        if (!e.clipboardData || !e.clipboardData.files) return;
        const file = e.clipboardData.files[0];
        if (file && file.type.startsWith('image/')) {
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
            handleFile(file);
        }
    });
})();
</script>
