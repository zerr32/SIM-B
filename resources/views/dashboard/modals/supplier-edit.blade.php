<x-modal name="supplier-edit" title="Ubah Data Supplier">
    <form method="POST" :action="`/suppliers/${data.id}`" class="space-y-4">
        @csrf
        @method('PATCH')
        <div>
            <label class="label" for="edit-s-name">Nama Supplier</label>
            <input id="edit-s-name" name="name" type="text" x-model="data.name" required class="field">
        </div>
        <div>
            <label class="label" for="edit-s-email">Email</label>
            <input id="edit-s-email" name="email" type="email" x-model="data.email" class="field">
        </div>
        <div>
            <label class="label" for="edit-s-phone">No. WhatsApp</label>
            <input id="edit-s-phone" name="phone" type="text" x-model="data.phone" required class="field">
        </div>
        <div>
            <label class="label" for="edit-s-address">Alamat / Catatan</label>
            <input id="edit-s-address" name="address" type="text" x-model="data.address" class="field">
        </div>
        <div class="flex justify-end gap-2 pt-2">
            <button type="button" @click="close()" class="btn-ghost">Batal</button>
            <button type="submit" class="btn-primary">Simpan</button>
        </div>
    </form>
</x-modal>
