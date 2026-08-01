<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cylinder;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function dashboardSummary()
    {
        $cylinders = Cylinder::all();
        $todayStart = Carbon::today();
        $todayEnd = Carbon::today()->endOfDay();
        $todayTx = Transaction::whereBetween('created_at', [$todayStart, $todayEnd])->get();

        return response()->json([
            'cylinders' => $cylinders,
            'today' => [
                'full_sold' => (int) $todayTx->where('action_type', 'purchase_full')->sum('qty'),
                'empty_received' => (int) $todayTx->where('action_type', 'return_empty')->sum('qty'),
                'total_amount' => (float) $todayTx->sum('amount'),
                'total_paid' => (float) $todayTx->sum('paid_amount'),
                'total_unpaid' => (float) $todayTx->sum('unpaid_amount'),
            ],
        ]);
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ]);

        $from = Carbon::parse($validated['from'])->startOfDay();
        $to = Carbon::parse($validated['to'])->endOfDay();

        $transactions = Transaction::with(['customer:id,name,phone', 'cylinder:id,type'])
            ->whereBetween('created_at', [$from, $to])
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'transactions' => $transactions,
            'summary' => [
                'total_full_sold' => (int) $transactions->where('action_type', 'purchase_full')->sum('qty'),
                'total_empty_received' => (int) $transactions->where('action_type', 'return_empty')->sum('qty'),
                'total_amount' => (float) $transactions->sum('amount'),
                'total_paid' => (float) $transactions->sum('paid_amount'),
                'total_unpaid' => (float) $transactions->sum('unpaid_amount'),
            ],
        ]);
    }

    // Single transaction, for bill generation
    public function show($id)
    {
        $transaction = Transaction::with(['customer', 'cylinder'])->findOrFail($id);
        return response()->json($transaction, 200);
    }
}