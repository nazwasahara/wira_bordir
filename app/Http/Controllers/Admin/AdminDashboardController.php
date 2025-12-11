<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // === STATISTICS ===

        // Total Users
        $totalUsers = User::where('role', 'customer')->count();
        $usersLastMonth = User::where('role', 'customer')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->count();
        $usersThisMonth = User::where('role', 'customer')
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();
        $usersGrowth = $usersLastMonth > 0
            ? round((($usersThisMonth - $usersLastMonth) / $usersLastMonth) * 100)
            : 0;

        // Total Orders
        $totalOrders = Order::count();
        $ordersLastMonth = Order::whereMonth('created_at', Carbon::now()->subMonth()->month)->count();
        $ordersThisMonth = Order::whereMonth('created_at', Carbon::now()->month)->count();
        $ordersGrowth = $ordersLastMonth > 0
            ? round((($ordersThisMonth - $ordersLastMonth) / $ordersLastMonth) * 100)
            : 0;

        // Pending Orders
        $pendingOrders = Order::where('status', 'pending')->count();
        $pendingYesterday = Order::where('status', 'pending')
            ->whereDate('created_at', Carbon::yesterday())
            ->count();
        $pendingToday = Order::where('status', 'pending')
            ->whereDate('created_at', Carbon::today())
            ->count();
        $pendingGrowth = $pendingYesterday > 0
            ? round((($pendingToday - $pendingYesterday) / $pendingYesterday) * 100)
            : 0;

        // Total Products
        $totalProducts = Product::count();

        // === SALES CHART DATA ===
        $salesData = [];
        $months = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = $month->locale('id')->isoFormat('MMM');

            $sales = Order::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->where('status', 'done')
                ->sum('total_price');

            $salesData[] = $sales / 1000; // Convert to thousands
        }

        // === RECENT ACTIVITIES ===
        $recentActivities = collect();

        // Recent Orders
        $recentOrders = Order::with('user')
            ->latest()
            ->limit(3)
            ->get()
            ->map(function ($order) {
                return [
                    'type' => 'order',
                    'icon' => 'shopping-cart',
                    'color' => 'success',
                    'title' => 'Pesanan Baru',
                    'description' => $order->order_number,
                    'time' => $order->created_at,
                ];
            });

        // Recent Users
        $recentUsers = User::where('role', 'customer')
            ->latest()
            ->limit(2)
            ->get()
            ->map(function ($user) {
                return [
                    'type' => 'user',
                    'icon' => 'user',
                    'color' => 'primary',
                    'title' => 'Pengguna Baru',
                    'description' => $user->username . ' terdaftar',
                    'time' => $user->created_at,
                ];
            });

        // Completed Orders
        $completedOrders = Order::where('status', 'done')
            ->latest()
            ->limit(1)
            ->get()
            ->map(function ($order) {
                return [
                    'type' => 'completed',
                    'icon' => 'check-circle',
                    'color' => 'success',
                    'title' => 'Pesanan Selesai',
                    'description' => $order->order_number,
                    'time' => $order->updated_at,
                ];
            });

        // Cancelled Orders
        $cancelledOrders = Order::where('status', 'cancel')
            ->latest()
            ->limit(1)
            ->get()
            ->map(function ($order) {
                return [
                    'type' => 'cancelled',
                    'icon' => 'ban',
                    'color' => 'danger',
                    'title' => 'Pesanan Dibatalkan',
                    'description' => $order->order_number,
                    'time' => $order->updated_at,
                ];
            });

        $recentActivities = $recentActivities
            ->concat($recentOrders)
            ->concat($recentUsers)
            ->concat($completedOrders)
            ->concat($cancelledOrders)
            ->sortByDesc('time')
            ->take(5);

        // === RECENT ORDERS TABLE ===
        $recentOrdersList = Order::with('user')
            ->latest()
            ->limit(10)
            ->get();

        // === ADDITIONAL STATS ===
        $stats = [
            'total_users' => $totalUsers,
            'users_growth' => $usersGrowth,
            'total_orders' => $totalOrders,
            'orders_growth' => $ordersGrowth,
            'pending_orders' => $pendingOrders,
            'pending_growth' => $pendingGrowth,
            'total_products' => $totalProducts,
            'total_revenue' => Order::where('status', 'done')->sum('total_price'),
            'revenue_this_month' => Order::where('status', 'done')
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('total_price'),
        ];

        return view('admin.dashboard', compact(
            'stats',
            'salesData',
            'months',
            'recentActivities',
            'recentOrdersList'
        ));
    }
}
