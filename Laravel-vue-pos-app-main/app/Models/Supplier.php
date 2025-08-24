<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'address', 'company_name', 'brand_id' , 'user_id'];

    protected $hidden = ['created_at', 'updated_at'];

    //relation with brand table
    public function brand(){
        return $this->belongsTo(Brand::class);
    }

    //relation with purchase invocie table
    public function invoices(){
        return $this->hasMany(PurchaseInvoice::class);
    }
}
