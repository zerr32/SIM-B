<x-modal name="supplier" title="Tambah Supplier Baru">
    <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="label" for="s-name">Nama Supplier</label>
            <input id="s-name" name="name" type="text" required placeholder="cth: PT Jaya Distribusi Mandiri" class="field">
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label" for="s-email">Email</label>
                <input id="s-email" name="email" type="email" placeholder="supplier@gmail.com" class="field">
            </div>
            <div>
                <label class="label" for="s-phone">No. WhatsApp</label>
                <input id="s-phone" name="phone" type="text" required placeholder="081234567890" class="field">
            </div>
        </div>
        <div>
            <label class="label" for="s-address">Alamat / Catatan</label>
            <input id="s-address" name="address" type="text" placeholder="Jakarta Barat / Kontak: Pak Herman" class="field">
        </div>
        <div class="flex justify-end gap-2 pt-2">
            <button type="button" @click="close()" class="btn-ghost">Batal</button>
            <button type="submit" class="btn-primary">Simpan</button>
        </div>
    </form>
</x-modal>
