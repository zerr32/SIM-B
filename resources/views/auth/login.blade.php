@extends('layouts.guest')

@section('title', 'Log in')

@section('content')
<main class="min-h-screen lg:grid lg:grid-cols-[625fr_815fr]">

    {{-- Kiri: form --}}
    <section class="flex min-h-screen items-center bg-simb-bg px-6 pb-16 sm:px-14 lg:px-[58px]">
        <div class="w-full max-w-[500px]">

            <img src="{{ asset('images/logo-simb.png') }}" alt="Logo Sim-B"
                 class="mb-6 h-[72px] w-auto animate-fade-up">

            <h1 class="mb-7 text-2xl font-normal text-slate-900 animate-fade-up" style="animation-delay:80ms">
                Log in to Sim-B
            </h1>

            <form method="POST" action="{{ route('login.store') }}"
                  x-data="{ show: false, loading: false }" @submit="loading = true">
                @csrf

                @if ($errors->any())
                    <p class="mb-4 rounded-xl bg-white/80 px-4 py-3 text-sm text-red-600 animate-fade-up" role="alert">
                        {{ $errors->first() }}
                    </p>
                @endif

                {{-- Email / username --}}
                <div class="relative animate-fade-up" style="animation-delay:160ms">
                    <label for="login" class="sr-only">Email atau username</label>
                    <input id="login" name="login" type="text" value="{{ old('login') }}"
                           required autofocus autocomplete="username"
                           placeholder="Input email or username"
                           class="h-[50px] w-full rounded-xl border-0 bg-white pl-6 pr-14 text-[13px] text-slate-800 shadow-sm transition placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-simb-to/70">
                    <i class="fa-solid fa-right-to-bracket pointer-events-none absolute right-5 top-1/2 -translate-y-1/2 text-slate-800"></i>
                </div>

                {{-- Password --}}
                <div class="relative mt-[30px] animate-fade-up" style="animation-delay:240ms">
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" :type="show ? 'text' : 'password'"
                           required autocomplete="current-password"
                           placeholder="Input Password"
                           class="h-[50px] w-full rounded-xl border-0 bg-white pl-6 pr-14 text-[13px] text-slate-800 shadow-sm transition placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-simb-to/70">
                    <button type="button" @click="show = !show"
                            :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'"
                            class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-800 transition hover:text-simb-from">
                        <i :class="show ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash'"></i>
                    </button>
                </div>

                <button type="submit" :disabled="loading"
                        class="mt-[18px] h-[26px] w-full animate-fade-up rounded-full bg-gradient-to-r from-[#4a69b0] via-[#6a88c6] to-[#4b86f0] text-[13px] font-semibold text-white transition duration-200 hover:brightness-110 active:scale-[.98] disabled:opacity-70"
                        style="animation-delay:360ms">
                    <span x-text="loading ? 'Memproses...' : 'Log in'">Log in</span>
                </button>
            </form>
        </div>
    </section>

    {{-- Kanan: kolase (disembunyikan di layar kecil) --}}
    <aside class="relative hidden overflow-hidden lg:block" aria-hidden="true">
        <img src="{{ asset('images/login-collage.jpg') }}" alt=""
             class="absolute inset-0 h-full w-full animate-zoom-out object-cover">
    </aside>
</main>
@endsection
