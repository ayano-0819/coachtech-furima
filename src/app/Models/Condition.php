<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    use HasFactory;

    /**
     * 一括代入を許可するカラム
     * ・状態名を登録できるようにする
     */
    protected $fillable = [
        'name',
        // 状態名（例：良好、やや傷あり など）
    ];

    /**
     * 商品とのリレーション（1対多）
     * ・1つの状態に複数の商品が紐づく
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }
}