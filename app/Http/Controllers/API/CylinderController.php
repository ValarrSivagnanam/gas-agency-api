<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Cylinder;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CylinderController extends Controller
{
    public function index()
    {
        return response()->json(Cylinder::all(), 200);
    }

    // GST slab: domestic cylinders 5%, commercial cylinders 18%
    private function taxPercentFor(Cylinder $cylinder): float
    {
        return str_contains(strtolower($cylinder->type), 'domestic') ? 5.0 : 18.0;
    }

    public function updatePrice(Request $request, $id)
    {
        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
        ]);

        $cylinder = Cylinder::findOrFail($id);
        $cylinder->price = $validated['price'];
        $cylinder->save();

        return response()->json($cylinder, 200);
    }

    public function recordTransaction(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'action_type' => 'required|in:purchase_full,return_empty',
            'qty' => 'required|integer|min:1',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $cylinder = Cylinder::findOrFail($id);
        $customer = Customer::findOrFail($validated['customer_id']);
        $qty = $validated['qty'];

        if ($validated['action_type'] === 'purchase_full') {
            if ($cylinder->full_stock < $qty) {
                return response()->json(['message' => 'Insufficient full cylinder inventory available.'], 422);
            }
            $cylinder->full_stock -= $qty;
            $cylinder->empty_stock += $qty;
        } else {
            if ($cylinder->empty_stock < $qty) {
                return response()->json(['message' => 'Cannot return more empties than recorded pool.'], 422);
            }
            $cylinder->empty_stock -= $qty;
            $cylinder->full_stock += $qty;
        }

        // Billing only applies to a purchase; a return of empties has no charge.
        $unitPrice = (float) $cylinder->price;
        $discountPercent = (float) $customer->discount_percent;
        $taxPercent = $this->taxPercentFor($cylinder);

        if ($validated['action_type'] === 'purchase_full') {
            $discountedUnitPrice = $unitPrice * (1 - $discountPercent / 100);
            $taxableAmount = round($discountedUnitPrice * $qty, 2);
            $taxAmount = round($taxableAmount * $taxPercent / 100, 2);
            $totalAmount = round($taxableAmount + $taxAmount, 2);
        } else {
            $taxableAmount = 0;
            $taxAmount = 0;
            $totalAmount = 0;
        }

        if ($validated['paid_amount'] > $totalAmount) {
            throw ValidationException::withMessages([
                'paid_amount' => ['Paid amount cannot exceed the transaction total.'],
            ]);
        }

        $unpaid = round($totalAmount - $validated['paid_amount'], 2);
        $transaction = null;

        DB::transaction(function () use (
            $cylinder, $customer, $validated, $unitPrice, $discountPercent,
            $taxableAmount, $taxPercent, $taxAmount, $totalAmount, $unpaid, &$transaction
        ) {
            $cylinder->save();

            $transaction = Transaction::create([
                'customer_id' => $customer->id,
                'cylinder_id' => $cylinder->id,
                'action_type' => $validated['action_type'],
                'qty' => $validated['qty'],
                'unit_price' => $unitPrice,
                'discount_percent' => $discountPercent,
                'taxable_amount' => $taxableAmount,
                'tax_percent' => $taxPercent,
                'tax_amount' => $taxAmount,
                'amount' => $totalAmount,
                'paid_amount' => $validated['paid_amount'],
                'unpaid_amount' => $unpaid,
            ]);

            $customer->balance += $unpaid;
            $customer->save();
        });

        return response()->json([
            'cylinder' => $cylinder,
            'transaction' => $transaction,
            'customer_balance' => $customer->balance,
        ], 200);
    }
}