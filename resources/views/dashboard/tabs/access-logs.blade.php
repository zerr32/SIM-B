<section x-show="tab === 'logs'" x-cloak class="animate-fade-up">
    <div class="card overflow-x-auto">
        <table class="tbl">
            <thead><tr><th>Waktu</th><th>Aktor</th><th>Tipe Aksi</th><th>Deskripsi</th></tr></thead>
            <tbody>
                @forelse ($accessLogs as $log)
                    <tr class="animate-fade-up" style="animation-delay: {{ $loop->index * 45 }}ms">
                        <td class="whitespace-nowrap font-mono text-xs text-slate-500">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                        <td class="font-medium text-simb-from">{{ $log->user?->username ? '@'.$log->user->username : '-' }}</td>
                        <td><span class="badge-admin">{{ $log->action_type }}</span></td>
                        <td class="text-xs text-slate-600">{{ $log->description }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-10 text-center text-slate-400">Belum ada aktivitas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
