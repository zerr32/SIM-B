<x-modal name="item-edit" title="Ubah Data Barang">
    <form method="POST" :action="`/items/${data.id}`" class="space-y-4">
        @csrf
        @method('PATCH')
        <div>
            <label class="label" for="edit-i-name">Nama Barang</label>
            <input id="edit-i-name" name="name" type="text" x-model="data.name" required class="field">
        </div>
        <div>
            <label class="label" for="edit-i-category">Kategori</label>
            <select id="edit-i-category" name="category_id" x-model="data.category_id" required class="field">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label" for="edit-i-supplier">Supplier</label>
            <select id="edit-i-supplier" name="supplier_id" x-model="data.supplier_id" class="field">
                <option value="">Tanpa supplier</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label" for="edit-i-unit">Jenis/Satuan Barang</label>
            <input id="edit-i-unit" name="unit" type="text" x-model="data.unit" required maxlength="50"
                   placeholder="cth: pcs, box, renceng" class="field">
        </div>
        <div>
            <label class="label" for="edit-i-price">Harga Satuan (Rp)</label>
            <input id="edit-i-price" name="purchase_price" type="text" inputmode="numeric"
                   :value="data.purchase_price" @input="formatPrice($event)" required class="field">
        </div>
        <div class="flex justify-end gap-2 pt-2">
            <button type="button" @click="close()" class="btn-ghost">Batal</button>
            <button type="submit" class="btn-primary">Simpan</button>
        </div>
    </form>
</x-modal>
