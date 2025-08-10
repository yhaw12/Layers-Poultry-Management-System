<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Expense;
use App\Models\Inventory;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = $request->query('q', '');
            if (empty($query)) {
                return response()->json([]);
            }

            $results = [];

            // Search Sales (example: search by customer name or sale ID)
            $sales = Sale::where('customer_name', 'like', "%$query%")
                ->orWhere('id', 'like', "%$query%")
                ->take(5)
                ->get()
                ->map(function ($sale) {
                    return [
                        'id' => $sale->id,
                        'type' => 'sale',
                        'name' => "Sale #{$sale->id} - {$sale->customer_name}",
                        'url' => route('sales.show', $sale->id),
                    ];
                });

            // Search Expenses (example: search by description)
            $expenses = Expense::where('description', 'like', "%$query%")
                ->take(5)
                ->get()
                ->map(function ($expense) {
                    return [
                        'id' => $expense->id,
                        'type' => 'expense',
                        'name' => "Expense: {$expense->description}",
                        'url' => route('expenses.show', $expense->id),
                    ];
                });

            // Search Inventory (example: search by item name)
            $inventory = Inventory::where('item_name', 'like', "%$query%")
                ->take(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'inventory',
                        'name' => "Inventory: {$item->item_name}",
                        'url' => route('inventory.show', $item->id),
                    ];
                });

            // Combine results
            $results = collect($sales)->merge($expenses)->merge($inventory)->take(10)->toArray();

            return response()->json($results);
        } catch (\Exception $e) {
            \Log::error('Search failed: ' . $e->getMessage());
            return response()->json(['error' => 'Search failed'], 500);
        }
    }
}
