<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\SaleInvoice;
use Illuminate\Http\Request;
use App\Models\PurchaseInvoice;
use App\Models\SaleInvoiceProducts;
use App\Http\Controllers\Controller;
use App\Models\PurchaseInvoiceProducts;

class DashboardController extends Controller
{
    //======================get deashboard summary========================//
    //dashboard summary
    public function showDashboard(Request $request)
    {
        $user_id = $request->header('id');

        // Products, Categories, Brands/Companies, Customers & Suppliers
        $products = Product::where('user_id', $user_id)->count();
        $categories = Category::where('user_id', $user_id)->count();
        $companies = Brand::where('user_id', $user_id)->count();
        $customers = Customer::where('user_id', $user_id)->count();
        $suppliers = Supplier::where('user_id', $user_id)->count();

        // total sales infomation
        $saleInvoices = SaleInvoice::where('user_id', $user_id)->count();
        $totalAmount = SaleInvoice::where('user_id', $user_id)->sum('subtotal');
        $totalReceivedAmount = SaleInvoice::where('user_id', $user_id)->sum('paid');
        $acReceivableAmount = SaleInvoice::where('user_id', $user_id)->sum('customer_payable');

        //total purchase informaiton
        $purchaseInvoices = PurchaseInvoice::where('user_id', $user_id)->count();
        $totalPurshaseAmount = PurchaseInvoice::where('user_id', $user_id)->sum('total');
        $totalPaidAmount = PurchaseInvoice::where('user_id', $user_id)->sum('paid');
        $accPayableAmount = PurchaseInvoice::where('user_id', $user_id)->sum('account_payable');

        // daily business update
        $todaysSoldCount = SaleInvoiceProducts::where('user_id', $user_id)
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->count();
        $todaysSoldAmount = SaleInvoice::where('user_id', $user_id)
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->sum('subtotal');
        $todaysRestAmount = SaleInvoice::where('user_id', $user_id)
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->sum('rest');
        $todaysReceivedAmount = SaleInvoice::where('user_id', $user_id)
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->sum('paid');
        $todaysPurchaseProductsCount = PurchaseInvoiceProducts::where('user_id', $user_id)
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->count();
        $todaysPurchasedAmount = PurchaseInvoice::where('user_id', $user_id)
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->sum('total');
        $todaysPaidAmount = PurchaseInvoice::where('user_id', $user_id)
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->sum('paid');
        $todaysAccPayableAmount = PurchaseInvoice::where('user_id', $user_id)
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->sum('account_payable');

        // stock out product count
        $StockInDanger = Product::where('user_id', $user_id)->where('stock', '<=', 5)->count();

        return Inertia::render('Dashboard/DashboardPage', [
            // Products, Categories, Brands/Companies, Customers & Suppliers
            'products' => $products,
            'categories' => $categories,
            'companies' => $companies,
            'customers' => $customers,
            'suppliers' => $suppliers,

            // total sales infomation
            'saleInvoices' => $saleInvoices,
            'totalAmount' => $totalAmount,
            'totalReceivedAmount' => $totalReceivedAmount,
            'acReceivableAmount' => $acReceivableAmount,

            // total purchase information
            'purchaseInvoices'=> $purchaseInvoices,
            'totalPurshaseAmount'=> $totalPurshaseAmount,
            'totalPaidAmount'=> $totalPaidAmount,
            'accPayableAmount'=> $accPayableAmount,

            // daily update
            'todaysSoldCount' => $todaysSoldCount,
            'todaysSoldAmount' => $todaysSoldAmount,
            'todaysRestAmount' => $todaysRestAmount,
            'todaysReceivedAmount' => $todaysReceivedAmount,
            'todaysPurchaseProductsCount' => $todaysPurchaseProductsCount,
            'todaysPurchasedAmount' => $todaysPurchasedAmount,
            'todaysPaidAmount' => $todaysPaidAmount,
            'todaysAccPayableAmount' => $todaysAccPayableAmount,

            // danger stock position alert
            'StockInDanger' => $StockInDanger,
        ]);
    }
}
