<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ErpCashbookEntry;
use App\Models\ErpOrder;
use App\Models\ErpOrderPayment;
use App\Models\ErpSupplierPayment;
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
            'payment_method' => 'required|string|in:cash,bank_acb,bank_mb,bank_minh_nguyen,bank_truong_hung,other',
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
            'payment_method' => 'sometimes|required|string|in:cash,bank_acb,bank_mb,bank_minh_nguyen,bank_truong_hung,other',
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

    private function accountCatalog(): array
    {
        return [
            'bank_acb' => ['label' => 'ACB (51189927 - CTY IN ẤN NHẬT NGUYÊN)', 'short_label' => 'ACB 51189927'],
            'bank_mb' => ['label' => 'MB Bank (679838699 - HUYNH THANH TRUC)', 'short_label' => 'MB Bank 679838699'],
            'bank_minh_nguyen' => ['label' => 'TK MINH NGUYEN', 'short_label' => 'TK MINH NGUYEN'],
            'bank_truong_hung' => ['label' => 'TK TRUONG HUNG', 'short_label' => 'TK TRUONG HUNG'],
            'cash' => ['label' => 'Tiền mặt tại cửa hàng', 'short_label' => 'Tiền mặt'],
            'other' => ['label' => 'Khác', 'short_label' => 'Khác'],
        ];
    }

    public function statistics(Request $request)
    {
        $fromDate = $request->input('from_date', date('Y-m-01'));
        $toDate = $request->input('to_date', date('Y-m-t'));
        $fromDateTime = $fromDate . ' 00:00:00';
        $toDateTime = $toDate . ' 23:59:59';

        $totalReceipts = ErpCashbookEntry::where('type', 'receipt')
            ->whereBetween('entry_date', [$fromDate, $toDate])
            ->sum('amount');

        $totalPayments = ErpCashbookEntry::where('type', 'payment')
            ->whereBetween('entry_date', [$fromDate, $toDate])
            ->sum('amount');

        $netBalance = $totalReceipts - $totalPayments;

        // Breakdown by method (cashbook entries)
        $methods = ErpCashbookEntry::select('payment_method', 'type', DB::raw('SUM(amount) as total'))
            ->whereBetween('entry_date', [$fromDate, $toDate])
            ->groupBy('payment_method', 'type')
            ->get();

        // Order payments (+ Thu từ khách hàng) grouped by payment_method
        $orderPaymentsByMethod = ErpOrderPayment::whereHas('order')
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->whereBetween('paid_at', [$fromDateTime, $toDateTime])
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method');

        // Supplier payments (- Chi trả xưởng gia công) grouped by payment_method
        $supplierPaymentsByMethod = ErpSupplierPayment::whereHas('supplier')
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->whereBetween('paid_at', [$fromDateTime, $toDateTime])
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method');

        $cashbookInByMethod = [];
        $cashbookOutByMethod = [];
        foreach ($methods as $row) {
            $pm = $row->payment_method ?: 'other';
            if ($row->type === 'receipt') {
                $cashbookInByMethod[$pm] = ($cashbookInByMethod[$pm] ?? 0) + (float) $row->total;
            } else {
                $cashbookOutByMethod[$pm] = ($cashbookOutByMethod[$pm] ?? 0) + (float) $row->total;
            }
        }

        $accounts = [];
        $combinedIn = 0.0;
        $combinedOut = 0.0;

        foreach ($this->accountCatalog() as $key => $meta) {
            $orderReceipts = (float) ($orderPaymentsByMethod[$key] ?? 0);
            $cashbookReceipts = (float) ($cashbookInByMethod[$key] ?? 0);
            $totalIn = $orderReceipts + $cashbookReceipts;

            $supplierPayments = (float) ($supplierPaymentsByMethod[$key] ?? 0);
            $cashbookPayments = (float) ($cashbookOutByMethod[$key] ?? 0);
            $totalOut = $supplierPayments + $cashbookPayments;

            $combinedIn += $totalIn;
            $combinedOut += $totalOut;

            $accounts[] = [
                'payment_method' => $key,
                'label' => $meta['label'],
                'short_label' => $meta['short_label'],
                'order_receipts' => $orderReceipts,
                'cashbook_receipts' => $cashbookReceipts,
                'total_in' => $totalIn,
                'supplier_payments' => $supplierPayments,
                'cashbook_payments' => $cashbookPayments,
                'total_out' => $totalOut,
                'net_balance' => $totalIn - $totalOut,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'total_receipts' => (float)$totalReceipts,
                'total_payments' => (float)$totalPayments,
                'net_balance' => (float)$netBalance,
                'combined_total_in' => $combinedIn,
                'combined_total_out' => $combinedOut,
                'combined_net_balance' => $combinedIn - $combinedOut,
                'breakdown' => $methods,
                'accounts' => $accounts,
            ]
        ]);
    }

    public function accountLedger(Request $request)
    {
        $validated = $request->validate([
            'payment_method' => 'required|string|in:bank_acb,bank_mb,bank_minh_nguyen,bank_truong_hung,cash,other',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
        ]);

        $method = $validated['payment_method'];
        $fromDate = $validated['from_date'] ?? date('Y-m-01');
        $toDate = $validated['to_date'] ?? date('Y-m-t');
        $fromDateTime = $fromDate . ' 00:00:00';
        $toDateTime = $toDate . ' 23:59:59';

        $items = [];

        $orderPayments = ErpOrderPayment::with('order:id,code,customer_name')
            ->whereHas('order')
            ->where('payment_method', $method)
            ->whereBetween('paid_at', [$fromDateTime, $toDateTime])
            ->get();

        foreach ($orderPayments as $p) {
            $items[] = [
                'id' => 'op_' . $p->id,
                'source' => 'order_payment',
                'source_label' => 'Thu đơn hàng',
                'direction' => 'in',
                'amount' => (float) $p->amount,
                'date' => $p->paid_at ? $p->paid_at->format('Y-m-d H:i') : $p->created_at->format('Y-m-d H:i'),
                'title' => 'Đơn ' . ($p->order->code ?? '#' . $p->order_id) . ($p->order->customer_name ? ' — ' . $p->order->customer_name : ''),
                'note' => $p->note ?: 'Thanh toán đơn hàng',
                'order_id' => $p->order_id,
            ];
        }

        $supplierPayments = ErpSupplierPayment::with('supplier:id,name')
            ->whereHas('supplier')
            ->where('payment_method', $method)
            ->whereBetween('paid_at', [$fromDateTime, $toDateTime])
            ->get();

        foreach ($supplierPayments as $sp) {
            $items[] = [
                'id' => 'sp_' . $sp->id,
                'source' => 'supplier_payment',
                'source_label' => 'Trả xưởng',
                'direction' => 'out',
                'amount' => (float) $sp->amount,
                'date' => $sp->paid_at ? $sp->paid_at->format('Y-m-d H:i') : $sp->created_at->format('Y-m-d H:i'),
                'title' => 'Trả xưởng: ' . ($sp->supplier->name ?? '#' . $sp->supplier_id),
                'note' => $sp->note ?: 'Thanh toán công nợ xưởng',
                'order_id' => null,
            ];
        }

        $cashbookEntries = ErpCashbookEntry::with('order:id,code')
            ->where('payment_method', $method)
            ->whereBetween('entry_date', [$fromDate, $toDate])
            ->get();

        foreach ($cashbookEntries as $cb) {
            $items[] = [
                'id' => 'cb_' . $cb->id,
                'source' => 'cashbook',
                'source_label' => $cb->type === 'receipt' ? 'Phiếu thu' : 'Phiếu chi',
                'direction' => $cb->type === 'receipt' ? 'in' : 'out',
                'amount' => (float) $cb->amount,
                'date' => $cb->entry_date ? \Carbon\Carbon::parse($cb->entry_date)->format('Y-m-d') : $cb->created_at->format('Y-m-d'),
                'title' => $cb->description . ($cb->order ? ' (Đơn ' . $cb->order->code . ')' : ''),
                'note' => $cb->payer_recipient ?: $cb->category,
                'order_id' => $cb->order_id,
            ];
        }

        usort($items, fn($a, $b) => strcmp($b['date'], $a['date']));

        return response()->json([
            'status' => 'success',
            'data' => [
                'payment_method' => $method,
                'account' => $this->accountCatalog()[$method] ?? ['label' => $method, 'short_label' => $method],
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'items' => $items,
            ]
        ]);
    }
}

