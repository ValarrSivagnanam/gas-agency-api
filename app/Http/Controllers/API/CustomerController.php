<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index() {
        return response()->json(Customer::latest()->get(), 200);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:customers,phone',
            'address' => 'nullable|string',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        $customer = Customer::create($validated);
        return response()->json($customer, 201);
    }

    public function update(Request $request, $id) {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:customers,phone,' . $customer->id,
            'address' => 'nullable|string',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        $customer->update($validated);
        return response()->json($customer, 200);
    }

    public function destroy($id) {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return response()->json(['message' => 'Customer removed successfully'], 200);
    }

    public function transactions($id)
    {
        $customer = Customer::findOrFail($id);

        $transactions = $customer->transactions()
            ->with('cylinder:id,type')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'customer' => $customer,
            'transactions' => $transactions,
            'summary' => [
                'total_amount' => (float) $transactions->sum('amount'),
                'total_paid' => (float) $transactions->sum('paid_amount'),
                'total_unpaid' => (float) $transactions->sum('unpaid_amount'),
            ],
        ]);
    }
}