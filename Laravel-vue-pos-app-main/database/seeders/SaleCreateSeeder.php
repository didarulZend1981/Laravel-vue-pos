<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\SaleInvoice;
use App\Models\SaleInvoiceProducts;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SaleCreateSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 1; // Assuming your logged-in user id or default user id

        // Assuming customers have ids 1 to 10
        $customerIds = range(1, 10);

        $productIds = Product::pluck('id')->toArray();

        for ($i = 1; $i <= 10; $i++) {
            $customerId = $customerIds[array_rand($customerIds)];

            // Generate a unique invoice name, e.g. INV-20250522-001
            $invoiceName = 'INV-' . date('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);

            // Select 5 to 10 unique products
            $numProducts = rand(5, 10);
            $selectedProductIds = (array)array_rand(array_flip($productIds), $numProducts);

            $total = 0;
            $productsData = [];

            foreach ($selectedProductIds as $productId) {
                $product = Product::find($productId);
                $qty = rand(1, 5);
                $rate = $product->price;
                $amount = $rate * $qty;

                $total += $amount;

                $productsData[] = [
                    'product_id' => $productId,
                    'product_name' => $product->name,
                    'qty' => $qty,
                    'rate' => $rate,
                    'sale_price' => $rate, // Assuming sale_price = rate here
                    'amount' => $amount,
                ];
            }

            // Discount and VAT fixed 10% for example
            $discountPercent = 10;
            $vatPercent = 10;

            $discountAmount = ($total * $discountPercent) / 100;
            $subtotal = $total - $discountAmount;
            $vatAmount = ($subtotal * $vatPercent) / 100;
            $customerPayable = $subtotal + $vatAmount;

            // Paid amount randomly between 50% to 100% of customer payable
            $paid = rand((int)($customerPayable * 0.5), (int)$customerPayable);
            $rest = $customerPayable - $paid;

            // Create sale invoice
            $saleInvoice = SaleInvoice::create([
                'invoice_name' => $invoiceName,
                'total' => $total,
                'discount' => $discountAmount,
                'vat' => $vatAmount,
                'subtotal' => $subtotal,
                'paid' => $paid,
                'rest' => $rest,
                'customer_payable' => $customerPayable,
                'user_id' => $userId,
                'customer_id' => $customerId,
            ]);

            // Insert sale invoice products
            foreach ($productsData as $prod) {
                SaleInvoiceProducts::create([
                    'sale_invoice_id' => $saleInvoice->id,
                    'user_id' => $userId,
                    'product_id' => $prod['product_id'],
                    'product_name' => $prod['product_name'],
                    'qty' => $prod['qty'],
                    'rate' => $prod['rate'],
                    'sale_price' => $prod['sale_price'],
                    'amount' => $prod['amount'],
                ]);
            }
        }
    }
}

