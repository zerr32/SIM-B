<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use App\Models\Supplier;
use App\Support\Phone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    private function rules(): array
    {
        return [
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:100',
            'phone' => 'required|digits_between:8,15',
            'address' => 'nullable|string',
        ];
    }

    public function index()
    {
        return view('suppliers.index', [
            'suppliers' => Supplier::withCount('items')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->merge(['phone' => Phone::normalize($request->input('phone'))]);
        $data = $request->validate($this->rules());

        DB::transaction(function () use ($data, $request) {
            $supplier = Supplier::create($data);

            AccessLog::record(
                'CREATE_SUPPLIER',
                "{$request->user()->username} menambahkan supplier {$supplier->name}"
            );
        });

        return back()->with('success', 'Supplier ditambahkan.');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->merge(['phone' => Phone::normalize($request->input('phone'))]);
        $data = $request->validate($this->rules());

        DB::transaction(function () use ($supplier, $data, $request) {
            $supplier->update($data);

            if ($supplier->wasChanged(['name', 'email', 'phone', 'address'])) {
                AccessLog::record(
                    'UPDATE_SUPPLIER',
                    "{$request->user()->username} memperbarui supplier {$supplier->name}"
                );
            }
        });

        return back()->with('success', 'Supplier diperbarui.');
    }

    public function destroy(Request $request, Supplier $supplier)
    {
        DB::transaction(function () use ($request, $supplier) {
            AccessLog::record(
                'DELETE_SUPPLIER',
                "{$request->user()->username} menghapus supplier {$supplier->name}"
            );

            $supplier->delete();
        });

        return back()->with('success', 'Supplier dihapus.');
    }
}
