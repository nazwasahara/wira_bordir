<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $reportType = $request->get('report_type', 'monthly');
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Determine date range based on report type
        if ($reportType === 'custom' && $dateFrom && $dateTo) {
            $startDate = Carbon::parse($dateFrom);
            $endDate = Carbon::parse($dateTo);
        } elseif ($reportType === 'yearly') {
            $startDate = Carbon::create($year, 1, 1)->startOfDay();
            $endDate = Carbon::create($year, 12, 31)->endOfDay();
        } else {
            // Monthly
            $startDate = Carbon::create($year, $month, 1)->startOfDay();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();
        }

        // === REVENUE (PEMASUKAN) ===
        $revenueData = $this->getRevenueData($startDate, $endDate);
        // === EXPENSES (PENGELUARAN) ===
        $expensesData = $this->getExpensesData($startDate, $endDate);

        // === NET PROFIT/LOSS ===
        $netProfit = $revenueData['total'] - $expensesData['total'];
        $profitMargin = $revenueData['total'] > 0
            ? round(($netProfit / $revenueData['total']) * 100, 2)
            : 0;

        // === DETAILED BREAKDOWN ===
        $detailedRevenue = $this->getDetailedRevenue($startDate, $endDate);
        $detailedExpenses = $this->getDetailedExpenses($startDate, $endDate);

        // === COMPARISON DATA ===
        $comparisonData = $this->getComparisonData($reportType, $month, $year, $dateFrom, $dateTo);

        // === PAYMENT STATUS ===
        $paymentStatus = $this->getPaymentStatus($startDate, $endDate);

        // === TOP REVENUE SOURCES ===
        $topRevenueSources = $this->getTopRevenueSources($startDate, $endDate);

        // === SALES STATISTICS (Menggunakan Stored Procedure) ===
        $salesStatistics = $this->getSalesStatisticsFromSP($startDate, $endDate);

        // Compile all data
        $report = [
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
                'label' => $this->getPeriodLabel($reportType, $month, $year, $startDate, $endDate),
            ],
            'revenue' => $revenueData,
            'expenses' => $expensesData,
            'net_profit' => $netProfit,
            'profit_margin' => $profitMargin,
            'detailed_revenue' => $detailedRevenue,
            'detailed_expenses' => $detailedExpenses,
            'comparison' => $comparisonData,
            'payment_status' => $paymentStatus,
            'top_sources' => $topRevenueSources,
            'sales_statistics' => $salesStatistics,
        ];

        return view('owner.reports.financial', compact(
            'report',
            'reportType',
            'month',
            'year'
        ));
    }

    /**
     * Print view (no PDF library needed)
     */
    public function print(Request $request)
    {
        // Get same data as index
        $reportType = $request->get('report_type', 'monthly');
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Recreate report data
        if ($reportType === 'custom' && $dateFrom && $dateTo) {
            $startDate = Carbon::parse($dateFrom);
            $endDate = Carbon::parse($dateTo);
        } elseif ($reportType === 'yearly') {
            $startDate = Carbon::create($year, 1, 1)->startOfDay();
            $endDate = Carbon::create($year, 12, 31)->endOfDay();
        } else {
            $startDate = Carbon::create($year, $month, 1)->startOfDay();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();
        }

        $revenueData = $this->getRevenueData($startDate, $endDate);
        $expensesData = $this->getExpensesData($startDate, $endDate);
        $netProfit = $revenueData['total'] - $expensesData['total'];
        $profitMargin = $revenueData['total'] > 0
            ? round(($netProfit / $revenueData['total']) * 100, 2)
            : 0;

        $detailedRevenue = $this->getDetailedRevenue($startDate, $endDate);
        $detailedExpenses = $this->getDetailedExpenses($startDate, $endDate);
        $comparisonData = $this->getComparisonData($reportType, $month, $year, $dateFrom, $dateTo);
        $paymentStatus = $this->getPaymentStatus($startDate, $endDate);
        $topRevenueSources = $this->getTopRevenueSources($startDate, $endDate);
        $salesStatistics = $this->getSalesStatisticsFromSP($startDate, $endDate);

        $report = [
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
                'label' => $this->getPeriodLabel($reportType, $month, $year, $startDate, $endDate),
            ],
            'revenue' => $revenueData,
            'expenses' => $expensesData,
            'net_profit' => $netProfit,
            'profit_margin' => $profitMargin,
            'detailed_revenue' => $detailedRevenue,
            'detailed_expenses' => $detailedExpenses,
            'comparison' => $comparisonData,
            'payment_status' => $paymentStatus,
            'top_sources' => $topRevenueSources,
            'sales_statistics' => $salesStatistics,
        ];

        // Return print-friendly view
        return view('owner.reports.financial-print', compact('report'));
    }

    /**
     * Get revenue data using stored procedure
     */
    private function getRevenueData($startDate, $endDate)
    {
        // Menggunakan stored procedure sp_get_financial_report
        $result = DB::select("CALL sp_get_financial_report(?, ?)", [
            $startDate->toDateString(),
            $endDate->toDateString()
        ]);

        if (!empty($result)) {
            $data = (array) $result[0];
            return [
                'total' => $data['total_revenue'] ?? 0,
                'count' => $data['total_orders'] ?? 0,
                'average' => $data['avg_order_value'] ?? 0,
                'cash_received' => $data['cash_received'] ?? 0,
                'receivable' => $data['receivable'] ?? 0,
            ];
        }

        // Fallback jika stored procedure tidak tersedia
        // Menggunakan status: done, confirm, paid dan amount_paid
        $orders = DB::table('view_order_details')
            ->whereBetween('order_date', [$startDate, $endDate])
            ->whereIn('order_status', ['done', 'confirm', 'paid'])
            ->get();

        return [
            'total' => $orders->sum('amount_paid'),
            'count' => $orders->count(),
            'average' => $orders->avg('amount_paid') ?? 0,
            'cash_received' => $orders->sum('amount_paid'),
            'receivable' => $orders->sum('remaining_payment'),
        ];
    }

    /**
     * Get expenses data
     */
    private function getExpensesData($startDate, $endDate)
    {
        // Get from purchase_invoices
        $invoices = DB::table('view_purchase_invoice_complete')
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->get();

        $total = $invoices->sum('line_total');

        // Breakdown by category
        $byCategory = $invoices->groupBy('item_type')->map(function ($items) {
            return [
                'total' => $items->sum('line_total'),
                'count' => $items->count(),
            ];
        });

        return [
            'total' => $total,
            'count' => $invoices->count(),
            'by_category' => $byCategory,
        ];
    }

    /**
     * Get detailed revenue breakdown
     */
    private function getDetailedRevenue($startDate, $endDate)
    {
        // Menggunakan status: done, confirm, paid dan amount_paid
        return DB::table('view_order_details')
            ->whereBetween('order_date', [$startDate, $endDate])
            ->whereIn('order_status', ['done', 'confirm', 'paid'])
            ->select(
                DB::raw('DATE(order_date) as date'),
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(amount_paid) as total_revenue'),
                DB::raw('SUM(amount_paid) as total_paid'),
                DB::raw('AVG(amount_paid) as avg_order_value')
            )
            ->groupBy(DB::raw('DATE(order_date)'))
            ->orderBy('date', 'desc')
            ->get();
    }

    /**
     * Get detailed expenses breakdown
     */
    private function getDetailedExpenses($startDate, $endDate)
    {
        return DB::table('view_purchase_invoice_complete')
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->select(
                'invoice_id',
                'invoice_date',
                'item_type',
                'item_name',
                'quantity',
                'unit_price',
                'line_total'
            )
            ->orderBy('invoice_date', 'desc')
            ->get()
            ->groupBy('invoice_id');
    }

    /**
     * Get comparison data with previous period
     */
    private function getComparisonData($reportType, $month, $year, $dateFrom, $dateTo)
    {
        if ($reportType === 'custom' && $dateFrom && $dateTo) {
            $start = Carbon::parse($dateFrom);
            $end = Carbon::parse($dateTo);
            $days = $start->diffInDays($end) + 1;

            $prevStart = $start->copy()->subDays($days);
            $prevEnd = $start->copy()->subDay();
        } elseif ($reportType === 'yearly') {
            $prevStart = Carbon::create($year - 1, 1, 1)->startOfDay();
            $prevEnd = Carbon::create($year - 1, 12, 31)->endOfDay();
        } else {
            // Monthly
            $prevStart = Carbon::create($year, $month, 1)->subMonth()->startOfDay();
            $prevEnd = Carbon::create($year, $month, 1)->subMonth()->endOfMonth()->endOfDay();
        }

        // Menggunakan status: done, confirm, paid dan amount_paid
        $prevRevenue = DB::table('view_order_details')
            ->whereBetween('order_date', [$prevStart, $prevEnd])
            ->whereIn('order_status', ['done', 'confirm', 'paid'])
            ->sum('amount_paid');

        $prevExpenses = DB::table('view_purchase_invoice_complete')
            ->whereBetween('invoice_date', [$prevStart, $prevEnd])
            ->sum('line_total');

        $prevNetProfit = $prevRevenue - $prevExpenses;

        return [
            'revenue' => $prevRevenue,
            'expenses' => $prevExpenses,
            'net_profit' => $prevNetProfit,
            'period_label' => $this->getPeriodLabel($reportType, $month - 1, $year, $prevStart, $prevEnd),
        ];
    }

    /**
     * Get payment status breakdown
     */
    private function getPaymentStatus($startDate, $endDate)
    {
        // Menggunakan status: done, confirm, paid
        $orders = DB::table('view_order_details')
            ->whereBetween('order_date', [$startDate, $endDate])
            ->whereIn('order_status', ['done', 'confirm', 'paid'])
            ->get();

        return [
            'fully_paid' => $orders->where('payment_status', 'LUNAS')->count(),
            'partial_paid' => $orders->where('payment_status', 'PARTIAL')->count(),
            'unpaid' => $orders->where('payment_status', 'UNPAID')->count(),
            'total_receivable' => $orders->sum('remaining_payment'),
        ];
    }

    /**
     * Get top revenue sources (products)
     */
    private function getTopRevenueSources($startDate, $endDate)
    {
        // Menggunakan status: done, confirm, paid
        // Note: Untuk revenue per product, tetap menggunakan subtotal dari items
        // karena amount_paid adalah total per order, bukan per item
        return DB::table('view_order_items_details')
            ->join('view_order_details', 'view_order_items_details.order_id', '=', 'view_order_details.order_id')
            ->whereBetween('view_order_details.order_date', [$startDate, $endDate])
            ->whereIn('view_order_details.order_status', ['done', 'confirm', 'paid'])
            ->select(
                'view_order_items_details.product_name',
                DB::raw('SUM(view_order_items_details.quantity) as total_quantity'),
                DB::raw('SUM(view_order_items_details.subtotal) as total_revenue')
            )
            ->groupBy('view_order_items_details.product_name')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get period label
     */
    private function getPeriodLabel($reportType, $month, $year, $startDate, $endDate)
    {
        if ($reportType === 'custom') {
            return $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
        } elseif ($reportType === 'yearly') {
            return 'Tahun ' . $year;
        } else {
            return Carbon::create($year, $month, 1)->locale('id')->isoFormat('MMMM YYYY');
        }
    }

    /**
     * Get sales statistics using stored procedure
     */
    private function getSalesStatisticsFromSP($startDate, $endDate)
    {
        try {
            $results = DB::select("CALL sp_get_sales_statistics(?, ?)", [
                $startDate->toDateString(),
                $endDate->toDateString()
            ]);

            return collect($results)->map(function ($item) {
                return [
                    'status' => $item->status,
                    'order_count' => $item->order_count,
                    'total_revenue' => $item->total_revenue,
                    'total_paid' => $item->total_paid,
                    'avg_order_value' => $item->avg_order_value,
                ];
            });
        } catch (\Exception $e) {
            // Fallback jika stored procedure tidak tersedia
            \Log::warning('Stored procedure sp_get_sales_statistics error: ' . $e->getMessage());
            return collect([]);
        }
    }
}
