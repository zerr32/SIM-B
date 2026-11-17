<x-modal name="item" title="Tambah Barang Baru">
    <form method="POST" action="{{ route('items.store') }}" class="space-y-4"
          x-data="{ cat: '{{ $categories->first()->id ?? 'NEW' }}', sup: '' }">
        @csrf
        <div>
            <label class="label" for="i-name">Nama Barang</label>
            <input id="i-name" name="name" type="text" required placeholder="cth: Laptop ASUS ROG" class="field">
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label" for="i-cat">Kategori</label>
                <select id="i-cat" name="category_id" x-model="cat" class="field">
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                    <option value="NEW">+ Kategori baru...</option>
                </select>
                <input x-show="cat === 'NEW'" x-transition x-cloak name="category_new" type="text"
                       :required="cat === 'NEW'" placeholder="Nama kategori baru" class="field mt-2">
            </div>
            <div>
                <label class="label" for="i-sup">Supplier</label>
                <select id="i-sup" name="supplier_id" x-model="sup" class="field">
                    <option value="">Tanpa supplier</option>
                    @foreach ($suppliers as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                    <option value="NEW">+ Supplier baru...</option>
                </select>
            </div>
        </div>

        <div x-show="sup === 'NEW'" x-transition x-cloak class="space-y-2 rounded-xl bg-slate-50 p-3">
            <p class="text-xs font-medium text-slate-500">Data supplier baru</p>
            <input name="supplier_new_name" type="text" placeholder="Nama supplier" :required="sup === 'NEW'" class="field">
            <div class="grid gap-2 sm:grid-cols-2">
                <input name="supplier_new_email" type="email" placeholder="Email" class="field">
                <input name="supplier_new_phone" type="text" placeholder="No. WA" :required="sup === 'NEW'" class="field">
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label" for="i-unit">Jenis/Satuan Barang</label>
                <input id="i-unit" name="unit" type="text" required maxlength="50" value="{{ old('unit', 'pcs') }}"
                       placeholder="cth: pcs, box, renceng" class="field">
            </div>
            <div>
                <label class="label" for="i-stock">Stok Awal</label>
                <input id="i-stock" name="stock" type="number" min="0" value="0" required class="field">
            </div>
            <div>
                <label class="label" for="i-price">Harga Satuan (Rp)</label>
                <input id="i-price" name="purchase_price" type="text" inputmode="numeric" value="{{ old('purchase_price', '0') }}"
                       @input="formatPrice($event)" required class="field">
            </div>
        </div>
        <p class="text-xs text-slate-400">Kode barang dibuat otomatis oleh sistem.</p>

        <div class="flex justify-end gap-2 pt-2">
            <button type="button" @click="close()" class="btn-ghost">Batal</button>
            <button type="submit" class="btn-primary">Simpan</button>
        </div>
    </form>
</x-modal>
