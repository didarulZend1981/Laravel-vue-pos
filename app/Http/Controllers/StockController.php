<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseInvoiceProducts;
use App\Models\SaleInvoiceProducts;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{

    // ===================== using open summery ====================

        public function getOpening($from, $modelClass = \App\Models\PurchaseInvoiceProducts::class, $qty, $purchase_price){
                $openingPurchases = $modelClass::where('created_at', '<', $from)->get();

                $openingPurchaseQty = $openingPurchases->sum($qty);

                $openingPurchaseAmount = $openingPurchases->sum(function ($item) use ($qty, $purchase_price) {
                    return $item->$qty * $item->$purchase_price;
                });

                return [
                    'qty' => $openingPurchaseQty,
                    'amount' => $openingPurchaseAmount,
                ];
         }

          public function pofitLost($from, $to){
            $startDate = $from;
            $endDate =$to;

            // === Opening Balance ===
            $openingPurchase = $this->getOpening($startDate, PurchaseInvoiceProducts::class, 'qty', 'purchase_price');
            $openingPurchaseQty = $openingPurchase['qty'];
            $openingPurchaseAmount = $openingPurchase['amount'];

            $openingSales = $this->getOpening($startDate, SaleInvoiceProducts::class, 'qty', 'sale_price');
            $openingSaleQty = $openingSales['qty'];
            $openingSaleAmount = $openingSales['amount'];

            $openingQty = $openingPurchaseQty - $openingSaleQty;
            $openingValue = $openingPurchaseAmount - $openingSaleAmount;

            // === Period Purchases ===
            $periodPurchase = PurchaseInvoiceProducts::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('SUM(qty) as total_qty, SUM(qty * purchase_price) as total_amount')
                ->first();

            $periodPurchaseQty = $periodPurchase->total_qty ?? 0;
            $periodPurchaseAmount = $periodPurchase->total_amount ?? 0;

            // === Period Sales ===
            $periodSale = SaleInvoiceProducts::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('
                    SUM(qty) as total_qty,
                    SUM(qty * sale_price) as total_amount,
                    SUM(qty * (sale_price - rate)) as profit_amount
                ')
                ->first();

            $periodSaleQty = $periodSale->total_qty ?? 0;
            $periodSaleAmount = $periodSale->total_amount ?? 0;
            $periodSaleAmountProfit = $periodSale->profit_amount ?? 0;

            // === Closing ===
            $closingQty = $openingQty + $periodPurchaseQty - $periodSaleQty;
            $closingValue = $openingValue + $periodPurchaseAmount - $periodSaleAmount;

            $totalPurchaseQty = $openingPurchaseQty + $periodPurchaseQty;
            $totalPurchaseAmount = $openingPurchaseAmount + $periodPurchaseAmount;

            $profitOrLoss = $periodSaleAmountProfit;

            // === Return Summary ===
            return [
                'opening_qty' => $openingQty,
                'opening_value' => round($openingValue, 2),

                'period_purchase_qty' => $periodPurchaseQty,
                'period_purchase_amount' => round($periodPurchaseAmount, 2),

                'period_sale_qty' => $periodSaleQty,
                'period_sale_amount' => round($periodSaleAmount, 2),

                'total_purchase_qty' => $totalPurchaseQty,
                'total_purchase_amount' => round($totalPurchaseAmount, 2),

                'closing_qty' => $closingQty,
                'closing_value' => round($closingValue, 2),

                'profit_or_loss' => round($profitOrLoss, 2),
            ];
        }




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

            $saleProfit = $product->sales()
                ->whereBetween('created_at', [$from, $to])
                ->sum(DB::raw('qty * (sale_price - rate)'));

            $salePurchaseBalance = $product->sales()
                ->whereBetween('created_at', [$from, $to])
                ->sum(DB::raw('qty * rate'));

            // -----------------------------
            // 4️⃣ Closing Qty & Balance
            // -----------------------------
            $closingQty = $openingQty + $purchaseQty - $saleQty;
            // $closingBalance = $openingBalance + $purchaseBalance - $saleBalance;
            $closingBalance = $openingBalance + $purchaseBalance -$saleBalance;

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
                'profit' => $saleProfit,
                'profit_percen' => $salePurchaseBalance != 0 ? ($saleProfit / $salePurchaseBalance) * 100 : 0,
            ];
        })  ->sortBy(function ($item) {
        $allZero = (
            $item['opening_qty'] == 0 &&
            $item['opening_balance'] == 0 &&
            $item['purchase_qty'] == 0 &&
            $item['purchase_balance'] == 0 &&
            $item['sale_qty'] == 0 &&
            $item['sale_balance'] == 0 &&
            $item['closing_qty'] == 0 &&
            $item['closing_balance'] == 0 &&
            $item['profit'] == 0 &&
            $item['profit_percen'] == 0
        );
        return $allZero ? 1 : 0; // 0 মানে আগে, 1 মানে পরে
    })->values();


        return $report;
    }

    public function StockPage(){
        //  return Inertia::render('Dashboard/Stock/StockPage');
         $timezone = 'Asia/Dhaka';
         $now = Carbon::now($timezone);
         $from = $now->copy()->startOfMonth()->startOfDay();
         $to = $now->copy()->endOfDay();


        $report =$this->getStockReport($from, $to);
        $reportSummaery =$this->pofitLost($from, $to);



        // return Inertia::render('Dashboard/Stock/StockPage', ['report' => $report]);

        return Inertia::render('Dashboard/Stock/StockPage', [
            'report' => $report,
            'reportSummaery' => $reportSummaery,
            'stockDateFrom' => $from->toDateString(),
            'stockDateTo' => $to->toDateString(),
        ]);


    }

    public function stocksReport(Request $request){
        $timezone = 'Asia/Dhaka';
        $now = Carbon::now($timezone);
        $defaultFrom = $now->copy()->startOfMonth()->startOfDay();
        $defaultTo = $now->copy()->endOfDay();

//  $startDate = Carbon::create(2025, 9, 1)->startOfDay();
//            $endDate = Carbon::create(2025, 10, 30)->endOfDay();


//         $from = $startDate;

//         $to = $endDate;


        $from = $request->stockDateFrom
            ? Carbon::parse($request->stockDateFrom, $timezone)->startOfDay()
            : $defaultFrom;

        $to = $request->stockDateTo
            ? Carbon::parse($request->stockDateTo, $timezone)->endOfDay()
            : $defaultTo;






        $report =$this->getStockReport($from, $to);
        $reportSummaery =$this->pofitLost($from, $to);

        // return Inertia::render('Dashboard/Stock/StockPage', ['report' => $report]);


        return Inertia::render('Dashboard/Stock/StockPage', [
            'report' => $report,
            'reportSummaery' => $reportSummaery,
            'stockDateFrom' => $from->toDateString(),
            'stockDateTo' => $to->toDateString(),
        ]);




    }


}
