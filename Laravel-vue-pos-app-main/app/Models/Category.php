<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'description', 'user_id', 'image'];
    protected $hidden = ['created_at', 'updated_at'];

    // Category.php
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
