@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div x-data="dashboard" @keydown.escape.window="close()" class="min-h-screen lg:pl-64">

    @include('dashboard.partials.tabs')
    @include('dashboard.partials.navbar')

    <main class="mx-auto w-full max-w-7xl space-y-6 px-4 py-6 sm:px-6">
        @if ($errors->any())
            <div class="rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700" role="alert">
                {{ $errors->first() }}
            </div>
        @endif
        @if (session('success'))
            <div class="rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700" role="status">
                {{ session('success') }}
            </div>
        @endif
        @include('dashboard.partials.stats')

        @include('dashboard.tabs.inventory')
        @include('dashboard.tabs.mutations')
        @include('dashboard.tabs.users')
        @include('dashboard.tabs.suppliers')

        @if ($user->isOwner())
            @include('dashboard.tabs.login-logs')
            @include('dashboard.tabs.access-logs')
        @endif
    </main>

    @include('dashboard.modals.item')
    @include('dashboard.modals.item-edit')
    @include('dashboard.modals.mutation')
    @include('dashboard.modals.supplier')
    @include('dashboard.modals.supplier-edit')
    @if ($user->isOwner() || $user->isAdmin())
        @include('dashboard.modals.user')
        @include('dashboard.modals.edit-role')
        @include('dashboard.modals.user-password')
    @endif

    <div x-show="profileOpen" x-cloak
         @click.self="profileOpen = false"
         class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/45 p-4 backdrop-blur-sm"
         role="dialog" aria-modal="true" aria-labelledby="profile-title">
        <section x-transition
                 class="w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
            <div class="relative bg-gradient-to-br from-simb-from to-simb-to px-6 pb-8 pt-7 text-white">
                <button type="button" @click="profileOpen = false"
                        class="absolute right-4 top-4 flex h-9 w-9 items-center justify-center rounded-full bg-white/15 transition hover:bg-white/25"
                        aria-label="Tutup profil">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <div class="flex items-center gap-4">
                    <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-white/20 text-3xl ring-1 ring-white/40">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-medium uppercase tracking-[.16em] text-white/75">Profil pengguna</p>
                        <h2 id="profile-title" class="mt-1 truncate text-xl font-semibold">{{ $user->name }}</h2>
                        <p class="mt-0.5 text-sm text-white/80">&#64;{{ $user->username }}</p>
                    </div>
                </div>
            </div>

            <div class="-mt-3 space-y-3 rounded-t-3xl bg-white px-6 pb-6 pt-5">
                <div class="flex items-start gap-3 rounded-2xl bg-slate-50 p-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-simb-from">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs text-slate-500">Email</p>
                        <p class="break-all text-sm font-medium text-slate-800">{{ $user->email ?: 'Belum diisi' }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 rounded-2xl bg-slate-50 p-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                        <i class="fa-solid fa-phone"></i>
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs text-slate-500">Nomor telepon</p>
                        <p class="break-all text-sm font-medium text-slate-800">{{ $user->phone ?: 'Belum diisi' }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 rounded-2xl bg-slate-50 p-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-violet-700">
                        <i class="fa-solid fa-shield-halved"></i>
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs text-slate-500">Role</p>
                        <p class="text-sm font-semibold capitalize text-slate-800">{{ $user->role }}</p>
                    </div>
                </div>

                <button type="button" @click="profileOpen = false"
                        class="btn-primary mt-2 w-full">Tutup</button>
            </div>
        </section>
    </div>

    {{-- Toast --}}
    <div x-show="toastOn" x-cloak
         x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="translate-y-4 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed bottom-5 right-5 z-[60] flex items-center gap-3 rounded-xl bg-slate-800 px-5 py-3 text-sm text-white shadow-xl">
        <i class="fa-solid fa-circle-check text-emerald-400"></i>
        <span x-text="toastMsg"></span>
    </div>
</div>
@endsection
