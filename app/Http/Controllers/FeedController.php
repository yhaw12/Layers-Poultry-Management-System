<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use App\Models\FeedConsumption;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FeedController extends Controller
{
    public function index()
    {
        $feeds = Feed::orderBy('purchase_date', 'desc')->paginate(10);
        $totalQuantity = Feed::sum('quantity') ?? 0;
        $totalCost = Feed::sum('cost') ?? 0;
        $totalConsumed = FeedConsumption::sum('quantity') ?? 0;

        return view('feed.index', compact('feeds', 'totalQuantity', 'totalCost', 'totalConsumed'));
    }

    public function create()
    {
        return view('feed.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'weight' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'cost' => 'required|numeric|min:0',
        ]);

        Feed::create($data);
        return redirect()->route('feed.index')->with('success', 'Feed added successfully');
    }

    public function edit(Feed $feed)
    {
        return view('feed.edit', compact('feed'));
    }

    public function update(Request $request, Feed $feed)
    {
        $data = $request->validate([
            'type' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'weight' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'cost' => 'required|numeric|min:0',
        ]);

        $feed->update($data);
        return redirect()->route('feed.index')->with('success', 'Feed updated successfully');
    }

    public function destroy(Request $request, $id)
    {
        try {
            $feed = Feed::findOrFail($id);

            // Log the activity
            UserActivityLog::create([
                'user_id' => auth()->id() ?? 1,
                'action' => 'deleted_feed',
                'details' => "Deleted feed record of {$feed->quantity} bags of {$feed->type} costing ₵{$feed->cost} on {$feed->purchase_date}",
            ]);

            // Delete the feed (soft delete if enabled)
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
        $consumptions = FeedConsumption::orderBy('date', 'desc')->paginate(10);
        $totalConsumed = FeedConsumption::sum('quantity') ?? 0;

        return view('feed.consumption', compact('feeds', 'consumptions', 'totalConsumed'));
    }

    public function storeConsumption(Request $request)
    {
        $data = $request->validate([
            'feed_id' => 'required|exists:feeds,id',
            'date' => 'required|date',
            'quantity' => 'required|numeric|min:0.01',
        ]);

        FeedConsumption::create($data);
        return redirect()->route('feed.consumption')->with('success', 'Feed consumption recorded successfully');
    }

    
}