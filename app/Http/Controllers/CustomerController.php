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

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer removed.');
    }
}