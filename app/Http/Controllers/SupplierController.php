<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    public function index()
    {
        try {
            $suppliers = Supplier::whereNull('deleted_at')->orderBy('name')->paginate(10);
            return view('suppliers.index', compact('suppliers'));
        } catch (\Exception $e) {
            Log::error('Failed to load suppliers', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to load suppliers.');
        }
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'contact' => 'nullable|string|max:20',
                'email' => 'nullable|email',
            ]);

            $supplier = Supplier::create($validated);

            \App\Models\UserActivityLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => 'created_supplier',
                'details' => "Created supplier {$validated['name']}",
            ]);

            return redirect()->route('suppliers.index')->with('success', 'Supplier added.');
        } catch (\Exception $e) {
            Log::error('Failed to store supplier', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to add supplier.');
        }
    }

    public function destroy($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);

            // Optional: Authorization check
            // $this->authorize('delete', $supplier);

            $supplier->delete();

            \App\Models\UserActivityLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => 'deleted_supplier',
                'details' => "Deleted supplier ID {$id}",
            ]);

            return response()->json([
                'message' => 'Supplier deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete supplier', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'supplier_id' => $id,
                'user_id' => Auth::id() ?? null,
            ]);

            return response()->json([
                'message' => 'Failed to delete supplier. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

