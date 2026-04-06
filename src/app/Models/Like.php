<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    /**
     * 一括代入を許可するカラム
     * ・いいね作成時にまとめて保存できるようにする
     */
    protected $fillable = [
        'user_id',
        // いいねしたユーザーID

        'item_id',
        // いいね対象の商品ID
    ];

    /**
     * ユーザーとのリレーション（多対1）
     * ・1つのいいねは1人のユーザーに属する
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 商品とのリレーション（多対1）
     * ・1つのいいねは1つの商品に紐づく
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}