<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\ItemController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ItemController::class, 'index'])->name('items.index');

Route::middleware('auth')->group(function () {

    // プロフィール画面表示
    Route::get('/mypage/profile', [MypageController::class, 'editProfile'])
        ->name('mypage.profile.edit');
    // 更新処理
    Route::post('/mypage/profile', [MypageController::class, 'updateProfile'])
        ->name('mypage.profile.update');

});
