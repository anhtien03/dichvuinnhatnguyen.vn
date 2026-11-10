<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ErpCashbookEntry;
use App\Models\ErpOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ErpCashbookController extends Controller
{
    public function index(Request $request)
    {
        $query = ErpCashbookEntry::with('order');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('funding_owner')) {
            $query->where('funding_owner', $request->funding_owner);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('entry_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('entry_date', '<=', $request->to_date);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('code', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhere('payer_recipient', 'like', "%{$s}%");
            });
        }

        $entries = $query->orderBy('entry_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($request->input('per_page', 25));

        return response()->json([
            'status' => 'success',
            'data' => $entries
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:receipt,payment',
            'category' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string|in:cash,bank_acb,bank_mb,other',
            'order_id' => 'nullable|integer',
            'funding_owner' => 'nullable|string|in:company,truong_hung,minh_nguyen',
            'payer_recipient' => 'nullable|string',
            'description' => 'required|string',
            'entry_date' => 'required|date',
        ]);

        // Auto generate code PT-YYMM-XXX or PC-YYMM-XXX
        $prefix = ($validated['type'] === 'receipt') ? 'PT' : 'PC';
        $period = date('ym');
        $last = ErpCashbookEntry::where('code', 'like', "{$prefix}-{$period}-%")->latest('id')->first();
        $seq = 1;
        if ($last && preg_match('/-(\d+)$/', $last->code, $m)) {
            $seq = (int)$m[1] + 1;
        }
        $code = sprintf("%s-%s-%03d", $prefix, $period, $seq);

        $entry = ErpCashbookEntry::create([
            'code' => $code,
            'type' => $validated['type'],
            'category' => $validated['category'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'order_id' => $validated['order_id'] ?? null,
            'funding_owner' => $validated['funding_owner'] ?? 'company',
            'payer_recipient' => $validated['payer_recipient'] ?? null,
            'description' => $validated['description'],
            'entry_date' => $validated['entry_date'],
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã tạo phiếu sổ quỹ',
            'data' => $entry->load('order')
        ], 201);
    }

    public function show($id)
    {
        $entry = ErpCashbookEntry::with(['order', 'creator'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $entry
        ]);
    }

    public function update(Request $request, $id)
    {
        $entry = ErpCashbookEntry::findOrFail($id);

        $validated = $request->validate([
            'category' => 'sometimes|required|string',
            'amount' => 'sometimes|required|numeric|min:1',
            'payment_method' => 'sometimes|required|string|in:cash,bank_acb,bank_mb,other',
            'order_id' => 'nullable|integer',
            'funding_owner' => 'nullable|string|in:company,truong_hung,minh_nguyen',
            'payer_recipient' => 'nullable|string',
            'description' => 'sometimes|required|string',
            'entry_date' => 'sometimes|required|date',
        ]);

        $entry->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã cập nhật phiếu sổ quỹ',
            'data' => $entry->load('order')
        ]);
    }

    public function destroy($id)
    {
        $entry = ErpCashbookEntry::findOrFail($id);
        $entry->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Đã xóa phiếu sổ quỹ'
        ]);
    }

    public function statistics(Request $request)
    {
        $fromDate = $request->input('from_date', date('Y-m-01'));
        $toDate = $request->input('to_date', date('Y-m-t'));

        $totalReceipts = ErpCashbookEntry::where('type', 'receipt')
            ->whereBetween('entry_date', [$fromDate, $toDate])
            ->sum('amount');

        $totalPayments = ErpCashbookEntry::where('type', 'payment')
            ->whereBetween('entry_date', [$fromDate, $toDate])
            ->sum('amount');

        $netBalance = $totalReceipts - $totalPayments;

        // Breakdown by method
        $methods = ErpCashbookEntry::select('payment_method', 'type', DB::raw('SUM(amount) as total'))
            ->whereBetween('entry_date', [$fromDate, $toDate])
            ->groupBy('payment_method', 'type')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'total_receipts' => (float)$totalReceipts,
                'total_payments' => (float)$totalPayments,
                'net_balance' => (float)$netBalance,
                'breakdown' => $methods
            ]
        ]);
    }
}

