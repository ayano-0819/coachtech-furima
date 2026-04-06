<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * 一括代入を許可するカラム
     * ・カテゴリー名を登録できるようにする
     */
    protected $fillable = [
        'name',
        // カテゴリー名
    ];

    /**
     * 商品とのリレーション（多対多）
     * ・1つのカテゴリーに複数の商品が紐づく
     * ・中間テーブル category_items を使う
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'category_items')
                    ->withTimestamps();
    }

    /**
     * category_items テーブルとのリレーション（1対多）
     * ・中間テーブルのレコードを直接扱いたい時に使う
     */
    public function categoryItems()
    {
        return $this->hasMany(CategoryItem::class);
    }
}