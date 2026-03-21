<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function condition()
    {
        return $this->belongsTo(\App\Models\Condition::class);
    }

    public function categories()
    {
        return $this->belongsToMany(\App\Models\Category::class, 'category_items')
                    ->withTimestamps();
    }

    public function categoryItems()
    {
        return $this->hasMany(\App\Models\CategoryItem::class);
    }

    public function order()
    {
        return $this->hasOne(\App\Models\Order::class);
    }

    public function likes()
    {
        return $this->hasMany(\App\Models\Like::class);
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\Comment::class);
    }

}
