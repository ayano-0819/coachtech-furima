<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryItem extends Model
{
    use HasFactory;

    /**
     * 一括代入を許可するカラム
     * ・中間テーブルとして商品とカテゴリーの紐づけを保存する
     */
    protected $fillable = [
        'item_id',
        // 商品ID

        'category_id',
        // カテゴリーID
    ];

    /**
     * 商品とのリレーション（多対1）
     * ・1つの中間レコードは1つの商品に属する
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * カテゴリーとのリレーション（多対1）
     * ・1つの中間レコードは1つのカテゴリーに属する
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}