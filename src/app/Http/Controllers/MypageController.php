<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $user->name = $request->name;
        $user->postal_code = $request->postal_code;
        $user->address = $request->address;
        $user->building = $request->building;

        // ★画像処理
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profiles', 'public');
            $user->profile_image_path = $path;
        }
        $user->save();

        // ★ マイページに戻す
        return redirect()->route('mypage');
    }
}