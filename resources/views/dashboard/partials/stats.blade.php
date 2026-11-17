@php
    $cards = [
        ['Jenis Barang', $stats['items'],     'fa-box',         'bg-sky-50 text-sky-500'],
        ['Supplier',     $stats['suppliers'], 'fa-truck-field', 'bg-violet-50 text-violet-500'],
        ['Total Stok',   number_format($stats['stock']), 'fa-layer-group', 'bg-emerald-50 text-emerald-500'],
        ['Valuasi Aset', 'Rp '.number_format($stats['asset'], 0, ',', '.'), 'fa-wallet', 'bg-blue-50 text-simb-from'],
    ];
@endphp

<div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
    @foreach ($cards as [$label, $value, $icon, $tone])
        <div class="card flex animate-fade-up items-center justify-between p-5 transition duration-300 hover:-translate-y-1 hover:shadow-md"
             style="animation-delay: {{ $loop->index * 80 }}ms">
            <div class="min-w-0">
                <p class="text-xs text-slate-500">{{ $label }}</p>
                <p class="mt-1 truncate text-xl font-semibold text-slate-800">{{ $value }}</p>
            </div>
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $tone }}">
                <i class="fa-solid {{ $icon }}"></i>
            </div>
        </div>
    @endforeach
</div>
