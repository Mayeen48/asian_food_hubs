<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'sku',
        'name',
        'description',
        'price',
        'currency',
        'unit',
        'image',
        'image_storage',
        'sort_order',
        'published',
        'category_id',
        'stock',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
