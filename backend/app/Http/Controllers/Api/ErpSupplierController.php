<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ErpSupplier;
use App\Models\ErpSupplierPayment;
use App\Models\ErpOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ErpSupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = ErpSupplier::query();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('services', 'like', "%{$s}%");
            });
        }

        $suppliers = $query->withSum('orderItems as total_cost', 'cost_total')
            ->withSum('payments as total_paid', 'amount')
            ->orderBy('id', 'desc')
            ->get();

        // Calculate current debt balance: total_cost - total_paid
        $suppliers->transform(function ($item) {
            $cost = (float) ($item->total_cost ?? 0);
            $paid = (float) ($item->total_paid ?? 0);
            $item->balance = $cost - $paid;
            return $item;
        });

        return response()->json([
            'status' => 'success',
            'data' => $suppliers
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'services' => 'nullable|string',
            'bank_name' => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:100',
            'bank_owner' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $supplier = ErpSupplier::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã thêm nhà cung cấp / xưởng in',
            'data' => $supplier
        ], 201);
    }

    public function show($id)
    {
        $supplier = ErpSupplier::with([
            'payments' => function ($q) {
                $q->orderBy('paid_at', 'desc')->take(30);
            },
            'orderItems' => function ($q) {
                $q->with('order')->orderBy('id', 'desc')->take(30);
            }
        ])
        ->withSum('orderItems as total_cost', 'cost_total')
        ->withSum('payments as total_paid', 'amount')
        ->findOrFail($id);

        $cost = (float) ($supplier->total_cost ?? 0);
        $paid = (float) ($supplier->total_paid ?? 0);
        $supplier->balance = $cost - $paid;

        return response()->json([
            'status' => 'success',
            'data' => $supplier
        ]);
    }

    public function update(Request $request, $id)
    {
        $supplier = ErpSupplier::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'services' => 'nullable|string',
            'bank_name' => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:100',
            'bank_owner' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $supplier->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã cập nhật nhà cung cấp',
            'data' => $supplier
        ]);
    }

    public function destroy($id)
    {
        $supplier = ErpSupplier::findOrFail($id);
        $supplier->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Đã xóa nhà cung cấp'
        ]);
    }

    public function recordPayment(Request $request, $id)
    {
        $supplier = ErpSupplier::findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'nullable|string|in:bank_acb,bank_mb,bank_minh_nguyen,bank_truong_hung,cash,other',
            'funding_owner' => 'nullable|string|in:company,truong_hung,minh_nguyen',
            'note' => 'nullable|string',
            'paid_at' => 'nullable|date',
        ]);

        $payment = ErpSupplierPayment::create([
            'supplier_id' => $supplier->id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'] ?? 'bank_acb',
            'funding_owner' => $validated['funding_owner'] ?? 'company',
            'note' => $validated['note'] ?? 'Thanh toán tiền xưởng gia công',
            'paid_at' => $validated['paid_at'] ?? now(),
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã ghi nhận thanh toán công nợ xưởng',
            'data' => $payment
        ]);
    }
}

