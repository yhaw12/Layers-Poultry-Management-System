<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use App\Models\FeedConsumption;
use Illuminate\Http\Request;

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

    public function destroy($id)
    {   
        $feed = Feed::findorFail($id);
        $feed->delete();
        return redirect()->route('feed.index')->with('success', 'Feed deleted successfully');
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