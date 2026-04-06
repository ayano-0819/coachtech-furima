<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;

class MypageController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        $sellItems = $user->items()->latest()->get();

        $buyItems = $user->orders()->with('item')->latest()->get();

        return view('users.show', compact('user', 'sellItems', 'buyItems'));
    }

    public function editProfile()
    {
        $user = Auth::user();

        return view('users.edit', compact('user'));
    }

    public function updateProfile(ProfileRequest $request)
    {
        $user = Auth::user();

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profiles', 'public');
            $user->profile_image_path = $path;
        }

        $user->fill([
            'name' => $request->name,
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'building' => $request->building,
        ]);

        $user->save();

        return redirect()->route('items.index');
    }
}