<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use Inertia\Inertia;
use App\Models\SaleInvoice;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    //====================show sales report page=================//
    public function reportPage()
    {
        return Inertia::render('Dashboard/Report/ReportPage');
    }

    //create sale report
    public function salesReport($FormDate, $ToDate, Request $request)
    {
        $user_id = $request->header('id');

        // Validate date format
        if (!strtotime($FormDate) || !strtotime($ToDate)) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        // Convert route parameters to proper date formats
        $from_data = date('Y-m-d', strtotime($FormDate));
        $to_data = date('Y-m-d', strtotime($ToDate));

        // Get data
        $total = SaleInvoice::where('user_id', $user_id)->whereDate('created_at', '>=', $from_data)->whereDate('created_at', '<=', $to_data)->sum('total');
        $vat = SaleInvoice::where('user_id', $user_id)->whereDate('created_at', '>=', $from_data)->whereDate('created_at', '<=', $to_data)->sum('vat');
        $discount = SaleInvoice::where('user_id', $user_id)->whereDate('created_at', '>=', $from_data)->whereDate('created_at', '<=', $to_data)->sum('discount');
        $paid = SaleInvoice::where('user_id', $user_id)->whereDate('created_at', '>=', $from_data)->whereDate('created_at', '<=', $to_data)->sum('paid');
        $payable = SaleInvoice::where('user_id', $user_id)->whereDate('created_at', '>=', $from_data)->whereDate('created_at', '<=', $to_data)->sum('customer_payable');

        $list = SaleInvoice::where('user_id', $user_id)->whereDate('created_at', '>=', $from_data)->whereDate('created_at', '<=', $to_data)->with('customer')->get();

        $data = [
            'total' => $total,
            'vat' => $vat,
            'discount' => $discount,
            'paid' => $paid,
            'payable' => $payable,
            'list' => $list,
            'FormDate' => $FormDate,
            'ToDate' => $ToDate,
        ];

        $timestamp = now()->format('Ymd_His'); // e.g., 20241130_120000
        $pdf = Pdf::loadView('report.saleReport', $data);
        return $pdf->download("saleReport_{$timestamp}.pdf");
    }

    //purchase report
    public function purchaseReport($FormDate, $ToDate, Request $request)
    {
        $user_id = $request->header('id');

        // Validate date format
        if (!strtotime($FormDate) || !strtotime($ToDate)) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        // Convert route parameters to proper date formats
        $from_data = date('Y-m-d', strtotime($FormDate));
        $to_data = date('Y-m-d', strtotime($ToDate));

        // Get data
        $total = PurchaseInvoice::where('user_id', $user_id)->whereDate('created_at', '>=', $from_data)->whereDate('created_at', '<=', $to_data)->sum('total');
        $paid = PurchaseInvoice::where('user_id', $user_id)->whereDate('created_at', '>=', $from_data)->whereDate('created_at', '<=', $to_data)->sum('paid');
        $account_payable = PurchaseInvoice::where('user_id', $user_id)->whereDate('created_at', '>=', $from_data)->whereDate('created_at', '<=', $to_data)->sum('account_payable');

        $list = PurchaseInvoice::where('user_id', $user_id)->whereDate('created_at', '>=', $from_data)->whereDate('created_at', '<=', $to_data)->with('supplier.brand')->get();

        $data = [
            'total' => $total,
            'paid' => $paid,
            'account_payable' => $account_payable,
            'list' => $list,
            'FormDate' => $FormDate,
            'ToDate' => $ToDate,
        ];

        $timestamp = now()->format('Ymd_His'); // e.g., 20241130_120000
        $pdf = Pdf::loadView('report.purchaseReport', $data);
        return $pdf->download("purchaseReport_{$timestamp}.pdf");
    }
}
