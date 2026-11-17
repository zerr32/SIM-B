@props(['name', 'title'])

<div x-show="modal === '{{ $name }}'" x-cloak
     x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">

    <div @click="close()" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>

    <div class="relative max-h-[90vh] w-full max-w-lg animate-pop overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
        <div class="mb-5 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-800">{{ $title }}</h3>
            <button type="button" @click="close()" class="text-slate-400 transition hover:rotate-90 hover:text-slate-700" aria-label="Tutup">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        {{ $slot }}
    </div>
</div>
