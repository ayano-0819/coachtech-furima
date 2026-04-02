<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\Item;
use App\Models\Like;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * いいねした商品だけが表示される
     */
    public function test_only_liked_items_are_displayed_in_mylist(): void
    {
        $condition = Condition::create([
            'name' => '良好',
        ]);

        $user = User::create([
            'name' => 'ログインユーザー',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->email_verified_at = now();
        $user->save();

        $seller1 = User::create([
            'name' => '出品者1',
            'email' => 'seller1@example.com',
            'password' => bcrypt('password'),
        ]);

        $seller2 = User::create([
            'name' => '出品者2',
            'email' => 'seller2@example.com',
            'password' => bcrypt('password'),
        ]);

        $likedItem = Item::create([
            'user_id' => $seller1->id,
            'condition_id' => $condition->id,
            'name' => 'いいねした商品',
            'brand_name' => null,
            'description' => 'いいねした商品です',
            'price' => 1000,
            'image_path' => 'items/liked.jpg',
            'is_sold' => false,
        ]);

        $notLikedItem = Item::create([
            'user_id' => $seller2->id,
            'condition_id' => $condition->id,
            'name' => 'いいねしていない商品',
            'brand_name' => null,
            'description' => 'いいねしていない商品です',
            'price' => 2000,
            'image_path' => 'items/not_liked.jpg',
            'is_sold' => false,
        ]);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('いいねした商品');
        $response->assertDontSee('いいねしていない商品');
    }

    /**
     * 購入済み商品は「Sold」と表示される
     */
    public function test_sold_label_is_displayed_for_sold_items_in_mylist(): void
    {
        $condition = Condition::create([
            'name' => '良好',
        ]);

        $user = User::create([
            'name' => 'ログインユーザー',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->email_verified_at = now();
        $user->save();

        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => bcrypt('password'),
        ]);

        $soldItem = Item::create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => '購入済み商品',
            'brand_name' => null,
            'description' => '購入済み商品です',
            'price' => 3000,
            'image_path' => 'items/sold.jpg',
            'is_sold' => true,
        ]);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $soldItem->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('購入済み商品');
        $response->assertSee('Sold');
    }

    /**
     * 未認証の場合は何も表示されない
     */
    public function test_nothing_is_displayed_for_guest_in_mylist(): void
    {
        $condition = Condition::create([
            'name' => '良好',
        ]);

        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => bcrypt('password'),
        ]);

        Item::create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => '商品A',
            'brand_name' => null,
            'description' => '商品Aです',
            'price' => 1000,
            'image_path' => 'items/a.jpg',
            'is_sold' => false,
        ]);

        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertDontSee('商品A');
    }
}