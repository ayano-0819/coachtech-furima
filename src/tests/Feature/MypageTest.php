<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\Item;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MypageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_profile_displays_profile_image_name_and_selling_items()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'profile_image_path' => 'profiles/test-user.jpg',
        ]);

        $condition = Condition::create([
            'name' => '良好',
        ]);

        $sellItem1 = Item::create([
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'name' => '出品商品1',
            'brand_name' => 'ブランドA',
            'description' => '出品商品の説明1',
            'price' => 1000,
            'image_path' => 'items/sell1.jpg',
            'is_sold' => false,
        ]);

        $sellItem2 = Item::create([
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'name' => '出品商品2',
            'brand_name' => 'ブランドB',
            'description' => '出品商品の説明2',
            'price' => 2000,
            'image_path' => 'items/sell2.jpg',
            'is_sold' => false,
        ]);

        $response = $this->actingAs($user)->get('/mypage?page=sell');

        $response->assertStatus(200);
        $response->assertSee('テストユーザー');
        $response->assertSee('storage/profiles/test-user.jpg');
        $response->assertSee('出品商品1');
        $response->assertSee('出品商品2');
    }

    public function test_user_profile_displays_bought_items()
    {
        $buyer = User::factory()->create([
            'name' => '購入者ユーザー',
            'profile_image_path' => 'profiles/buyer.jpg',
        ]);

        $seller = User::factory()->create();

        $condition = Condition::create([
            'name' => '良好',
        ]);

        $boughtItem1 = Item::create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => '購入商品1',
            'brand_name' => 'ブランドC',
            'description' => '購入商品の説明1',
            'price' => 3000,
            'image_path' => 'items/buy1.jpg',
            'is_sold' => true,
        ]);

        $boughtItem2 = Item::create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => '購入商品2',
            'brand_name' => 'ブランドD',
            'description' => '購入商品の説明2',
            'price' => 4000,
            'image_path' => 'items/buy2.jpg',
            'is_sold' => true,
        ]);

        Order::create([
            'user_id' => $buyer->id,
            'item_id' => $boughtItem1->id,
            'payment_method' => 1,
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストビル101',
        ]);

        Order::create([
            'user_id' => $buyer->id,
            'item_id' => $boughtItem2->id,
            'payment_method' => 1,
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストビル101',
        ]);

        $response = $this->actingAs($buyer)->get('/mypage?page=buy');

        $response->assertStatus(200);
        $response->assertSee('購入者ユーザー');
        $response->assertSee('storage/profiles/buyer.jpg');
        $response->assertSee('購入商品1');
        $response->assertSee('購入商品2');
    }
}
