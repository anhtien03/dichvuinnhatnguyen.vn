<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ErpOrder;
use App\Models\ErpCashbookEntry;
use App\Models\ErpSupplier;
use App\Models\ErpCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ErpFinanceController extends Controller
{
    public function dashboardOverview()
    {
        $today = date('Y-m-d');
        $thisMonthStart = date('Y-m-01');
        $thisMonthEnd = date('Y-m-t');

        // Orders metrics
        $todayRevenue = ErpOrder::whereDate('created_at', $today)->sum('total_amount');
        $monthRevenue = ErpOrder::whereBetween('created_at', [$thisMonthStart, $thisMonthEnd . ' 23:59:59'])->sum('total_amount');
        $monthProfit = ErpOrder::whereBetween('created_at', [$thisMonthStart, $thisMonthEnd . ' 23:59:59'])->sum('profit_amount');
        $monthOrdersCount = ErpOrder::whereBetween('created_at', [$thisMonthStart, $thisMonthEnd . ' 23:59:59'])->count();

        // Pending production stages
        $inProductionCount = ErpOrder::whereIn('production_stage', ['thiet_ke', 'gui_file_in', 'dang_in', 'gia_cong'])->count();
        $readyOrDeliveringCount = ErpOrder::whereIn('production_stage', ['da_in_xong', 'dang_giao'])->count();
        $unpaidCustomerDebt = ErpOrder::where('payment_status', '!=', 'paid')
            ->selectRaw('SUM(total_amount - paid_amount) as total_debt')
            ->value('total_debt') ?? 0;

        // 12 Months Chart Data
        $chartData = [];
        for ($i = 11; $i >= 0; $i--) {
            $monthDate = date('Y-m', strtotime("-{$i} months"));
            $mStart = "{$monthDate}-01";
            $mEnd = date('Y-m-t', strtotime($mStart));

            $rev = ErpOrder::whereBetween('created_at', [$mStart, $mEnd . ' 23:59:59'])->sum('total_amount');
            $cost = ErpOrder::whereBetween('created_at', [$mStart, $mEnd . ' 23:59:59'])->sum('cost_amount');
            $profit = ErpOrder::whereBetween('created_at', [$mStart, $mEnd . ' 23:59:59'])->sum('profit_amount');

            $chartData[] = [
                'month' => date('m/Y', strtotime($mStart)),
                'revenue' => (float)$rev,
                'cost' => (float)$cost,
                'profit' => (float)$profit,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'today_revenue' => (float)$todayRevenue,
                'month_revenue' => (float)$monthRevenue,
                'month_profit' => (float)$monthProfit,
                'month_orders_count' => $monthOrdersCount,
                'in_production_count' => $inProductionCount,
                'delivering_count' => $readyOrDeliveringCount,
                'customer_debt' => (float)$unpaidCustomerDebt,
                'chart_data' => $chartData,
            ]
        ]);
    }

    public function profitSharing(Request $request)
    {
        $fromDate = $request->input('from_date', date('Y-m-01'));
        $toDate = $request->input('to_date', date('Y-m-t'));

        // Orders in period
        $ordersQuery = ErpOrder::whereBetween('created_at', [$fromDate, $toDate . ' 23:59:59']);
        $totalRevenue = (float) $ordersQuery->sum('total_amount');
        $totalCost = (float) $ordersQuery->sum('cost_amount');
        $orderProfit = (float) $ordersQuery->sum('profit_amount');

        // General cashbook expenses in period (expenses not linked to order)
        $unlinkedExpenses = (float) ErpCashbookEntry::where('type', 'payment')
            ->whereNull('order_id')
            ->whereBetween('entry_date', [$fromDate, $toDate])
            ->sum('amount');

        $netProfit = max(0, $orderProfit - $unlinkedExpenses);
        $halfShare = $netProfit / 2;

        // Advances paid out-of-pocket by each partner in this period
        $hungAdvances = (float) ErpCashbookEntry::where('funding_owner', 'truong_hung')
            ->where('type', 'payment')
            ->whereBetween('entry_date', [$fromDate, $toDate])
            ->sum('amount');

        $nguyenAdvances = (float) ErpCashbookEntry::where('funding_owner', 'minh_nguyen')
            ->where('type', 'payment')
            ->whereBetween('entry_date', [$fromDate, $toDate])
            ->sum('amount');

        // Withdrawals already received by each partner
        $hungWithdrawals = (float) ErpCashbookEntry::where('category', 'advance')
            ->where('payer_recipient', 'like', '%Hưng%')
            ->whereBetween('entry_date', [$fromDate, $toDate])
            ->sum('amount');

        $nguyenWithdrawals = (float) ErpCashbookEntry::where('category', 'advance')
            ->where('payer_recipient', 'like', '%Nguyên%')
            ->whereBetween('entry_date', [$fromDate, $toDate])
            ->sum('amount');

        // Payout = 50% Share + Advances - Withdrawals
        $hungPayout = $halfShare + $hungAdvances - $hungWithdrawals;
        $nguyenPayout = $halfShare + $nguyenAdvances - $nguyenWithdrawals;

        return response()->json([
            'status' => 'success',
            'data' => [
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'total_revenue' => $totalRevenue,
                'total_cost' => $totalCost,
                'order_profit' => $orderProfit,
                'unlinked_expenses' => $unlinkedExpenses,
                'net_profit' => $netProfit,
                'partners' => [
                    'truong_hung' => [
                        'name' => 'Trường Hưng',
                        'share_50' => $halfShare,
                        'advances' => $hungAdvances,
                        'withdrawals' => $hungWithdrawals,
                        'final_payout' => $hungPayout,
                    ],
                    'minh_nguyen' => [
                        'name' => 'Minh Nguyên',
                        'share_50' => $halfShare,
                        'advances' => $nguyenAdvances,
                        'withdrawals' => $nguyenWithdrawals,
                        'final_payout' => $nguyenPayout,
                    ]
                ]
            ]
        ]);
    }
}

