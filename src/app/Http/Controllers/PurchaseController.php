<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\PurchaseRequest;

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

    public function updateAddress(AddressRequest $request, $item_id)
    {
        session([
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'building' => $request->building,
        ]);

        return redirect()->route('purchase.create', [
        'item_id' => $item_id,
        'payment_method' => $request->payment_method,
        ]);
    }

    public function checkout(PurchaseRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = auth()->user();

        if ($item->is_sold) {
            return redirect()->route('items.show', ['item_id' => $item->id])
                ->with('error', 'この商品はすでに購入されています。');
        }

        if ($item->user_id === auth()->id()) {
            return redirect()->route('items.show', ['item_id' => $item->id])
                ->with('error', '自分の商品は購入できません。');
        }

        $postalCode = session('postal_code', $user->postal_code);
        $address = session('address', $user->address);
        $building = session('building', $user->building);

        if (empty($postalCode) || empty($address)) {
            return redirect()->route('purchase.address.edit', ['item_id' => $item->id])
                ->with('error', '配送先を入力してください。');
    }

        // 支払い方法を一時保存
        // 例：カード支払い=1、コンビニ支払い=2
        $paymentMethod = $request->payment_method === 'card' ? 1 : 2;

        session([
            'payment_method' => $paymentMethod,
            'postal_code' => session('postal_code', $user->postal_code),
            'address' => session('address', $user->address),
            'building' => session('building', $user->building),
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'success_url' => route('purchase.success', ['item_id' => $item->id]),
            'cancel_url' => route('purchase.cancel', ['item_id' => $item->id]),
        ]);

        return redirect($session->url);
    }

    public function success($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = auth()->user();

        if ($item->is_sold) {
            return redirect()->route('items.show', ['item_id' => $item->id])
                ->with('success', 'この商品はすでに購入処理済みです。');
        }

        DB::transaction(function () use ($item, $user) {
            Order::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'payment_method' => session('payment_method', 1),
                'postal_code' => session('postal_code', $user->postal_code),
                'address' => session('address', $user->address),
                'building' => session('building', $user->building),
            ]);

            $item->update([
                'is_sold' => true,
            ]);
        });

        return redirect()->route('items.index')
            ->with('success', '購入が完了しました。');
    }

    public function cancel($item_id)
    {
        return redirect()->route('purchase.create', ['item_id' => $item_id])
            ->with('error', '決済をキャンセルしました。');
    }
}