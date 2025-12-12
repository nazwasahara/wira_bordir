<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderSummaryController extends Controller
{
    public function index(Request $request)
    {
        // Filter parameters
        $status = $request->get('status');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        // Base query - Menggunakan Eloquent Model
        $query = Order::with('user');

        // Apply filters
        if ($status) {
            $query->where('status', $status);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone_number', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);

        // Get paginated results (Eloquent Model Collection)
        $orders = $query->paginate(15)->withQueryString();

        // === STATISTICS menggunakan VIEW: view_order_status_summary ===
        $statusSummary = DB::table('view_order_status_summary')->get()->keyBy('status');

        // Pesanan Masuk
        $incomingOrders = collect(['pending', 'paid', 'confirm'])
            ->sum(fn($status) => $statusSummary->get($status)->total_count ?? 0);

        // Pesanan Selesai
        $completedOrders = $statusSummary->get('done')->total_count ?? 0;

        // Pesanan dalam Proses
        $processingOrders = $statusSummary->get('processing')->total_count ?? 0;

        // Pesanan Dibatalkan
        $cancelledOrders = $statusSummary->get('cancel')->total_count ?? 0;

        // Total Orders
        $totalOrders = $statusSummary->sum('total_count');

        // Status Distribution
        $statusDistribution = $statusSummary->pluck('total_count', 'status')->toArray();

        // Financial Stats - Menggunakan status: done, confirm, paid dan amount_paid
        $totalRevenue = Order::whereIn('status', ['done', 'confirm', 'paid'])->sum('amount_paid') ?? 0;
        $pendingPayment = Order::where('status', 'pending')->sum('total_price') ?? 0;

        // Date Range Stats (if filtered)
        $dateRangeStats = null;
        if ($dateFrom || $dateTo) {
            $rangeQuery = Order::query();
            if ($dateFrom) $rangeQuery->whereDate('created_at', '>=', $dateFrom);
            if ($dateTo) $rangeQuery->whereDate('created_at', '<=', $dateTo);

            $dateRangeStats = [
                'total_orders' => $rangeQuery->count(),
                'total_revenue' => $rangeQuery->whereIn('status', ['done', 'confirm', 'paid'])->sum('amount_paid'),
                'completed' => $rangeQuery->whereIn('status', ['done', 'confirm', 'paid'])->count(),
            ];
        }

        // Compile stats
        $stats = [
            'incoming_orders' => $incomingOrders,
            'completed_orders' => $completedOrders,
            'processing_orders' => $processingOrders,
            'cancelled_orders' => $cancelledOrders,
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'pending_payment' => $pendingPayment,
            'status_distribution' => $statusDistribution,
            'date_range_stats' => $dateRangeStats,
        ];

        // Get all statuses for filter
        $statuses = Order::getStatuses();

        return view('owner.orders.summary', compact(
            'orders',
            'stats',
            'statuses'
        ));
    }

    /**
     * Display order detail
     */
    public function show(Order $order)
    {
        // Load relationships
        $order->load([
            'user',
            'orderItems.product',
            'orderItems.material',
            'orderItems.materialColor',
            'orderItems.sashType',
            'orderItems.font',
            'orderItems.sideMotif',
            'orderItems.ribbonColor',
            'orderItems.laceOption',
            'orderItems.rombeOption',
            'orderItems.motifRibbonOption',
            'orderItems.additionalItemOption.additionalItem',
            'cancelledTransaction'
        ]);

        // Get additional stats from VIEW for context
        $orderStats = DB::table('view_order_details')
            ->where('order_id', $order->id)
            ->first();

        return view('owner.orders.show', compact('order', 'orderStats'));
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
