<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceProducts;
use App\Models\SaleInvoice;
use App\Models\SaleInvoiceProducts;
use App\Models\WasteProduct;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class WasteProductController extends Controller
{


        private function getWasteProducts(){
            return WasteProduct::with(['product', 'purchaseInvoice'])
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($wp) {
                    return [
                        'id'             => $wp->id,
                        'waste_qty'      => $wp->waste_qty,
                        'purchase_price' => $wp->purchase_price,
                        'case'           => $wp->case,
                        'refound'        => $wp->refound,
                        'invoice_name'   => $wp->purchaseInvoice->invoice_name ?? null,
                        'invoice_id'   => $wp->purchaseInvoice->id ?? null,
                        'invoice_supplier_id'   => $wp->purchaseInvoice->supplier_id ?? null,
                        'product_name'   => $wp->product->name ?? null,
                        'image'          => $wp->product->image ?? null,
                        'product_id'   => $wp->product->id ?? null,
                    ];
                });
        }
        public function generateInvoiceCode(string $prefix = 'Refund'): string{
            $datePart = date('Ymd'); // e.g., 20251001
            $randomPart = mt_rand(1000, 9999);
            $invoiceCode = $prefix . '-' . $datePart . '-' . $randomPart;

            // Ensure uniqueness
            while (PurchaseInvoice::where('invoice_name', $invoiceCode)->exists()) {
                $randomPart = mt_rand(1000, 9999);
                $invoiceCode = $prefix . '-' . $datePart . '-' . $randomPart;
            }

            return $invoiceCode;
        }


        public function createRefundInvoice(Request $request, $user_id, $invoice_Refound = null){
                // যদি ইনভয়েস না থাকে, নতুন তৈরি করো
                $invoice_Refound = $request->input('invoice_Refound') ?: $this->generateInvoiceCode('Refund');

                return PurchaseInvoice::create([
                    'invoice_name'      => $invoice_Refound,
                    'user_id'           => $user_id,
                    'total'             => "00",
                    'paid'              => "00",
                    'rest'              => "00",
                    'account_payable'   => "00",
                    'supplier_id'       => $request->input('invoice_supplier_id'),
                    'case'              => $request->input('case'),
                    'refund'            => $request->input('invoice_name'),
                    'created_at'        => $request->input('RefundDate'),
                ]);
        }

        public function PurchaseInvoiceProducts(Request $request, $user_id, $purchase_invoice_id){
                // যদি ইনভয়েস না থাকে, নতুন তৈরি করো


                PurchaseInvoiceProducts::create([
                    'purchase_invoice_id' => $purchase_invoice_id,
                    'user_id' => $user_id,
                    'product_id' => $request->input('product_id'),
                    'qty' => $request->input('qty'), // ✅ FIXED HERE
                    'stock_qty' => $request->input('qty'),
                    'purchase_price' => $request->input('purchase_price'),
                    'manufacture_date' => $request->input('ManufectureDate'),
                    'expiry_date' => $request->input('ExpaireDate'),
                    'created_at' => $request->input('RefundDate'),
                ]);

        }

        public function Products(Request $request){
                $productRecord = Product::find($request->input('product_id'));
                $productRecord->stock += $request->input('qty');
                $productRecord->save();

        }

        public function updateOrDeleteWasteProduct($modelClass,$waste_products_id, $qty){
            $wasteProduct = $modelClass::find($waste_products_id);

                if ($wasteProduct) {
                    $remainingQty = $wasteProduct->waste_qty - $qty;

                    if ($remainingQty > 0) {
                        $wasteProduct->waste_qty = $remainingQty;
                        $wasteProduct->save();
                    } else {
                        $modelClass::where('id', $waste_products_id)
                            ->delete();
                    }

                    return $this->getWasteProducts();
                } else {
                    return false;
                }

        }


        public function moveExpiredToWaste() {
            $today = Carbon::today()->toDateString();

            // 1. expired ও stock>0 পণ্যগুলো নাও
            $expiredProducts = PurchaseInvoiceProducts::where('stock_qty', '>', 0)
                                ->whereDate('expiry_date', '<=', $today)
                                ->get();

            // if ($expiredProducts->isEmpty()) {
            //     return response()->json(['message' => 'No expired products found.']);
            // }

            if ($expiredProducts->isEmpty()) {
                    return Inertia::render('Dashboard/Waste/WastePage', [
                        'message' => 'No expired products till now.',
                        'wasteProduct' => $this->getWasteProducts(),
                    ]);
                }

            DB::transaction(function() use ($expiredProducts) {
                foreach ($expiredProducts as $product) {

                    // 2. waste_products এ insert
                    WasteProduct::create([
                        'product_id' => $product->product_id,
                        'waste_qty' => $product->stock_qty,
                        'purchase_price' => $product->purchase_price,
                        'case' => "date expiry",
                        'purchase_invoice_id' => $product->purchase_invoice_id,
                        'refound' => 0,
                    ]);

                    // 3. purchase_invoice_products এর stock_qty update
                    $qtyToReduce = $product->stock_qty;
                    $product->stock_qty = 0;
                    $product->save();

                    // 4. products টেবিল থেকে product বের করে stock update করো
                    $productStock = Product::find($product->product_id);
                    if ($productStock) {
                        $productStock->stock -= $qtyToReduce;
                        if ($productStock->stock < 0) $productStock->stock = 0;
                        $productStock->save();
                    }
                }
            });



           return Inertia::render('Dashboard/Waste/WastePage', [
                'wasteProduct' => $this->getWasteProducts(),
                'message' => 'Expired products till now.',
            ]);

            // return response()->json(['message' => 'Expired products moved to waste successfully.']);
        }






        public function allWaste() {
                // test dd($results);
                return Inertia::render('Dashboard/Waste/WastePage', [
                    'wasteProduct' =>  $this->getWasteProducts(),
                ]);
        }


        public function wasteProductRefound(Request $request){



        try {
            $user_id = $request->header('id');
            $invoice_Refound =null;



                 $purchase_invoice = $this->createRefundInvoice($request, $user_id, $invoice_Refound);

                $this->PurchaseInvoiceProducts($request, $user_id, $purchase_invoice->id);

                $this->Products($request);

               $wasteProd=$this->updateOrDeleteWasteProduct(
                    \App\Models\WasteProduct::class,
                    $request->input('waste_products_id'),
                    $request->input('qty')

                );
                // $wasteProd= $this->getWasteProducts();

                if (!$wasteProd) {
                    $data = ['message' => 'waste product  not refund', 'status' => false, 'code' => 404];
                    return redirect()->back()->with($data);
                }
                $data = ['message' => 'waste product   refund successfully', 'status' => true, 'code' => 200];
                return redirect()->back()->with($data);

                // return to_route('waste.page')->with('message', 'Refund completed.');

            } catch (Exception $e) {
                // DB::rollBack();

                // Return error response
                return response()->json(
                    [
                        'message' => 'Error creating invoice. ' . $e->getMessage(),
                        'status' => false,
                        'code' => 200,
                    ],
                    200,
                );
            }






        }

        public function damageProduct(Request $request){
            // dd($request->all());


            // 2. waste_products এ insert
                    WasteProduct::create([
                        'product_id' => $request->input('product_id'),
                        'waste_qty' => $request->input('waste_qty'),
                        'purchase_price' =>$request->input('purchase_price'),
                        'case' => $request->input('case'),
                        'purchase_invoice_id' => $request->input('purchase_invoice_id'),
                        'refound' => 0,
                    ]);

                    // // 3. purchase_invoice_products এর stock_qty update
                        $qtyToReduce = $request->input('waste_qty');
                        $productStock = PurchaseInvoiceProducts::find($request->input('id'));

                        $productStock->stock_qty -= $qtyToReduce;
                        $productStock->save();

                    // // 4. products টেবিল থেকে product বের করে stock update করো
                    $productStock = Product::find($request->input('product_id'));
                    if ($productStock) {
                        $productStock->stock -= $qtyToReduce;
                        if ($productStock->stock < 0) $productStock->stock = 0;
                        $productStock->save();
                    }


                     return Inertia::render('Dashboard/Waste/WastePage', [
                        'wasteProduct' => $this->getWasteProducts(),
                        'message' => 'Expired products till now.',
                    ]);

        }

// ===================== using open summery ====================

        public function getOpening($startDate, $modelClass = \App\Models\PurchaseInvoiceProducts::class, $qty, $purchase_price){
                $openingPurchases = $modelClass::where('created_at', '<', $startDate)->get();

                $openingPurchaseQty = $openingPurchases->sum($qty);

                $openingPurchaseAmount = $openingPurchases->sum(function ($item) use ($qty, $purchase_price) {
                    return $item->$qty * $item->$purchase_price;
                });

                return [
                    'qty' => $openingPurchaseQty,
                    'amount' => $openingPurchaseAmount,
                ];
         }





        //  public function pofitLost(){

        //    $startDate = Carbon::create(2025, 10, 1)->startOfDay();
        //    $endDate = Carbon::create(2025, 10, 30)->endOfDay();

        //     // === Opening Balance (startDate এর আগ পর্যন্ত) ===

        //     $openingPurchase=$this->getOpening($startDate, \App\Models\PurchaseInvoiceProducts::class, 'qty', 'purchase_price');
        //     $openingPurchaseQty = $openingPurchase['qty'];
        //     $openingPurchaseAmount = $openingPurchase['amount'];
        //     $openingSales=$this->getOpening($startDate, \App\Models\SaleInvoiceProducts::class, 'qty', 'sale_price');
        //     $openingSaleQty = $openingSales['qty'];
        //     $openingSaleAmount = $openingSales['amount'];

        //     $openingQty = $openingPurchaseQty - $openingSaleQty;


        //     $openingValue = $openingPurchaseAmount -  $openingSaleAmount;



        //     // === Period Purchases ===
        //     $periodPurchases = PurchaseInvoiceProducts::whereBetween('created_at', [$startDate, $endDate])->get();
        //     $periodPurchaseQty = $periodPurchases->sum('qty');
        //     $periodPurchaseAmount = $periodPurchases->sum(function ($item) {
        //         return $item->stock_qty * $item->purchase_price;
        //     });

        //     // === Period Sales ===
        //     $periodSales = SaleInvoiceProducts::whereBetween('created_at', [$startDate, $endDate])->get();


        //     // dd($periodSales);
        //     $periodSaleQty = $periodSales->sum('qty');
        //     $periodSaleAmount = $periodSales->sum(function ($item) {
        //         return $item->qty * $item->sale_price;
        //     });

        //     $periodSaleAmountProfit = $periodSales->sum(function ($item) {
        //         return $item->qty * ($item->sale_price-$item->rate);
        //     });

        //     // === Closing calculation ===
        //     $closingQty = $openingQty + $periodPurchaseQty - $periodSaleQty;

        //     $closingValue=  $openingValue+$periodPurchaseAmount-$periodSaleAmount;

        //     $totalPurchaseQty = $openingPurchaseQty + $periodPurchaseQty;
        //     $totalPurchaseAmount = $openingPurchaseAmount + $periodPurchaseAmount;

        //     // $avgPurchasePriceClosing = $totalPurchaseQty > 0 ? ($totalPurchaseAmount / $totalPurchaseQty) : 0;
        //     // $closingValue = $closingQty * $avgPurchasePriceClosing;

        //     $profitOrLoss = ($openingValue + $periodPurchaseAmount + $periodSaleAmount)-($openingValue + $periodPurchaseAmount);


        //     // --- Return the summary ---
        //     return [
        //         'opening_qty' =>  $openingQty,
        //         'opening_value' => round($openingValue, 2),

        //         'period_purchase_qty' => $periodPurchaseQty,
        //         'period_purchase_amount' => round($periodPurchaseAmount, 2),
        //         'period_sale_qty' => $periodSaleQty,
        //         'period_sale_amount' => round($periodSaleAmount, 2),


        //         'total_purchase_qty' => $totalPurchaseQty,
        //         'total_purchase_amount' => round($totalPurchaseAmount, 2),


        //         'closing_qty' => $closingQty,
        //         'closing_value' => round($closingValue, 2),
        //         'profit_or_loss' => round($periodSaleAmountProfit,2),
        //     ];


        //  }




        public function pofitLost(){
            $startDate = Carbon::create(2025, 10, 1)->startOfDay();
            $endDate = Carbon::create(2025, 10, 30)->endOfDay();

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






         public function productProfitReport(){

           $startDate = Carbon::create(2025, 10, 1)->startOfDay();
           $endDate = Carbon::create(2025, 10, 30)->endOfDay();

            $products = Product::all(); // যদি product টেবিল থাকে

            $productReports = $products->map(function ($product) use ($startDate, $endDate) {

                // === Opening balance (before $startDate) ===
                $openingPurchases = PurchaseInvoiceProducts::where('product_id', $product->id)
                    ->where('created_at', '<', $startDate)
                    ->get();


                $openingPurchaseQty = $openingPurchases->sum('qty');
                $openingPurchaseAmount = $openingPurchases->sum(function ($item) {
                    return $item->qty * $item->purchase_price;
                });

                // === Opening Sale (before $startDate) ===

                $openingSales = SaleInvoiceProducts::where('product_id', $product->id)
                    ->where('created_at', '<', $startDate)
                    ->get();

                $openingSaleQty = $openingSales->sum('qty');
                $openingSaleAmount = $openingSales->sum(function ($item) {
                    return $item->qty * $item->sale_price;
                });

                $openingQty = $openingPurchaseQty - $openingSaleQty;
                $openingValue = $openingPurchaseAmount- $openingSaleAmount;

                // === Current period (between startDate and endDate PurchaseInvoiceProducts) ===
                $periodPurchases = PurchaseInvoiceProducts::where('product_id', $product->id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get();


                $periodPurchaseQty = $periodPurchases->sum('qty');
                $periodPurchaseAmount = $periodPurchases->sum(function ($item) {
                    return $item->qty * $item->purchase_price;
                });
                // === Current period (between startDate and endDate SaleInvoiceProducts) ===
                $periodSales = SaleInvoiceProducts::where('product_id', $product->id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get();

                $periodSaleQty = $periodSales->sum('qty');
                $periodSaleAmount = $periodSales->sum(function ($item) {
                    return $item->qty * $item->sale_price;
                });

                 $periodSaleAmountProfit = $periodSales->sum(function ($item) {
                    return $item->qty * ($item->sale_price-$item->rate);
                });

                  $periodSaleAmountPurches = $periodSales->sum(function ($item) {
                    return $item->qty * $item->sale_price;
                });

                // === Closing balance ===
                $closingQty = $openingQty + $periodPurchaseQty - $periodSaleQty;
                $closingValue= $openingValue + $periodPurchaseAmount - $periodSaleAmount;

                // For avg purchase price during current period + opening combined (weighted average)
                $totalQty = $openingPurchaseQty + $periodPurchaseQty;
                $totalPurchaseAmount = $openingPurchaseAmount + $periodPurchaseAmount;

                $avgPurchasePriceClosing = $totalQty > 0 ? $totalPurchaseAmount / $totalQty : 0;
                // $closingValue = $closingQty * $avgPurchasePriceClosing;

                $profitOrLoss = round(($openingValue + $periodPurchaseAmount + $periodSaleAmount)-($openingValue + $periodPurchaseAmount),2);

                return [
                    'product_id' => $product->id,
                    'product_name' => $product->name,



                    // opening
                    'opening_qty' => $openingQty,
                    'opening_value' => round($openingValue, 2),

                    // period_purchase
                    'period_purchase_qty' => $periodPurchaseQty,
                    'period_purchase_amount' => round($periodPurchaseAmount, 2),


                    // period_sale
                    'period_sale_qty' => $periodSaleQty,
                    'period_sale_amount' => round($periodSaleAmount, 2),


                    // closing_qty
                    'closing_qty' => $closingQty,
                    'closing_value' => round($closingValue, 2),




                    // 'profit_or_loss' => round(($openingValue + $periodPurchaseAmount + $periodSaleAmount)-($openingValue + $periodPurchaseAmount),2),
                    'profit_or_loss' => round($periodSaleAmountProfit, 2),
                    'profit_percentage' => $periodSaleAmountPurches != 0 ? round(($periodSaleAmountProfit * 100) / $periodSaleAmountPurches, 2)
    : 0,

                ];
            });


            dd($productReports);


            }














}
