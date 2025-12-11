<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        // === REQUIREMENT #2: Jumlah pesanan masuk, selesai, pelanggan aktif ===
        // Menggunakan VIEW: view_order_status_summary
        $statusSummary = DB::table('view_order_status_summary')->get()->keyBy('status');

        // Pesanan Masuk (Pending + Paid + Confirm)
        $incomingOrders = collect(['pending', 'paid', 'confirm'])
            ->sum(fn($status) => $statusSummary->get($status)->total_count ?? 0);

        // Pesanan Selesai
        $completedOrders = $statusSummary->get('done')->total_count ?? 0;

        // Pesanan dalam Proses
        $processingOrders = $statusSummary->get('processing')->total_count ?? 0;

        // Pesanan Dibatalkan
        $cancelledOrders = $statusSummary->get('cancel')->total_count ?? 0;

        // Total Pesanan
        $totalOrders = $statusSummary->sum('total_count');

        // === Pelanggan Aktif ===
        // Menggunakan VIEW: view_customer_statistics
        $activeCustomers = DB::table('view_customer_statistics')
            ->where('is_active', true)
            ->count();

        // Total Admin Aktif
        $activeAdmins = DB::table('users')
            ->where('role', 'admin')
            ->where('is_active', true)
            ->count();

        // Total Produk
        $totalProducts = DB::table('products')
            ->where('is_active', true)
            ->count();

        // === FINANCIAL STATS ===
        // Menggunakan VIEW: view_monthly_sales
        $currentMonth = DB::table('view_monthly_sales')
            ->where('sale_year', Carbon::now()->year)
            ->where('sale_month', Carbon::now()->month)
            ->first();

        $lastMonth = DB::table('view_monthly_sales')
            ->where('sale_year', Carbon::now()->subMonth()->year)
            ->where('sale_month', Carbon::now()->subMonth()->month)
            ->first();

        $revenueThisMonth = $currentMonth->confirmed_revenue ?? 0;
        $revenueLastMonth = $lastMonth->confirmed_revenue ?? 0;

        // Total Revenue (All Time)
        $totalRevenue = $statusSummary->get('done')->total_value ?? 0;

        // Revenue Growth
        $revenueGrowth = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : 0;

        // Average Order Value
        $avgOrderValue = $completedOrders > 0
            ? $totalRevenue / $completedOrders
            : 0;

        // === REQUIREMENT #5: Grafik Penjualan (Last 30 Days) ===
        $salesChartData = $this->getSalesChartData();

        // === REQUIREMENT #7: Top 5 Produk Terlaris ===
        // Menggunakan VIEW: view_top_selling_products
        $topProducts = DB::table('view_top_selling_products')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'product_name' => $item->product_name,
                    'total_sold' => $item->total_quantity,
                    'revenue' => $item->total_revenue,
                ];
            });

        // === Recent Orders ===
        // Menggunakan VIEW: view_order_details
        $recentOrders = DB::table('view_order_details')
            ->orderBy('order_date', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($order) {
                return (object)[
                    'order_number' => 'ORD-' . str_pad($order->order_id, 6, '0', STR_PAD_LEFT),
                    'customer_name' => $order->customer_name,
                    'customer_phone_number' => $order->customer_phone_number,
                    'total_price' => $order->total_price,
                    'formatted_total_price' => 'Rp ' . number_format($order->total_price, 0, ',', '.'),
                    'status' => $order->order_status,
                    'status_text' => Order::getStatuses()[$order->order_status] ?? $order->order_status,
                    'status_badge_color' => $this->getStatusBadgeColor($order->order_status),
                    'created_at' => Carbon::parse($order->order_date),
                ];
            });

        // Orders Growth
        $ordersThisMonth = $currentMonth->total_orders ?? 0;
        $ordersLastMonth = $lastMonth->total_orders ?? 0;
        $ordersGrowth = $ordersLastMonth > 0
            ? round((($ordersThisMonth - $ordersLastMonth) / $ordersLastMonth) * 100, 1)
            : 0;

        // Customer Growth
        $customersThisMonth = DB::table('view_customer_statistics')
            ->whereMonth('registered_date', Carbon::now()->month)
            ->whereYear('registered_date', Carbon::now()->year)
            ->count();

        $customersLastMonth = DB::table('view_customer_statistics')
            ->whereMonth('registered_date', Carbon::now()->subMonth()->month)
            ->whereYear('registered_date', Carbon::now()->subMonth()->year)
            ->count();

        $customersGrowth = $customersLastMonth > 0
            ? round((($customersThisMonth - $customersLastMonth) / $customersLastMonth) * 100, 1)
            : 0;

        // Order Status Distribution
        $ordersByStatus = $statusSummary->pluck('total_count', 'status')->toArray();

        // Compile all stats
        $stats = [
            // Main Stats (Requirement #2)
            'incoming_orders' => $incomingOrders,
            'completed_orders' => $completedOrders,
            'active_customers' => $activeCustomers,

            // Additional Stats
            'total_orders' => $totalOrders,
            'processing_orders' => $processingOrders,
            'cancelled_orders' => $cancelledOrders,
            'active_admins' => $activeAdmins,
            'total_products' => $totalProducts,

            // Financial
            'total_revenue' => $totalRevenue,
            'revenue_this_month' => $revenueThisMonth,
            'revenue_last_month' => $revenueLastMonth,
            'revenue_growth' => $revenueGrowth,
            'avg_order_value' => $avgOrderValue,

            // Growth
            'orders_growth' => $ordersGrowth,
            'customers_growth' => $customersGrowth,

            // Status Distribution
            'orders_by_status' => $ordersByStatus,
        ];

        return view('owner.dashboard', compact(
            'stats',
            'salesChartData',
            'topProducts',
            'recentOrders'
        ));
    }

    /**
     * Get sales chart data for last 30 days
     * Using VIEW: view_daily_sales
     */
    private function getSalesChartData()
    {
        $startDate = Carbon::now()->subDays(29)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        // Get data from view_daily_sales
        $salesData = DB::table('view_daily_sales')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->orderBy('sale_date', 'asc')
            ->get()
            ->keyBy('sale_date');

        $data = [];
        $labels = [];

        // Fill all 30 days (including days with no sales)
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateKey = $date->format('Y-m-d');
            $labels[] = $date->format('d M');

            $daySales = $salesData->get($dateKey);
            $data[] = $daySales ? ($daySales->confirmed_revenue / 1000) : 0; // Convert to thousands
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get status badge color
     */
    private function getStatusBadgeColor($status)
    {
        return match ($status) {
            'pending' => 'warning',
            'paid' => 'info',
            'confirm' => 'primary',
            'processing' => 'info',
            'done' => 'success',
            'cancel' => 'danger',
            default => 'secondary',
        };
    }
}
