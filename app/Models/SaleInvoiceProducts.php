<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleInvoiceProducts extends Model
{
protected $fillable = ['sale_invoice_id', 'user_id', 'product_id', 'product_name', 'qty', 'rate', 'sale_price', 'amount'];

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
