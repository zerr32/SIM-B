<section x-show="tab === 'suppliers'" x-cloak class="animate-fade-up space-y-4">
    <div class="flex flex-col items-stretch justify-between gap-3 sm:flex-row sm:items-center">
        <input type="search" x-model="qSuppliers" placeholder="Cari nama supplier atau email..." class="field sm:w-96">
        <button type="button" @click="open('supplier')" class="btn-primary"><i class="fa-solid fa-plus text-xs"></i> Tambah Supplier</button>
    </div>

    <div class="card overflow-x-auto">
        <table class="tbl">
            <thead>
                <tr><th>Nama Supplier</th><th>Email</th><th>Telepon / WA</th><th>Alamat</th><th class="text-center">Hubungi</th><th class="text-center">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($suppliers as $s)
                    @php
                        $digits = preg_replace('/\D+/', '', $s->phone);
                        $wa = 'https://wa.me/'.(str_starts_with($digits, '0') ? '62'.substr($digits, 1) : $digits)
                            .'?text='.rawurlencode("Halo {$s->name}, kami dari Sim-B ingin menanyakan stok barang.");
                    @endphp
                    <tr class="animate-fade-up" style="animation-delay: {{ $loop->index * 45 }}ms"
                        data-search="{{ strtolower($s->name.' '.$s->email) }}"
                        x-show="$el.dataset.search.includes(qSuppliers.toLowerCase())">
                        <td class="font-medium text-slate-800">{{ $s->name }}</td>
                        <td class="text-slate-500">{{ $s->email }}</td>
                        <td class="font-mono text-xs">{{ $s->phone }}</td>
                        <td class="text-xs text-slate-500">{{ $s->address ?: '-' }}</td>
                        <td class="text-center">
                            <a href="{{ $wa }}" target="_blank" rel="noopener"
                               class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-600 transition hover:bg-emerald-100 active:scale-95">
                                <i class="fa-brands fa-whatsapp"></i> Chat WA
                            </a>
                        </td>
                        <td class="text-center">
                            <button type="button" title="Ubah supplier"
                                    @click="open('supplier-edit', @js(['id' => $s->id, 'name' => $s->name, 'email' => $s->email, 'phone' => $s->phone, 'address' => $s->address]))"
                                    class="btn-icon bg-sky-50 text-sky-600 hover:bg-sky-100"><i class="fa-solid fa-pen"></i></button>
                            <form method="POST" action="{{ route('suppliers.destroy', $s) }}" class="inline"
                                  onsubmit="return confirm(@js('Hapus supplier '.$s->name.'?'))">
                                @csrf @method('DELETE')
                                <button type="submit" title="Hapus" class="btn-icon bg-slate-100 text-slate-500 hover:bg-rose-50 hover:text-rose-600"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-10 text-center text-slate-400">Belum ada data supplier.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
