<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\Item;
use App\Models\Like;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * いいねアイコンを押すと、いいねした商品として登録され、いいね数が増加表示される
     */
    public function test_user_can_like_an_item_and_like_count_is_displayed()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $condition = Condition::create([
            'name' => '良好',
        ]);

        $item = Item::create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => 'テスト説明',
            'price' => 1200,
            'image_path' => 'items/test.jpg',
            'is_sold' => false,
        ]);

        $response = $this->actingAs($user)->post("/item/{$item->id}/like");

        $response->assertRedirect();

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $detailResponse = $this->actingAs($user)->get("/item/{$item->id}");
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('1');
        $detailResponse->assertSee('images/heart-liked.png');
    }

    /**
     * 追加済みのアイコンは色が変化する
     */
    public function test_liked_item_shows_liked_icon()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $condition = Condition::create([
            'name' => '良好',
        ]);

        $item = Item::create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => 'テスト説明',
            'price' => 1200,
            'image_path' => 'items/test.jpg',
            'is_sold' => false,
        ]);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->get("/item/{$item->id}");

        $response->assertStatus(200);
        $response->assertSee('images/heart-liked.png');
        $response->assertDontSee('images/heart-default.png');
    }

    /**
     * 再度いいねアイコンを押すと、いいねを解除でき、いいね数が減少表示される
     */
    public function test_user_can_unlike_an_item_and_like_count_decreases()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $condition = Condition::create([
            'name' => '良好',
        ]);

        $item = Item::create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => 'テスト説明',
            'price' => 1200,
            'image_path' => 'items/test.jpg',
            'is_sold' => false,
        ]);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->delete("/item/{$item->id}/like");

        $response->assertRedirect();

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $detailResponse = $this->actingAs($user)->get("/item/{$item->id}");

        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('0');
        $detailResponse->assertSee('images/heart-default.png');
    }
}
