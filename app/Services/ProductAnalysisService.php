<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Expense;
class ProductAnalysisService
{
    
public function dashboardMetrics(int $userId): array
    {
        return [
            // Products
            'total_products' => Product::where('user_id', $userId)->count(),

            'published' => Product::where('user_id', $userId)
                ->where('status', 'published')
                ->count(),

            'draft' => Product::where('user_id', $userId)
                ->where('status', 'draft')
                ->count(),

            'low_stock' => Product::where('user_id', $userId)
                ->where('stock', '<', 500)
                ->count(),

            // Sales
            'total_sales' => DB::table('orders')
                ->where('user_id', $userId)
                ->count(),

            // Customers (all)
 'total_customers' => DB::table('customers')
                ->where('user_id', $userId)
                ->count(),
            // Revenue
            'total_revenue' => DB::table('orders')
                ->where('user_id', $userId)
                ->sum('total_amount'),
        ];
}
      public function getstats(int $userId): array
    {
        $base = DB::table('product_details')
            ->join('products', 'products.id', '=', 'product_details.product_id')
            ->where('products.user_id', $userId);

        return [
            // cards
            'total_products' => (clone $base)->count(),

            'status' => [
                'published' => (clone $base)->where('product_details.status', 'published')->count(),
                'draft'     => (clone $base)->where('product_details.status', 'draft')->count(),
            ],

            'categories' => [
                'men'   => (clone $base)->where('product_details.category', 'men')->count(),
                'women' => (clone $base)->where('product_details.category', 'women')->count(),
                'kids'  => (clone $base)->where('product_details.category', 'kids')->count(),
            ],

            'low_stock' => (clone $base)
                ->where('product_details.stock', '<', 500)
                ->count(),
        ];
    }
public function newlyAddedProducts(int $userId, int $days = 7)
{
    return Product::query()
        ->join('product_details', 'products.id', '=', 'product_details.product_id')
        ->where('products.user_id', $userId)
        ->where('products.created_at', '>=', Carbon::now()->subDays($days))
        ->orderBy('products.created_at', 'desc')
        ->limit(7)
        ->get([
            'products.id',
            'products.product_name',
            'products.sku',
            'product_details.discounted_price',
            'products.product_image',
            'products.created_at',
        ]);
}
    public function totalRevenue(int $userId): float
    {
        return Order::where('user_id', $userId)
            ->where('status', 'completed')
            ->sum('total_amount');
    }

    /**
     * Revenue by date range
     */
    public function revenueByDate(
        int $userId,
        Carbon $from,
        Carbon $to
    ): float {
        return Order::where('user_id', $userId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$from, $to])
            ->sum('total_amount');
    }

    /**
     * Daily revenue (for bar charts)
     */
    public function dailyRevenue(
        int $userId,
        int $days = 7
    ) {
        return Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
     public function getYearWiseRevenue(int $year)
    {
        $dailyRevenue = Order::where('status', 'completed')
            ->whereYear('created_at', $year)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $totalRevenue = $dailyRevenue->sum('total');

        return [
            'total_revenue' => $totalRevenue,
            'daily_revenue' => $dailyRevenue
        ];
    }
    public function getCategoryWiseExpensesForUser(int $userId)
    {
        return Expense::where('user_id', $userId)
            ->select(
                'category',
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();
    }
    public function getProfitSummary(int $userId): array
    {
        // Total Revenue (completed orders only)
        $totalRevenue = Order::where('user_id', $userId)
            ->where('status', 'completed')
            ->sum('total_amount');

        // Total Expenses
        $totalExpenses = Expense::where('user_id', $userId)
            ->sum('amount');

        return [
            'revenue' => (float) $totalRevenue,
            'expenses' => (float) $totalExpenses,
            'profit' => (float) ($totalRevenue - $totalExpenses)
        ];
    }
     public function expensesByYear(int $userId, int $year): array
    {
        $expenses = Expense::where('user_id', $userId)
            ->whereYear('expense_date', $year)
            ->select(
                'category',
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('category')
            ->get();

        return [
            'total' => $expenses->sum('total'),
            'categories' => $expenses
        ];
    }
}
