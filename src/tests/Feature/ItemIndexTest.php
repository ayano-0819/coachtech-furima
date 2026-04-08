<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_items_are_displayed(): void
    {
        $condition = Condition::create([
            'name' => '良好',
        ]);

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

        $item1 = Item::create([
            'user_id' => $seller1->id,
            'condition_id' => $condition->id,
            'name' => '腕時計',
            'brand_name' => 'Rolax',
            'description' => 'テスト用腕時計',
            'price' => 15000,
            'image_path' => 'items/watch.jpg',
            'is_sold' => false,
        ]);

        $item2 = Item::create([
            'user_id' => $seller2->id,
            'condition_id' => $condition->id,
            'name' => 'HDD',
            'brand_name' => '西芝',
            'description' => 'テスト用HDD',
            'price' => 5000,
            'image_path' => 'items/hdd.jpg',
            'is_sold' => false,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('腕時計');
        $response->assertSee('HDD');
    }

    public function test_sold_label_is_displayed_for_sold_items(): void
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
            'name' => '腕時計',
            'brand_name' => 'Rolax',
            'description' => 'テスト用腕時計',
            'price' => 15000,
            'image_path' => 'items/watch.jpg',
            'is_sold' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('腕時計');
        $response->assertSee('Sold');
    }

    public function test_own_items_are_not_displayed_for_authenticated_user(): void
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

        $otherUser = User::create([
            'name' => '他ユーザー',
            'email' => 'other@example.com',
            'password' => bcrypt('password'),
        ]);

        Item::create([
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'name' => '自分の商品',
            'brand_name' => null,
            'description' => '自分の商品です',
            'price' => 1000,
            'image_path' => 'items/my_item.jpg',
            'is_sold' => false,
        ]);

        Item::create([
            'user_id' => $otherUser->id,
            'condition_id' => $condition->id,
            'name' => '他人の商品',
            'brand_name' => null,
            'description' => '他人の商品です',
            'price' => 2000,
            'image_path' => 'items/other_item.jpg',
            'is_sold' => false,
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('自分の商品');
        $response->assertSee('他人の商品');
    }
}
