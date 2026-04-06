<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;

class MypageController extends Controller
{
    /**
     * マイページ表示
     * ・出品した商品一覧
     * ・購入した商品一覧
     * を表示する
     */
    public function show()
    {
        // ログイン中のユーザーを取得する
        $user = Auth::user();

        // 出品した商品一覧を新しい順で取得する
        $sellItems = $user->items()->latest()->get();

        // 購入した商品一覧を商品情報付きで新しい順に取得する
        $buyItems = $user->orders()->with('item')->latest()->get();

        // マイページ画面へ渡す
        return view('users.show', compact('user', 'sellItems', 'buyItems'));
    }

    /**
     * プロフィール編集画面
     * ・現在のユーザー情報を表示する
     */
    public function editProfile()
    {
        // ログイン中のユーザーを取得する
        $user = Auth::user();

        // プロフィール編集画面へ渡す
        return view('users.edit', compact('user'));
    }

    /**
     * プロフィール更新処理
     * ・プロフィール画像
     * ・ユーザー名
     * ・住所情報
     * を更新する
     */
    public function updateProfile(ProfileRequest $request)
    {
        // ログイン中のユーザーを取得する
        $user = Auth::user();

        // 画像がアップロードされている場合は storage/app/public/profiles に保存する
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profiles', 'public');
            $user->profile_image_path = $path;
        }

        // 入力されたプロフィール情報をまとめて反映する
        $user->fill([
            'name' => $request->name,
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'building' => $request->building,
        ]);

        // users テーブルへ保存する
        $user->save();

        // 商品一覧画面へ戻る
        return redirect()->route('items.index');
    }
}