<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\Item;
use App\Models\Like;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 「商品名」で部分一致検索ができる
     */
    public function test_items_can_be_searched_by_partial_name_match(): void
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
            'name' => '赤いバッグ',
            'brand_name' => null,
            'description' => '赤いバッグです',
            'price' => 3000,
            'image_path' => 'items/red_bag.jpg',
            'is_sold' => false,
        ]);

        Item::create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => '青い靴',
            'brand_name' => null,
            'description' => '青い靴です',
            'price' => 4000,
            'image_path' => 'items/blue_shoes.jpg',
            'is_sold' => false,
        ]);

        $response = $this->get('/?keyword=バッグ');

        $response->assertStatus(200);
        $response->assertSee('赤いバッグ');
        $response->assertDontSee('青い靴');
    }

    /**
     * 検索状態がマイリストでも保持されている
     */
    public function test_search_keyword_is_kept_on_mylist_page(): void
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

        $likedItem1 = Item::create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => '赤いバッグ',
            'brand_name' => null,
            'description' => '赤いバッグです',
            'price' => 3000,
            'image_path' => 'items/red_bag.jpg',
            'is_sold' => false,
        ]);

        $likedItem2 = Item::create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => '青い靴',
            'brand_name' => null,
            'description' => '青い靴です',
            'price' => 4000,
            'image_path' => 'items/blue_shoes.jpg',
            'is_sold' => false,
        ]);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $likedItem1->id,
        ]);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $likedItem2->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist&keyword=バッグ');

        $response->assertStatus(200);

        // 検索結果として部分一致した商品だけ表示
        $response->assertSee('赤いバッグ');
        $response->assertDontSee('青い靴');

        // 検索キーワードが保持されている
        $response->assertSee('value="バッグ"', false);
    }
}