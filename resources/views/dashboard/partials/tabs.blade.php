@php
    $tabs = [
        'inventory' => ['Barang & Stok', 'fa-boxes-stacked'],
        'mutations' => ['Mutasi Stok', 'fa-arrow-right-arrow-left'],
        'users' => ['Manajemen User', 'fa-users'],
        'suppliers' => ['Supplier', 'fa-truck-field'],
    ];
    if ($user->isOwner()) {
        $tabs += [
            'login-logs' => ['Log Login', 'fa-right-to-bracket'],
            'logs' => ['Log Aktivitas', 'fa-clock-rotate-left'],
        ];
    }
@endphp

<div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
     class="fixed inset-0 z-40 bg-slate-950/40 lg:hidden" aria-hidden="true"></div>

<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
       class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-slate-200 bg-white transition-transform duration-200 lg:translate-x-0">
    <div class="flex h-20 items-center gap-3 border-b border-slate-100 px-6">
        <img src="{{ asset('images/logo-simb.png') }}" alt="Sim-B" class="h-11 w-auto rounded-lg">
        <div>
            <p class="text-sm font-semibold text-slate-800">SIM-B</p>
            <p class="text-[11px] text-slate-500">Manajemen Barang</p>
        </div>
        <button type="button" @click="sidebarOpen = false"
                class="btn-icon ml-auto bg-slate-50 text-slate-500 lg:hidden" aria-label="Tutup menu">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <nav class="flex-1 space-y-1 px-3 py-5" aria-label="Menu dashboard">
        <p class="mb-3 px-3 text-[10px] font-semibold uppercase tracking-[.16em] text-slate-400">Menu utama</p>
        @foreach ($tabs as $key => [$label, $icon])
            <button type="button" data-tab="{{ $key }}"
                    @click="tab = '{{ $key }}'; sidebarOpen = false"
                    :class="tab === '{{ $key }}' ? 'bg-blue-50 text-simb-from font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
                    class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-left text-sm transition">
                <i class="fa-solid {{ $icon }} w-5 text-center"></i>
                <span>{{ $label }}</span>
                <i x-show="tab === '{{ $key }}'" class="fa-solid fa-chevron-right ml-auto text-xs"></i>
            </button>
        @endforeach
    </nav>

    <div class="border-t border-slate-100 p-4">
        <button type="button" @click="toggleProfile(); sidebarOpen = false"
                class="flex w-full items-center gap-3 rounded-xl border border-slate-200 bg-white p-3 text-left transition hover:border-blue-200 hover:bg-blue-50/60">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-simb-from">
                <i class="fa-solid fa-user"></i>
            </span>
            <span class="min-w-0 flex-1">
                <span class="block truncate text-sm font-semibold text-slate-800">{{ $user->name }}</span>
                <span class="block truncate text-xs capitalize text-slate-500">{{ $user->role }}</span>
            </span>
            <i class="fa-solid fa-chevron-right text-xs text-slate-400"></i>
        </button>
    </div>
</aside>
