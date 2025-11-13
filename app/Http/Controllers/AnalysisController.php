<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Market;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalysisController extends Controller
{
    /**
     * Get sales analysis for a specific period
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function salesAnalysis(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subMonth());
        $endDate = $request->input('end_date', Carbon::now());

        $salesData = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_sales'),
                DB::raw('AVG(total_amount) as average_order_value')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $salesData,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }

    /**
     * Get product performance analysis
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function productPerformance(Request $request)
    {
        $limit = $request->input('limit', 10);
        $categoryId = $request->input('category_id');
        $brandId = $request->input('brand_id');

        $query = Product::with(['category', 'brand'])
            ->select(
                'products.*',
                DB::raw('(SELECT COUNT(*) FROM order WHERE order.product_id = products.id) as times_ordered'),
                DB::raw('(SELECT SUM(quantity) FROM order WHERE order.product_id = products.id) as total_quantity_sold'),
                DB::raw('(SELECT SUM(price * quantity) FROM order WHERE order.product_id = products.id) as total_revenue')
            )
            ->orderBy('times_ordered', 'desc');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($brandId) {
            $query->where('brand_id', $brandId);
        }

        $products = $query->limit($limit)->get();

        return response()->json([
            'status' => 'success',
            'data' => $products,
            'filters' => [
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'limit' => $limit
            ]
        ]);
    }

    /**
     * Get market performance analysis
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function marketPerformance(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subMonth());
        $endDate = $request->input('end_date', Carbon::now());

        $marketPerformance = Market::with(['area'])
            ->select(
                'markets.*',
                DB::raw('(SELECT COUNT(*) FROM orders WHERE orders.market_id = markets.id AND orders.created_at BETWEEN ? AND ?) as total_orders')
            )
            ->addBinding([$startDate, $endDate], 'select')
            ->orderBy('total_orders', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $marketPerformance,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }

    /**
     * Get category-wise sales analysis
     *
     * @return \Illuminate\Http\Response
     */
    public function categoryAnalysis()
    {
        $categories = Category::withCount('products')
            ->withSum(['products' => function($query) {
                $query->select(DB::raw('COALESCE(SUM(price * (SELECT SUM(quantity) FROM order WHERE order.product_id = products.id)), 0)'));
            }], 'price')
            ->orderBy('products_sum_price', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    /**
     * Get order status distribution
     *
     * @return \Illuminate\Http\Response
     */
    public function orderStatusAnalysis()
    {
        $statusDistribution = Order::select(
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->status => $item->count];
            });

        return response()->json([
            'status' => 'success',
            'data' => $statusDistribution
        ]);
    }

    /**
     * Generate comprehensive financial reports
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function financialReports(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());
        $groupBy = $request->input('group_by', 'day'); // day, week, month, year
        $marketId = $request->input('market_id');
        $categoryId = $request->input('category_id');

        // Base query for orders within date range
        $query = Order::whereBetween('created_at', [$startDate, $endDate]);

        // Apply market filter if provided
        if ($marketId) {
            $query->where('market_id', $marketId);
        }

        // Apply category filter if provided
        if ($categoryId) {
            $query->whereHas('product', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        // Group by time period
        $dateFormat = $this->getDateFormatForGroupBy($groupBy);
        
        // Get sales data grouped by time period
        $salesData = (clone $query)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as period"),
                DB::raw('COUNT(DISTINCT order_id) as total_orders'),
                DB::raw('SUM(total_order_price) as total_revenue'),
                DB::raw('SUM(CASE WHEN paid = 1 THEN total_order_price ELSE 0 END) as paid_amount'),
                DB::raw('SUM(CASE WHEN paid = 0 THEN total_order_price ELSE 0 END) as pending_payment'),
                DB::raw('AVG(total_order_price) as average_order_value')
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        // Get product performance data
        $productPerformance = (clone $query)
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->join('products_packs_sizes', 'orders.products_packs_sizes_id', '=', 'products_packs_sizes.id')
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                DB::raw('SUM(orders.quantity) as total_quantity_sold'),
                DB::raw('SUM(orders.total_order_price) as total_revenue'),
                DB::raw('SUM(products_packs_sizes.pack_price * orders.quantity) as total_retail_value'),
                DB::raw('SUM(orders.total_order_price - (products_packs_sizes.pack_price * orders.quantity)) as total_profit')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();

        // Get category performance
        $categoryPerformance = (clone $query)
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'categories.id as category_id',
                'categories.name as category_name',
                DB::raw('COUNT(DISTINCT orders.order_id) as order_count'),
                DB::raw('SUM(orders.total_order_price) as total_revenue'),
                DB::raw('SUM(orders.quantity) as total_quantity_sold')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->get();

        // Get payment status summary
        $paymentSummary = (clone $query)
            ->select(
                DB::raw('COUNT(DISTINCT CASE WHEN paid = 1 THEN order_id END) as paid_orders'),
                DB::raw('COUNT(DISTINCT CASE WHEN paid = 0 THEN order_id END) as unpaid_orders'),
                DB::raw('SUM(CASE WHEN paid = 1 THEN total_order_price ELSE 0 END) as total_paid_amount'),
                DB::raw('SUM(CASE WHEN paid = 0 THEN total_order_price ELSE 0 END) as total_unpaid_amount')
            )
            ->first();

        // Calculate growth metrics if we have previous period data
        $previousPeriodEnd = Carbon::parse($startDate)->subDay();
        $previousPeriodStart = $this->getPreviousPeriodStart($startDate, $groupBy);
        
        $previousPeriodData = null;
        if ($previousPeriodStart) {
            $previousPeriodData = (clone $query)
                ->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])
                ->select(
                    DB::raw('COUNT(DISTINCT order_id) as total_orders'),
                    DB::raw('SUM(total_order_price) as total_revenue')
                )
                ->first();
        }

        // Calculate growth percentages
        $growthMetrics = [];
        if ($previousPeriodData) {
            $currentPeriodData = $query->select(
                DB::raw('COUNT(DISTINCT order_id) as total_orders'),
                DB::raw('SUM(total_order_price) as total_revenue')
            )->first();

            $growthMetrics = [
                'order_growth_percentage' => $this->calculateGrowth(
                    $previousPeriodData->total_orders,
                    $currentPeriodData->total_orders
                ),
                'revenue_growth_percentage' => $this->calculateGrowth(
                    $previousPeriodData->total_revenue,
                    $currentPeriodData->total_revenue
                ),
                'previous_period' => [
                    'start_date' => $previousPeriodStart->format('Y-m-d'),
                    'end_date' => $previousPeriodEnd->format('Y-m-d'),
                    'total_orders' => $previousPeriodData->total_orders,
                    'total_revenue' => $previousPeriodData->total_revenue
                ]
            ];
        }

        return response()->json([
            'status' => 'success',
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'group_by' => $groupBy
            ],
            'summary' => [
                'total_orders' => $paymentSummary ? $paymentSummary->paid_orders + $paymentSummary->unpaid_orders : 0,
                'total_revenue' => $paymentSummary ? $paymentSummary->total_paid_amount + $paymentSummary->total_unpaid_amount : 0,
                'total_paid_amount' => $paymentSummary ? $paymentSummary->total_paid_amount : 0,
                'total_unpaid_amount' => $paymentSummary ? $paymentSummary->total_unpaid_amount : 0,
                'payment_completion_rate' => $paymentSummary && ($paymentSummary->paid_orders + $paymentSummary->unpaid_orders) > 0 
                    ? round(($paymentSummary->paid_orders / ($paymentSummary->paid_orders + $paymentSummary->unpaid_orders)) * 100, 2)
                    : 0,
                'average_order_value' => $paymentSummary && ($paymentSummary->paid_orders + $paymentSummary->unpaid_orders) > 0
                    ? round(($paymentSummary->total_paid_amount + $paymentSummary->total_unpaid_amount) / 
                           ($paymentSummary->paid_orders + $paymentSummary->unpaid_orders), 2)
                    : 0,
            ],
            'growth_metrics' => $growthMetrics,
            'sales_trend' => $salesData,
            'top_products' => $productPerformance,
            'category_performance' => $categoryPerformance,
            'filters' => [
                'market_id' => $marketId,
                'category_id' => $categoryId
            ]
        ]);
    }

    /**
     * Helper method to get date format for group by clause
     */
    private function getDateFormatForGroupBy($groupBy)
    {
        return match($groupBy) {
            'day' => '%Y-%m-%d',
            'week' => '%x-W%v', // ISO week
            'month' => '%Y-%m',
            'year' => '%Y',
            default => '%Y-%m-%d',
        };
    }

    /**
     * Helper method to get previous period start date
     */
    private function getPreviousPeriodStart($currentStart, $groupBy)
    {
        $start = Carbon::parse($currentStart);
        
        return match($groupBy) {
            'day' => $start->copy()->subDay(),
            'week' => $start->copy()->subWeek(),
            'month' => $start->copy()->subMonth(),
            'year' => $start->copy()->subYear(),
            default => null,
        };
    }

    /**
     * Helper method to calculate growth percentage
     */
    private function calculateGrowth($previous, $current)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 2);
    }
}
