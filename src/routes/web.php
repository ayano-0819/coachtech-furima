<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\PurchaseController;

Route::get('/', [ItemController::class, 'index'])
    ->name('items.index');

// 商品詳細画面表示
Route::get('/item/{item_id}', [ItemController::class, 'show'])
    ->name('items.show');

Route::middleware('auth')->group(function () {
    // =======================
    // メール認証関連
    // =======================

    // メール認証誘導画面
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    // メール内リンククリック時（認証完了）
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        // 認証後はプロフィール設定へ
        return redirect()->route('mypage.profile.edit');
    })->middleware(['signed'])->name('verification.verify');

    // 認証メール再送
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', '認証メールを再送しました！');
    })->middleware(['throttle:6,1'])->name('verification.send');

    // =======================
    // メール認証済みユーザーのみ
    // =======================
    Route::middleware('verified')->group(function () {
        // マイページ表示
        Route::get('/mypage', [MypageController::class, 'show'])
            ->name('mypage');

        // プロフィール画面表示
        Route::get('/mypage/profile', [MypageController::class, 'editProfile'])
            ->name('mypage.profile.edit');

        // プロフィール更新処理
        Route::post('/mypage/profile', [MypageController::class, 'updateProfile'])
            ->name('mypage.profile.update');

        // 商品出品画面表示・出品処理
        Route::get('/sell', [ItemController::class, 'create'])
            ->name('items.create');
        Route::post('/sell', [ItemController::class, 'store'])
            ->name('items.store');

        // いいね機能
        Route::post('/item/{item_id}/like', [ItemController::class, 'like'])
            ->name('likes.store');
        Route::delete('/item/{item_id}/like', [ItemController::class, 'unlike'])
            ->name('likes.destroy');

        // コメント送信
        Route::post('/item/{item_id}/comments', [ItemController::class, 'storeComment'])
            ->name('comments.store');

        // 商品購入画面表示
        Route::get('/purchase/{item_id}', [PurchaseController::class, 'create'])
            ->name('purchase.create');

        // 住所変更画面表示
        Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])
            ->name('purchase.address.edit');

        // 住所変更更新
        Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])
            ->name('purchase.address.update');

        // stripe決済
        Route::post('/purchase/{item_id}/checkout', [PurchaseController::class, 'checkout'])
            ->name('purchase.checkout');
        Route::get('/purchase/success/{item_id}', [PurchaseController::class, 'success'])
            ->name('purchase.success');
        Route::get('/purchase/cancel/{item_id}', [PurchaseController::class, 'cancel'])
            ->name('purchase.cancel');
    });
});