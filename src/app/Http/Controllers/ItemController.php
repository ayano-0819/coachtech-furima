<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
{
    if ($request->tab === 'mylist') {

        // マイリスト表示
        $items = Item::whereHas('likes', function ($query) {
            $query->where('user_id', auth()->id());
        })->get();

    } else {

        // おすすめ（通常商品）
        $items = Item::all();
    }

    return view('items.index', compact('items'));
}
}
