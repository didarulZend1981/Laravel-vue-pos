<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'brand_id', 'category_id', 'user_id', 'description', 'price', 'unit', 'stock','image'];

    //relation with brand table
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    //relation with category table
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    //relation with user table
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $hidden = ['created_at', 'updated_at'];
}
