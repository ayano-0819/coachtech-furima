<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'category_id',
    ];

    public function item()
    {
        return $this->belongsTo(\App\Models\Item::class);
    }

    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }
}
