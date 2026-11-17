<section x-show="tab === 'mutations'" x-cloak class="animate-fade-up">
    <div class="card overflow-x-auto">
        <table class="tbl">
            <thead>
                <tr><th>Waktu</th><th>Barang</th><th>Tipe</th><th>Jumlah</th><th>Jenis</th><th>Total Nilai Mutasi</th><th>Stok Akhir</th><th>Sisa Value Aset</th><th>Karyawan</th><th>Petugas</th><th>Keterangan</th></tr>
            </thead>
            <tbody>
                @forelse ($mutations as $m)
                    <tr class="animate-fade-up" style="animation-delay: {{ $loop->index * 45 }}ms">
                        <td class="whitespace-nowrap font-mono text-xs text-slate-500">{{ $m->created_at->format('Y-m-d H:i') }}</td>
                        <td class="font-medium text-slate-800">
                            {{ $m->item_code ?? '' }} {{ $m->item?->name ?? $m->item_name ?? 'Barang dihapus' }}
                        </td>
                        <td>
                            @if ($m->type === 'IN') <span class="badge-in">Masuk</span>
                            @elseif ($m->type === 'OUT') <span class="badge-out">Keluar</span>
                            @else <span class="badge-delete">Delete</span>
                            @endif
                        </td>
                        <td class="font-semibold">{{ number_format($m->quantity) }}</td>
                        <td>{{ $m->unit ?? $m->item?->unit ?? '—' }}</td>
                        <td>{{ $m->total_price !== null ? 'Rp '.number_format((float) $m->total_price, 0, ',', '.') : '—' }}</td>
                        <td>{{ number_format($m->current_stock) }}</td>
                        <td>{{ $m->remaining_value !== null ? 'Rp '.number_format((float) $m->remaining_value, 0, ',', '.') : '—' }}</td>
                        <td class="text-simb-from">{{ $m->user?->username ? '@'.$m->user->username : '-' }}</td>
                        <td>{{ $m->recipient_name ?? '—' }}</td>
                        <td class="text-xs text-slate-500">{{ $m->notes }}</td>
                    </tr>
                @empty
                    <tr><td colspan="11" class="py-10 text-center text-slate-400">Belum ada mutasi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
