<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExhibitionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 商品出品画面で必要な情報が正しく保存される
     */
    public function test_user_can_create_item_with_all_required_fields()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $condition = Condition::create([
            'name' => '良好',
        ]);

        $category1 = Category::create([
            'name' => 'ファッション',
        ]);

        $category2 = Category::create([
            'name' => 'メンズ',
        ]);

        $response = $this->actingAs($user)->post(route('items.store'), [
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => 'テスト用の商品説明です。',
            'price' => 5000,
            'condition_id' => $condition->id,
            'categories' => [$category1->id, $category2->id],
            'image' => UploadedFile::fake()->create('test.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => 'テスト用の商品説明です。',
            'price' => 5000,
            'condition_id' => $condition->id,
        ]);

        $item = Item::first();

        $this->assertDatabaseHas('category_items', [
            'item_id' => $item->id,
            'category_id' => $category1->id,
        ]);

        $this->assertDatabaseHas('category_items', [
            'item_id' => $item->id,
            'category_id' => $category2->id,
        ]);
    }
}