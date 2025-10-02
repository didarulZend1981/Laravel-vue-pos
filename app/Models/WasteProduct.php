<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WasteProduct extends Model{
        protected $fillable = [
            'product_id',
            'waste_qty',
            'purchase_price',
            'case',
            'purchase_invoice_id',
            'refound',
        ];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function purchaseInvoice(){
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id');
    }


}
