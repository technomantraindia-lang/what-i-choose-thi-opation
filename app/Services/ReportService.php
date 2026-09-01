<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Inquiry;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Refund;
use App\Models\OrderReturn;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Resolve start and end Carbon dates from request input.
     */
    public function resolveDateRange(?string $preset, ?string $dateFrom, ?string $dateTo): array
    {
        $now = Carbon::now();

        switch ($preset) {
            case 'today':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                break;

            case 'yesterday':
                $start = $now->copy()->subDay()->startOfDay();
                $end = $now->copy()->subDay()->endOfDay();
                break;

            case 'last_7_days':
                $start = $now->copy()->subDays(6)->startOfDay();
                $end = $now->copy()->endOfDay();
                break;

            case 'last_30_days':
                $start = $now->copy()->subDays(29)->startOfDay();
                $end = $now->copy()->endOfDay();
                break;

            case 'last_month':
                $start = $now->copy()->subMonth()->startOfMonth();
                $end = $now->copy()->subMonth()->endOfMonth();
                break;

            case 'this_year':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                break;

            case 'custom':
                $start = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : $now->copy()->startOfMonth();
                $end = $dateTo ? Carbon::parse($dateTo)->endOfDay() : $now->copy()->endOfDay();
                if ($start->greaterThan($end)) {
                    $tmp = $start;
                    $start = $end->copy()->startOfDay();
                    $end = $tmp->copy()->endOfDay();
                }
                break;

            case 'this_month':
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfDay();
                $preset = 'this_month';
                break;
        }

        return [
            'preset' => $preset,
            'start' => $start,
            'end' => $end,
            'date_from' => $start->toDateString(),
            'date_to' => $end->toDateString(),
        ];
    }

    /**
     * Compute Sales & Overview Statistics for the date range.
     */
    public function getOverviewReport(array $range): array
    {
        $start = $range['start'];
        $end = $range['end'];
        $customerRoleId = Role::where('name', 'Customer')->value('id');

        $paidOrdersQuery = Order::where('pay_status', 'paid')->whereBetween('created_at', [$start, $end]);
        $ordersQuery = Order::whereBetween('created_at', [$start, $end]);

        $totalRevenue = (float) $paidOrdersQuery->sum('total');
        $totalOrders = (int) $ordersQuery->count();
        $deliveredOrders = (int) Order::whereBetween('created_at', [$start, $end])->where('status', 'delivered')->count();
        $pendingOrders = (int) Order::whereBetween('created_at', [$start, $end])->where('status', 'pending')->count();
        $cancelledOrders = (int) Order::whereBetween('created_at', [$start, $end])->where('status', 'cancelled')->count();
        
        $totalCustomers = (int) User::where('role_id', $customerRoleId)->whereBetween('created_at', [$start, $end])->count();
        $totalProducts = (int) Product::count();
        $lowStockCount = (int) Product::whereColumn('stock_qty', '<=', 'low_stock_qty')->count();

        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0.0;

        return [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'delivered_orders' => $deliveredOrders,
            'pending_orders' => $pendingOrders,
            'cancelled_orders' => $cancelledOrders,
            'total_customers' => $totalCustomers,
            'total_products' => $totalProducts,
            'low_stock_products' => $lowStockCount,
            'avg_order_value' => round($avgOrderValue, 2),
        ];
    }

    /**
     * Compute Profit & Margin Report.
     */
    public function getProfitReport(array $range): array
    {
        $start = $range['start'];
        $end = $range['end'];

        $orderItems = OrderItem::with('product')
            ->whereHas('order', function ($oq) use ($start, $end) {
                $oq->where('pay_status', 'paid')->whereBetween('created_at', [$start, $end]);
            })
            ->get();

        $revenue = 0.0;
        $cogs = 0.0;

        foreach ($orderItems as $item) {
            $itemRevenue = (float) ($item->line_total ?? ($item->qty * $item->price));
            $itemCost = (float) ($item->qty * ($item->product?->cost_price ?? 0.0));

            $revenue += $itemRevenue;
            $cogs += $itemCost;
        }

        $grossProfit = $revenue - $cogs;
        $grossMarginPct = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0.0;

        // Product Breakdown
        $productBreakdown = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.pay_status', 'paid')
            ->whereBetween('orders.created_at', [$start, $end])
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                'products.cost_price',
                DB::raw('SUM(order_items.qty) as units_sold'),
                DB::raw('SUM(order_items.qty * order_items.price) as revenue'),
                DB::raw('SUM(order_items.qty * COALESCE(products.cost_price, 0)) as cost')
            )
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.cost_price')
            ->orderByDesc('revenue')
            ->get()
            ->map(function ($row) {
                $profit = (float) ($row->revenue - $row->cost);
                $marginPct = $row->revenue > 0 ? ($profit / $row->revenue) * 100 : 0.0;
                return [
                    'id' => $row->id,
                    'name' => $row->name,
                    'sku' => $row->sku,
                    'units_sold' => (int) $row->units_sold,
                    'revenue' => (float) $row->revenue,
                    'cost' => (float) $row->cost,
                    'gross_profit' => round($profit, 2),
                    'margin_pct' => round($marginPct, 2),
                ];
            });

        return [
            'revenue' => round($revenue, 2),
            'cogs' => round($cogs, 2),
            'gross_profit' => round($grossProfit, 2),
            'gross_margin_pct' => round($grossMarginPct, 2),
            'product_breakdown' => $productBreakdown,
        ];
    }

    /**
     * Compute GST & HSN Breakdown Report.
     */
    public function getGstReport(array $range, ?string $hsn = null): array
    {
        $start = $range['start'];
        $end = $range['end'];

        $orders = Order::with(['items.product', 'user.addresses'])
            ->where('pay_status', 'paid')
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $totalTaxable = 0.0;
        $totalGst = 0.0;
        $totalCgst = 0.0;
        $totalSgst = 0.0;
        $totalIgst = 0.0;
        $missingLocationCount = 0;

        $sellerStateSetting = \App\Models\Setting::where('key', 'seller_state')->value('value')
            ?? config('app.seller_state', 'Gujarat');

        $hsnSummaryMap = [];

        foreach ($orders as $order) {
            $gstAmt = (float) $order->gst_amt;
            $totalAmt = (float) $order->total;
            $taxable = max($totalAmt - $gstAmt, 0.0);

            $totalTaxable += $taxable;
            $totalGst += $gstAmt;

            // Determine seller state
            $sellerState = $order->seller_state ?: $sellerStateSetting;

            // Determine customer state
            $customerState = $order->customer_state;
            if (! $customerState && $order->ship_addr) {
                $shipAddr = is_string($order->ship_addr) ? json_decode($order->ship_addr, true) : $order->ship_addr;
                $customerState = $shipAddr['state'] ?? null;
            }
            if (! $customerState && $order->bill_addr) {
                $billAddr = is_string($order->bill_addr) ? json_decode($order->bill_addr, true) : $order->bill_addr;
                $customerState = $billAddr['state'] ?? null;
            }
            if (! $customerState && $order->user && $order->user->addresses) {
                $defaultAddr = $order->user->addresses->first();
                $customerState = $defaultAddr?->state;
            }

            if ($sellerState && $customerState) {
                if (strcasecmp(trim($sellerState), trim($customerState)) === 0) {
                    // Intra-state sale (Same State) -> CGST + SGST
                    $cgst = $gstAmt / 2.0;
                    $sgst = $gstAmt / 2.0;
                    $igst = 0.0;
                } else {
                    // Inter-state sale (Different State) -> IGST
                    $cgst = 0.0;
                    $sgst = 0.0;
                    $igst = $gstAmt;
                }
            } else {
                // Missing location data: default to estimated intra-state CGST + SGST split
                $missingLocationCount++;
                $cgst = $gstAmt / 2.0;
                $sgst = $gstAmt / 2.0;
                $igst = 0.0;
            }

            $totalCgst += $cgst;
            $totalSgst += $sgst;
            $totalIgst += $igst;

            foreach ($order->items as $item) {
                $itemHsn = $item->product?->hsn_code ?? 'OTHER';
                if ($hsn && strcasecmp($itemHsn, $hsn) !== 0) {
                    continue;
                }

                $qty = (int) $item->qty;
                $itemRevenue = (float) ($item->qty * $item->price);

                if (! isset($hsnSummaryMap[$itemHsn])) {
                    $hsnSummaryMap[$itemHsn] = [
                        'hsn_code' => $itemHsn,
                        'total_qty' => 0,
                        'revenue' => 0.0,
                    ];
                }

                $hsnSummaryMap[$itemHsn]['total_qty'] += $qty;
                $hsnSummaryMap[$itemHsn]['revenue'] += $itemRevenue;
            }
        }

        return [
            'total_taxable' => round($totalTaxable, 2),
            'total_gst' => round($totalGst, 2),
            'total_cgst' => round($totalCgst, 2),
            'total_sgst' => round($totalSgst, 2),
            'total_igst' => round($totalIgst, 2),
            'missing_location_orders_count' => $missingLocationCount,
            'is_complete_location_data' => $missingLocationCount === 0,
            'hsn_summary' => array_values($hsnSummaryMap),
        ];
    }
}
