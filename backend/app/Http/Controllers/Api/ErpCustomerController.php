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
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'commission_type' => 'nullable|string|in:monthly,per_order',
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
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'commission_type' => 'nullable|string|in:monthly,per_order',
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
                    'commission_rate' => $item['commission_rate'] ?? 0,
                    'commission_type' => $item['commission_type'] ?? 'monthly',
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

    /**
     * Bảng tổng hợp doanh số và chốt chiết khấu / hoa hồng định kỳ theo khách hàng
     */
    public function commissionStatement(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $tag = $request->input('tag');
        $search = trim($request->input('search', ''));

        $startDate = "{$year}-{$month}-01 00:00:00";
        $endDate = date('Y-m-t 23:59:59', strtotime($startDate));

        $customersQuery = ErpCustomer::query();

        if (!empty($tag)) {
            $customersQuery->where('tag', $tag);
        }

        if (!empty($search)) {
            $customersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        // Lấy danh sách khách hàng và kèm các đơn hàng trong tháng
        $customers = $customersQuery->with(['orders' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate])
              ->where('status', '!=', 'cancelled')
              ->select([
                  'id', 'code', 'customer_id', 'subtotal', 'discount', 'discount_mode',
                  'total_amount', 'paid_amount', 'commission_rate_snapshot',
                  'commission_amount', 'commission_status', 'status', 'created_at'
              ]);
        }])->get();

        $statements = $customers->map(function ($c) use ($month, $year) {
            $orders = $c->orders;
            $orderCount = $orders->count();

            // Tổng tiền in (subtotal) và tổng tiền khách trả
            $totalSubtotal = (float) $orders->sum('subtotal');
            $totalBilled = (float) $orders->sum('total_amount');
            $totalPaid = (float) $orders->sum('paid_amount');

            // Tính hoa hồng / chiết khấu:
            // 1. Hoa hồng đã được lưu từ từng đơn (nếu có)
            $recordedCommission = (float) $orders->where('discount_mode', 'monthly_commission')->sum('commission_amount');
            
            // 2. Nếu khách có cài tỷ lệ commission_rate mà đơn chưa tính riêng thì tính theo tổng subtotal
            $rate = (float) ($c->commission_rate ?? 0);
            $calculatedCommission = $recordedCommission > 0 ? $recordedCommission : round(($totalSubtotal * $rate) / 100);

            // Đơn có chiết khấu trừ trực tiếp
            $directDiscount = (float) $orders->where('discount_mode', 'direct')->sum('discount');

            return [
                'customer_id' => $c->id,
                'customer_name' => $c->name,
                'customer_phone' => $c->phone,
                'company_name' => $c->company_name,
                'tag' => $c->tag,
                'commission_rate' => $rate,
                'commission_type' => $c->commission_type ?? 'monthly',
                'order_count' => $orderCount,
                'total_subtotal' => $totalSubtotal,
                'total_billed' => $totalBilled,
                'total_paid' => $totalPaid,
                'direct_discount' => $directDiscount,
                'monthly_commission' => $calculatedCommission,
                'month' => (int) $month,
                'year' => (int) $year,
                'orders' => $orders
            ];
        });

        // Lọc ra các khách có đơn hàng trong tháng hoặc có cài tỷ lệ hoa hồng
        $statements = $statements->filter(function ($item) {
            return $item['order_count'] > 0 || $item['commission_rate'] > 0;
        })->values();

        $summary = [
            'total_customers' => $statements->count(),
            'total_orders' => $statements->sum('order_count'),
            'total_subtotal' => $statements->sum('total_subtotal'),
            'total_billed' => $statements->sum('total_billed'),
            'total_commission' => $statements->sum('monthly_commission'),
            'month' => (int) $month,
            'year' => (int) $year
        ];

        return response()->json([
            'status' => 'success',
            'data' => $statements,
            'summary' => $summary
        ]);
    }
}

