<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('name')->paginate(10);
        return view('customers.index', compact('customers'));
    }
    public function show(Customer $customer)
        {
            $sales = Sale::where('customer_id', $customer->id)->with('saleable')->get();
            return view('customers.show', compact('customer', 'sales'));
        }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',  
            'phone' => 'required|string|max:20',
        ]);

        Customer::create($data);
        return redirect()->route('customers.index')
                         ->with('success', 'Customer added.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $customer->update($data);
        return redirect()->route('customers.index')
                         ->with('success', 'Customer updated.');
    }

    public function destroy(Request $request, $id)
    {
            try {
                $customer = Customer::findOrFail($id);
                $customer->delete();

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Customer deleted successfully.'
                    ], 200);
                }

                return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
            } catch (\Exception $e) {
                // Log::error('Failed to delete bird: ' . $e->getMessage());
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to delete customer. ' . ($e->getCode() == 23000 ? 'This record is linked to other data.' : 'Please try again.')
                    ], 500);
                }

            return redirect()->route('customers.index')->with('success', 'Customer removed.');
        }
   }
}