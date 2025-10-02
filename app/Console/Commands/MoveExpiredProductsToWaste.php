<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\PurchaseInvoiceProducts;
use App\Models\WasteProduct;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MoveExpiredProductsToWaste extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:move-expired-to-waste';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move expired products to waste if expired and still in stock';

    /**
     * Execute the console command.
     */


    public function handle(): void
    {
        $today = Carbon::today()->toDateString();

        // expired ও stock > 0 পণ্যগুলো নাও
        $expiredProducts = PurchaseInvoiceProducts::where('stock_qty', '>', 0)
            ->whereDate('expiry_date', '<=', $today)
            ->get();

        if ($expiredProducts->isEmpty()) {
            $this->info('No expired products found.');
            return;
        }

        DB::transaction(function () use ($expiredProducts) {
            foreach ($expiredProducts as $product) {

                // waste_products এ insert
                WasteProduct::create([
                    'product_id' => $product->product_id,
                    'waste_qty' => $product->stock_qty,
                    'purchase_price' => $product->purchase_price,
                    'case' => null,
                    'purchase_invoice_id' => $product->purchase_invoice_id,
                    'refound' => 0,
                ]);

                // stock কমাও
                $qtyToReduce = $product->stock_qty;
                $product->stock_qty = 0;
                $product->save();

                // main product stock কমাও
                $productStock = Product::find($product->product_id);
                if ($productStock) {
                    $productStock->stock -= $qtyToReduce;
                    if ($productStock->stock < 0) {
                        $productStock->stock = 0;
                    }
                    $productStock->save();
                }
            }
        });

        $this->info('Expired products moved to waste successfully.');
        $this->info('Command started at ' . now());
    }
}
