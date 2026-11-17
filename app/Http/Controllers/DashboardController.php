<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use App\Models\Category;
use App\Models\Item;
use App\Models\LoginLog;
use App\Models\Mutation;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isOwner = $user->isOwner();
        $items = Item::with(['category', 'supplier'])->orderBy('name')->get();

        $loginLogs = collect();
        $accessLogs = collect();
        if ($isOwner) {
            $loginLogs = LoginLog::with('user')->latest('created_at')->limit(25)->get()
                ->map(fn (LoginLog $log) => (object) [
                    'username' => $log->user?->username ?? $log->username_input,
                    'role' => $log->user?->role ?? '-',
                    'status' => $log->status,
                    'ip_address' => $log->ip_address,
                    'device' => $log->user_agent,
                    'created_at' => $log->created_at,
                ]);
            $accessLogs = AccessLog::with('user')->latest('created_at')->latest('id')->get();
        }

        return view('dashboard.index', [
            'user' => $user,
            'stats' => [
                'items' => $items->count(),
                'suppliers' => Supplier::count(),
                'stock' => $items->sum('stock'),
                'asset' => $items->sum(fn (Item $item) => $item->stock * $item->purchase_price),
            ],
            'items' => $items,
            'categories' => Category::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'mutations' => Mutation::with(['item', 'user'])->latest('created_at')->latest('id')->get(),
            'loginLogs' => $loginLogs,
            'users' => User::with('creator')
                ->when(! $isOwner, fn ($query) => $query->where('role', 'admin'))
                ->orderBy('name')
                ->get(),
            'accessLogs' => $accessLogs,
        ]);
    }
}
