<section x-show="tab === 'login-logs'" x-cloak class="animate-fade-up">
    <div class="card overflow-x-auto">
        <table class="tbl">
            <thead><tr><th>Waktu</th><th>Username</th><th>Role</th><th>Status</th><th>IP / Device</th></tr></thead>
            <tbody>
                @forelse ($loginLogs as $log)
                    <tr class="animate-fade-up" style="animation-delay: {{ $loop->index * 45 }}ms">
                        <td class="whitespace-nowrap font-mono text-xs text-slate-500">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                        <td class="font-medium text-slate-800">&#64;{{ $log->username }}</td>
                        <td class="text-xs uppercase text-simb-from">{{ $log->role }}</td>
                        <td>
                            <span @class([
                                'badge-in'      => $log->status === 'SUCCESS',
                                'badge-out'     => $log->status === 'FAILED',
                                'badge-neutral' => ! in_array($log->status, ['SUCCESS', 'FAILED']),
                            ])>{{ $log->status }}</span>
                        </td>
                        <td class="font-mono text-xs text-slate-500">{{ $log->ip_address }} ({{ $log->device }})</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-10 text-center text-slate-400">Belum ada log login.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
