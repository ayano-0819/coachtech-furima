<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    /**
     * 一括代入を許可するカラム
     * ・商品作成時にまとめて保存できるようにする
     */
    protected $fillable = [
        'user_id',
        // 出品者のユーザーID

        'condition_id',
        // 商品状態ID

        'name',
        // 商品名

        'brand_name',
        // ブランド名（任意）

        'description',
        // 商品説明

        'price',
        // 価格

        'image_path',
        // 保存した商品画像パス

        'is_sold',
        // 売り切れ状態
    ];

    /**
     * ユーザーとのリレーション（多対1）
     * ・1つの商品は1人のユーザーに属する
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 商品状態とのリレーション（多対1）
     * ・1つの商品は1つの状態に属する
     */
    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    /**
     * カテゴリーとのリレーション（多対多）
     * ・1つの商品は複数カテゴリーを持てる
     * ・中間テーブル category_items を使う
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_items')
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

    /**
     * 注文とのリレーション（1対1）
     * ・1つの商品は1つの注文に紐づく
     */
    public function order()
    {
        return $this->hasOne(Order::class);
    }

    /**
     * いいねとのリレーション（1対多）
     * ・1つの商品に複数のいいねがつく
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * コメントとのリレーション（1対多）
     * ・1つの商品に複数のコメントがつく
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
