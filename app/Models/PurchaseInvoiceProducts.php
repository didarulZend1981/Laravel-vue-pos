<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoiceProducts extends Model
{
    protected $fillable = ['purchase_invoice_id', 'product_id', 'user_id', 'qty','stock_qty', 'purchase_price','created_at'];

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
