<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use App\Models\FeedConsumption;
use App\Models\Supplier;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class FeedController extends Controller
{
    public function index()
    {
        $feeds = Feed::orderBy('purchase_date', 'desc')->paginate(10);
        $totalQuantity = Feed::sum('quantity') ?? 0;
        $totalCost = Feed::sum('cost') ?? 0;
        $totalConsumed = Schema::hasTable('feed_consumption') ? FeedConsumption::sum('quantity') ?? 0 : 0;

        return view('feed.index', compact('feeds', 'totalQuantity', 'totalCost', 'totalConsumed'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('feed.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|string|max:255',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'quantity' => 'required|integer|min:1',
            'weight' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'cost' => 'required|numeric|min:0',
        ]);

        $feed = Feed::create($data);

        UserActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'action' => 'created_feed',
            'details' => "Added feed '{$feed->type}' (Quantity: {$feed->quantity}, Cost: ₵{$feed->cost})",
        ]);

        return redirect()->route('feed.index')->with('success', 'Feed added successfully');
    }

    public function show(Feed $feed)
{
    return view('feed.show', compact('feed'));
}

    public function edit(Feed $feed)
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('feed.edit', compact('feed', 'suppliers'));
    }

    public function update(Request $request, Feed $feed)
    {
        $data = $request->validate([
            'type' => 'required|string|max:255',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'quantity' => 'required|integer|min:1',
            'weight' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'cost' => 'required|numeric|min:0',
        ]);

        $feed->update($data);

        UserActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'action' => 'updated_feed',
            'details' => "Updated feed '{$feed->type}' (Quantity: {$feed->quantity}, Cost: ₵{$feed->cost})",
        ]);

        return redirect()->route('feed.index')->with('success', 'Feed updated successfully');
    }

    public function destroy(Request $request, $id)
    {
        try {
            $feed = Feed::findOrFail($id);

            UserActivityLog::create([
                'user_id' => auth()->id() ?? 1,
                'action' => 'deleted_feed',
                'details' => "Deleted feed record of {$feed->quantity} bags of {$feed->type} costing ₵{$feed->cost} on {$feed->purchase_date}",
            ]);

            $feed->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Feed record deleted successfully.'
                ], 200);
            }

            return redirect()->route('feed.index')->with('success', 'Feed record deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete feed: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete feed record. ' . ($e->getCode() == 23000 ? 'This feed is linked to other data.' : 'Please try again.')
                ], 500);
            }

            return redirect()->route('feed.index')->with('error', 'Failed to delete feed record.');
        }
    }

    public function consumption()
    {
        $feeds = Feed::orderBy('type')->get();
        $consumptions = Schema::hasTable('feed_consumption') ? FeedConsumption::orderBy('date', 'desc')->paginate(10) : collect();
        $totalConsumed = Schema::hasTable('feed_consumption') ? FeedConsumption::sum('quantity') ?? 0 : 0;

       return view('feed.consumption.index', compact('feeds', 'consumptions', 'totalConsumed'));
    }

    public function consumptionCreate()
    {
        $feeds = Feed::orderBy('type')->get();
        return view('feed.consumption.create', compact('feeds'));
    }


    public function storeConsumption(Request $request)
    {
        if (!Schema::hasTable('feed_consumption')) {
            return redirect()->route('feed.consumption')->with('error', 'Feed consumption table not found. Please run migrations.');
        }

        $data = $request->validate([
            'feed_id' => 'required|exists:feed,id',
            'date' => 'required|date',
            'quantity' => 'required|numeric|min:0.01',
        ]);

        FeedConsumption::create($data);
        return redirect()->route('feed.consumption')->with('success', 'Feed consumption recorded successfully');
    }
}