<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SalesAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // Get period filter (default: last 12 months)
        $period = $request->get('period', '12months');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Get chart data based on period
        $chartData = $this->getChartData($period, $dateFrom, $dateTo);

        // === STATISTICS ===

        // Current Period Stats
        $currentPeriodStats = $this->getCurrentPeriodStats($period, $dateFrom, $dateTo);

        // Previous Period Stats for comparison
        $previousPeriodStats = $this->getPreviousPeriodStats($period, $dateFrom, $dateTo);

        // Calculate Growth
        $growth = $this->calculateGrowth($currentPeriodStats, $previousPeriodStats);

        // === TOP PRODUCTS ===
        $topProducts = DB::table('view_top_selling_products')
            ->limit(10)
            ->get();

        // === SALES BY STATUS ===
        $salesByStatus = DB::table('view_order_status_summary')->get();

        // === MONTHLY COMPARISON ===
        $monthlyComparison = DB::table('view_monthly_sales')
            ->orderBy('sale_year', 'desc')
            ->orderBy('sale_month', 'desc')
            ->limit(12)
            ->get()
            ->reverse()
            ->values();

        // === DAILY SALES (Last 30 days) ===
        $dailySales = DB::table('view_daily_sales')
            ->whereBetween('sale_date', [
                Carbon::now()->subDays(29)->format('Y-m-d'),
                Carbon::now()->format('Y-m-d')
            ])
            ->orderBy('sale_date', 'asc')
            ->get();

        // Compile stats
        $stats = [
            'total_revenue' => $currentPeriodStats['revenue'],
            'total_orders' => $currentPeriodStats['orders'],
            'avg_order_value' => $currentPeriodStats['avg_value'],
            'unique_customers' => $currentPeriodStats['customers'],
            'completed_orders' => $currentPeriodStats['completed'],
            'revenue_growth' => $growth['revenue'],
            'orders_growth' => $growth['orders'],
            'customers_growth' => $growth['customers'],
        ];

        return view('owner.sales.analytics', compact(
            'chartData',
            'stats',
            'topProducts',
            'salesByStatus',
            'monthlyComparison',
            'dailySales',
            'period'
        ));
    }

    /**
     * Get chart data based on period
     */
    private function getChartData($period, $dateFrom = null, $dateTo = null)
    {
        $labels = [];
        $data = [];
        $ordersData = [];

        if ($period === 'custom' && $dateFrom && $dateTo) {
            // Custom date range
            $start = Carbon::parse($dateFrom);
            $end = Carbon::parse($dateTo);
            $days = $start->diffInDays($end);

            if ($days <= 31) {
                // Show daily
                $salesData = DB::table('view_daily_sales')
                    ->whereBetween('sale_date', [$dateFrom, $dateTo])
                    ->orderBy('sale_date', 'asc')
                    ->get()
                    ->keyBy('sale_date');

                for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                    $dateKey = $date->format('Y-m-d');
                    $labels[] = $date->format('d M');
                    $daySales = $salesData->get($dateKey);
                    $data[] = $daySales ? ($daySales->confirmed_revenue / 1000) : 0;
                    $ordersData[] = $daySales ? $daySales->completed_orders : 0;
                }
            } else {
                // Show monthly
                $salesData = DB::table('view_monthly_sales')
                    ->whereBetween('period_month', [
                        $start->format('Y-m'),
                        $end->format('Y-m')
                    ])
                    ->orderBy('sale_year', 'asc')
                    ->orderBy('sale_month', 'asc')
                    ->get();

                foreach ($salesData as $month) {
                    $labels[] = Carbon::createFromFormat('Y-m', $month->period_month)->format('M Y');
                    $data[] = $month->confirmed_revenue / 1000;
                    $ordersData[] = $month->completed_orders;
                }
            }
        } elseif ($period === '30days') {
            // Last 30 days
            $salesData = DB::table('view_daily_sales')
                ->whereBetween('sale_date', [
                    Carbon::now()->subDays(29)->format('Y-m-d'),
                    Carbon::now()->format('Y-m-d')
                ])
                ->orderBy('sale_date', 'asc')
                ->get()
                ->keyBy('sale_date');

            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $dateKey = $date->format('Y-m-d');
                $labels[] = $date->format('d M');
                $daySales = $salesData->get($dateKey);
                $data[] = $daySales ? ($daySales->confirmed_revenue / 1000) : 0;
                $ordersData[] = $daySales ? $daySales->completed_orders : 0;
            }
        } elseif ($period === '6months') {
            // Last 6 months
            $salesData = DB::table('view_monthly_sales')
                ->orderBy('sale_year', 'desc')
                ->orderBy('sale_month', 'desc')
                ->limit(6)
                ->get()
                ->reverse()
                ->values();

            foreach ($salesData as $month) {
                $labels[] = Carbon::create($month->sale_year, $month->sale_month)->format('M Y');
                $data[] = $month->confirmed_revenue / 1000;
                $ordersData[] = $month->completed_orders;
            }
        } else {
            // Default: Last 12 months
            $salesData = DB::table('view_monthly_sales')
                ->orderBy('sale_year', 'desc')
                ->orderBy('sale_month', 'desc')
                ->limit(12)
                ->get()
                ->reverse()
                ->values();

            foreach ($salesData as $month) {
                $labels[] = Carbon::create($month->sale_year, $month->sale_month)->format('M Y');
                $data[] = $month->confirmed_revenue / 1000;
                $ordersData[] = $month->completed_orders;
            }
        }

        return [
            'labels' => $labels,
            'revenue' => $data,
            'orders' => $ordersData,
        ];
    }

    /**
     * Get current period statistics
     */
    private function getCurrentPeriodStats($period, $dateFrom = null, $dateTo = null)
    {
        if ($period === 'custom' && $dateFrom && $dateTo) {
            $query = DB::table('view_order_details')
                ->whereBetween('order_date', [$dateFrom, $dateTo]);
        } elseif ($period === '30days') {
            $query = DB::table('view_order_details')
                ->whereBetween('order_date', [
                    Carbon::now()->subDays(29),
                    Carbon::now()
                ]);
        } elseif ($period === '6months') {
            $query = DB::table('view_order_details')
                ->whereBetween('order_date', [
                    Carbon::now()->subMonths(6),
                    Carbon::now()
                ]);
        } else {
            // 12 months
            $query = DB::table('view_order_details')
                ->whereBetween('order_date', [
                    Carbon::now()->subMonths(12),
                    Carbon::now()
                ]);
        }

        $data = $query->get();

        // Menggunakan status: done, confirm, paid dan amount_paid
        $relevantOrders = $data->whereIn('order_status', ['done', 'confirm', 'paid']);
        
        return [
            'revenue' => $relevantOrders->sum('amount_paid'),
            'orders' => $data->count(),
            'completed' => $relevantOrders->count(),
            'avg_value' => $relevantOrders->avg('amount_paid') ?? 0,
            'customers' => $data->pluck('user_id')->filter()->unique()->count(),
        ];
    }

    /**
     * Get previous period statistics for comparison
     */
    private function getPreviousPeriodStats($period, $dateFrom = null, $dateTo = null)
    {
        if ($period === 'custom' && $dateFrom && $dateTo) {
            $start = Carbon::parse($dateFrom);
            $end = Carbon::parse($dateTo);
            $days = $start->diffInDays($end);

            $query = DB::table('view_order_details')
                ->whereBetween('order_date', [
                    $start->copy()->subDays($days),
                    $start
                ]);
        } elseif ($period === '30days') {
            $query = DB::table('view_order_details')
                ->whereBetween('order_date', [
                    Carbon::now()->subDays(59),
                    Carbon::now()->subDays(30)
                ]);
        } elseif ($period === '6months') {
            $query = DB::table('view_order_details')
                ->whereBetween('order_date', [
                    Carbon::now()->subMonths(12),
                    Carbon::now()->subMonths(6)
                ]);
        } else {
            // Previous 12 months
            $query = DB::table('view_order_details')
                ->whereBetween('order_date', [
                    Carbon::now()->subMonths(24),
                    Carbon::now()->subMonths(12)
                ]);
        }

        $data = $query->get();

        // Menggunakan status: done, confirm, paid dan amount_paid
        $relevantOrders = $data->whereIn('order_status', ['done', 'confirm', 'paid']);

        return [
            'revenue' => $relevantOrders->sum('amount_paid'),
            'orders' => $data->count(),
            'customers' => $data->pluck('user_id')->filter()->unique()->count(),
        ];
    }

    /**
     * Calculate growth percentage
     */
    private function calculateGrowth($current, $previous)
    {
        return [
            'revenue' => $previous['revenue'] > 0
                ? round((($current['revenue'] - $previous['revenue']) / $previous['revenue']) * 100, 1)
                : 0,
            'orders' => $previous['orders'] > 0
                ? round((($current['orders'] - $previous['orders']) / $previous['orders']) * 100, 1)
                : 0,
            'customers' => $previous['customers'] > 0
                ? round((($current['customers'] - $previous['customers']) / $previous['customers']) * 100, 1)
                : 0,
        ];
    }
}
