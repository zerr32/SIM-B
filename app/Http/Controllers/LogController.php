<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use App\Models\LoginLog;

class LogController extends Controller
{
    public function login()
    {
        return view('logs_login', [
            'logs' => LoginLog::with('user')->latest('created_at')->latest('id')->paginate(25),
        ]);
    }

    public function access()
    {
        return view('logs_access', [
            'logs' => AccessLog::with(['user', 'target'])->latest('created_at')->latest('id')->paginate(25),
        ]);
    }
}
