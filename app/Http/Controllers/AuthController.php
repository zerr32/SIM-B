<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string|max:255',
            'password' => 'required|string',
        ]);

        $identifier = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        $meta = [
            'username_input' => $credentials['login'],
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ];

        if (Auth::attempt([
            $identifier => $credentials['login'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();
            LoginLog::create($meta + ['user_id' => Auth::id(), 'status' => 'SUCCESS']);

            return redirect()->intended(route('dashboard'));
        }

        LoginLog::create($meta + [
            'user_id' => User::where($identifier, $credentials['login'])->value('id'),
            'status' => 'FAILED',
        ]);

        return back()
            ->withErrors(['login' => 'Username/email atau password salah.'])
            ->onlyInput('login');
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        LoginLog::create([
            'user_id' => $user->id,
            'username_input' => $user->username,
            'status' => 'LOGOUT',
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to(route('login'), 303)->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }
}
