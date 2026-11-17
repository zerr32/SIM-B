<x-modal name="mutasi" title="Transaksi Stok">
    <form method="POST" action="{{ route('mutations.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="item_id" :value="data.id">
        <input type="hidden" name="type" :value="data.type">

        <div class="rounded-xl bg-slate-50 p-3">
            <span :class="data.type === 'IN' ? 'badge-in' : 'badge-out'" x-text="data.type === 'IN' ? 'Barang Masuk' : 'Barang Keluar'"></span>
            <p class="mt-2 text-sm font-medium text-slate-800" x-text="`${data.code} - ${data.name}`"></p>
            <p class="text-xs text-slate-500" x-text="`Kategori: ${data.category} | Satuan: ${data.unit} | Stok saat ini: ${data.stock} ${data.unit}`"></p>
        </div>

        <div>
            <label class="label" for="m-qty">Jumlah (<span x-text="data.unit"></span>)</label>
            <input id="m-qty" name="quantity" type="number" min="1" :max="data.type === 'OUT' ? data.stock : null" value="1" required class="field">
        </div>

        <div x-show="data.type === 'OUT'" x-transition>
            <label class="label" for="m-recipient">Nama Petugas yang Mengambil</label>
            <input id="m-recipient" name="recipient_name" type="text" maxlength="150"
                   :required="data.type === 'OUT'" placeholder="Nama petugas/karyawan penerima barang" class="field">
        </div>

        <div class="rounded-xl bg-slate-50 p-3 text-sm text-slate-600">
            Harga satuan:
            <span class="font-semibold text-slate-800" x-text="`Rp ${data.purchase_price}`"></span>
        </div>

        <div x-show="data.type === 'IN'" x-transition>
            <label class="label" for="m-sup">Supplier Pengirim</label>
            <select id="m-sup" name="supplier_id" x-model="data.supplier_id" class="field">
                <option value="">Tanpa supplier</option>
                @foreach ($suppliers as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="label" for="m-notes">Keterangan</label>
            <input id="m-notes" name="notes" type="text" placeholder="cth: Restock bulanan / Barang rusak" class="field">
        </div>

        <div class="flex justify-end gap-2 pt-2">
            <button type="button" @click="close()" class="btn-ghost">Batal</button>
            <button type="submit" class="btn-primary">Proses</button>
        </div>
    </form>
</x-modal>
