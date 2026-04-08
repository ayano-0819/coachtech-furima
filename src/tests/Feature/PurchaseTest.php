<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\Item;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_complete_purchase()
    {
        $buyer = User::factory()->create([
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストマンション101',
        ]);

        $seller = User::factory()->create();

        $condition = Condition::create([
            'name' => '良好',
        ]);

        $item = Item::create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => '購入用商品',
            'brand_name' => 'テストブランド',
            'description' => '購入テスト用の商品です。',
            'price' => 5000,
            'image_path' => 'items/test.jpg',
            'is_sold' => false,
        ]);

        $this->actingAs($buyer)
            ->withSession([
                'payment_method' => 1,
                'postal_code' => '123-4567',
                'address' => '東京都渋谷区1-1-1',
                'building' => 'テストマンション101',
            ]);

        $response = $this->get(route('purchase.success', ['item_id' => $item->id]));

        $response->assertRedirect(route('items.index'));

        $this->assertDatabaseHas('orders', [
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'payment_method' => 1,
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストマンション101',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'is_sold' => true,
        ]);
    }

    public function test_purchased_item_is_displayed_as_sold_on_item_index()
    {
        $buyer = User::factory()->create([
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストマンション101',
        ]);

        $seller = User::factory()->create();

        $condition = Condition::create([
            'name' => '良好',
        ]);

        $item = Item::create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => 'Sold確認用商品',
            'brand_name' => 'テストブランド',
            'description' => 'Sold表示確認用です。',
            'price' => 3000,
            'image_path' => 'items/test.jpg',
            'is_sold' => false,
        ]);

        $this->actingAs($buyer)
            ->withSession([
                'payment_method' => 1,
                'postal_code' => '123-4567',
                'address' => '東京都渋谷区1-1-1',
                'building' => 'テストマンション101',
            ]);

        $this->get(route('purchase.success', ['item_id' => $item->id]));

        $response = $this->get(route('items.index'));

        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    public function test_purchased_item_is_displayed_in_mypage_buy_list()
    {
        $buyer = User::factory()->create([
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストマンション101',
        ]);

        $seller = User::factory()->create();

        $condition = Condition::create([
            'name' => '良好',
        ]);

        $item = Item::create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => '購入履歴確認用商品',
            'brand_name' => 'テストブランド',
            'description' => '購入履歴確認用です。',
            'price' => 4000,
            'image_path' => 'items/test.jpg',
            'is_sold' => false,
        ]);

        $this->actingAs($buyer)
            ->withSession([
                'payment_method' => 1,
                'postal_code' => '123-4567',
                'address' => '東京都渋谷区1-1-1',
                'building' => 'テストマンション101',
            ]);

        $this->get(route('purchase.success', ['item_id' => $item->id]));

        $response = $this->actingAs($buyer)->get('/mypage?page=buy');

        $response->assertStatus(200);
        $response->assertSee('購入履歴確認用商品');
    }
}
