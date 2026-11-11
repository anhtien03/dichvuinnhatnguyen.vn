<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ErpOrder;
use App\Models\ErpOrderItem;
use App\Models\ErpOrderPayment;
use App\Models\ErpCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ErpOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = ErpOrder::with(['items', 'customer']);

        // Search code, customer name, phone
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('code', 'like', "%{$s}%")
                  ->orWhere('customer_name', 'like', "%{$s}%")
                  ->orWhere('customer_phone', 'like', "%{$s}%")
                  ->orWhere('invoice_number', 'like', "%{$s}%");
            });
        }

        // Filter by production stage
        if ($request->filled('production_stage')) {
            $query->where('production_stage', $request->production_stage);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by VAT invoice
        if ($request->filled('invoice_status')) {
            $query->where('invoice_status', $request->invoice_status);
        }
        if ($request->boolean('is_vat_required')) {
            $query->where('is_vat_required', true);
        }

        // Delete requests only
        if ($request->boolean('delete_requested')) {
            $query->where('delete_requested', true);
        }

        // Date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $orders = $query->orderBy('id', 'desc')->paginate($request->input('per_page', 25));

        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    public function generateNextCode()
    {
        $dateStr = date('dmY'); // e.g. 07112026
        $prefix = "NN-{$dateStr}-";

        $latestOrder = ErpOrder::where('code', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($latestOrder && preg_match('/-(\d+)$/', $latestOrder->code, $matches)) {
            $nextNum = str_pad((int)$matches[1] + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '001';
        }

        return response()->json([
            'status' => 'success',
            'code' => $prefix . $nextNum
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:50',
            'customer_address' => 'nullable|string',
            'customer_company' => 'nullable|string',
            'customer_tax_code' => 'nullable|string',
            'printer_name' => 'nullable|string',
            'followers' => 'nullable|string',
            'notes' => 'nullable|string',

            // Financial & shipping
            'vat_rate' => 'nullable|numeric|min:0',
            'shipping_fee_customer' => 'nullable|numeric|min:0',
            'shipping_fee_shop' => 'nullable|numeric|min:0',
            'surcharge' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',

            // VAT invoice flag
            'is_vat_required' => 'nullable|boolean',
            'invoice_number' => 'nullable|string',

            // Items
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'nullable|string',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_type' => 'nullable|string',
            'items.*.discount_value' => 'nullable|numeric|min:0',
            'items.*.supplier_id' => 'nullable|integer',
            'items.*.supplier_name' => 'nullable|string',
            'items.*.cost_price' => 'nullable|numeric|min:0',
            'items.*.vat_input_rate' => 'nullable|numeric|min:0',
            'items.*.vat_input_deductible' => 'nullable|boolean',
            'items.*.specifications' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            // Auto sync customer
            $customer = ErpCustomer::firstOrCreate(
                ['phone' => trim($validated['customer_phone'])],
                [
                    'name' => $validated['customer_name'],
                    'address' => $validated['customer_address'] ?? null,
                    'company_name' => $validated['customer_company'] ?? null,
                    'tax_code' => $validated['customer_tax_code'] ?? null,
                    'tag' => 'regular'
                ]
            );

            // Generate unique code
            $dateStr = date('dmY');
            $prefix = "NN-{$dateStr}-";
            $latestOrder = ErpOrder::where('code', 'like', "{$prefix}%")->lockForUpdate()->latest('id')->first();
            $nextNum = ($latestOrder && preg_match('/-(\d+)$/', $latestOrder->code, $m))
                ? str_pad((int)$m[1] + 1, 3, '0', STR_PAD_LEFT)
                : '001';
            $orderCode = $prefix . $nextNum;

            // Calculate totals from items
            $subtotal = 0;
            $costAmount = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $qty = (float) $item['quantity'];
                $price = (float) $item['unit_price'];
                $rowSubtotal = $qty * $price;

                // row discount
                $rowDisc = 0;
                if (!empty($item['discount_type']) && $item['discount_type'] === 'percent') {
                    $rowDisc = $rowSubtotal * ((float) ($item['discount_value'] ?? 0) / 100);
                } elseif (!empty($item['discount_type']) && $item['discount_type'] === 'fixed') {
                    $rowDisc = (float) ($item['discount_value'] ?? 0);
                }
                $rowFinalSubtotal = max(0, $rowSubtotal - $rowDisc);
                $subtotal += $rowFinalSubtotal;

                // cost
                $costPrice = (float) ($item['cost_price'] ?? 0);
                $costTotal = $qty * $costPrice;
                $costAmount += $costTotal;

                $itemsData[] = [
                    'product_name' => $item['product_name'],
                    'quantity' => $qty,
                    'unit' => $item['unit'] ?? 'Cái',
                    'unit_price' => $price,
                    'subtotal' => $rowFinalSubtotal,
                    'discount_type' => $item['discount_type'] ?? 'none',
                    'discount_value' => (float) ($item['discount_value'] ?? 0),
                    'supplier_id' => $item['supplier_id'] ?? null,
                    'supplier_name' => $item['supplier_name'] ?? null,
                    'cost_price' => $costPrice,
                    'cost_total' => $costTotal,
                    'vat_input_rate' => (float) ($item['vat_input_rate'] ?? 0),
                    'vat_input_amount' => $costTotal * ((float) ($item['vat_input_rate'] ?? 0) / 100),
                    'vat_input_deductible' => (bool) ($item['vat_input_deductible'] ?? false),
                    'specifications' => $item['specifications'] ?? null,
                ];
            }

            $orderDiscount = (float) ($validated['discount'] ?? 0);
            $vatRate = (float) ($validated['vat_rate'] ?? 0);
            $vatAmount = ($subtotal - $orderDiscount) * ($vatRate / 100);
            $shipCustomer = (float) ($validated['shipping_fee_customer'] ?? 0);
            $shipShop = (float) ($validated['shipping_fee_shop'] ?? 0);
            $surcharge = (float) ($validated['surcharge'] ?? 0);

            $totalAmount = max(0, ($subtotal - $orderDiscount) + $vatAmount + $shipCustomer + $surcharge);
            $profitAmount = ($subtotal + $surcharge) - $costAmount - $orderDiscount - $shipShop;
            $paidAmount = (float) ($validated['paid_amount'] ?? 0);

            $paymentStatus = 'unpaid';
            if ($paidAmount >= $totalAmount && $totalAmount > 0) {
                $paymentStatus = 'paid';
            } elseif ($paidAmount > 0) {
                $paymentStatus = 'partial';
            }

            $order = ErpOrder::create([
                'code' => $orderCode,
                'customer_id' => $customer->id,
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_address' => $validated['customer_address'] ?? null,
                'customer_company' => $validated['customer_company'] ?? null,
                'customer_tax_code' => $validated['customer_tax_code'] ?? null,
                'production_stage' => 'cho_duyet_file',
                'status' => 'in_progress',
                'printer_name' => $validated['printer_name'] ?? null,
                'followers' => $validated['followers'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $orderDiscount,
                'vat_rate' => $vatRate,
                'vat_amount' => $vatAmount,
                'shipping_fee_customer' => $shipCustomer,
                'shipping_fee_shop' => $shipShop,
                'surcharge' => $surcharge,
                'total_amount' => $totalAmount,
                'cost_amount' => $costAmount,
                'profit_amount' => $profitAmount,
                'paid_amount' => $paidAmount,
                'payment_status' => $paymentStatus,
                'is_vat_required' => (bool) ($validated['is_vat_required'] ?? false),
                'invoice_number' => $validated['invoice_number'] ?? null,
                'invoice_status' => (!empty($validated['invoice_number']) ? 'issued' : (($validated['is_vat_required'] ?? false) ? 'pending' : 'none')),
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($itemsData as $row) {
                $order->items()->create($row);
            }

            // If initial deposit / payment given, record payment entry
            if ($paidAmount > 0) {
                $order->payments()->create([
                    'amount' => $paidAmount,
                    'payment_method' => $validated['payment_method'] ?? 'bank_acb',
                    'funding_owner' => 'company',
                    'note' => 'Khách đặt cọc / thanh toán khi tạo đơn',
                    'paid_at' => now(),
                    'created_by' => auth()->id(),
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => "Đã tạo đơn hàng thành công ({$orderCode})",
                'data' => $order->load('items', 'payments', 'customer')
            ], 201);
        });
    }

    public function show($id)
    {
        $order = ErpOrder::with(['items.supplier', 'payments', 'cashbookEntries', 'customer'])
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $order
        ]);
    }

    public function update(Request $request, $id)
    {
        $order = ErpOrder::findOrFail($id);

        $validated = $request->validate([
            'customer_name' => 'sometimes|required|string|max:255',
            'customer_phone' => 'sometimes|required|string|max:50',
            'customer_address' => 'nullable|string',
            'customer_company' => 'nullable|string',
            'customer_tax_code' => 'nullable|string',
            'printer_name' => 'nullable|string',
            'followers' => 'nullable|string',
            'notes' => 'nullable|string',
            'production_stage' => 'nullable|string',
            'is_vat_required' => 'nullable|boolean',
            'invoice_number' => 'nullable|string',
            'invoice_date' => 'nullable|date',
            'invoice_status' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'vat_rate' => 'nullable|numeric|min:0',
            'shipping_fee_customer' => 'nullable|numeric|min:0',
            'shipping_fee_shop' => 'nullable|numeric|min:0',
            'surcharge' => 'nullable|numeric|min:0',
            'items' => 'nullable|array',
        ]);

        return DB::transaction(function () use ($order, $validated) {
            if (isset($validated['items'])) {
                // Remove existing items and recreate
                $order->items()->delete();
                $subtotal = 0;
                $costAmount = 0;

                foreach ($validated['items'] as $item) {
                    $qty = (float) $item['quantity'];
                    $price = (float) $item['unit_price'];
                    $rowSubtotal = $qty * $price;

                    $rowDisc = 0;
                    if (!empty($item['discount_type']) && $item['discount_type'] === 'percent') {
                        $rowDisc = $rowSubtotal * ((float) ($item['discount_value'] ?? 0) / 100);
                    } elseif (!empty($item['discount_type']) && $item['discount_type'] === 'fixed') {
                        $rowDisc = (float) ($item['discount_value'] ?? 0);
                    }
                    $rowFinalSubtotal = max(0, $rowSubtotal - $rowDisc);
                    $subtotal += $rowFinalSubtotal;

                    $costPrice = (float) ($item['cost_price'] ?? 0);
                    $costTotal = $qty * $costPrice;
                    $costAmount += $costTotal;

                    $order->items()->create([
                        'product_name' => $item['product_name'],
                        'quantity' => $qty,
                        'unit' => $item['unit'] ?? 'Cái',
                        'unit_price' => $price,
                        'subtotal' => $rowFinalSubtotal,
                        'discount_type' => $item['discount_type'] ?? 'none',
                        'discount_value' => (float) ($item['discount_value'] ?? 0),
                        'supplier_id' => $item['supplier_id'] ?? null,
                        'supplier_name' => $item['supplier_name'] ?? null,
                        'cost_price' => $costPrice,
                        'cost_total' => $costTotal,
                        'vat_input_rate' => (float) ($item['vat_input_rate'] ?? 0),
                        'vat_input_amount' => $costTotal * ((float) ($item['vat_input_rate'] ?? 0) / 100),
                        'vat_input_deductible' => (bool) ($item['vat_input_deductible'] ?? false),
                        'specifications' => $item['specifications'] ?? null,
                    ]);
                }

                $order->subtotal = $subtotal;
                $order->cost_amount = $costAmount;
            }

            if (isset($validated['discount'])) $order->discount = (float) $validated['discount'];
            if (isset($validated['vat_rate'])) $order->vat_rate = (float) $validated['vat_rate'];
            if (isset($validated['shipping_fee_customer'])) $order->shipping_fee_customer = (float) $validated['shipping_fee_customer'];
            if (isset($validated['shipping_fee_shop'])) $order->shipping_fee_shop = (float) $validated['shipping_fee_shop'];
            if (isset($validated['surcharge'])) $order->surcharge = (float) $validated['surcharge'];

            // Recalculate
            $order->vat_amount = ($order->subtotal - $order->discount) * ($order->vat_rate / 100);
            $order->total_amount = max(0, ($order->subtotal - $order->discount) + $order->vat_amount + $order->shipping_fee_customer + $order->surcharge);
            $order->profit_amount = ($order->subtotal + $order->surcharge) - $order->cost_amount - $order->discount - $order->shipping_fee_shop;

            // Recalculate paid amount from payment rows
            $paidTotal = (float) $order->payments()->sum('amount');
            $order->paid_amount = $paidTotal;
            if ($paidTotal >= $order->total_amount && $order->total_amount > 0) {
                $order->payment_status = 'paid';
            } elseif ($paidTotal > 0) {
                $order->payment_status = 'partial';
            } else {
                $order->payment_status = 'unpaid';
            }

            if (isset($validated['customer_name'])) $order->customer_name = $validated['customer_name'];
            if (isset($validated['customer_phone'])) $order->customer_phone = $validated['customer_phone'];
            if (isset($validated['customer_address'])) $order->customer_address = $validated['customer_address'];
            if (isset($validated['customer_company'])) $order->customer_company = $validated['customer_company'];
            if (isset($validated['customer_tax_code'])) $order->customer_tax_code = $validated['customer_tax_code'];
            if (isset($validated['printer_name'])) $order->printer_name = $validated['printer_name'];
            if (isset($validated['followers'])) $order->followers = $validated['followers'];
            if (isset($validated['notes'])) $order->notes = $validated['notes'];
            if (isset($validated['production_stage'])) $order->production_stage = $validated['production_stage'];
            if (isset($validated['is_vat_required'])) $order->is_vat_required = $validated['is_vat_required'];
            if (isset($validated['invoice_number'])) $order->invoice_number = $validated['invoice_number'];
            if (isset($validated['invoice_date'])) $order->invoice_date = $validated['invoice_date'];
            if (isset($validated['invoice_status'])) $order->invoice_status = $validated['invoice_status'];

            $order->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Đã cập nhật đơn hàng',
                'data' => $order->load('items', 'payments', 'customer')
            ]);
        });
    }

    public function updateStage(Request $request, $id)
    {
        $order = ErpOrder::findOrFail($id);

        $validated = $request->validate([
            'stage' => 'required|string|in:cho_duyet_file,thiet_ke,gui_file_in,dang_in,gia_cong,da_in_xong,dang_giao,hoan_thanh'
        ]);

        $order->production_stage = $validated['stage'];
        if ($validated['stage'] === 'hoan_thanh') {
            $order->status = 'completed';
        }
        $order->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Đã chuyển giai đoạn sản xuất',
            'data' => $order
        ]);
    }

    public function addPayment(Request $request, $id)
    {
        $order = ErpOrder::findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'nullable|string|in:bank_acb,bank_mb,bank_minh_nguyen,bank_truong_hung,cash,other',
            'funding_owner' => 'nullable|string|in:company,truong_hung,minh_nguyen',
            'note' => 'nullable|string',
            'paid_at' => 'nullable|date',
        ]);

        $order->payments()->create([
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'] ?? 'bank_acb',
            'funding_owner' => $validated['funding_owner'] ?? 'company',
            'note' => $validated['note'] ?? 'Thanh toán đợt / tiền cọc',
            'paid_at' => $validated['paid_at'] ?? now(),
            'created_by' => auth()->id(),
        ]);

        $newPaidTotal = (float) $order->payments()->sum('amount');
        $order->paid_amount = $newPaidTotal;
        if ($newPaidTotal >= $order->total_amount && $order->total_amount > 0) {
            $order->payment_status = 'paid';
        } elseif ($newPaidTotal > 0) {
            $order->payment_status = 'partial';
        }
        $order->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Đã thêm lượt thanh toán',
            'data' => $order->load('payments')
        ]);
    }

    public function requestDelete(Request $request, $id)
    {
        $order = ErpOrder::findOrFail($id);
        $order->delete_requested = true;
        $order->delete_reason = $request->input('reason', 'Nhân viên yêu cầu hủy đơn');
        $order->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Đã gửi yêu cầu xóa đơn đến Quản Trị Viên'
        ]);
    }

    public function destroy($id)
    {
        $order = ErpOrder::findOrFail($id);
        $order->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Đã xóa đơn hàng thành công'
        ]);
    }

    public function vietQR($id, Request $request)
    {
        $order = ErpOrder::findOrFail($id);
        $bank = $request->input('bank', 'acb'); // acb hoặc mb

        // Remain balance to pay
        $amount = max(0, $order->total_amount - $order->paid_amount);
        $accountNo = $bank === 'mb' ? '679838699' : '51189927';
        $bankBin = $bank === 'mb' ? '970422' : '970416'; // MB: 970422, ACB: 970416
        $accountName = $bank === 'mb' ? 'HUYNH THANH TRUC' : 'CONG TY TNHH THIET KE IN AN NHAT NGUYEN';
        $memo = rawurlencode($order->code);

        $qrUrl = "https://img.vietqr.io/image/{$bankBin}-{$accountNo}-compact2.png?amount={$amount}&addInfo={$memo}&accountName=" . rawurlencode($accountName);

        return response()->json([
            'status' => 'success',
            'data' => [
                'order_code' => $order->code,
                'amount' => $amount,
                'bank' => $bank,
                'bank_name' => $bank === 'mb' ? 'MB Bank (Quân Đội)' : 'ACB (Á Châu)',
                'account_no' => $accountNo,
                'account_name' => $accountName,
                'memo' => $order->code,
                'qr_url' => $qrUrl
            ]
        ]);
    }
}

