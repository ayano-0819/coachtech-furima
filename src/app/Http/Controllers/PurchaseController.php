<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\PurchaseRequest;

class PurchaseController extends Controller
{
    /**
     * 購入画面表示
     */
    public function create($item_id)
    {
        // 商品取得
        $item = Item::findOrFail($item_id);

        // ログインユーザー取得
        $user = Auth::user();

        return view('purchase.create', compact('item', 'user'));
    }

    /**
     * 配送先変更画面
     */
    public function editAddress($item_id)
    {
        // ログインユーザー取得
        $user = Auth::user();

        return view('purchase.address', compact('user', 'item_id'));
    }

    /**
     * 配送先更新（session保存）
     */
    public function updateAddress(AddressRequest $request, $item_id)
    {
        // 配送先を session に保存
        session([
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'building' => $request->building,
        ]);

        // 購入画面へ戻る（支払い方法も引き継ぐ）
        return redirect()->route('purchase.create', [
            'item_id' => $item_id,
            'payment_method' => $request->payment_method,
        ]);
    }

    /**
     * Stripe決済画面へ遷移
     */
    public function checkout(PurchaseRequest $request, $item_id)
    {
        // 商品取得
        $item = Item::findOrFail($item_id);

        // ログインユーザー取得
        $user = Auth::user();

        // 売り切れチェック
        if ($item->is_sold) {
            return redirect()->route('items.show', ['item_id' => $item->id])
                ->with('error', 'この商品はすでに購入されています。');
        }

        // 自分の商品チェック
        if ($item->user_id === Auth::id()) {
            return redirect()->route('items.show', ['item_id' => $item->id])
                ->with('error', '自分の商品は購入できません。');
        }

        // 配送先取得（session優先）
        $postalCode = session('postal_code', $user->postal_code);
        $address = session('address', $user->address);
        $building = session('building', $user->building);

        // 配送先未入力チェック
        if (empty($postalCode) || empty($address)) {
            return redirect()->route('purchase.address.edit', ['item_id' => $item->id])
                ->with('error', '配送先を入力してください。');
        }

        // 支払い方法を数値変換
        $paymentMethod = $request->payment_method === 'card' ? 1 : 2;

        // session保存
        session([
            'payment_method' => $paymentMethod,
            'postal_code' => $postalCode,
            'address' => $address,
            'building' => $building,
        ]);

        // Stripe設定
        Stripe::setApiKey(config('services.stripe.secret'));

        // Checkoutセッション作成
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

    /**
     * 購入成功処理
     */
    public function success($item_id)
    {
        // 商品取得
        $item = Item::findOrFail($item_id);

        // ログインユーザー取得
        $user = Auth::user();

        // 二重購入防止
        if ($item->is_sold) {
            return redirect()->route('items.show', ['item_id' => $item->id])
                ->with('success', 'この商品はすでに購入処理済みです。');
        }

        // 注文 + sold更新（トランザクション）
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

    /**
     * 決済キャンセル
     */
    public function cancel($item_id)
    {
        return redirect()->route('purchase.create', ['item_id' => $item_id])
            ->with('error', '決済をキャンセルしました。');
    }
}