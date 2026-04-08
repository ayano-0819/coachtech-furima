<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Condition;
use App\Models\Item;
use App\Models\Like;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_detail_page_displays_required_information()
    {
        $seller = User::factory()->create();

        $commentUser = User::factory()->create([
            'name' => 'コメント太郎',
        ]);

        $condition = Condition::create([
            'name' => '良好',
        ]);

        $category1 = Category::create([
            'name' => 'ファッション',
        ]);

        $category2 = Category::create([
            'name' => 'メンズ',
        ]);

        $item = Item::create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => 'これはテスト用の商品説明です。',
            'price' => 1200,
            'image_path' => 'items/test.jpg',
            'is_sold' => false,
        ]);

        $item->categories()->attach([$category1->id, $category2->id]);

        Like::create([
            'user_id' => $commentUser->id,
            'item_id' => $item->id,
        ]);

        Comment::create([
            'user_id' => $commentUser->id,
            'item_id' => $item->id,
            'content' => 'これはテストコメントです。',
        ]);

        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);
        $response->assertSee('テスト商品');
        $response->assertSee('テストブランド');
        $response->assertSee('¥1,200');
        $response->assertSee('これはテスト用の商品説明です。');
        $response->assertSee('商品説明');
        $response->assertSee('商品の情報');
        $response->assertSee('カテゴリー');
        $response->assertSee('商品の状態');
        $response->assertSee('ファッション');
        $response->assertSee('メンズ');
        $response->assertSee('良好');
        $response->assertSee('コメント(1)');
        $response->assertSee('コメント太郎');
        $response->assertSee('これはテストコメントです。');
        $response->assertSee('storage/items/test.jpg');
    }

    public function test_multiple_categories_are_displayed_on_item_detail_page()
    {
        $seller = User::factory()->create();

        $condition = Condition::create([
            'name' => '良好',
        ]);

        $category1 = Category::create([
            'name' => '家電',
        ]);

        $category2 = Category::create([
            'name' => 'インテリア',
        ]);

        $item = Item::create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => 'カテゴリ確認用商品',
            'brand_name' => 'カテゴリ確認ブランド',
            'description' => 'カテゴリ確認用の商品説明です。',
            'price' => 3000,
            'image_path' => 'items/category-test.jpg',
            'is_sold' => false,
        ]);

        $item->categories()->attach([$category1->id, $category2->id]);

        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);
        $response->assertSee('家電');
        $response->assertSee('インテリア');
    }
}
