<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionHistoryController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $status = $request->get('status');
        $paymentStatus = $request->get('payment_status');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'order_date');
        $sortOrder = $request->get('sort_order', 'desc');

        // Base query menggunakan VIEW
        $query = DB::table('view_order_details');

        // Apply filters
        if ($status) {
            $query->where('order_status', $status);
        }

        if ($paymentStatus) {
            $query->where('payment_status', $paymentStatus);
        }

        if ($dateFrom) {
            $query->whereDate('order_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('order_date', '<=', $dateTo);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone_number', 'like', "%{$search}%")
                    ->orWhere('order_id', 'like', "%{$search}%")
                    ->orWhere('user_username', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);

        // Get paginated results
        $transactions = $query->paginate(20)->withQueryString();

        // Transform data
        $transactions->through(function ($order) {
            return (object)[
                'id' => $order->order_id,
                'order_number' => 'ORD-' . str_pad($order->order_id, 6, '0', STR_PAD_LEFT),
                'customer_name' => $order->customer_name,
                'customer_phone_number' => $order->customer_phone_number,
                'customer_address' => $order->customer_address,
                'total_price' => $order->total_price,
                'formatted_total_price' => 'Rp ' . number_format($order->total_price, 0, ',', '.'),
                'amount_paid' => $order->amount_paid,
                'formatted_amount_paid' => 'Rp ' . number_format($order->amount_paid, 0, ',', '.'),
                'remaining_payment' => $order->remaining_payment,
                'formatted_remaining_payment' => 'Rp ' . number_format($order->remaining_payment, 0, ',', '.'),
                'status' => $order->order_status,
                'status_text' => Order::getStatuses()[$order->order_status] ?? $order->order_status,
                'status_badge_color' => $this->getStatusBadgeColor($order->order_status),
                'payment_status' => $order->payment_status,
                'payment_proof' => $order->payment_proof,
                'user' => $order->user_id ? (object)[
                    'id' => $order->user_id,
                    'username' => $order->user_username,
                    'email' => $order->user_email,
                    'is_active' => $order->user_active,
                ] : null,
                'total_items' => $order->total_items,
                'total_quantity' => $order->total_quantity,
                'created_at' => Carbon::parse($order->order_date),
                'updated_at' => Carbon::parse($order->order_updated),
                'cancellation_date' => $order->cancellation_date ? Carbon::parse($order->cancellation_date) : null,
                'cancellation_reason' => $order->cancellation_reason,
            ];
        });

        // === STATISTICS ===
        $allTransactions = DB::table('view_order_details');

        // Apply same filters untuk stats
        if ($status) $allTransactions->where('order_status', $status);
        if ($paymentStatus) $allTransactions->where('payment_status', $paymentStatus);
        if ($dateFrom) $allTransactions->whereDate('order_date', '>=', $dateFrom);
        if ($dateTo) $allTransactions->whereDate('order_date', '<=', $dateTo);
        if ($search) {
            $allTransactions->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone_number', 'like', "%{$search}%")
                    ->orWhere('order_id', 'like', "%{$search}%")
                    ->orWhere('user_username', 'like', "%{$search}%");
            });
        }

        $filteredData = $allTransactions->get();

        // Menggunakan status: done, confirm, paid dan amount_paid untuk revenue
        $revenueOrders = $filteredData->whereIn('order_status', ['done', 'confirm', 'paid']);
        
        $stats = [
            'total_transactions' => $filteredData->count(),
            'total_value' => $filteredData->sum('total_price'), // Total semua transaksi
            'total_revenue' => $revenueOrders->sum('amount_paid'), // Revenue dari done/confirm/paid
            'total_paid' => $filteredData->sum('amount_paid'),
            'total_unpaid' => $filteredData->sum('remaining_payment'),
            'avg_transaction' => $filteredData->avg('total_price') ?? 0,
            'avg_revenue' => $revenueOrders->avg('amount_paid') ?? 0,

            // By Status
            'pending_count' => $filteredData->where('order_status', 'pending')->count(),
            'paid_count' => $filteredData->where('order_status', 'paid')->count(),
            'confirm_count' => $filteredData->where('order_status', 'confirm')->count(),
            'processing_count' => $filteredData->where('order_status', 'processing')->count(),
            'done_count' => $filteredData->where('order_status', 'done')->count(),
            'cancel_count' => $filteredData->where('order_status', 'cancel')->count(),

            // By Payment Status
            'lunas_count' => $filteredData->where('payment_status', 'LUNAS')->count(),
            'partial_count' => $filteredData->where('payment_status', 'PARTIAL')->count(),
            'unpaid_count' => $filteredData->where('payment_status', 'UNPAID')->count(),
        ];

        // === TIMELINE DATA (for chart) ===
        $timelineData = $this->getTimelineData($dateFrom, $dateTo, $status);

        // Get all statuses for filter
        $statuses = Order::getStatuses();

        return view('owner.transactions.history', compact(
            'transactions',
            'stats',
            'timelineData',
            'statuses'
        ));
    }

    /**
     * Display transaction detail
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

        // Get additional stats from VIEW
        $orderStats = DB::table('view_order_details')
            ->where('order_id', $order->id)
            ->first();

        // Get customer transaction history
        $customerHistory = Order::where('customer_phone_number', $order->customer_phone_number)
            ->where('id', '!=', $order->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('owner.transactions.show', compact('order', 'orderStats', 'customerHistory'));
    }

    /**
     * Get timeline data for chart
     */
    private function getTimelineData($dateFrom, $dateTo, $status)
    {
        // If no date filter, get last 30 days
        if (!$dateFrom || !$dateTo) {
            $dateFrom = Carbon::now()->subDays(29)->format('Y-m-d');
            $dateTo = Carbon::now()->format('Y-m-d');
        }

        $query = DB::table('view_order_details')
            ->whereBetween('order_date', [$dateFrom, $dateTo])
            ->select(
                DB::raw('DATE(order_date) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_price) as total_value'),
                DB::raw('SUM(amount_paid) as total_paid'),
                // Revenue hanya dari status done/confirm/paid
                DB::raw('SUM(CASE WHEN order_status IN (\'done\', \'confirm\', \'paid\') THEN amount_paid ELSE 0 END) as revenue')
            )
            ->groupBy(DB::raw('DATE(order_date)'))
            ->orderBy('date', 'asc');

        if ($status) {
            $query->where('order_status', $status);
        }

        return $query->get();
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
