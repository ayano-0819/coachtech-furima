<?php

namespace App\Models;

use App\Models\Item;
use App\Models\Order;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * 一括代入を許可するカラム
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'postal_code',
        'address',
        'building',
        'profile_image_path',
    ];

    /**
     * JSON変換時に隠すカラム
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * 型キャスト
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 出品した商品（1対多）
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * 購入した注文（1対多）
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * いいね（1対多）
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * コメント（1対多）
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}