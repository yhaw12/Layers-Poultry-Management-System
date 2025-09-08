<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Feed;
use App\Models\Inventory;
use App\Models\MedicineLog;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
   public function index()
    {
        // Fetch paginated inventory items
        $items = Inventory::paginate(10);

        // Inventory low stock
        $lowInventory = Inventory::where('qty', '<', DB::raw('threshold'))
            ->get(['id', 'name', 'qty', 'threshold', DB::raw('"Inventory" as type')]);

        /**
         * Feed low stock
         * - feed table uses supplier_id now; fetch supplier name via left join
         * - feed table has no 'threshold' column so we use a default threshold (10)
         *   — change FEED_THRESHOLD to whatever makes sense for your operations.
         */
        $FEED_THRESHOLD = 10;

        $lowFeed = Feed::leftJoin('suppliers', 'feed.supplier_id', '=', 'suppliers.id')
            ->where('feed.quantity', '<', $FEED_THRESHOLD)
            ->get([
                'feed.id',
                DB::raw('COALESCE(suppliers.name, "Unknown Supplier") as name'),
                DB::raw('feed.quantity as qty'),
                DB::raw((int) $FEED_THRESHOLD . ' as threshold'),
                DB::raw('"Feed" as type'),
            ]);

        // Medicine low stock (aggregated)
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

        // Combined low-stock items (Inventory + Feed + Medicine)
        $combinedLow = $lowInventory->concat($lowFeed)->concat($lowMedicine);

        // Deduplicate by name (case-insensitive) to avoid duplicate alerts for the same-name items across sources.
        $lowStockItems = $combinedLow
            ->filter(function ($it) {
                return isset($it->name) && trim($it->name) !== '';
            })
            ->unique(function ($it) {
                return strtolower(trim($it->name));
            })
            ->values();

        // counts/totals for the dashboard
        $inventoryTotal   = $items->total();
        $lowInventoryCount = $lowInventory->count();
        $lowFeedCount      = $lowFeed->count();
        $lowMedicineCount  = $lowMedicine->count();
        $lowStockCount     = $lowStockItems->count(); // deduped combined count

        return view('inventory.index', compact(
            'items',
            'lowStockItems',
            'inventoryTotal',
            'lowInventoryCount',
            'lowFeedCount',
            'lowMedicineCount',
            'lowStockCount'
        ));
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
            'threshold' => 'required|integer|min:0',
        ]);

        $inventory = Inventory::create($req->only('name', 'sku', 'qty', 'threshold'));

        // Check for low stock and create alert if needed
        if ($inventory->qty <= $inventory->threshold) {
            $this->createLowStockAlert('Inventory', $inventory->name, $inventory->qty, $inventory->threshold, Auth::id());
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
            $this->createLowStockAlert('Inventory', $inventory->name, $inventory->qty, $inventory->threshold, Auth::id());
        }

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory item updated.');
    }

    public function destroy(Request $request, $id)
    {
        try {
            $item = Inventory::findOrFail($id);

            // Log the activity (if applicable)
            UserActivityLog::create([
                'user_id' => auth()->id() ?? 1,
                'action' => 'deleted_inventory_item',
                'details' => "Deleted inventory item {$item->name} (SKU: {$item->sku}) with quantity {$item->qty}",
            ]);

            // Delete the inventory item (soft delete if enabled)
            $item->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Inventory item deleted successfully.'
                ], 200);
            }

            return redirect()->route('inventory.index')->with('success', 'Inventory item deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete inventory item: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete inventory item. ' . ($e->getCode() == 23000 ? 'This item is linked to other data.' : 'Please try again.')
                ], 500);
            }

            return redirect()->route('inventory.index')->with('error', 'Failed to delete inventory item.');
        }
    }

     public function lowStock()
    {
        $lowStockItems = collect();

        // Inventory low stock
        $lowInventory = Inventory::where('qty', '<', DB::raw('threshold'))
            ->get(['id', 'name', 'qty', 'threshold', DB::raw('"Inventory" as type')]);
        $lowStockItems = $lowStockItems->concat($lowInventory);

        // Feed low stock (same approach as index)
        $FEED_THRESHOLD = 10;

        $lowFeed = Feed::leftJoin('suppliers', 'feed.supplier_id', '=', 'suppliers.id')
            ->where('feed.quantity', '<', $FEED_THRESHOLD)
            ->get([
                'feed.id',
                DB::raw('COALESCE(suppliers.name, "Unknown Supplier") as name'),
                DB::raw('feed.quantity as qty'),
                DB::raw((int) $FEED_THRESHOLD . ' as threshold'),
                DB::raw('"Feed" as type'),
            ]);
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