<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Feed;
use App\Models\Inventory;
use App\Models\MedicineLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        // Fetch paginated inventory items
        $items = Inventory::paginate(15);

        // Fetch low stock items for Inventory, Feed, and Medicine
        $lowStockItems = collect();

        // Inventory low stock
        $lowInventory = Inventory::where('qty', '<', DB::raw('threshold'))
            ->get(['id', 'item_name as name', 'qty', 'threshold', DB::raw('"Inventory" as type')]);
        $lowStockItems = $lowStockItems->concat($lowInventory);

        // Feed low stock
        $lowFeed = Feed::where('quantity', '<', DB::raw('threshold'))
            ->get(['id', 'name', 'quantity as qty', 'threshold', DB::raw('"Feed" as type')]);
        $lowStockItems = $lowStockItems->concat($lowFeed);

        // Medicine low stock
        $lowMedicine = MedicineLog::select('medicine_name as name')
            ->selectRaw('SUM(CASE WHEN type = "purchase" THEN quantity ELSE -quantity END) as qty')
            ->selectRaw('10 as threshold') // Adjust threshold as needed
            ->groupBy('medicine_name')
            ->havingRaw('qty < 10')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => null, // No single ID since it's aggregated
                    'name' => $item->name,
                    'qty' => $item->qty,
                    'threshold' => $item->threshold,
                    'type' => 'Medicine',
                ];
            });
        $lowStockItems = $lowStockItems->concat($lowMedicine);

        return view('inventory.index', compact('items', 'lowStockItems'));
    }

    public function create()
    {
        return view('inventory.create');
    }

    public function store(Request $req)
    {
        $req->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|unique:inventories,sku',
            'qty' => 'required|integer|min:0',
            'threshold' => 'required|integer|min:0', // Add threshold validation
        ]);

        $inventory = Inventory::create($req->only('name', 'sku', 'qty', 'threshold'));

        // Check for low stock and create alert if needed
        if ($inventory->qty <= $inventory->threshold) {
            $this->createLowStockAlert('Inventory', $inventory->item_name, $inventory->qty, $inventory->threshold, Auth::id());
        }

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
            'sku' => 'required|unique:inventories,sku,' . $inventory->id,
            'qty' => 'required|integer|min:0',
            'threshold' => 'required|integer|min:0',
        ]);

        $inventory->update($req->only('name', 'sku', 'qty', 'threshold'));

        // Check for low stock and create alert if needed
        if ($inventory->qty <= $inventory->threshold) {
            $this->createLowStockAlert('Inventory', $inventory->item_name, $inventory->qty, $inventory->threshold, Auth::id());
        }

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory item updated.');
    }

    public function destroy($id)
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->delete();
        return back()->with('success', 'Item removed.');
    }

    public function lowStock()
    {
        // Reuse the low stock query from index
        $lowStockItems = collect();

        // Inventory low stock
        $lowInventory = Inventory::where('qty', '<', DB::raw('threshold'))
            ->get(['id', 'item_name as name', 'qty', 'threshold', DB::raw('"Inventory" as type')]);
        $lowStockItems = $lowStockItems->concat($lowInventory);

        // Feed low stock
        $lowFeed = Feed::where('quantity', '<', DB::raw('threshold'))
            ->get(['id', 'name', 'quantity as qty', 'threshold', DB::raw('"Feed" as type')]);
        $lowStockItems = $lowStockItems->concat($lowFeed);

        // Medicine low stock
        $lowMedicine = MedicineLog::select('medicine_name as name')
            ->selectRaw('SUM(CASE WHEN type = "purchase" THEN quantity ELSE -quantity END) as qty')
            ->selectRaw('10 as threshold')
            ->groupBy('medicine_name')
            ->havingRaw('qty < 10')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => null,
                    'name' => $item->name,
                    'qty' => $item->qty,
                    'threshold' => $item->threshold,
                    'type' => 'Medicine',
                ];
            });
        $lowStockItems = $lowStockItems->concat($lowMedicine);

        // Create alerts for low stock items
        foreach ($lowStockItems as $item) {
            $this->createLowStockAlert($item->type, $item->name, $item->qty, $item->threshold, Auth::id());
        }

        return view('inventory.low-stock', compact('lowStockItems'));
    }

    /**
     * Create a low stock alert if one doesn't already exist.
     *
     * @param string $type
     * @param string $name
     * @param int $quantity
     * @param int $threshold
     * @param int|null $userId
     * @return void
     */
    private function createLowStockAlert($type, $name, $quantity, $threshold, $userId)
    {
        // Check if an alert already exists for this item
        $existingAlert = Alert::where('message', "Low stock for {$name}: {$quantity} remaining (Threshold: {$threshold})")
            ->where('type', 'warning')
            ->whereNull('read_at')
            ->exists();

        if (!$existingAlert) {
            Alert::create([
                'message' => "Low stock for {$name}: {$quantity} remaining (Threshold: {$threshold})",
                'type' => 'warning',
                'user_id' => $userId,
                'created_at' => now(),
            ]);
        }
    }
}