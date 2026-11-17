<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Mutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MutationController extends Controller
{
    public function index(Request $request)
    {
        $mutations = Mutation::with(['item', 'user'])
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->latest('created_at')->latest('id')
            ->paginate(20)->appends($request->query());

        return view('mutations', [
            'mutations' => $mutations,
            'items' => Item::orderBy('name')->get(['id', 'code', 'name', 'stock']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'item_id' => ['required', Rule::exists('items', 'id')->whereNull('deleted_at')],
            'type' => 'required|in:IN,OUT',
            'quantity' => 'required|integer|min:1|max:4294967295',
            'recipient_name' => 'required_if:type,OUT|nullable|string|max:150',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($data, $request) {
                $item = Item::lockForUpdate()->findOrFail($data['item_id']);
                $previous = $item->stock;
                $current = $data['type'] === 'IN' ? $previous + $data['quantity'] : $previous - $data['quantity'];
                $unitPrice = (float) $item->purchase_price;

                if ($current < 0) {
                    throw new \DomainException("Stok tidak mencukupi (tersisa $previous).");
                }
                if ($current > 4294967295) {
                    throw new \DomainException('Stok melewati batas maksimum yang didukung database.');
                }

                $item->update(['stock' => $current]);

                Mutation::create([
                    'item_id' => $item->id,
                    'user_id' => $request->user()->id,
                    'item_code' => $item->code,
                    'item_name' => $item->name,
                    'type' => $data['type'],
                    'unit' => $item->unit,
                    'recipient_name' => $data['type'] === 'OUT' ? $data['recipient_name'] : null,
                    'quantity' => $data['quantity'],
                    'previous_stock' => $previous,
                    'current_stock' => $current,
                    'unit_price' => $unitPrice,
                    'total_price' => $data['quantity'] * $unitPrice,
                    'remaining_value' => $current * (float) $item->purchase_price,
                    'notes' => $data['notes'] ?? null,
                ]);
            });
        } catch (\DomainException $e) {
            return back()->withErrors(['quantity' => $e->getMessage()])->withInput();
        }

        return back()->with('success', 'Mutasi stok berhasil dicatat.');
    }
}
