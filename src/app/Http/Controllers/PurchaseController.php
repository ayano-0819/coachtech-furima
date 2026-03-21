<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function create($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = auth()->user();

        return view('purchase.create', compact('item', 'user'));
    }

    public function editAddress($item_id)
    {
        $user = auth()->user();

        return view('purchase.address', compact('user', 'item_id'));
    }

    public function updateAddress(Request $request, $item_id)
    {
        session([
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'building' => $request->building,
        ]);

        return redirect()->route('purchase.create', ['item_id' => $item_id]);
    }
}
