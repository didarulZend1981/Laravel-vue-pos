<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    function getStockReport($from, $to){
            $report = Product::with(['purchases', 'sales'])->get()->map(function ($product) use ($from, $to) {

            // -----------------------------
            // 1️⃣ Opening Qty & Balance
            // -----------------------------
            $openingPurchaseQty = $product->purchases()
                ->whereDate('created_at', '<', $from)
                ->sum('qty');

            $openingSaleQty = $product->sales()
                ->whereDate('created_at', '<', $from)
                ->sum('qty');

            $openingQty = $openingPurchaseQty - $openingSaleQty;

            $openingPurchaseBalance = $product->purchases()
                ->whereDate('created_at', '<', $from)
                ->sum(DB::raw('qty * purchase_price'));

            $openingSaleBalance = $product->sales()
                ->whereDate('created_at', '<', $from)
                ->sum(DB::raw('qty * sale_price'));

            $openingBalance = $openingPurchaseBalance - $openingSaleBalance;

            // -----------------------------
            // 2️⃣ Purchase Qty & Balance (Date Range)
            // -----------------------------
            $purchaseQty = $product->purchases()
                ->whereBetween('created_at', [$from, $to])
                ->sum('qty');

            $purchaseBalance = $product->purchases()
                ->whereBetween('created_at', [$from, $to])
                ->sum(DB::raw('qty * purchase_price'));

            // -----------------------------
            // 3️⃣ Sale Qty & Balance (Date Range)
            // -----------------------------
            $saleQty = $product->sales()
                ->whereBetween('created_at', [$from, $to])
                ->sum('qty');

            $saleBalance = $product->sales()
                ->whereBetween('created_at', [$from, $to])
                ->sum(DB::raw('qty * sale_price'));

            $salePurchaseBalance = $product->sales()
                ->whereBetween('created_at', [$from, $to])
                ->sum(DB::raw('qty * rate'));

            // -----------------------------
            // 4️⃣ Closing Qty & Balance
            // -----------------------------
            $closingQty = $openingQty + $purchaseQty - $saleQty;
            // $closingBalance = $openingBalance + $purchaseBalance - $saleBalance;
            $closingBalance = $openingBalance + $purchaseBalance - $salePurchaseBalance;

            // -----------------------------
            // 5️⃣ Return Report Array
            // -----------------------------
            return [
                'image' => $product->image ?? null,
                'product_name' => $product->name,
                'opening_qty' => $openingQty,
                'opening_balance' => $openingBalance,
                'purchase_qty' => $purchaseQty,
                'purchase_balance' => $purchaseBalance,
                'sale_qty' => $saleQty,
                'sale_balance' => $saleBalance,
                'closing_qty' => $closingQty,
                'closing_balance' => $closingBalance,
            ];
        });

        return $report;
    }

    public function StockPage(){
        //  return Inertia::render('Dashboard/Stock/StockPage');
         $timezone = 'Asia/Dhaka';
         $now = Carbon::now($timezone);
         $from = $now->copy()->startOfMonth()->startOfDay();
         $to = $now->copy()->endOfDay();


        $report =$this->getStockReport($from, $to);



        // return Inertia::render('Dashboard/Stock/StockPage', ['report' => $report]);

        return Inertia::render('Dashboard/Stock/StockPage', [
            'report' => $report,
            'stockDateFrom' => $from->toDateString(),
            'stockDateTo' => $to->toDateString(),
        ]);


    }

    public function stocksReport(Request $request){
        $timezone = 'Asia/Dhaka';
        $now = Carbon::now($timezone);
        $defaultFrom = $now->copy()->startOfMonth()->startOfDay();
        $defaultTo = $now->copy()->endOfDay();




        $from = $request->stockDateFrom
            ? Carbon::parse($request->stockDateFrom, $timezone)->startOfDay()
            : $defaultFrom;

        $to = $request->stockDateTo
            ? Carbon::parse($request->stockDateTo, $timezone)->endOfDay()
            : $defaultTo;






        $report =$this->getStockReport($from, $to);

        // return Inertia::render('Dashboard/Stock/StockPage', ['report' => $report]);


        return Inertia::render('Dashboard/Stock/StockPage', [
            'report' => $report,
            'stockDateFrom' => $from->toDateString(),
            'stockDateTo' => $to->toDateString(),
        ]);




    }


}
