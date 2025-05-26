<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $items = Inventory::paginate(15);
        return view('inventory.index', compact('items'));
    }

    public function create()
    {
        return view('inventory.create');
    }

    public function store(Request $req)
    {
        $req->validate([
            'name' => 'required|string|max:255',
            'sku'  => 'required|unique:inventories,sku',
            'qty'  => 'required|integer|min:0',
        ]);

        Inventory::create($req->only('name','sku','qty'));
        return redirect()->route('inventory.index')
                         ->with('success','Inventory item added.');
    }

    public function edit(Inventory $inventory)
    {
        return view('inventory.edit', compact('inventory'));
    }

    public function update(Request $req, InventorY $inventory)
    {
        $req->validate([
            'name' => 'required|string|max:255',
            'sku'  => 'required|unique:inventories,sku,'.$inventory->id,
            'qty'  => 'required|integer|min:0',
        ]);

        $inventory->update($req->only('name','sku','qty'));
        return redirect()->route('inventory.index')
                         ->with('success','Inventory item updated.');
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return back()->with('success','Item removed.');
    }
}
