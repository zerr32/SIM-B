<x-modal name="user-password" title="Ubah Password">
    <form method="POST" :action="`/users/${data.id}/password`" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="rounded-xl bg-slate-50 p-3">
            <p class="text-xs text-slate-500">Akun yang diubah</p>
            <p class="text-sm font-medium text-slate-800" x-text="`${data.name} (@${data.username})`"></p>
        </div>

        <div>
            <label class="label" for="new-password">Password baru</label>
            <div class="relative" x-data="{ visible: false }">
                <input id="new-password" name="password" :type="visible ? 'text' : 'password'" minlength="8"
                       required autocomplete="new-password" class="field pr-24">
                <button type="button" @click="visible = !visible"
                        class="absolute inset-y-0 right-3 flex items-center px-2 text-slate-500 hover:text-slate-800"
                        :aria-label="visible ? 'Sembunyikan password' : 'Lihat password'">
                    <i class="fa-solid" :class="visible ? 'fa-eye-slash' : 'fa-eye'" aria-hidden="true"></i>
                </button>
            </div>
        </div>
        <div>
            <label class="label" for="confirm-password">Konfirmasi password baru</label>
            <div class="relative" x-data="{ visible: false }">
                <input id="confirm-password" name="password_confirmation"
                       :type="visible ? 'text' : 'password'" minlength="8"
                       required autocomplete="new-password" class="field pr-24">
                <button type="button" @click="visible = !visible"
                        class="absolute inset-y-0 right-3 flex items-center px-2 text-slate-500 hover:text-slate-800"
                        :aria-label="visible ? 'Sembunyikan password' : 'Lihat password'">
                    <i class="fa-solid" :class="visible ? 'fa-eye-slash' : 'fa-eye'" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <p class="text-xs text-slate-500">
            Perubahan ini tercatat dan hanya dapat dilihat oleh owner.
        </p>

        <div class="flex justify-end gap-2 pt-2">
            <button type="button" @click="close()" class="btn-ghost">Batal</button>
            <button type="submit" class="btn-primary">Simpan Password</button>
        </div>
    </form>
</x-modal>
