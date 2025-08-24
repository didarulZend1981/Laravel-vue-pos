<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'zip', 'address', 'comment', 'user_id'];

    protected $hidden = ['created_at', 'updated_at'];

    //relation with invoice table
    public function invoices(){
        return $this->hasMany(SaleInvoice::class);
    }
}
