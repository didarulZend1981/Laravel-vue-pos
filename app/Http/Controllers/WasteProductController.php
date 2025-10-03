<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceProducts;
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
                // dd($results);
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









}
