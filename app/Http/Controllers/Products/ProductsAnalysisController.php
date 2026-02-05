<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductDetail;
use APP\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProductCreatedMail;
use Spatie\Activitylog\Models\Activity;
use App\Services\FcmService;
use Illuminate\Support\Facades\Log;
use App\Services\ProductAnalysisService;

class ProductsAnalysisController extends Controller
{
    public function view()
    {
         if (!auth()->check()) {
        return redirect()->route('login');
    }
        return view('products.analysis');
    }

public function metrics(ProductAnalysisService $service)
{
    return response()->json([
        'status' => true,
        'data' => $service->dashboardMetrics(auth()->id()),
    ]);
}
public function stats(ProductAnalysisService $service)
{
    return response()->json([
        'status' => true,
        'data' => $service->getstats(auth()->id()),
    ]);
}

public function newlyAdded(Request $request, ProductAnalysisService $service)
{
    $days = (int) $request->get('days', 7);

    return response()->json([
        'status' => true,
        'data' => $service->newlyAddedProducts(auth()->id(), $days)
    ]);
}
public function getcustomrevenue(ProductAnalysisService $analytics)
    {
        $userId = auth()->id();

        return response()->json([
            'status' => true,
            'data' => [
                'total_revenue' => $analytics->totalRevenue($userId),
                'last_7_days_revenue' => $analytics->revenueByDate(
                    $userId,
                    now()->subDays(7),
                    now()
                ),
                'daily_revenue' => $analytics->dailyRevenue($userId),
            ]
        ]);
    }
    public function yearWise(Request $request, ProductAnalysisService $service)
    {
        $year = $request->get('year', now()->year);

        $data = $service->getYearWiseRevenue($year);

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
   public function categoryWise(ProductAnalysisService $service)
    {
        $userId = auth()->id(); // ✅ logged-in user only

        $data = $service->getCategoryWiseExpensesForUser($userId);

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
     public function GetProfit(ProductAnalysisService $service)
    {
        $userId = auth()->id();

        $data = $service->getProfitSummary($userId);

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
}
