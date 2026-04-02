<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingAddressTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 送付先住所変更画面で登録した住所が商品購入画面に反映されている
     */
    public function test_updated_shipping_address_is_reflected_on_purchase_page()
    {
        $user = User::factory()->create([
            'postal_code' => '111-1111',
            'address' => '東京都新宿区1-1-1',
            'building' => '旧住所ビル',
        ]);

        $seller = User::factory()->create();

        $condition = Condition::create([
            'name' => '良好',
        ]);

        $item = Item::create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => '配送先確認用商品',
            'brand_name' => 'テストブランド',
            'description' => '配送先確認用です。',
            'price' => 3000,
            'image_path' => 'items/test.jpg',
            'is_sold' => false,
        ]);

        $response = $this->actingAs($user)->post(
            route('purchase.address.update', ['item_id' => $item->id]),
            [
                'postal_code' => '222-2222',
                'address' => '大阪府大阪市2-2-2',
                'building' => '新住所マンション202',
                'payment_method' => 'card',
            ]
        );

        $response->assertRedirect(route('purchase.create', [
            'item_id' => $item->id,
            'payment_method' => 'card',
        ]));

        $purchaseResponse = $this->actingAs($user)
            ->withSession([
                'postal_code' => '222-2222',
                'address' => '大阪府大阪市2-2-2',
                'building' => '新住所マンション202',
            ])
            ->get(route('purchase.create', [
                'item_id' => $item->id,
                'payment_method' => 'card',
            ]));

        $purchaseResponse->assertStatus(200);
        $purchaseResponse->assertSee('222-2222');
        $purchaseResponse->assertSee('大阪府大阪市2-2-2');
        $purchaseResponse->assertSee('新住所マンション202');
    }

    /**
     * 購入した商品に送付先住所が紐づいて登録される
     */
    public function test_purchased_item_is_saved_with_updated_shipping_address()
    {
        $buyer = User::factory()->create([
            'postal_code' => '111-1111',
            'address' => '東京都新宿区1-1-1',
            'building' => '旧住所ビル',
        ]);

        $seller = User::factory()->create();

        $condition = Condition::create([
            'name' => '良好',
        ]);

        $item = Item::create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => '配送先保存確認用商品',
            'brand_name' => 'テストブランド',
            'description' => '配送先保存確認用です。',
            'price' => 5000,
            'image_path' => 'items/test.jpg',
            'is_sold' => false,
        ]);

        $this->actingAs($buyer)
            ->withSession([
                'payment_method' => 1,
                'postal_code' => '333-3333',
                'address' => '福岡県福岡市3-3-3',
                'building' => '配送先ビル303',
            ]);

        $response = $this->get(route('purchase.success', ['item_id' => $item->id]));

        $response->assertRedirect(route('items.index'));

        $this->assertDatabaseHas('orders', [
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'payment_method' => 1,
            'postal_code' => '333-3333',
            'address' => '福岡県福岡市3-3-3',
            'building' => '配送先ビル303',
        ]);
    }
}