<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\PurchaseInvoice;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseInvoiceProducts;

class PurchaseInvoiceController extends Controller
{
    //=========================show invoice page=======================//
    public function showPurchaseInvoiceList(Request $request)
    {
        $user_id = $request->header('id');
        $purchase_invoices = PurchaseInvoice::where('user_id', $user_id)->with('supplier.brand')->orderBy('id', 'desc')->get();
        return Inertia::render('Dashboard/Invoice/PurchaseInvoicePage', ['purchase_invoices' => $purchase_invoices]);
    }

    // =======================show create purchase invoice page=======================//
    public function showPurchaseInvoice(Request $request)
    {
        $user_id = $request->header('id');
        $suppliers = Supplier::where('user_id', $user_id)->orderBy('id', 'desc')->get();
        $products = Product::where('user_id', $user_id)->where('stock', '>=', 0)->get();

        return Inertia::render('Dashboard/Purchase/PurchasePage', [
            'suppliers' => $suppliers,
            'products' => $products,
        ]);
    }

    //=======================create purchase invoice=========================//
    public function createPurchaseInvoice(Request $request){
         DB::beginTransaction();
        // dd($request->all());

        try {
            $user_id = $request->header('id');
            $total = $request->input('total');
            $paid = $request->input('paid');
            $account_payable = $request->input('account_payable');
            $supplier_id = $request->input('supplier_id');
            $invoice_name = $request->input('invoice_name');
            $products = $request->input('products');
            $created_at = $request->input('purchase_date');

            if ($invoice_name == null) {
                $datePart = date('Ymd'); // ২০২৫০৯০৮
                $randomPart = mt_rand(1000, 9999); // ৪ সংখ্যার র‍্যান্ডম নাম্বার
                $invoice_name = 'PUR-' . $datePart . '-' . $randomPart;

                // ইউনিকনেস নিশ্চিত করতে চাইলে:
                while (PurchaseInvoice::where('invoice_name', $invoice_name)->exists()) {
                    $randomPart = mt_rand(1000, 9999);
                    $invoice_name = 'PUR-' . $datePart . '-' . $randomPart;
                }
            }

            // $invoice_name = !empty($request->input('invoice_name'))? $request->input('invoice_name'): 'Default Name';

            // Validation for required fields
            if (!$supplier_id || empty($products)) {
                return response()->json(
                    [
                        'message' => 'Supplier and products are required',
                        'status' => false,
                        'code' => 422,
                    ],
                    422,
                );
            }

            // Create Sale Invoice
            $purchase_invoice = PurchaseInvoice::create([
                'invoice_name' => $invoice_name,
                'user_id' => $user_id,
                'total' => $total,
                'paid' => $paid,
                'rest' => $request->input('rest'),
                'account_payable' => $account_payable,
                'supplier_id' => $supplier_id,
                'created_at' => $created_at,


            ]);

            // Attach products to the invoice and update stock
            foreach ($products as $product) {
                $productRecord = Product::find($product['product_id']);

                // Attach product to invoice
                PurchaseInvoiceProducts::create([
                    'purchase_invoice_id' => $purchase_invoice->id,
                    'user_id' => $user_id,
                    'product_id' => $product['product_id'],
                    'qty' => $product['qty'],
                    'purchase_price' => $product['purchase_price'],
                    'created_at' => $created_at,
                ]);

                // Deduct stock
                $productRecord->stock += $product['qty'];
                $productRecord->save();
            }

            DB::commit();

            // Return success response
            return response()->json(
                [
                    'message' => 'Invoice created successfully',
                    'status' => true,
                    'code' => 200,
                ],
                200,
            );
        } catch (Exception $e) {
            DB::rollBack();

            // Return error response
            return response()->json(
                [
                    'message' => 'Error creating invoice. ' . $e->getMessage(),
                    'status' => false,
                    'code' => 500,
                ],
                500,
            );
        }


    }

    //======================show custom invoice create page===================//
    public function showCustomPurchaseInvoice(Request $request)
    {
        $user_id = $request->header('id');
        $brands = Brand::select('id', 'name')->get();
        $categories = Category::where('user_id', $user_id)->get();
        return Inertia::render('Dashboard/Purchase/CustomPurchasePage', [
            'brands' => $brands,
            'categories' => $categories,
        ]);
    }

    //=====================create custom purchase===========================//
    public function createCustomPurchaseInvoice(Request $request)
    {
        DB::beginTransaction();

        try {
            $user_id = $request->header('id');

            if (!$user_id) {
                return response()->json(
                    [
                        'message' => 'Unauthorized',
                        'status' => false,
                        'code' => 401,
                    ],
                    401,
                );
            }

            // Step 1: Insert into supplier table
            $supplier = Supplier::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'company_name' => $request->input('company_name'),
                'user_id' => $user_id,
            ]);

            // Step 2: Insert products into product table
            $products = $request->input('products');

            if (empty($products)) {
                return response()->json(
                    [
                        'message' => 'Products are required',
                        'status' => false,
                        'code' => 422,
                    ],
                    422,
                );
            }

            $savedProducts = [];

            foreach ($products as $product) {
                // Create the product
                $newProduct = Product::create([
                    'name' => $product['name'],
                    'brand_id' => $product['brand_id'],
                    'category_id' => $product['category_id'],
                    'description' => $product['description'] ?? null,
                    'price' => $product['price'],
                    'unit' => $product['unit'],
                    'stock' => $product['qty'],
                    'user_id' => $user_id,
                ]);

                // Store created product with additional info for invoice
                $savedProducts[] = [
                    'product_id' => $newProduct->id,
                    'qty' => $product['qty'],
                    'purchase_price' => $product['purchase_price'],
                ];
            }

            // Step 3: Create purchase invoice
            $purchase_invoice = PurchaseInvoice::create([
                'invoice_name' => $request->input('invoice_name'),
                'total' => $request->input('total'),
                'paid' => $request->input('paid'),
                'rest' => $request->input('rest'),
                'account_payable' => $request->input('account_payable'),
                'user_id' => $user_id,
                'supplier_id' => $supplier->id,
            ]);

            // Step 4: Insert into purchase_invoice_products
            foreach ($savedProducts as $item) {
                PurchaseInvoiceProducts::create([
                    'purchase_invoice_id' => $purchase_invoice->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'purchase_price' => $item['purchase_price'],
                    'user_id' => $user_id,
                ]);
            }

            DB::commit();

            return response()->json(
                [
                    'message' => 'Invoice created successfully',
                    'status' => true,
                    'code' => 200,
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(
                [
                    'message' => 'Error creating invoice. ' . $e->getMessage(),
                    'status' => false,
                    'code' => 500,
                ],
                500,
            );
        }
    }

    //==========================get invoice by id=========================//
    public function purchaseInvoiceDetails(Request $request, $id)
    {
        $user_id = $request->header('id');
        $supplier_id = $request->input('supplier_id');

        $supplier_details = Supplier::with('brand')->where('user_id', $user_id)->where('id', $supplier_id)->first();
        $invoice_details = PurchaseInvoice::where('user_id', $user_id)->where('id', $id)->first();
        $invoice_products = PurchaseInvoiceProducts::where('purchase_invoice_id', $id)->where('user_id', $user_id)->with('products')->get();

        if ($supplier_details && $invoice_details && $invoice_products) {
            return response()->json(
                [
                    'supplier_details' => $supplier_details,
                    'invoice_details' => $invoice_details,
                    'invoice_products' => $invoice_products,
                ],
                200,
            );
        } else {
            return response()->json(['message' => 'No invoice found'], 404);
        }
    }

    //======================purchase invoice details========================//
    public function purchaseInvoiceDelete(Request $request, $id)
    {
        $user_id = $request->header('id');

        DB::beginTransaction();

        try {
            $purchase_invoice_product = PurchaseInvoiceProducts::where('purchase_invoice_id', $id)
                ->where('user_id', $user_id)
                ->first();

            // Check if the purchase invoice product exists
            if ($purchase_invoice_product) {
                $stock = $purchase_invoice_product->qty;

                // Retrieve the corresponding product
                $product = Product::where('id', $purchase_invoice_product->product_id)
                    ->where('user_id', $user_id)
                    ->first();

                if ($product) {
                    // Deduct stock based on purchase invoice product quantity
                    $product->stock = $product->stock - $stock;
                    $product->save(); // Update stock in database
                }
            }

            $purchase_invoice_product = PurchaseInvoiceProducts::where('purchase_invoice_id', $id)->where('user_id', $user_id)->delete();
            $purchase_invoice = PurchaseInvoice::where('id', $id)->where('user_id', $user_id)->delete();

            if ($purchase_invoice_product && $purchase_invoice) {
                DB::commit();
                $data = ['message' => 'Invoice deleted successfully', 'status' => true, 'code' => 200];
                return redirect()->back()->with($data);
            } else {
                $data = ['message' => 'Invoice not found', 'status' => false, 'code' => 404];
                return redirect()->back()->with($data);
            }
        } catch (Exception $e) {
            DB::rollBack();
            $data = ['message' => 'Error deleting invoice', 'status' => false, 'code' => 500];
            return redirect()->back()->with($data);
        }
    }
}
