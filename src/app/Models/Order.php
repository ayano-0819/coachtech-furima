<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * 一括代入を許可するカラム
     * ・注文作成時にまとめて保存できるようにする
     */
    protected $fillable = [
        'user_id',
        // 購入者のユーザーID

        'item_id',
        // 購入された商品のID

        'payment_method',
        // 支払い方法（例：1=カード、2=コンビニ）

        'postal_code',
        // 配送先の郵便番号

        'address',
        // 配送先の住所

        'building',
        // 建物名（任意）
    ];

    /**
     * ユーザーとのリレーション（多対1）
     * ・1つの注文は1人のユーザーに属する
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 商品とのリレーション（多対1）
     * ・1つの注文は1つの商品に紐づく
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
