<header class="sticky top-0 z-30 border-b border-white/60 bg-simb-bg/90 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6">
        <div class="flex items-center gap-3 animate-fade-in">
            <button type="button" @click="sidebarOpen = !sidebarOpen"
                    class="btn-icon bg-white/70 text-slate-700 lg:hidden" aria-label="Buka menu">
                <i class="fa-solid fa-bars"></i>
            </button>
            <img src="{{ asset('images/logo-simb.png') }}" alt="Sim-B" class="h-10 w-auto rounded-lg">
            <span class="hidden text-sm text-slate-600 sm:block">Sistem Informasi Manajemen Barang</span>
        </div>

        <div class="flex items-center gap-2 animate-fade-in">
            <button type="button" @click="toggleProfile()"
                    class="inline-flex h-11 min-w-0 max-w-40 items-center gap-2 rounded-full bg-white/80 px-3 text-left text-sm font-medium text-slate-700 ring-1 ring-slate-200 transition hover:bg-white hover:text-simb-from sm:max-w-56"
                    aria-label="Buka profil {{ $user->name }}" title="Profil {{ $user->name }}">
                <i class="fa-solid fa-circle-user shrink-0 text-lg"></i>
                <span class="min-w-0">
                    <span class="block truncate font-semibold">{{ $user->name }}</span>
                    <span class="block text-xs capitalize text-slate-500">{{ $user->role }}</span>
                </span>
            </button>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex h-10 items-center gap-2 rounded-full bg-white/80 px-4 text-sm font-medium text-slate-600 ring-1 ring-slate-200 transition hover:bg-rose-50 hover:text-rose-600">
                    <i class="fa-solid fa-power-off text-xs"></i><span>Keluar</span>
                </button>
            </form>
        </div>
    </div>
</header>
