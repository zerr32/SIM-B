<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use App\Models\User;
use App\Support\Phone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return view('users', [
            'users' => User::with('creator')
                ->when(request()->user()->isAdmin(), fn ($query) => $query->where('role', 'admin'))
                ->orderByRaw("FIELD(role,'owner','admin')")
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $me = $request->user();
        $request->merge(['phone' => Phone::normalize($request->input('phone'))]);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username',
            'email' => 'required|email|max:100|unique:users,email',
            'phone' => 'required|digits_between:8,15|unique:users,phone',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in($me->isOwner() ? ['owner', 'admin'] : ['admin'])],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'created_by' => $me->id,
        ]);

        AccessLog::record('CREATE_USER', "{$me->username} membuat akun @{$user->username} dengan role {$user->role}", $user->id);

        return back()->with('success', 'User ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $me = $request->user();
        abort_unless($me->canUpdateAccountDetails($user), 403, 'Anda tidak berhak mengelola akun ini.');
        $request->merge(['phone' => Phone::normalize($request->input('phone'))]);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
            'phone' => 'required|digits_between:8,15|unique:users,phone,' . $user->id,
            'role' => ['nullable', Rule::in(['owner', 'admin'])],
        ]);

        if ($me->isAdmin() && $request->has('role')) {
            abort(403, 'Admin tidak dapat mengubah role akun.');
        }

        if (!empty($data['role']) && $data['role'] !== $user->role) {
            abort_unless($me->isOwner(), 403, 'Hanya owner yang dapat mengubah role.');
            if ($user->id === $me->id) {
                return back()->withErrors(['role' => 'Anda tidak dapat mengubah role akun sendiri.']);
            }
            $old = $user->role;
            $user->role = $data['role'];
            $user->save();
            AccessLog::record('UPDATE_ROLE', "{$me->username} mengubah role @{$user->username} dari {$old} menjadi {$user->role}", $user->id);
        }

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
        ])->save();

        if ($user->wasChanged(['name', 'email', 'phone'])) {
            AccessLog::record('UPDATE_USER', "{$me->username} memperbarui data @{$user->username}", $user->id);
        }

        return back()->with('success', 'Data user diperbarui.');
    }

    public function password(Request $request, User $user)
    {
        $me = $request->user();
        abort_unless($me->canChangePasswordOf($user), 403, 'Anda tidak berhak mengganti password akun ini.');

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        AccessLog::record(
            'CHANGE_PASSWORD',
            $me->id === $user->id
                ? "{$me->username} mengganti password akunnya sendiri"
                : "{$me->username} mengganti password @{$user->username}",
            $user->id
        );

        return back()->with('success', 'Password berhasil diganti.');
    }

    public function destroy(Request $request, User $user)
    {
        $me = $request->user();
        abort_unless($me->canDelete($user), 403, 'Anda tidak berhak menghapus akun ini.');

        DB::transaction(function () use ($me, $user) {
            $username = $user->username;

            AccessLog::record(
                'DELETE_USER',
                "{$me->username} menghapus akun @{$username}",
                $user->id
            );

            $user->tokens()->delete();
            $user->forceDelete();
        });

        return back()->with('success', 'User dihapus.');
    }
}
