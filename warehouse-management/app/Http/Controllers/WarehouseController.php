<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::latest()->get();
        return view('warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        return view('warehouses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:warehouses,name',
            'location' => 'nullable|string',
        ]);

        Warehouse::create($request->only('name', 'location'));

        return redirect()->route('warehouses.index')->with('success', 'Gudang baru berhasil ditambahkan.');
    }

    public function show(Warehouse $warehouse)
    {
        $warehouse->load('products'); 
        return view('warehouses.show', compact('warehouse'));
    }

    public function edit(Warehouse $warehouse)
    {
        return view('warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('warehouses', 'name')->ignore($warehouse->id)], 
            'location' => 'nullable|string',
        ]);

        $warehouse->update($request->only('name', 'location'));

        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil diperbarui.');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete(); 

        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil dihapus.');
    }
}