<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cylinder;
use Illuminate\Http\Request;

class CylinderController extends Controller
{
    public function index() {
        return response()->json(Cylinder::all(), 200);
    }

    public function recordTransaction(Request $request, $id) {
        $request->validate([
            'action_type' => 'required|in:purchase_full,return_empty',
            'qty' => 'required|integer|min:1'
        ]);

        $cylinder = Cylinder::findOrFail($id);
        $qty = $request->qty;

        if ($request->action_type === 'purchase_full') {
            if ($cylinder->full_stock < $qty) {
                return response()->json(['message' => 'Insufficient full cylinder inventory available.'], 422);
            }
            // Business rule rule sequence: full items go down, empty casings incoming to store go up
            $cylinder->full_stock -= $qty;
            $cylinder->empty_stock += $qty;
        } else if ($request->action_type === 'return_empty') {
            if ($cylinder->empty_stock < $qty) {
                return response()->json(['message' => 'Cannot return more empties than recorded pool.'], 422);
            }
            // Alternative sequence: admin moves empty stock to refill center, bringing back full stocks
            $cylinder->empty_stock -= $qty;
            $cylinder->full_stock += $qty;
        }

        $cylinder->save();
        return response()->json($cylinder, 200);
    }
}
