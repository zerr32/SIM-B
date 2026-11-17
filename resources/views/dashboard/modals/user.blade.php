<x-modal name="user" title="Tambah User Baru">
    <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="label" for="u-name">Nama Lengkap</label>
            <input id="u-name" name="name" type="text" required placeholder="cth: Rian Pratama" class="field">
        </div>
        <div>
            <label class="label" for="u-username">Username</label>
            <input id="u-username" name="username" type="text" required placeholder="cth: rian_admin" class="field">
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label" for="u-email">Email</label>
                <input id="u-email" name="email" type="email" required class="field">
            </div>
            <div>
                <label class="label" for="u-phone">Telepon / WA</label>
                <input id="u-phone" name="phone" type="text" required class="field">
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <!-- Input Password dengan Toggle Show/Hide -->
            <div x-data="{ showPassword: false }">
                <label class="label" for="u-pass">Password Awal</label>
                <div class="relative">
                    <input id="u-pass"
                           :type="showPassword ? 'text' : 'password'"
                           name="password"
                           minlength="8"
                           required
                           autocomplete="new-password"
                           class="field pr-10">

                    <button type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none">
                        <!-- Icon Mata Terbuka (Muncul saat password disembunyikan) -->
                        <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <!-- Icon Mata Dicoret (Muncul saat password terlihat) -->
                        <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.057 10.057 0 012.162-3.858m3.922-2.316A9.989 9.989 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.027 10.027 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
            </div>
            <div>
                <label class="label" for="u-role">Role</label>
                <select id="u-role" name="role" class="field">
                    @if ($user->role === 'owner')
                        <option value="admin">Admin</option>
                        <option value="owner">Owner</option>
                    @else
                        <option value="admin">Admin</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="flex justify-end gap-2 pt-2">
            <button type="button" @click="close()" class="btn-ghost">Batal</button>
            <button type="submit" class="btn-primary">Simpan</button>
        </div>
    </form>
</x-modal>
