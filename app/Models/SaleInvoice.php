<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleInvoice extends Model
{
    protected $fillable = ['invoice_name', 'total', 'discount', 'vat', 'subtotal', 'paid', 'rest', 'customer_payable', 'user_id', 'customer_id'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
