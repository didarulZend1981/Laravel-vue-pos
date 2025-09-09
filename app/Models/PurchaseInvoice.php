<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    protected $fillable = ['user_id', 'total', 'paid', 'rest', 'account_payable', 'supplier_id', 'invoice_name','created_at'];


    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }
}
