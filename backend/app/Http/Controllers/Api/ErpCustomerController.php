<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ErpCustomer;
use App\Models\ErpOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ErpCustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = ErpCustomer::query();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('company_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('tag')) {
            $query->where('tag', $request->tag);
        }

        $customers = $query->withCount('orders')
            ->orderBy('id', 'desc')
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $customers
        ]);
    }

    public function search(Request $request)
    {
        $phone = trim($request->input('phone', ''));
        if (empty($phone)) {
            return response()->json(['status' => 'success', 'data' => null]);
        }

        $customer = ErpCustomer::where('phone', $phone)->first();
        if (!$customer) {
            // Also check latest order with that phone
            $latestOrder = ErpOrder::where('customer_phone', $phone)->latest('id')->first();
            if ($latestOrder) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'name' => $latestOrder->customer_name,
                        'phone' => $latestOrder->customer_phone,
                        'address' => $latestOrder->customer_address,
                        'company_name' => $latestOrder->customer_company,
                        'tax_code' => $latestOrder->customer_tax_code,
                        'tag' => 'regular'
                    ]
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $customer
        ]);
    }

    public function dailyCare(Request $request)
    {
        $days = (int) $request->input('days', 15);
        $thresholdDate = now()->subDays($days);

        // Find customers whose latest order was before thresholdDate or have never ordered
        $customers = ErpCustomer::select('erp_customers.*')
            ->addSelect([
                'last_order_date' => ErpOrder::select('created_at')
                    ->whereColumn('customer_phone', 'erp_customers.phone')
                    ->latest()
                    ->limit(1),
                'total_orders' => ErpOrder::selectRaw('count(*)')
                    ->whereColumn('customer_phone', 'erp_customers.phone'),
                'total_spent' => ErpOrder::selectRaw('coalesce(sum(total_amount), 0)')
                    ->whereColumn('customer_phone', 'erp_customers.phone')
            ])
            ->where(function ($q) use ($thresholdDate) {
                $q->whereDoesntHave('orders')
                  ->orWhere(function ($sub) use ($thresholdDate) {
                      $sub->whereRaw('(SELECT created_at FROM erp_orders WHERE customer_phone = erp_customers.phone ORDER BY created_at DESC LIMIT 1) <= ?', [$thresholdDate]);
                  });
            })
            ->orderByRaw('last_order_date ASC')
            ->limit(50)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $customers
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'company_name' => 'nullable|string|max:255',
            'tax_code' => 'nullable|string|max:100',
            'tag' => 'nullable|string|in:vip,regular,bulk,demanding,care_needed',
            'notes' => 'nullable|string',
        ]);

        $customer = ErpCustomer::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã thêm khách hàng thành công',
            'data' => $customer
        ], 201);
    }

    public function show($id)
    {
        $customer = ErpCustomer::with(['orders' => function ($q) {
            $q->orderBy('id', 'desc')->take(20);
        }])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $customer
        ]);
    }

    public function update(Request $request, $id)
    {
        $customer = ErpCustomer::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'company_name' => 'nullable|string|max:255',
            'tax_code' => 'nullable|string|max:100',
            'tag' => 'nullable|string|in:vip,regular,bulk,demanding,care_needed',
            'notes' => 'nullable|string',
        ]);

        $customer->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã cập nhật khách hàng',
            'data' => $customer
        ]);
    }

    public function destroy($id)
    {
        $customer = ErpCustomer::findOrFail($id);
        $customer->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Đã xóa khách hàng'
        ]);
    }

    public function import(Request $request)
    {
        $items = $request->input('customers', []);
        if (!is_array($items) || empty($items)) {
            return response()->json(['status' => 'error', 'message' => 'Dữ liệu nhập không hợp lệ'], 422);
        }

        $imported = 0;
        foreach ($items as $item) {
            if (empty($item['phone'])) continue;
            ErpCustomer::updateOrCreate(
                ['phone' => trim($item['phone'])],
                [
                    'name' => $item['name'] ?? 'Khách lẻ',
                    'email' => $item['email'] ?? null,
                    'address' => $item['address'] ?? null,
                    'company_name' => $item['company_name'] ?? null,
                    'tax_code' => $item['tax_code'] ?? null,
                    'tag' => $item['tag'] ?? 'regular',
                    'notes' => $item['notes'] ?? null,
                ]
            );
            $imported++;
        }

        return response()->json([
            'status' => 'success',
            'message' => "Đã nhập thành công {$imported} khách hàng",
            'imported_count' => $imported
        ]);
    }
}

