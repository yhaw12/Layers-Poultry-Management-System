<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {

              // In InventoryController or wherever low stock is checked
        // if ($inventory->qty <= $inventory->threshold) {
        //     Alert::create([
        //         'user_id' => Auth::id(),
        //         'message' => "Low stock for {$inventory->item_name}: {$inventory->qty} remaining",
        //         'type' => 'warning',
        //     ]);
        //     // Do not create UserActivityLog entry
        // }
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

        Inventory::create($req->only('name', 'sku', 'qty'));
        return redirect()->route('inventory.index')
                         ->with('success', 'Inventory item added.');
    }

    public function edit(Inventory $inventory)
    {
        return view('inventory.edit', compact('inventory'));
    }

    public function update(Request $req, Inventory $inventory)
    {
        $req->validate([
            'name' => 'required|string|max:255',
            'sku'  => 'required|unique:inventories,sku,'.$inventory->id,
            'qty'  => 'required|integer|min:0',
        ]);

        $inventory->update($req->only('name', 'sku', 'qty'));
        return redirect()->route('inventory.index')
                         ->with('success', 'Inventory item updated.');
    }

    public function destroy($id)
    {
        $inventory = Inventory::findorFail($id);
        $inventory->delete();
        return back()->with('success', 'Item removed.');
    }

    

    // public function lowStock()
    // {
    //     $lowItems = Inventory::where('qty', '<', 10)->get(); // Threshold example
    //     foreach ($lowItems as $item) {
    //         Alert::create([
    //             'message' => "Low stock for {$item->name} (Qty: {$item->qty})",
    //             'type' => 'inventory',
    //             'user_id' => auth()->id() ?? 1,
    //         ]);
    //     }
    //     return view('inventory.low-stock', compact('lowItems'));
    // }

    // Route::get('/alerts/low-stock', [InventoryController::class, 'lowStock'])->name('alerts.low-stock');
}