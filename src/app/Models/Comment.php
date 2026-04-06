<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    /**
     * 一括代入を許可するカラム
     * ・コメント投稿時にまとめて保存できるようにする
     */
    protected $fillable = [
        'user_id',
        // コメントしたユーザーID

        'item_id',
        // コメント対象の商品ID

        'content',
        // コメント本文
    ];

    /**
     * ユーザーとのリレーション（多対1）
     * ・1つのコメントは1人のユーザーに属する
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 商品とのリレーション（多対1）
     * ・1つのコメントは1つの商品に紐づく
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}