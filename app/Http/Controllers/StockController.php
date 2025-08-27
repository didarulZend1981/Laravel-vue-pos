<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StockController extends Controller
{
    public function StockPage(){
        //  return Inertia::render('Dashboard/Stock/StockPage');
         $timezone = 'Asia/Dhaka';
         $now = Carbon::now($timezone);
         $from = $now->copy()->startOfMonth()->startOfDay();
         $to = $now->copy()->endOfDay();
         $products = Product::with(['purchases', 'sales'])->get();
        $report = [];
        foreach ($products as $product) {
            $opening = $product->purchases->where('created_at', '<', $from)->sum('qty') -
                       $product->sales->where('created_at', '<', $from)->sum('qty');

            $purchase = $product->purchases->whereBetween('created_at', [$from, $to])->sum('qty');
            $sale = $product->sales->whereBetween('created_at', [$from, $to])->sum('qty');

            $closing = $opening + $purchase - $sale;

            $report[] = [
                'product_name' => $product->name,
                'opening' => $opening,
                'purchase' => $purchase,
                'sale' => $sale,
                'closing' => $closing,
            ];
        }



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


        $products = Product::with(['purchases', 'sales'])->get();
        $report = [];
        foreach ($products as $product) {
            $opening = $product->purchases->where('created_at', '<', $from)->sum('qty') -
                       $product->sales->where('created_at', '<', $from)->sum('qty');

            $purchase = $product->purchases->whereBetween('created_at', [$from, $to])->sum('qty');
            $sale = $product->sales->whereBetween('created_at', [$from, $to])->sum('qty');

            $closing = $opening + $purchase - $sale;

            $report[] = [
                'product_name' => $product->name,
                'image' => $product->image,
                'opening' => $opening,
                'purchase' => $purchase,
                'sale' => $sale,
                'closing' => $closing,
            ];
        }



        // return Inertia::render('Dashboard/Stock/StockPage', ['report' => $report]);


        return Inertia::render('Dashboard/Stock/StockPage', [
            'report' => $report,
            'stockDateFrom' => $from->toDateString(),
            'stockDateTo' => $to->toDateString(),
        ]);




    }


}
