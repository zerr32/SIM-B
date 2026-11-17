<x-modal name="edit-role" title="Ubah Data Akun">
    <form method="POST" :action="`/users/${data.id}`" class="space-y-4">
        @csrf @method('PATCH')
        <div class="rounded-xl bg-slate-50 p-3">
            <p class="text-xs text-slate-500">User target</p>
            <p class="text-sm font-medium text-slate-800" x-text="`${data.name} (@${data.username})`"></p>
        </div>

        <div>
            <label class="label" for="edit-u-name">Nama Lengkap</label>
            <input id="edit-u-name" name="name" type="text" x-model="data.name" required maxlength="100" class="field">
        </div>
        <div>
            <label class="label" for="edit-u-email">Email</label>
            <input id="edit-u-email" name="email" type="email" x-model="data.email" required maxlength="100" class="field">
        </div>
        <div>
            <label class="label" for="edit-u-phone">Telepon / WA</label>
            <input id="edit-u-phone" name="phone" type="text" x-model="data.phone" required class="field">
        </div>

        @if ($user->isOwner())
            <div>
                <label class="label" for="r-role">Role</label>
                <select id="r-role" name="role" x-model="data.role" class="field">
                    <option value="admin">Admin</option>
                    <option value="owner">Owner</option>
                </select>
            </div>
        @endif

        <div class="flex justify-end gap-2 pt-2">
            <button type="button" @click="close()" class="btn-ghost">Batal</button>
            <button type="submit" class="btn-primary">Simpan</button>
        </div>
    </form>
</x-modal>
