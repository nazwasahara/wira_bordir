<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BestSellerController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $period = $request->get('period', '30days');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $sortBy = $request->get('sort_by', 'revenue'); // revenue, quantity, orders

        // Determine date range
        if ($period === 'custom' && $dateFrom && $dateTo) {
            $startDate = Carbon::parse($dateFrom);
            $endDate = Carbon::parse($dateTo);
        } elseif ($period === 'all') {
            $startDate = null;
            $endDate = null;
        } elseif ($period === '7days') {
            $startDate = Carbon::now()->subDays(6)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } elseif ($period === '3months') {
            $startDate = Carbon::now()->subMonths(3)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } elseif ($period === '6months') {
            $startDate = Carbon::now()->subMonths(6)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            // Default: 30 days
            $startDate = Carbon::now()->subDays(29)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        }

        // === GET BESTSELLER DATA ===
        $bestsellerData = $this->getBestsellerData($startDate, $endDate, $sortBy);

        // === STATISTICS ===
        $stats = [
            'total_products' => $bestsellerData->count(),
            'total_revenue' => $bestsellerData->sum('total_revenue'),
            'total_quantity' => $bestsellerData->sum('total_quantity'),
            'total_orders' => $bestsellerData->sum('times_ordered'),
            'avg_revenue_per_product' => $bestsellerData->avg('total_revenue') ?? 0,
        ];

        // === TOP 10 PRODUCTS ===
        $topProducts = $bestsellerData->take(10);

        // === PRODUCT PERFORMANCE BREAKDOWN ===
        $performanceBreakdown = $this->getPerformanceBreakdown($bestsellerData);

        // === TREND DATA (Last 30 Days) ===
        $trendData = $this->getTrendData($topProducts->pluck('product_id'));

        // === CUSTOMIZATION POPULARITY ===
        $customizationStats = $this->getCustomizationStats($startDate, $endDate);

        return view('owner.products.bestseller', compact(
            'bestsellerData',
            'topProducts',
            'stats',
            'performanceBreakdown',
            'trendData',
            'customizationStats',
            'period',
            'sortBy'
        ));
    }

    /**
     * Get bestseller data from VIEW
     */
    private function getBestsellerData($startDate, $endDate, $sortBy)
    {
        $query = DB::table('view_order_items_details')
            ->join('view_order_details', 'view_order_items_details.order_id', '=', 'view_order_details.order_id')
            ->whereIn('view_order_details.order_status', ['done', 'confirm', 'paid']);

        // Apply date filter
        if ($startDate && $endDate) {
            $query->whereBetween('view_order_details.order_date', [$startDate, $endDate]);
        }

        // Group and aggregate
        $data = $query->select(
            'view_order_items_details.product_id',
            'view_order_items_details.product_name',
            'view_order_items_details.product_base_price',
            DB::raw('COUNT(DISTINCT view_order_items_details.order_id) as times_ordered'),
            DB::raw('SUM(view_order_items_details.quantity) as total_quantity'),
            DB::raw('SUM(view_order_items_details.subtotal) as total_revenue'),
            DB::raw('AVG(view_order_items_details.final_price) as avg_selling_price'),
            DB::raw('MIN(view_order_details.order_date) as first_order_date'),
            DB::raw('MAX(view_order_details.order_date) as last_order_date')
        )
            ->groupBy(
                'view_order_items_details.product_id',
                'view_order_items_details.product_name',
                'view_order_items_details.product_base_price'
            );

        // Apply sorting
        switch ($sortBy) {
            case 'quantity':
                $data->orderBy('total_quantity', 'desc');
                break;
            case 'orders':
                $data->orderBy('times_ordered', 'desc');
                break;
            default: // revenue
                $data->orderBy('total_revenue', 'desc');
                break;
        }

        return $data->get();
    }

    /**
     * Get performance breakdown (A, B, C categories)
     */
    private function getPerformanceBreakdown($data)
    {
        $totalRevenue = $data->sum('total_revenue');

        if ($totalRevenue == 0) {
            return [
                'category_a' => ['count' => 0, 'revenue' => 0, 'percentage' => 0],
                'category_b' => ['count' => 0, 'revenue' => 0, 'percentage' => 0],
                'category_c' => ['count' => 0, 'revenue' => 0, 'percentage' => 0],
            ];
        }

        $sortedData = $data->sortByDesc('total_revenue');
        $cumulativeRevenue = 0;

        $categoryA = collect();
        $categoryB = collect();
        $categoryC = collect();

        foreach ($sortedData as $product) {
            $cumulativeRevenue += $product->total_revenue;
            $percentage = ($cumulativeRevenue / $totalRevenue) * 100;

            if ($percentage <= 80) {
                $categoryA->push($product);
            } elseif ($percentage <= 95) {
                $categoryB->push($product);
            } else {
                $categoryC->push($product);
            }
        }

        return [
            'category_a' => [
                'count' => $categoryA->count(),
                'revenue' => $categoryA->sum('total_revenue'),
                'percentage' => $totalRevenue > 0 ? round(($categoryA->sum('total_revenue') / $totalRevenue) * 100, 1) : 0,
            ],
            'category_b' => [
                'count' => $categoryB->count(),
                'revenue' => $categoryB->sum('total_revenue'),
                'percentage' => $totalRevenue > 0 ? round(($categoryB->sum('total_revenue') / $totalRevenue) * 100, 1) : 0,
            ],
            'category_c' => [
                'count' => $categoryC->count(),
                'revenue' => $categoryC->sum('total_revenue'),
                'percentage' => $totalRevenue > 0 ? round(($categoryC->sum('total_revenue') / $totalRevenue) * 100, 1) : 0,
            ],
        ];
    }

    /**
     * Get trend data for top products (last 30 days)
     */
    private function getTrendData($productIds)
    {
        if ($productIds->isEmpty()) {
            return collect();
        }

        $startDate = Carbon::now()->subDays(29)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        return DB::table('view_order_items_details')
            ->join('view_order_details', 'view_order_items_details.order_id', '=', 'view_order_details.order_id')
            ->whereIn('view_order_items_details.product_id', $productIds)
            ->where('view_order_details.order_status', 'done')
            ->whereBetween('view_order_details.order_date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(view_order_details.order_date) as date'),
                'view_order_items_details.product_id',
                'view_order_items_details.product_name',
                DB::raw('SUM(view_order_items_details.quantity) as daily_quantity')
            )
            ->groupBy('date', 'view_order_items_details.product_id', 'view_order_items_details.product_name')
            ->orderBy('date', 'asc')
            ->get()
            ->groupBy('product_id');
    }

    /**
     * Get customization popularity stats
     */
    private function getCustomizationStats($startDate, $endDate)
    {
        $query = DB::table('view_order_items_details')
            ->join('view_order_details', 'view_order_items_details.order_id', '=', 'view_order_details.order_id')
            ->whereIn('view_order_details.order_status', ['done', 'confirm', 'paid']);

        if ($startDate && $endDate) {
            $query->whereBetween('view_order_details.order_date', [$startDate, $endDate]);
        }

        $items = $query->get();

        return [
            'materials' => $this->getCustomizationBreakdown($items, 'material_name'),
            'fonts' => $this->getCustomizationBreakdown($items, 'font_name'),
            'sash_types' => $this->getCustomizationBreakdown($items, 'sash_type_name'),
            'side_motifs' => $this->getCustomizationBreakdown($items, 'side_motif_name'),
        ];
    }

    /**
     * Helper: Get customization breakdown
     */
    private function getCustomizationBreakdown($items, $field)
    {
        return $items->filter(function ($item) use ($field) {
            return !empty($item->$field);
        })
            ->groupBy($field)
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'quantity' => $group->sum('quantity'),
                ];
            })
            ->sortByDesc('count')
            ->take(5);
    }
}
