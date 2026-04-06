<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\PurchaseController;

Route::get('/', [ItemController::class, 'index'])
    ->name('items.index');

Route::get('/item/{item_id}', [ItemController::class, 'show'])
    ->name('items.show');

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect()->route('mypage.profile.edit');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', '認証メールを再送しました！');
    })->middleware(['throttle:6,1'])->name('verification.send');

    Route::middleware('verified')->group(function () {
        Route::get('/mypage', [MypageController::class, 'show'])
            ->name('mypage');

        Route::get('/mypage/profile', [MypageController::class, 'editProfile'])
            ->name('mypage.profile.edit');

        Route::post('/mypage/profile', [MypageController::class, 'updateProfile'])
            ->name('mypage.profile.update');

        Route::get('/sell', [ItemController::class, 'create'])
            ->name('items.create');
        Route::post('/sell', [ItemController::class, 'store'])
            ->name('items.store');

        Route::post('/item/{item_id}/like', [ItemController::class, 'like'])
            ->name('likes.store');
        Route::delete('/item/{item_id}/like', [ItemController::class, 'unlike'])
            ->name('likes.destroy');

        Route::post('/item/{item_id}/comments', [ItemController::class, 'storeComment'])
            ->name('comments.store');

        Route::get('/purchase/{item_id}', [PurchaseController::class, 'create'])
            ->name('purchase.create');

        Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])
            ->name('purchase.address.edit');

        Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])
            ->name('purchase.address.update');

        Route::post('/purchase/{item_id}/checkout', [PurchaseController::class, 'checkout'])
            ->name('purchase.checkout');
        Route::get('/purchase/success/{item_id}', [PurchaseController::class, 'success'])
            ->name('purchase.success');
        Route::get('/purchase/cancel/{item_id}', [PurchaseController::class, 'cancel'])
            ->name('purchase.cancel');
    });
});