<section x-show="tab === 'users'" x-cloak class="animate-fade-up space-y-4">
    <div class="flex flex-col items-stretch justify-between gap-3 sm:flex-row sm:items-center">
        <p class="text-sm text-slate-500">Daftar akun admin{{ $user->isOwner() ? ' dan owner' : '' }}.</p>
        <button type="button" @click="open('user')" class="btn-primary"><i class="fa-solid fa-user-plus text-xs"></i> Tambah User</button>
    </div>

    <div class="card overflow-x-auto">
        <table class="tbl">
            <thead>
                <tr><th>Nama</th><th>Username</th><th>Email</th><th>Telepon / WA</th><th>Role</th><th>Dibuat Oleh</th><th class="text-center">Aksi</th></tr>
            </thead>
            <tbody>
                @foreach ($users as $u)
                    @php
                        $isSelf = $u->id === $user->id;
                        $canManage = ! $isSelf && $user->canManage($u);
                        $canEditDetails = $user->canUpdateAccountDetails($u);
                    @endphp
                    <tr class="animate-fade-up" style="animation-delay: {{ $loop->index * 45 }}ms">
                        <td class="font-medium text-slate-800">
                            {{ $u->name }}
                            @if ($isSelf) <span class="ml-1 text-xs font-medium text-simb-from">(Anda)</span> @endif
                        </td>
                        <td class="text-simb-from">&#64;{{ $u->username }}</td>
                        <td class="text-slate-500">{{ $u->email ?: '-' }}</td>
                        <td class="font-mono text-xs">{{ $u->phone ?: '-' }}</td>
                        <td><span class="badge-{{ $u->role }}">{{ $u->role }}</span></td>
                        <td class="text-xs text-slate-500">{{ $u->creator?->username ? '@'.$u->creator->username : 'Sistem' }}</td>
                        <td class="whitespace-nowrap text-center">
                            @php($canDelete = $user->canDelete($u))
                            @if ($canEditDetails)
                                <button type="button" title="Ubah data akun"
                                        @click="open('edit-role', @js(['id' => $u->id, 'name' => $u->name, 'username' => $u->username, 'email' => $u->email, 'phone' => $u->phone, 'role' => $u->role]))"
                                        class="btn-icon bg-sky-50 text-sky-600 hover:bg-sky-100"><i class="fa-solid fa-user-pen"></i></button>
                            @endif
                            @if ($canDelete)
                                @if ($user->isOwner())
                                    <form method="POST" action="{{ route('users.destroy', $u) }}" class="inline"
                                          onsubmit="return confirm(@js('Hapus user @'.$u->username.'?'))">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Hapus" class="btn-icon bg-slate-100 text-slate-500 hover:bg-rose-50 hover:text-rose-600"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('users.destroy', $u) }}" class="inline"
                                          onsubmit="return confirm(@js('Hapus user @'.$u->username.'?'))">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Hapus akun yang Anda buat" class="btn-icon bg-slate-100 text-slate-500 hover:bg-rose-50 hover:text-rose-600"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                @endif
                            @endif
                            @if ($user->canChangePasswordOf($u))
                                <button type="button" title="Ubah password"
                                        @click="open('user-password', @js(['id' => $u->id, 'name' => $u->name, 'username' => $u->username, 'isSelf' => $isSelf]))"
                                        class="btn-icon bg-amber-50 text-amber-600 hover:bg-amber-100"><i class="fa-solid fa-key"></i></button>
                            @endif
                            @if ($isSelf)
                                <span class="ml-1 text-xs italic text-slate-400">Akun Anda</span>
                            @elseif (! $canEditDetails && ! $canDelete && ! $user->canChangePasswordOf($u))
                                <span class="text-xs italic text-slate-400">Terkunci</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
