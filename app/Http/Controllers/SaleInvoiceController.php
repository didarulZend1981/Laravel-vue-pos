<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Customer;
use App\Models\SaleInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SaleInvoiceProducts;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\ValidateCustomInvoiceRequest;
use App\Models\PurchaseInvoiceProducts;

class SaleInvoiceController extends Controller
{
    public function showCreateSale(Request $request)
    {
        $user_id = $request->header('id');
        $customers = Customer::where('user_id', $user_id)->orderBy('id', 'desc')->get();
        $products = Product::where('user_id', $user_id)->where('stock', '>', 0)->orderByDesc('id')->get();
        $purches = PurchaseInvoiceProducts::with('products:id,name,image')->where('user_id', $user_id)->get();


        // dd($purches);
        // return response()->json([
        //     'status'=> true,
        //     'message'=> 'Events data',
        //     'data'=> $products,
        // ], 200);


        // return Inertia::render('Dashboard/Sale/SalePage', [
        //     'customers' => $customers, // Use a key-value pair
        //     'products' => $products, // Use a key-value pair
        // ]);

        return Inertia::render('Dashboard/Sale/SalePage', [
            'customers' => $customers, // Use a key-value pair
            'purches' => $purches, // Use a key-value pair
        ]);
    }

    //==========================create invoice=========================//
    public function createSaleInvoice(Request $request)
    {



        // dd($request->all());
        DB::beginTransaction();

        try {
            $invoice_name = $request->input('invoice_name');
            $total = $request->input('total');
            $discount = $request->input('discount');
            $vat = $request->input('vat');
            $subtotal = $request->input('subtotal');
            $paid = $request->input('paid');
            $rest = $request->input('rest');
            $customer_payable = $request->input('customer_payable');
            $customer_id = $request->input('customer_id');
            $products = $request->input('products');
            $user_id = $request->header('id');

            // Validation for required fields
            if (!$customer_id) {
                return response()->json(
                    [
                        'message' => 'Please! pick a customer first',
                        'status' => false,
                        'code' => 422,
                    ],
                    422,
                );
            }

            // products validation
            if (empty($products)) {
                return response()->json(
                    [
                        'message' => 'Please! pick products',
                        'status' => false,
                        'code' => 422,
                    ],
                    422,
                );
            }

            // Create Sale Invoice
            $sale_invoice = SaleInvoice::create([
                'invoice_name' => $invoice_name,
                'total' => $total,
                'discount' => $discount,
                'vat' => $vat,
                'subtotal' => $subtotal,
                'paid' => $paid,
                'rest' => $rest,
                'customer_payable' => $customer_payable,
                'user_id' => $user_id,
                'customer_id' => $customer_id,
            ]);

            // Attach products to the invoice and update stock
            foreach ($products as $product) {
                // Check if product exists
                $productRecord = Product::find($product['product_id']);
                $PurchaseInvoiceProductsRecord = PurchaseInvoiceProducts::find($product['id']);
                if (!$productRecord) {
                    DB::rollBack();
                    return response()->json(
                        [
                            'message' => 'Product not found: ID ' . $product['product_id'],
                            'status' => false,
                            'code' => 404,
                        ],
                        404,
                    );
                }

                // Check if enough stock is available
                if ($productRecord->stock < $product['qty']) {
                    DB::rollBack();
                    return response()->json(
                        [
                            'message' => 'Insufficient stock for product: ' . $productRecord->name,
                            'status' => false,
                            'code' => 400,
                        ],
                        400,
                    );
                }

                // Attach product to invoice
                SaleInvoiceProducts::create([
                    'sale_invoice_id' => $sale_invoice->id,
                    'user_id' => $user_id,
                    'product_id' => $product['product_id'],
                    'qty' => $product['qty'],
                    'rate' => $product['rate'],
                    'sale_price' => $product['sale_price'],
                    'amount' => $product['total'],
                ]);

                // Deduct stock
                $productRecord->stock -= $product['qty'];
                $productRecord->save();

                $PurchaseInvoiceProductsRecord->stock_qty -= $product['qty'];
                $PurchaseInvoiceProductsRecord->save();
            }

            DB::commit();

            // Return success response
            return response()->json(
                [
                    'message' => 'Invoice created successfully, and stock updated.',
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

    //==================show custome sale create page==========================//
    public function showCustomeSaleCreate()
    {
        return Inertia::render('Dashboard/Sale/CustomSalePage');
    }

    //====================create custom sale for random customer================//
    public function customeSaleCreate(Request $request)
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

            // Insert into customers table
            $customer = Customer::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'zip' => $request->input('zip'),
                'address' => $request->input('address'),
                'comment' => $request->input('comment'),
                'user_id' => $user_id,
            ]);

            // Insert into sale_invoices table
            $invoice = SaleInvoice::create([
                'invoice_name' => $request->input('invoice_name'),
                'total' => $request->input('total'),
                'discount' => $request->input('discount'),
                'vat' => $request->input('vat'),
                'subtotal' => $request->input('subtotal'),
                'paid' => $request->input('paid'),
                'rest' => $request->input('rest'),
                'customer_payable' => $request->input('customer_payable'),
                'user_id' => $user_id,
                'customer_id' => $customer->id,
            ]);

            $products = $request->input('products');

            // Insert products into sale_invoice_products table
            foreach ($products as $product) {
                SaleInvoiceProducts::create([
                    'sale_invoice_id' => $invoice->id,
                    'user_id' => $user_id,
                    'product_id' => null,
                    'product_name' => $product['product_name'],
                    'qty' => $product['qty'],
                    'rate' => $product['rate'] ?? 0,
                    'sale_price' => $product['sale_price'],
                    'amount' => 200,
                ]);
            }

            DB::commit();

            return response()->json(
                [
                    'message' => 'Invoice created successfully!',
                    'status' => true,
                    'code' => 200,
                ],
                200,
            );
        } catch (Exception $e) {
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

    //=========================show invoice page=======================//
    public function showInvoice(Request $request)
    {
        $user_id = $request->header('id');
        $sale_invoices = SaleInvoice::where('user_id', $user_id)->with('customer')->orderBy('id', 'desc')->get();
        return Inertia::render('Dashboard/Invoice/SaleInvoicePage', ['sale_invoices' => $sale_invoices]);
    }

    //==========================get invoice by id=========================//
    public function invoiceDetails(Request $request, $id)
    {
        $user_id = $request->header('id');
        $customer_id = $request->input('customer_id');

        $customer_details = Customer::where('user_id', $user_id)->where('id', $customer_id)->first();
        $invoice_details = SaleInvoice::where('user_id', $user_id)->where('id', $id)->first();
        $invoice_products = SaleInvoiceProducts::where('sale_invoice_id', $id)->where('user_id', $user_id)->with('products')->get();

        if ($customer_details && $invoice_details && $invoice_products) {
            return response()->json(
                [
                    'customer_details' => $customer_details,
                    'invoice_details' => $invoice_details,
                    'invoice_products' => $invoice_products,
                ],
                200,
            );
        } else {
            return response()->json(['message' => 'No invoice found'], 404);
        }
    }

    //==============================invoice delete ==============================//
    public function invoiceDelete(Request $request, $id)
    {
        $user_id = $request->header('id');

        DB::beginTransaction();

        try {
            $sale_invoice_product = SaleInvoiceProducts::where('sale_invoice_id', $id)->where('user_id', $user_id)->delete();
            $sale_invoice = SaleInvoice::where('id', $id)->where('user_id', $user_id)->delete();

            if ($sale_invoice_product && $sale_invoice) {
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
