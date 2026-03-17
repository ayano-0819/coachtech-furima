<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\ItemController;


Route::get('/', [ItemController::class, 'index'])->name('items.index');

Route::middleware('auth')->group(function () {

    // プロフィール画面表示
    Route::get('/mypage/profile', [MypageController::class, 'editProfile'])
        ->name('mypage.profile.edit');

    // 更新処理
    Route::post('/mypage/profile', [MypageController::class, 'updateProfile'])
        ->name('mypage.profile.update');

    // 商品出品画面表示・出品処理
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');

    // いいね機能
    Route::post('/item/{item_id}/like', [ItemController::class, 'like'])
        ->name('likes.store');

    Route::delete('/item/{item_id}/like', [ItemController::class, 'unlike'])
        ->name('likes.destroy');
});

// 商品詳細画面表示
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');