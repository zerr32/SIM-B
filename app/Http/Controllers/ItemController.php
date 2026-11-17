<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use App\Models\Category;
use App\Models\Item;
use App\Models\Mutation;
use App\Models\Supplier;
use App\Support\Phone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->route('dashboard');
    }

    public function store(Request $request)
    {
        $newCategory = $request->input('category_id') === 'NEW';
        $newSupplier = $request->input('supplier_id') === 'NEW';
        $request->merge([
            'category_id' => $newCategory ? null : $request->input('category_id'),
            'supplier_id' => $newSupplier ? null : $request->input('supplier_id'),
            'create_category' => $newCategory,
            'create_supplier' => $newSupplier,
            'category_name' => $request->input('category_new'),
            'supplier_name' => $request->input('supplier_new_name'),
            'supplier_email' => $request->input('supplier_new_email'),
            'supplier_phone' => Phone::normalize($request->input('supplier_new_phone')),
            'purchase_price' => $this->normalizePrice($request->input('purchase_price')),
        ]);

        $data = $request->validate([
            'name' => 'required|string|max:150',
            'category_id' => 'nullable|exists:categories,id',
            'create_category' => 'required|boolean',
            'category_name' => 'required_if:create_category,1|required_without:category_id|nullable|string|max:50',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'create_supplier' => 'required|boolean',
            'supplier_name' => 'required_if:create_supplier,1|nullable|string|max:150',
            'supplier_email' => 'nullable|email|max:100',
            'supplier_phone' => 'required_if:create_supplier,1|nullable|digits_between:8,15',
            'stock' => 'required|integer|min:0|max:4294967295',
            'unit' => 'required|string|max:50',
            'purchase_price' => 'required|numeric|min:0|max:9999999999999',
        ]);

        DB::transaction(function () use ($data, $request) {
            $categoryId = $data['category_id']
                ?? Category::firstOrCreate(['name' => trim($data['category_name'])])->id;
            $supplierId = $data['supplier_id'] ?? null;

            if ($data['create_supplier']) {
                $supplier = Supplier::create([
                    'name' => $data['supplier_name'],
                    'email' => $data['supplier_email'] ?? null,
                    'phone' => $data['supplier_phone'],
                ]);
                $supplierId = $supplier->id;

                AccessLog::record(
                    'CREATE_SUPPLIER',
                    "{$request->user()->username} menambahkan supplier {$supplier->name}"
                );
            }

            $item = Item::create([
                'code' => Item::generateCode(),
                'category_id' => $categoryId,
                'supplier_id' => $supplierId,
                'name' => $data['name'],
                'unit' => $data['unit'],
                'stock' => $data['stock'],
                'purchase_price' => $data['purchase_price'],
            ]);

            if ($item->stock > 0) {
                Mutation::create([
                    'item_id' => $item->id,
                    'user_id' => $request->user()->id,
                    'item_code' => $item->code,
                    'item_name' => $item->name,
                    'type' => 'IN',
                    'unit' => $item->unit,
                    'quantity' => $item->stock,
                    'previous_stock' => 0,
                    'current_stock' => $item->stock,
                    'unit_price' => $item->purchase_price,
                    'total_price' => $item->stock * (float) $item->purchase_price,
                    'remaining_value' => $item->stock * (float) $item->purchase_price,
                    'notes' => 'Stok awal',
                ]);
            }
        });

        return back()->with('success', 'Barang ditambahkan.');
    }

    public function update(Request $request, Item $item)
    {
        $request->merge([
            'supplier_id' => $request->input('supplier_id') === 'NEW' ? null : $request->input('supplier_id'),
            'purchase_price' => $this->normalizePrice($request->input('purchase_price')),
        ]);

        $data = $request->validate([
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'unit' => 'required|string|max:50',
            'purchase_price' => 'required|numeric|min:0|max:9999999999999',
        ]);
        DB::transaction(function () use ($item, $data, $request) {
            $item->update($data);

            if ($item->wasChanged(['name', 'category_id', 'supplier_id', 'unit', 'purchase_price'])) {
                AccessLog::record(
                    'UPDATE_ITEM',
                    "{$request->user()->username} memperbarui barang {$item->code} - {$item->name}"
                );
            }
        });

        return back()->with('success', 'Barang diperbarui.');
    }

    private function normalizePrice(mixed $price): ?string
    {
        if (!is_string($price) || !preg_match('/\A(?:\d+|\d{1,3}(?:\.\d{3})+)\z/', $price)) {
            return null;
        }

        return str_replace('.', '', $price);
    }

    public function destroy(Request $request, Item $item)
    {
        DB::transaction(function () use ($item, $request) {
            Mutation::where('item_id', $item->id)->update([
                'item_code' => $item->code,
                'item_name' => $item->name,
            ]);

            AccessLog::record(
                'DELETE_ITEM',
                "{$request->user()->username} menghapus barang {$item->code} - {$item->name}"
            );

            $item->forceDelete();
        });

        return back()->with('success', 'Barang dihapus.');
    }
}
