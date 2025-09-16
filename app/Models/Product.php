<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'stock',
    ];

    // 🔹 Relationships
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category')->withTimestamps();
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }
}
