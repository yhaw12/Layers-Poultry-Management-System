<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function index()
    {
        $feeds = Feed::all();
        return view('feed.index', compact('feeds'));
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

    public function destroy(Feed $feed)
    {
        $feed->delete();
        return redirect()->route('feed.index')->with('success', 'Feed deleted successfully');
    }
}