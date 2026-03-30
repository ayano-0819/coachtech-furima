<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;

class MypageController extends Controller
{
    // ★ マイページ表示
    public function show()
    {
        $user = Auth::user();

        $sellItems = $user->items()->latest()->get();
        $buyItems = $user->orders()->with('item')->latest()->get();

        return view('users.show', compact('user', 'sellItems', 'buyItems'));
    }

    // ★ プロフィール編集画面
    public function editProfile()
    {
        $user = Auth::user();

        return view('users.edit', compact('user'));
    }

    // ★ プロフィール更新
    public function updateProfile(ProfileRequest $request)
    {
        $user = auth()->user();

        // ★　画像処理
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profiles', 'public');
            $user->profile_image_path = $path;
        }

        $user->name = $request->name;
        $user->postal_code = $request->postal_code;
        $user->address = $request->address;
        $user->building = $request->building;
        $user->save();

        // ★ 商品一覧画面に戻す（コーチ確認済み）
        return redirect()->route('items.index');
    }
}