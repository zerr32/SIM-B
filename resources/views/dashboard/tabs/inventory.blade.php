<section x-show="tab === 'inventory'" x-cloak class="animate-fade-up space-y-4">
    <div class="flex flex-col items-stretch justify-between gap-3 sm:flex-row sm:items-center">
        <div class="flex flex-1 flex-col gap-2 sm:flex-row sm:items-center">
            <input type="search" x-model="qItems" placeholder="Cari nama, kategori, atau kode barang..." class="field sm:w-96">
            <div class="relative" @click.outside="filtersOpen = false">
                <button type="button" @click="filtersOpen = !filtersOpen" class="btn-ghost whitespace-nowrap">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                <div x-show="filtersOpen" x-transition x-cloak
                     class="absolute left-0 z-20 mt-2 grid w-64 gap-3 rounded-xl border border-slate-200 bg-white p-3 shadow-lg sm:left-auto sm:right-0">
                    <div>
                        <label class="label" for="filter-category">Kategori</label>
                        <select id="filter-category" x-model="filterCategory" class="field">
                            <option value="">Semua kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label" for="filter-unit">Jenis Barang</label>
                        <select id="filter-unit" x-model="filterUnit" class="field">
                            <option value="">Semua jenis</option>
                            @foreach ($items->pluck('unit')->filter()->unique()->sort() as $unit)
                                <option value="{{ $unit }}">{{ $unit }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label" for="filter-supplier">Supplier</label>
                        <select id="filter-supplier" x-model="filterSupplier" class="field">
                            <option value="">Semua supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" @click="filterCategory = ''; filterUnit = ''; filterSupplier = ''; qItems = ''"
                            class="btn-ghost w-full">Hapus Filter</button>
                </div>
            </div>
        </div>
        <button type="button" @click="open('item')" class="btn-primary"><i class="fa-solid fa-plus text-xs"></i> Tambah Barang</button>
    </div>

    <div class="card overflow-x-auto">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Supplier</th>
                    <th>Stok</th><th>Jenis</th><th>Harga Satuan</th><th>Total Aset</th><th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr class="animate-fade-up" style="animation-delay: {{ $loop->index * 45 }}ms"
                        data-search="{{ strtolower($item->code.' '.$item->name.' '.($item->category->name ?? '').' '.($item->supplier?->name ?? '')) }}"
                        data-category="{{ $item->category_id }}"
                        data-unit="{{ $item->unit }}"
                        data-supplier="{{ $item->supplier_id }}"
                        x-show="$el.dataset.search.includes(qItems.toLowerCase())
                            && (!filterCategory || $el.dataset.category === filterCategory)
                            && (!filterUnit || $el.dataset.unit === filterUnit)
                            && (!filterSupplier || $el.dataset.supplier === filterSupplier)">
                        <td class="font-mono text-xs font-medium text-simb-from">{{ $item->code }}</td>
                        <td class="font-medium text-slate-800">{{ $item->name }}</td>
                        <td><span class="badge-neutral normal-case">{{ $item->category->name ?? '-' }}</span></td>
                        <td class="text-slate-500">{{ $item->supplier?->name ?? '-' }}</td>
                        <td class="font-semibold text-emerald-600">{{ number_format($item->stock) }}</td>
                        <td>{{ $item->unit }}</td>
                        <td>Rp {{ number_format($item->purchase_price, 0, ',', '.') }}</td>
                        <td class="font-medium">Rp {{ number_format($item->stock * $item->purchase_price, 0, ',', '.') }}</td>
                        <td class="whitespace-nowrap text-center">
                            @php
                                $payload = [
                                    'id' => $item->id, 'code' => $item->code, 'name' => $item->name,
                                    'category' => $item->category->name ?? '-',
                                    'category_id' => (string) $item->category_id,
                                    'purchase_price' => number_format((float) $item->purchase_price, 0, ',', '.'),
                                    'unit' => $item->unit,
                                    'stock' => $item->stock,
                                    'supplier_id' => (string) ($item->supplier_id ?? ''),
                                ];
                            @endphp
                            <button type="button" title="Ubah barang" @click="open('item-edit', @js($payload))"
                                    class="btn-icon bg-sky-50 text-sky-600 hover:bg-sky-100"><i class="fa-solid fa-pen"></i></button>
                            <button type="button" title="Barang masuk"
                                    @click="open('mutasi', @js($payload + ['type' => 'IN']))"
                                    class="btn-icon bg-emerald-50 text-emerald-600 hover:bg-emerald-100"><i class="fa-solid fa-arrow-down"></i></button>
                            <button type="button" title="Barang keluar"
                                    @click="open('mutasi', @js($payload + ['type' => 'OUT']))"
                                    class="btn-icon bg-rose-50 text-rose-600 hover:bg-rose-100"><i class="fa-solid fa-arrow-up"></i></button>
                            <form method="POST" action="{{ route('items.destroy', $item) }}" class="inline"
                                  onsubmit="return confirm(@js('Hapus barang '.$item->name.'?'))">
                                @csrf @method('DELETE')
                                <button type="submit" title="Hapus" class="btn-icon bg-slate-100 text-slate-500 hover:bg-rose-50 hover:text-rose-600"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="py-10 text-center text-slate-400">Belum ada data barang.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
